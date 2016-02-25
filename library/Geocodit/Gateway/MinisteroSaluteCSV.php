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

class MinisteroSaluteCSV extends AbstractGateway {
	
	protected $reader = null; 
	
	protected function parseAddress($address) {
		
		return array('street', 'number');
	}
	
	public function setReader( $function ){
		$this->reader = $function;
	}

	public function getStream(){
		
		$source = $this->getSource();
		
		// open input stream
		if(!($handle = fopen($source, 'r'))) { throw new \Exception("Error Processing Request", 404); }
		
		//initialize output stream
		$stream = tmpfile();
		fwrite($stream, "# Dati provenienti dal ministero della salute
@prefix geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> .
@prefix gco: <http://geocodit.linkeddata.center/ontology#> .
@prefix ter: <http://datiopen.istat.it/odi/ontologia/territorio/> .
");		
				
	    // get the first row, which contains the column-titles
	    $header = fgetcsv($handle);		
			
	    // loop through the file line-by-line
	    $i=0;
	    while(($data = fgetcsv($handle, 2000 , ';')) !== false) {
	    	$i++;
	    	$reader =  $this->reader;
			list ($address, $codComune, $latitude, $longitude ) = 	$reader($data);
			list($street, $civicNumber ) = $this->parseAddress($address);
			
			fwrite( $stream, "
<$source#$i> a gco:Luogo ;
	gco:haComune <urn:geocodit:comune:istat:$codComune>  ;
	gco:haNumeroCivico \"$civicNumber\" ;
    gco:haToponimoStradale \"$street\" ;
    geo:lat $latitude ;
    geo:long $longitude 
.");

	    }
	    fclose($handle);
		
		// rewind and return output stream
		rewind($stream);		  
		return $stream;		

	}
	
} //END