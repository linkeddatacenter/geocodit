<?php
/**
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

use BOTK\Context\Context;				// get config vars and other inputs
use BOTK\Context\ContextNameSpace as V;
use Geocodit\Model\Benchmark;


class GeocoderBenchmarkController extends AbstractController {

	public function get() {
		$context = Context::factory();
		
		// get default parameters from config
		$config	 = $context->ns('geocodit');
		$defaultAddress		= $config->getValue( 'defaultAddress', 	'Via Montefiori 13, Esino Lario');
		$penality			= $config->getValue( 'penality',2);
		
		// get input patrameters from URL quesry string
		$input = $context->ns(INPUT_GET);
		$query	= $input->getValue( 'q', $defaultAddress);
	
		$adapter  = new \Ivory\HttpAdapter\CurlHttpAdapter();
		$benchmark = new Benchmark($query);
		
		foreach(explode ('|', self::SUPPORTED_PROVIDERS) as $providerName) {
			$provider = $this->geocoderFactory($adapter, trim($providerName));
			$benchmark->compare($provider);
		}
		
		usleep($penality*1000000);
		
		return  $this->stateTransfer($benchmark);
    }				
		
} // END