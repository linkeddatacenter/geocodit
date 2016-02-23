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

class Geocodit extends \Geocoder\Provider\AbstractHttpProvider implements \Geocoder\Provider\Provider
{

    /**
     * @param HttpAdapterInterface $adapter An HTTP adapter.
     * @param string               $endpoint Root URL of the SPAQL knowledge base
     * @param string               $user authenticatd user.     
	 * @param string               $user authenticatd password.
     */
    public function __construct(HttpAdapterInterface $adapter, $istatCode=null, $minQuality=null, $kbid=null, $secretKey=null)
    {
		if (is_null($kbid)) {$kbid='demo';}
		if (is_null($secretKey)) {$secretKey='demo';}		
		$endpoint="https://hub1.linkeddata.center/$kbid";
		
        parent::__construct($adapter);
    }

    /**
     * {@inheritDoc}
     */
    public function geocode($query)
    {		
		throw new NoResult(sprintf('Could not execute query "%s".', $query));
    }


    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude){
    	throw new NoResult(sprintf('Could not execute query "%s".', $query));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'geocodit';
    }

}


