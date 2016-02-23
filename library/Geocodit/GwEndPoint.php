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
			 //'nome'	=>  function() { return new Gateway\GWClass('http://data.example.com/source2');},
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