# GeocodIT project
[![Latest Version](https://img.shields.io/packagist/v/linkeddatacenter/geocodit.svg?style=flat-square)](https://packagist.org/packages/linkeddatacenter/geocodit)

GeocodIT is a system that manages a [geocoding](https://en.wikipedia.org/wiki/Geocoding) knowledge base using W3C semantic web best practices and standards.

Data sets on the web are literary exploding. Of course they’re still fragmented and of different quality but that will be fixed and it is only a matter of time. With Linked Open Data, it does not make sense to use only the biggest dataset (i.e. Open Street Map, & Google Maps) because the highest quality data are often in the smallest ones. These are in the long tail of the web. Hence you’ve to use them or you’ll lose a lot of value.

GeocodIT exposes:

- a new geocoder that leverages open data to enrich the results of existing  geocoding services (e.g. Google Maps Bings Maps and OSM);
- a tool to compare the results and performances of various geocoders;
- a language profile (ontologies) to be used to describe geocoding data in RDF;
- a set of gateways to transform 3 stars linked data into five stars linked data accordingly to GeocodIT language profile;
- a shareable knowledge base description to be used to populate a graph database with all needed geocoding information (LinkedData.Center ingestion APIs required).

GeocodIT was designed for the italian territory but can be easily adapted to other geographies.

For more about GeocodIT project see [this article](http://linkeddata.center/help/business/cases/geocodit-v1) 

## Demo
A running demo is available at http://geocodit.linkeddata.center/ . Please use this server just as a demo and learning resource. 
To avoid abuses, a performance penality of 2 sec. is added to each geocoder API call.

## How it works
GeocodIT uses geocoder services (free and private) and open data to increase the quality of the geocoding process.
For instance, it uses ISTAT open data to add codes to administrative level in Open Street Map Results.

## Why it is needed
In some circumstances, commercial global services (like Google Maps) fail to solve addresses or produce inaccurate results. 
This often happens for some rural and isolated places, in private industral areas and in new sites.
This information is sometime available in open data but it could also be present in private owned files.

GeocodIT tries to use all these data sources to produce accurate results.

Furthermore, commercial services put lot of constraints on GeocodIT data and this limit companies to embed geolocation data in their information systems.

## Requirements

GeocodIT requires:

- a [LinkedData.Center](http://LinkedData.Center/) account ([free plans
    available](http://linkeddata.center/home/pricing#cta)). For testing purposes a limited 
    prepoulated knowledge base is available at http://pub.linkeddata.center/demo (username: demo password: demo)
	Note that the demo kb is "read only" (i.e. you are not enabled to change existing configuration);
- optional Google Maps API key if you need Google Maps integration;
- optional Bing Maps API key if you need Bing Maps integration.

GeocodIT is a set of web services developed in PHP and tested for ubuntu LTS 14.04 but nothing prevents it to work on any PHP5+apache2 platform  (no MySQL needed!). 
Even apache2 is not a strong requirement; with few changes (mainly to web/.httpcaccess file) it should work with any php-enabled web server.

## Install

### APIs installation

- Create and Login to a fresh ubuntu box (physical or virtual) ;
- clone GeocodIT project in /opt/geocodit : `sudo cd /opt; git clone https://github.com/linkeddatacenter/geocodit.git`;
- execute the install script `sudo /opt/gecodit/test/_support/install.sh`;
- copy the config/geocodit.ini.dist file into geocodit.ini and 
    add your credentials. Be sure that geocodit.it is
    readable to the web server (i.e. www-data user).

**IMPORTANT: GeocodIT uses your keys to access payed services, if you put it in a public server, consider to limit 
	APIs access by configuring your web server. 
	The default configuration allows free access to all APIS but inserts a penality of 2 seconds to each api call to discurage abuse.**

### Knowledge base population
The demo knowledge base is pre-populated, use these instructions to create your own knowledge base:

- add the following line to your LinkedData.Center graph db instance:
    ```
    [] kees:includes <http://geocodit.linkeddata.center/kees.ttl> .
    ```
- Refresh the knowledge base (i.e. create a new ingestion activity). Most of the resources contained in the knowledge base are quite static (territory does not change so frequently).
	
## Usage

GeocodIT can be used in different ways:
- use the geocoder API like you should do with Google Maps API or Openstreet map. GeocodIT geocoder is a RESTful web service that supports http content negotiation for all main GeocodIT formats. **GeocodIT is able to play along with your trust in all geocoder algoritms** allowing you to choose your preferred trust profile (e.g. open data, google, microsoft, etc.);
- use GeocodIT benchmark to get an idea of how different geocoders perform;
- query the knowledge base and import geocoding raw data in your systems;
- use the provided gateways to transform 3 stars open data in 5 stars full flagged Linked Open Data;
- use it as a composer library to build your own service;

### Using data interface

After data ingestion, the knowledge base data interfaces will be available at <your LinkedData.Center account enpoint>/queries (e.g. https://hub1.linkeddata.center/demo/queries).
Just point your browser (or your http client) to the desidered table (e.g https://hub1.linkeddata.center/demo/table/istat:comuni) and 
provide your LinkedData.Center credential as basic http authentication. Data interface supports content negotiation.
Of course you can access SPARQL endpoint (e.g. https://hub1.linkeddata.center/demo/query) fully compliant with [SPARQL 1.1 specifications](https://www.w3.org/TR/sparql11-query/) and protocol.

### Using geocoder API

The geocoder enpoint will be available at <your server ip or FQDN>/api/geocode (e.g. http://geocodit.linkeddata.center/api/geocode)

    geocode?q=address[&trust=trust_profile]

It returns the address geolocation using a geocoding optimization.

Available trust profiles:

- **opendata** (default): search knowledge base, if address is not found, falls back on open street map enriching results with istat codes
- **ms**: try bing maps first, if no results try search KB;
- **google**: try google maps first, if no results try search KB;
- **osm**: try open street map first, if no results try search KB;
- **all**: try google maps first, then bing maps, then open street map, then if still no results try search KB.

Beside this, you can trust on result from a single provider:

- **geocodit** : just use kb data (free);
- **geocoditOSM** : openstreet map enriched with KB data (free, no more than a query per second);
- **google_map** : google maps (requires key, license restriction on data usage);
- **bin_map** : bing maps (requires key, license restriction on data usage);
- **openstreetmap** : open street map (free, no more than a query per second).

### Using benchmark API

The benchmark enpoint will be available at <your server ip or FQDN>/api/benchark (e.g. http://geocodit.linkeddata.center/api/benchark)

    benchmark?q=address
    
It compares the result of all supported geocoders.
    
### Using  gateways

The GeocodIT gateways will be available at <your server ip or FQDN>/gw/{*gateway name*} (e.g. http://geocodit.linkeddata.center/gw/).

All gateways stream a three star resource as a RDF turtle resource. Data are transformed in real time [TBD: and cached for one day].

## Using geocodit as a library
Add the following dependance to **composer.json** file in your project root:

```
    {
        "require": {
            "linkeddatacenter/geocodit": "dev-master"
        }
    }
```

GeocodIT is a companion of [Geocoder](https://github.com/geocoder-php/Geocoder).

It exposes two additional providers: geocodit and geocoditOSM, the first is a stand alone provider that uses knowledge base data for toponimy resolution,
the second is an extension of openstreetmap providers that enrich the geocoder algoritm with some open data (mainly by istat).

Choose the one that fits your needs first. Let's say the `geocoditOSM` one is what
you were looking for, so let's see how to use it. In the code snippet below,
`curl` has been chosen as [HTTP layer](#http-adapters) but it is up to you
since each HTTP-based provider implements
[PSR-7](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-7-http-message.md).

```php
$curl     = new \Ivory\HttpAdapter\CurlHttpAdapter();
$geocoder = new \Geocodit\Provider\GeocoditOSM($curl,  'demo', 'demo');

$geocoder->geocode(...);
```

## Extending geocodit
More or less you just have to find new data sources and design a proper ingestion policy to be added to the knowledge base configuration file.
In some cases, you will have to write a gateway to transform data to RDF. Use existing gateways as a starting point.
See the file [CONTRIBUTING.md] for some note about development environment.

## License and Authors
Author: E.Fagnoni - 
Copyrigth: (c) 2016 http://LinkedData.Center/ The MIT License (MIT)

