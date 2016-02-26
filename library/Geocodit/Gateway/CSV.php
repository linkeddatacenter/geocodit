<?php
/**
 * 
 * @author Enrico Fagnoni <enrico@linkeddata.center>
 * @copyright (c) 2016 LinkedData.Center. All right reserved.
 * @package geocodit
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 */

namespace Geocodit\Gateway;

class CSV extends AbstractGateway {
	
	protected $selector = null; 
	
	/**
	 * $function is a closure function that get a data array (a row readed by fgetcsv) and returns an array of 5 values:
	 * ($civico, $odonimo, $idComune, $latitude, $longitude ). 
	 * 		$civico can be null
	 * 		$idComune is the comune name or istat code
	 */
	public function setFieldsSelector( $function ){
		$this->selector = $function;
		return $this ;
	}
	


	public function getStream(){
		
		$source = $this->getSource();
		
		// open input stream
		if(!($handle = fopen($source, 'r'))) { throw new \Exception("Error Processing Request", 404); }
		
		//initialize output stream
		$stream = tmpfile();
		fwrite($stream, "# Source: $source
@prefix geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> .
@prefix gco: <http://geocodit.linkeddata.center/ontology#> .
");		
				
	    // get the first row, which contains the column-titles
	    $header = fgetcsv($handle);		
			
	    // loop through the file line-by-line
	    $i=0;
	    while(($data = fgetcsv($handle, 2000 , ';')) !== false) {
	    	$i++;
	    	$selector =  $this->selector;
			list ($civico, $odonimo, $idComune, $latitude, $longitude ) = 	$selector($data);

			// $idComune can be the istat code or a name, in this case must be normalized
			$encodedIdComune = GwHelpers::encodeForUri($idComune);
			$civicoProp = $civico?'gco:haNumeroCivico "'.GwHelpers::quote($civico).'" ;':'';
			
			fwrite( $stream, "
<$source#$i> a gco:Luogo ;
	gco:haComune <urn:geocodit:comune:$encodedIdComune>  ;
	$civicoProp
	gco:haToponimoStradale \"".GwHelpers::quote($odonimo)."\" ;
	geo:lat ".GwHelpers::toFloat($latitude)." ;
	geo:long ".GwHelpers::toFloat($longitude)." 
.");
		}
	    fclose($handle);
		
		// rewind and return output stream
		rewind($stream);		  
		return $stream;		

	}
	
} //END