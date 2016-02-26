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

namespace Geocodit\Gateway;

class GwHelpers {
	
	/**
	 * PHP equivalent to UCASE(REPLACE(?id,"[^a-zA-Z0-9]",""))
	 */
	public static function encodeForUri($id){
		return strtoupper( preg_replace('/[^a-zA-Z0-9]/', '', $id));
	}
	
} //END