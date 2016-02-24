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

class DUG extends AbstractGateway {

	public function getStream(){
		
		// download source stream in a temporary file
		$tmpfname = tempnam(sys_get_temp_dir(), 'DUG');
		copy($this->getSource(),$tmpfname);

		// load Excel object		
		$objReader = new \PHPExcel_Reader_Excel5();
		$objPHPExcel = $objReader->load($tmpfname);
		unlink($tmpfname);


		// create a temporary output stream
		$stream = tmpfile();
		fwrite($stream, "
@prefix skos: <http://www.w3.org/2004/02/skos/core#> .
@prefix dug: <http://geocodit.linkeddata.center/gw/dug#> .
@prefix dct: <http://purl.org/dc/terms/> .

dug:schema a skos:ConceptScheme ;
	dct:title \"Elenco DUG validate da Istat\"@it  ;
	dct:source <http://www.agenziaentrate.gov.it/wps/file/Nsilib/Nsi/Home/CosaDeviFare/Consultare+dati+catastali+e+ipotecari/Scambio+dati+catastali+e+cartografici+con+enti+o+PA/Portale+per+i+Comuni/Servizi+portale+dei+comuni/toponomastica/Elenco+DUG/Copia+di+DUG_VALIDE_16122014.xls>
.
");
				
		// transform data
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$highestRow = $objWorksheet->getHighestRow(); 		
		for ($row = 2; $row <= $highestRow; ++$row) {
			$dug = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
			$dugLabel = ucwords(strtolower($dug));
			$dugUrified = preg_replace('/[^\w]/', '_', $dugLabel); 
			fwrite($stream, "dug:$dugUrified a skos:Concept; skos:inScheme dug:schema ; skos:prefLabel \"$dugLabel\"@it .\n");			 
		}
		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);

		// rewind and return output stream
		rewind($stream);		  
		return $stream;		

	}
	
} //END