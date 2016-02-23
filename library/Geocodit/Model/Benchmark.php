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

namespace Geocodit\Model;

use Ivory\HttpAdapter\HttpAdapterInterface;
use Geocoder\Exception\NoResult;

class Benchmark {
	public $query;
	public $table = array();
	
	
    public function __construct($query) {
		$this->query = $query;
    }


	public function compare(\Geocoder\Geocoder $geocoder){
	
		$data = new \stdClass;
		
		try {
			
			$time_pre = microtime(true);
			$address = $geocoder
				->limit(1)
				->geocode($this->query)
				->first();
			$time_post = microtime(true);
				
			$formatter = new \Geocoder\Formatter\StringFormatter();			
			$data->address = $formatter->format($address, "%S %n, %z %L");			
			$data->latitude=$address->getLatitude();
			$data->longitude=$address->getLongitude();
			$data->microtime = $time_post - $time_pre;

		} catch (\Exception $e) {
			$data = $e->getMessage();
		}
		
		$this->table[$geocoder->getName()] = $data;
		
		return $this;
	}
	
} // END