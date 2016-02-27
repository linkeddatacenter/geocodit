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


class GoogleAnalyticsEnabledRenderer extends Standard {

    public static $UniversalAnalyticsId  = null;
	
	public static function GoogleAnalyticsSnippet(){
		$UA = static::$UniversalAnalyticsId;
		return $UA
			?"
				<script>
				  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
				
				  ga('create', '$UA', 'auto');
				  ga('send', 'pageview');
				
				</script>
			":'';
	}
	
}
