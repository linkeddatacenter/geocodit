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
use Geocoder\Model\Address;


class GeocoderRenderer extends Standard {

    protected static $renderers = array(
        'application/vnd.geo+json'  => 'geoJSONRenderer',
        'application/json'          => 'geoJSONRenderer',
        'application/gpx+xml'		=> 'gpxRenderer',
        'application/xml'           => 'gpxRenderer', 
        'text/html'                 => 'mapRenderer',
        'text/plain'                => 'textRenderer',
    );
	
	
    public static function geoJSONRenderer(Address $address) {
        static::setContentType('application/vnd.geo+json');	
		$dumper = new \Geocoder\Dumper\GeoJSON();
		
		return  $dumper->dump($address);
    }
	
	
    public static function gpxRenderer(Address $address) {
        static::setContentType('application/gpx+xml');
		$dumper = new \Geocoder\Dumper\Gpx();
		
		return  $dumper->dump($address);
    }


    public static function textRenderer(Address $address) {
    	return Standard::plaintextRenderer($address->toArray());
    }   


    public static function mapRenderer(Address $address) {
        static::setContentType('text/html');	
		$formatter = new \Geocoder\Formatter\StringFormatter();	
		$latitude=$address->getLatitude();
		$longitude=$address->getLongitude();
		$title = $formatter->format($address, "%L, %S, %n");
		$body= "
			<iframe 
				frameborder='0'
				scrolling='no' 
				marginheight='0'
				marginwidth='0'
				width='300' 
				height='300' 
				src='https://maps.google.com/maps?hl=en&q=$latitude, $longitude&ie=UTF8&t=m&z=19&iwloc=B&output=embed'>
			</iframe>	
		";
     
        return static::htmlSerializer($body, static::$htmlMetadata, $title,null,null,true);
    }

}
