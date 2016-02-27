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

use BOTK\Core\Caching;                  						// manage HTTP caching

class ApiEndpoint extends \BOTK\Core\EndPoint {

    protected function setRoutes() {
    	
    	$this->get('/geocode', 	'Geocodit\Controller\GeocoderController')
			->accept(View\GeocoderRenderer::renderers())
			->through($this->representationCachingProcessor(Caching::CONSERVATIVE)); // three minute caching
				
    	$this->get('/benchmark', 	'Geocodit\Controller\GeocoderBenchmarkController')
			->accept(View\GeocoderBenchmarkRenderer::renderers())
			->through($this->representationCachingProcessor(Caching::AGGRESSIVE)); // one day caching
    }

} // END