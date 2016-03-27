<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocodit\Provider;

use \Ivory\HttpAdapter\HttpAdapterInterface;

use Geocoder\Model\AddressFactory;

use Geocodit\Gateway\GwHelpers;

use BOTK\RDF\HttpClient;;
use EasyRdf_Sparql_Client as SparqlClient;

/**
 * 
 * An extension of Open street map provider by Niklas Närhinen <niklas@narhinen.net>
 */
class GeocoditOSM extends \Geocoder\Provider\Nominatim
{
    /**
     * @var string
     */
    const ROOT_URL = 'http://nominatim.openstreetmap.org';
	
	protected $sparql;
	protected $factory;

    /**
     * @param HttpAdapterInterface $adapter An HTTP adapter.
     * @param string               $locale  A locale (optional).
     */
    public function __construct(HttpAdapterInterface $adapter,  $kbid=null, $secretKey=null, $endpoint=null)
    {
        parent::__construct($adapter, static::ROOT_URL, 'it_IT');
		
		if (is_null($kbid)) {$kbid='demo';}
		if (is_null($secretKey)) {$secretKey='demo';}
		if (is_null($endpoint))	{$endpoint="https://hub1.linkeddata.center/$kbid";}

        HttpClient::useIdentity($kbid,$secretKey);
        $this->sparql = new SparqlClient("$endpoint/sparql");
		$this->factory = new AddressFactory();
    }



    /**
     * @param array $data An array of data.
     *
     * @return \Geocoder\Model\AddressCollection
     */
    protected function returnResults(array $data = array()) {

        if (0 < $this->getLimit()) {
            $data = array_slice($data, 0, $this->getLimit());
        }
		
		
		foreach( $data as &$resultSet) {
			$uriComune = 'urn:geocodit:comune:'.GwHelpers::encodeForUri($resultSet['locality']);
			
			$query= "
				PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
				PREFIX gco: <http://linkeddata.center/ontology/geocodit/v1#>
				PREFIX owl: <http://www.w3.org/2002/07/owl#>
				PREFIX ter: <http://datiopen.istat.it/odi/ontologia/territorio/>
				SELECT ?comune ?codIstatComune ?provincia ?codIstatProvincia ?regione ?codIstatRegione ?lat ?long 
				WHERE {
					GRAPH <urn:istat:comuni> {
						 <$uriComune> owl:sameAs [ 
						 	a gco:Comune ;  
						 	ter:haNome ?comune ;
						 	ter:haCodIstat ?codIstatComune ;
						 	ter:provincia_di_COM ?uriProvincia
						 ] 
					} 
			        GRAPH <urn:istat:province> { 
			        	?uriProvincia
			            	ter:haNome ?provincia;  
							ter:haCodIstat ?codIstatProvincia ;
			            	ter:regione_di_PROV ?uriRegione
					}
					GRAPH <urn:istat:regioni> {
			        	?uriRegione ter:haNome ?regione ;
			        	ter:haCodIstat ?codIstatRegione 
			        }
				} 
			";	

			$solutions = $this->sparql->query($query);
			
			if( $solutions->numRows()){
					               
				$row = $solutions[0];
				// Ovveride admin list
	            $resultSet['adminLevels'] =	array(

	            	array(
	                    'name' => $row->regione->getValue(),
	                    'code' => $row->codIstatRegione->getValue(),
	                    'level' => 1
	            	),
	            	array(
	                    'name' => $row->provincia->getValue(),
	                    'code' => $row->codIstatProvincia->getValue(),
	                    'level' => 2
	            	),
	            	array(
	                    'name' => $row->comune->getValue(),
	                    'code' => $row->codIstatComune->getValue(),
	                    'level' => 3
	            	),
	            );	
            }	
		}
		
		

        return $this->factory->createFromArray($data);
    }



    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'geocoditOSM';
    }
}
