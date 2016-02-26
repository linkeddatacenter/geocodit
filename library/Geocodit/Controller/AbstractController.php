<?php
/**
 * 
 * @author Enrico Fagnoni <e.fagnoni@e-artspace.com>
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
 
namespace Geocodit\Controller;

use BOTK\Core\Controller;       // controls http protocol
use BOTK\Context\Context;				// get config vars and other inputs
use BOTK\Context\ContextNameSpace as V;

	
/**
 * A RESTful controller injection to make controlle testable
 */
abstract class AbstractController extends Controller {
	const SUPPORTED_PROVIDERS = 'geocodit|geocoditOSM|google_maps|openstreetmap|bing_maps';

	public function geocoderFactory($adapter, $providerName){
		$context = Context::factory();
		$input 	 = $context->ns(INPUT_GET);
		$config	 = $context->ns('geocodit');
		$locale = 'it_IT';
		
		switch ($providerName) {
			case 'geocoditOSM':
				$endpoint		= $config->getValue( 'endpoint', 'https://hub1.linkeddata.center/demo');
				$kbid			= $config->getValue( 'kbid','demo');
				$secretKey		= $config->getValue( 'secretKey', 'demo');
				$geocoder = new \Geocodit\Provider\GeocoditOSM($adapter, $kbid, $secretKey, $endpoint );
				break;
			case 'geocodit':
				$endpoint		= $config->getValue( 'endpoint', 'https://hub1.linkeddata.center/demo');
				$kbid			= $config->getValue( 'kbid','demo');
				$secretKey		= $config->getValue( 'secretKey', 'demo');
				$geocoder = new \Geocodit\Provider\Geocodit($adapter, $kbid, $secretKey, $endpoint );
				break;
			case 'google_maps':
				$googleApiKey	= $config->getValue( 'googleApiKey',	V::NULL_AS_DEFAULT);
				$geocoder = new \Geocoder\Provider\GoogleMaps($adapter,$locale ,'Italy', true, $googleApiKey);
				break;
			case 'openstreetmap':
				$geocoder = new \Geocoder\Provider\OpenStreetMap($adapter, $locale);
				break;
			case 'bing_maps':
				$bingApiKey		= $config->getValue( 'bingApiKey',		'here_your_bing_api_key');
				$geocoder = new \Geocoder\Provider\BingMaps($adapter,$bingApiKey, $locale);
				break;	
			default:
				throw new \InvalidArgumentException(sprintf('Unknown geocoder provider name "%s".', $providerName));
				break;
		}

		return $geocoder;
	}	
			
} // END
