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

class ZippedCSV extends CSV {
	protected $path='';
	
	public function setPath($path){
		$this->path=$path;
		return $this;
	}


	public function getStream(){
		
		try {
			$tmpfname = tempnam(sys_get_temp_dir(), 'zip');
			copy($this->getSource(),$tmpfname);
			
			$input = fopen("zip://$tmpfname#".$this->path, 'r'); 
			$output = $this->trasform($input);
			unlink($tmpfname);
			fclose($input);			
		} catch (\Exception $e) {
			@unlink($tmpfname);
			throw new \Exception($e->getMessage(), 404); 
		}
			
		return $output;
	}

	
} //END