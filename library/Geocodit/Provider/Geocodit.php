<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocodit\Provider;

use Ivory\HttpAdapter\HttpAdapterInterface;
use Geocoder\Exception\NoResult;
use Geocodit\Gateway\GwHelpers;

use BOTK\RDF\HttpClient;;
use EasyRdf_Sparql_Client as SparqlClient;

class Geocodit extends \Geocoder\Provider\AbstractHttpProvider implements \Geocoder\Provider\Provider
{
	protected $sparql;

    /**
     * @param HttpAdapterInterface $adapter An HTTP adapter.
     * @param string               $endpoint Root URL of the SPARQL knowledge base
     * @param string               $user authenticatd user.     
	 * @param string               $user authenticatd password.
     */
    public function __construct(HttpAdapterInterface $adapter,  $kbid=null, $secretKey=null, $endpoint=null)
    {
		if (is_null($kbid)) {$kbid='demo';}
		if (is_null($secretKey)) {$secretKey='demo';}
		if (is_null($endpoint))	{$endpoint="https://hub1.linkeddata.center/$kbid";}

        HttpClient::useIdentity($kbid,$secretKey);
        $this->sparql = new SparqlClient("$endpoint/sparql");
		
        parent::__construct($adapter);
    }


	protected function makeSparqlQuery($searchUri=null, $lat=null, $long=null){
		$reverse= is_null($searchUri);
		$query= "
			PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
			PREFIX gco: <http://geocodit.linkeddata.center/ontology#>
			PREFIX owl: <http://www.w3.org/2002/07/owl#>
			PREFIX ter: <http://datiopen.istat.it/odi/ontologia/territorio/>
			SELECT ?civico ?odonimo ?comune ?codIstatComune ?provincia ?codIstatProv ?regione ?lat ?long 
			WHERE {
		";
		$query .= $reverse?:"
				GRAPH ?g1 { <$searchUri> owl:sameAs ?luogo } 
		";
		$query .= $reverse?"
				GRAPH ?g2 {
					?luogo a gco:Luogo ;
						gco:haComune ?uriComune  ;		    
					    gco:haToponimoStradale ?odonimo ;
					    geo:lat $lat ;
					    geo:long $long .
					    BIND ( $lat AS ?lat )
					    BIND ( $long AS ?long )
					OPTIONAL { <$searchUri> gco:haNumeroCivico ?housenumber }
				}
		":"
				GRAPH ?g2 {
					?luogo a gco:Luogo ;
						gco:haComune ?uriComune  ;		    
					    gco:haToponimoStradale ?odonimo ;
					    geo:lat ?lat ;
					    geo:long ?lat .
					OPTIONAL { <$searchUri> gco:haNumeroCivico ?housenumber }
				}
		";
		$query .= "
				GRAPH <urn:istat:comuni> {
					 ?uriComune owl:sameAs [ 
					 	a gco:Comune ;  
					 	ter:haNome ?comune ;
					 	ter:haCodIstat ?codIstatComune ;
					 	ter:provincia_di_COM ?uriProvincia
					 ] 
				} 
		        GRAPH <urn:istat:province> { 
		        	?provinciaUrl
		            	ter:haNome ?provincia;  
						ter:haCodIstat ?codIstatProv ;
		            	ter:regione_di_PROV ?regioneUrl
				}
				GRAPH <urn:istat:regioni> {
		        	?regioneUrl ter:haNome ?regione 
		        }
			} LIMIT 10
		";
		
		return $query;
	}
	
	
	protected function buildResults($solutions){
		
		if (!$solutions || ! $solutions->numRows()) {throw new NoResult(sprintf('No solutions for found.',404));}
		$results = array();
		foreach ($solutions as $row) {
			$resultSet = $this->getDefaults();
			
			$resultSet['streetName'] = $row->odonimo->getValue();
			$resultSet['latitude']  = $row->lat->getValue();
            $resultSet['longitude'] = $row->long->getValue();
            $resultSet['bounds'] = null;
            
            $resultSet['adminLevels']= array(
            	array(
                    'name' => $row->regione->getValue(),
                    'code' => 'NA',
                    'level' => 1
            	),
            	array(
                    'name' => $row->provincia->getValue(),
                    'code' => $row->codIstatRegione->getValue(),
                    'level' => 2
            	),
            	array(
                    'name' => $row->comune->getValue(),
                    'code' => $row->codIstatComune->getValue(),
                    'level' => 3
            	),
            );
            
            $resultSet['country'] = "Italy";
            $resultSet['countryCode'] = "IT";
            $results[] = $resultSet;
		}	
		
		return $results;
	}
	
	
    /**
     * {@inheritDoc}
     */
    public function geocode($query)
    {
    	$searchUri = 'urn:luogo:'.GwHelpers::encodeForUri($query);
		$solutions = $this->sparql->query($this->makeSparqlQuery($searchUri));
		
		return $this->returnResults($this-> buildResults($solutions));
    }


    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude){
		$solutions = $this->sparql->query($this->makeSparqlQuery(null,$latitude, $longitude));
		
		return $this->returnResults($this-> buildResults($solutions));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'geocodit';
    }

}


