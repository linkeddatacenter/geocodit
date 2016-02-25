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
 
namespace Geocodit;

class GwEndPoint extends \BOTK\Core\EndPoint {

    protected function setRoutes() {
    	
		//  define here available gateways services
		$gateways = array(
		
			 'dug'			=>  function() {
			 	 return new Gateway\DUG('http://www.agenziaentrate.gov.it/wps/file/Nsilib/Nsi/Home/CosaDeviFare/Consultare+dati+catastali+e+ipotecari/Scambio+dati+catastali+e+cartografici+con+enti+o+PA/Portale+per+i+Comuni/Servizi+portale+dei+comuni/toponomastica/Elenco+DUG/Copia+di+DUG_VALIDE_16122014.xls');
			 },
			 
			 'farmacie'		=>  function() {		 	
			 	$gw = new Gateway\MinisteroSaluteCSV('http://www.dati.salute.gov.it/imgs/C_17_dataset_5_download_itemDownload0_upFile.CSV'); 
				$gw->setReader( function($data) { return array( $data[2], $data[6], $data[18], $data[19]); } );
				return $gw;
			 },
			 
			 'parafarmacie'	=>  function() {		 	
			 	$gw = new Gateway\MinisteroSaluteCSV('http://www.dati.salute.gov.it/imgs/C_17_dataset_7_download_itemDownload0_upFile.CSV'); 
				$gw->setReader( function($data) { return array( $data[2], $data[5], $data[14], $data[15]); } );
				return $gw;
			 },
		);

    	$this->get('/', array_keys($gateways))
			->accept(\BOTK\Core\Representations\Standard::renderers());
			
		$this->get('/*', 	function($name) use ($gateways) {
			if( !array_key_exists($name, $gateways) ) {
				throw new \Exception("Gateway not found", 404);	
			}
			
			header('Content-Type: text/turtle');
			return $gateways[$name]()->getStream();
		});
    	
			  
	}

} // END