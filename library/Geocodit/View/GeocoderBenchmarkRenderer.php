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
 
namespace Geocodit\View;

use BOTK\Core\Representations\Standard;
use Geocodit\Model\Benchmark;


class GeocoderBenchmarkRenderer extends Standard {
	
    public static function htmlRenderer($benchmark) {
        static::setContentType('text/html');
		
		$html = "
			<table border='1' >
				
				<tr>
					<th>Geocoder</th>
					<th>Map</th>
					<th>Seconds (without penality)</th>
					<th>Returned address</th>
				</tr>
		";
		foreach($benchmark->table as $geocoder=>$data){
			$encodedQuery=urlencode($benchmark->query); 
			$html .= "
				<tr>
					<th><a href='geocode?trust=$geocoder&q=$encodedQuery'>$geocoder</a></th>
			";
			if( is_string($data)){
				$html .= "
					<td>N.A.</td>
					<td>N.A.</td>
					<td>$data</td>
				";			
			} else {
				$html .= "
					<td>
						<iframe 
							frameborder='0'
							scrolling='no' 
							marginheight='0'
							marginwidth='0'
							width='300' 
							height='300' 
							src='https://maps.google.com/maps?hl=en&q={$data->latitude}, {$data->longitude}&ie=UTF8&t=m&z=19&iwloc=B&output=embed'>
						</iframe>
					<td>{$data->microtime}</td>
					<td>{$data->address}</td>
					</td>
				";					
			}
			$html .= "
				</tr>
			";
		}
				
		$html .=	"</table>";
	 
	 
        return static::htmlSerializer( $html, static::$htmlMetadata, "Geocoders benchmark", "<h3>$benchmark->query</h3>", null, true);
    }

}
