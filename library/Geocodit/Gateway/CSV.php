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
	
	
	/**
	 * Trasform an  csv stream into a geocodit rdf stream
	 */
	public function trasform($csvStream){
		
		$csvMetadata = stream_get_meta_data ($csvStream );
		$source = $csvMetadata['uri'];
		
		$rdfStream = tmpfile();
		
		//initialize output stream
		fwrite($rdfStream, "@prefix geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> .
@prefix gco: <http://linkeddata.center/ontology/geocodit/v1#> .
@prefix dct: <http://purl.org/dc/terms/> .
@prefix : <#> .

<> dct:source <$source> .
");		
				
	    // get the first row, which contains the column-titles
	    $header = fgetcsv($csvStream);		
			
	    // loop through the file line-by-line
	    $i=0; $lastSeenData= '';
	    while(($data = fgetcsv($csvStream, 2000 , ';')) !== false) {
	    	$i++;
	    	$selector =  $this->selector;
			$extractedData = $selector($data);
			$uniqueID = md5(implode(',', $extractedData));
			// qucik and dirty way to remove subsequent duplicates
			if($uniqueID==$lastSeenData) continue;
			$lastSeenData = $uniqueID;
			list ($cap, $civico, $odonimo, $idComune, $latitude, $longitude ) = $extractedData;

			// $idComune can be the istat code or a name, in this case must be normalized
			$encodedIdComune = GwHelpers::encodeForUri($idComune);
			$civicoProp = $civico?'gco:haNumeroCivico "'.GwHelpers::quote($civico).'" ;':'';
			$capProp = $cap?'gco:cap "'.GwHelpers::quote($cap).'" ;':'';
			
			fwrite( $rdfStream, "
:$uniqueID	 a gco:Luogo ;
	dct:identifier \"$i\" ;
	gco:haComune <urn:geocodit:comune:$encodedIdComune>  ;
	$capProp
	$civicoProp
	gco:haToponimoStradale \"".GwHelpers::quote($odonimo)."\" ;
	geo:lat ".GwHelpers::toFloat($latitude)." ;
	geo:long ".GwHelpers::toFloat($longitude)." 
.");
		}

		
		// rewind and return output stream
		rewind($rdfStream);
		return $rdfStream;		
	}



	public function getStream(){
		if(!($input = fopen($this->getSource(), 'r'))) { throw new \Exception("Error Processing Request", 404); }

		$output = $this->trasform($input);
		fclose($input);
		
		return $output;
	}
	
} //END