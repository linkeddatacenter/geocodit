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

abstract class AbstractGateway implements GatewayInterface {
	protected  $source;
	
    public function __construct($source) {
		$this->source = $source;
    }

	public function getSource(){ return $this->source;}
	abstract public function getStream();
	
} //END