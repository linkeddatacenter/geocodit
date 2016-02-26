# GeocodIT project

GeocodIT is a system that manages a [geocoding](https://en.wikipedia.org/wiki/Geocoding) knowledge base using W3C semantic web best practices and standards.

GeocodIT exposes:

- a new geocoder that leverages open data to enrich the results of existing  geocoding services (e.g. Google Maps Bings Maps and OSM);
- a tool to compares the results and performaces of various geocoder
- a language profile (ontologies) to be used to describe geocoding data in RDF
- a set of gateways to transform  3 star linked data into five stars linked data according  with GeocodIT language profile  
- a shareable knowledge base description to be used to populate a graph database with all needed geocoding information (LinkedData.Center ingestion APIs required)

GeocodIT was thinked for the italian territory but can be easily adaped to other geographies.

For more about GeocodIT project see [this article](http://linkeddata.center/help/business/cases/geocodit-v1) 

## Demo
A running demo is available at http://geocodit.linkeddata.center/ . Please use this server just as a demo and learning resource. 
To avoid abuses a performance penality of 2sec. is added to each geocoder API call.

## How it works
GeocodIT uses geocoder services (free and private) and open data to increase the quality of geocoding process.
For instance uses ISTAT open data to add codes to administrative level in Open Street Map Results

## Why is needed
In some circumstances commercial global services (like Google Maps) fails to solve address or produce inacurate results. 
This often happens for some rural and isolated places, in private industral areas and in new sites.
This information are sometime available in open data but could also be present in private owned files.

GeocodIT try to use all these data source to produce accurate results.

Besides this commercial services put lot of constraints on GeocodIT data and this limit companies to embedd gelocation data in their information systems.

## Requirements

GeocodIT requires:

- a [LinkedData.Center](http://LinkedData.Center/) account ([free plans
    available](http://linkeddata.center/home/pricing#cta)). For testing purposes a limited 
    prepoulated knowledge base is available at https://hub1.linkeddata.center/demo (username: demo password: demo)
	Note that the demo kb is "read only" (i.e. you are note enabled to change existing configuration)
- Optional Google Maps API key if you need Google Maps integration
- Optional Bing Maps API key if you need Bing Maps integration

GeocodIT is a set of web services developed in PHP and tested for ubuntu LTS 14.04 but nothing prevent it to work on any PHP5+apache2 platform  (no MySQL needed!). 
Even apache2 it is not a strong requirement, with few changes (mainly to web/.httpcaccess file) it should  works with any php-enabled web server.

## Install

### APIs installation

- Create and Login to a fresh ubunto box (phisical or virtual) 
- Clone GeocodIT project in /opt/geocodit : `sudo cd /opt; git clone https://github.com/linkeddatacenter/geocodit.git`
- Execute the install script `sudo /opt/gecodit/scripts/install.sh`.
- Copy the config/geocodit.ini.dist file into /etc/geocodit/geocodit.ini (or geocodit.ini) and 
    add your LinkedData.Center and Google Api credential. Be sure that geocodit.it is
    readable to the web server (i.e. www-data user).

**IMPORTANT: GeocodIT uses your keys to access payed services, if you put it in a public server, consider to limit 
	APIs access by configuring your web server. 
	The default configuration allows free access to all APIS but inserts a penality of 2 seconds every api call to discurage abuse.**

### knowledge base population
The demo knowledge base is pre-populate, use these instructions to create your own knowledge base:

- Add following line to your LinkedData.Center graph db instance:
    ```
    [] kees:includes <http://geocodit.linkeddata.center/kees.ttl> .
    ```
- Refresh the knowledge base (i.e. create a new ingestion activity) .Most of the resources contained in the knowledge base are quite static (territory does not change so frequently).
	
## Usage

GeocodIT can be used in different ways:
- use geocoder API like you should do with Google Maps API or Openstreet map. GeocodIT geocoder is a RESTful web services that supports http content negotiation for all main GeocodIT formats. **GeocodIT  is able to play along with your trust in all geocoder algoritms** allowing you to choose your preferred trust profile (e.g. open data, google, microsoft, ect)
- use GeocodIT benchamark to get an idea of how different geocoders performs
- query the knowledge base and import geocoding information in your systems
- use the provided gateways to transform 3 stars open data in 5 stars full flagged Linked Open Data.

### Using data interface

After data ingestion, the knowledge base data interfaces will  be available at <your LinkedData.Center account enpoint>/queries (e.g. https://hub1.linkeddata.center/demo/queries).
Just point your browser (or your http client) to the desidered table (e.g https://hub1.linkeddata.center/demo/table/istat:comuni) and 
provide your LinkedData.Center credential as basic http authentication. Data interface supports content negotiation.
Of course you can access SPARQL endpoint (e.g. https://hub1.linkeddata.center/demo/query) fully compliant with [SPARQL 1.1 specifications](https://www.w3.org/TR/sparql11-query/) and protocol.

### Using geocoder

The geocoedr  enpoint will be available at <your server ip or FQDN>/api/geocode (e.g. http://geocodit.linkeddata.center/api)

    geocode?q=*address*[&trust=*trust_profile*] : returns address geolocation using a gecoding profile optimization(default: cost).

Available trust profiles:
- **opendata** (default): search knowledge base, if address not found fall backs on open street map enriching results with istat codes
- **ms**: try bing maps first, if no results try search KB
- **google**: try google  maps first, if no results try search KB
- **osm**: try open street map first, if no results try search KB
- **all**: try google maps first, then bing maps, then open street map, then if still no results try search KB

Beside this you can trust on result a provider alone:
- **geocodit** : just uste kb data (free)
- **geocoditOSM** : openstreet map enriched with KB data (free, no more than a query per second)
- **google_map** : google maps (require key, license rescstrition on data usage)
- **bin_map** : google maps (require key, license rescstrition on data usage)
- **openstreetmap** : open street map (free, no more than a query per second)

### Using benchmark

The benchmark  enpoint will be available at <your server ip or FQDN>/api/benchark (e.g. http://geocodit.linkeddata.center/api)
    benchmark?q=*address* : compare the results of all supported geocoders.
    
### Using  gateways

The GeocodIT gateways will be available at <your server ip or FQDN>/gw/{*gateway name*} (e.g. http://geocodit.linkeddata.center/gw/).

All gateways stream a three star resource as a RDF turtle resource. Data are transfrmed in real time [ TBD: and cached for one day].


## Extending geocodit
More or less you just have to find new data sources and design a proper ingestion policy to be added to knowledge base configuration file.
In some case you will have to write a gateway to transform data to RDF. Use existing gateways as startin pont.
See the file [CONTRIBUTING.md] for some note about development environment.

## License and Authors
Author: E.Fagnoni - 
Copyrigth: (c) 2016 http://LinkedData.Center/ The MIT License (MIT)

