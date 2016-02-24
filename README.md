# geocodit project

Geocodit is a system that manages a [geocoding](https://en.wikipedia.org/wiki/Geocoding) knowledge base using W3C semantic web best practices and standards.

geocodit exposes:

- a language profile description to be used to map geocoding data in RDF
- a set of gateways to transform  some 3 star linked data into five stars linked data compliant with geocodit language profile  
- a forward geocoder API that leverages the  knowledge base and other geocoding services (e.g. Google Map's and OSM) to produce accurate results; 
- a benchmark API that compares the performances of various geocoder
- a shareable knowledge base description to be used to populate a graph database with all needed geocoding information (LinkedData.Center ingestion APIs required)

geocodit was thinked for the italian territory but can be easily adaped to other geographies.

For more about geocodit project see [this article](http://linkeddata.center/help/business/cases/geocodit-v1) 

A running demo of this project is available at http://geocodit.linkeddata.center/ 


## How it works
Geocodit uses geocoder services (free and private) and open data to increase the quality of geocoding process.

## Why is needed
In some circumstances commercial global services (like Google Maps) fails to solve address or produce inacurate results. 
	This often happens for some rural and isolated places, in private industral areas and in new sites.
This information are sometime available in open data but could also be present in private owned files.

Geocodit try to use all these data source to produce accurate results.

## Requirements

Geocodit requires:

- a [LinkedData.Center](http://LinkedData.Center/) account ([free plans available](http://linkeddata.center/home/pricing#cta)). 
	For testing purposes you can use the knowledge base exposed by  https://hub1.linkeddata.Center/demo .
	Note that the demo kb is "read only" (i.e. you are note enabled to change existing configuration)
- Optional Google Maps API key if you need Google Maps integration
- Optional Bing Maps API key if you need Bing Maps integration
- PHP 5.5, apache2 and composer.

Geocodit is developed and tested for ubuntu LTS 14.04 but nothing prevent it to work on any PHP5+apache2 platform  (no MySQL needed!). 
Even apache2 it is not a strong requirement, with few changes (mainly to web/.httpcaccess file) it should  works with any php-conscious web server.

Geocodit system requires to acces the internet for Geocoder service providers and LinkedData.Center.

A running test installation of this project is available at http://geocoditdemo.linkeddata.center/ 
To avoid abuses Google Maps integration is disabled and a performance penality of 2sec. is added toeach OSM API call.

A limited prepoulated knowledge base is loaded in https://hub1.linkeddata.center/demo (username: demo password: demo)

## Install

### APIs installation
- Create and Login to a fresh ubunto box (phisical or virtual) 
- Clone geocodit project in /opt/geocodit : `sudo cd /opt; git clone https://github.com/linkeddatacenter/geocodit.git`
- Execute the install script `sudo /opt/gecodit/scripts/install.sh`.
- copy the geocodit.ini.dist file into /etc/geocodit/geocodit.ini (or geocodit.ini) and add your LinkedData.Center and Google Api credential. 
	Be sure that geocodit.it is readable to the web server (i.e. www-data user).

**IMPORTANT: geocodit uses your keys to access payed services, if you put it in a public server, consider to limit 
	APIs access by configuring your web server. 
	The default configuration allows free access to all APIS but inserts a penality of 2 seconds every api call to discurage abuse.**

### knowledge base population
- Add to your graph db configuration (https://hub1.linkeddata.center/*KID*/cpanel/config)) the contents of kees.ttl file
	and post a new ingestion activity request (https://hub1.linkeddata.center/*KID*/cpanel/new_activity).
	
Instead of copy content you can also just include:

```
@prefix kees: <http://linkeddata.center/kees/v1#> .
[] kees:includes <http://geocodit.linkeddata.center/kees.ttl> .

```

Refresh the knowledge base according your need, we suggest to schedule a monlty ingestion or a quarter. 
	Most of the resources contained in the knowledge base are quite static (territory does not change so frequently).
	

## Usage

### Using data interface

After data ingestion, the knowledge base data interfaces will  be available at <your LinkedData.Center account enpoint>/queries (e.g. https://hub1.linkeddata.center/demo/queries).
Just point your browser (or your http client) to the desidered table (e.g https://hub1.linkeddata.center/demo/table/istat:comuni) and 
provide your LinkedData.Center credential as basic http authentication. Data interface supports content negotiation.

### Using geocoding apis

The geocodit API enpoint will be available at <your server ip or FQDN>/api (e.g. http://geocodit.linkeddata.center/api)

**Usage:**
- geocode?q=*address*[& profile=cost|quality] : returns address geolocation using a gecoding profile optimization(default: cost).
- benchmark?q=*address* : compare the results of all supported geocoders.

### Using geocodit gateways

The geocodit gateways will be available at <your server ip or FQDN>/gw/{*gateway name*} (e.g. http://geocodit.linkeddata.center/gw/).
The list of available gateways is available as Void catalog if you do not specify any specific gateway.

All gateways stream a three star resource as a RDF turtle resource. Data are elaborated in real time and cached for one day.


## License and Authors
Author: E.Fagnoni - 
Copyrigth: (c) 2016 http://LinkedData.Center/ The MIT License (MIT)

Data license are inserted in knowledge base configuration web/kees
