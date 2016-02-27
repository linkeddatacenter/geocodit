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

require '../../vendor/autoload.php';


use BOTK\Core\EndPointFactory,          // Create end-point
    BOTK\Core\ErrorManager;             // Control errors
use Geocodit\View\GoogleAnalyticsEnabledRenderer; // for CSS
use BOTK\Context\Context;				// get config vars and other inputs
use BOTK\Context\ContextNameSpace as CX;

// search configs files in  in config and /etc/geocodit directories
if (! isset($_ENV['BOTK_CONFIGDIR'])) {
	if ( file_exists( __DIR__. '/../../config/geocodit.ini')) {
		$_ENV['BOTK_CONFIGDIR'] = realpath(__DIR__. '/../../config');
	} elseif ( is_dir('/etc/geocodit') ) {
		$_ENV['BOTK_CONFIGDIR'] = '/etc/geocodit';
	}
}

// Enable Universal Analytics code
$UA = Context::factory()->ns('geocodit')->getValue( 'UA', CX::NULL_AS_DEFAULT);
GoogleAnalyticsEnabledRenderer::$UniversalAnalyticsId = $UA;

// Enable the catching of PHP errors
$errorManager = ErrorManager::getInstance()->registerErrorHandler(); 

try {
	$endpoint = EndPointFactory::make('Geocodit\ApiEndpoint');
	$result = $endpoint->run();
} catch ( Exception $e) {
    $result = ErrorManager::getInstance()->render($e); 
}

echo $result;
