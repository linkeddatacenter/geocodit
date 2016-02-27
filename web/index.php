<?php
require '../vendor/autoload.php';

use BOTK\Context\Context;				// get config vars and other inputs
use Geocodit\View\GoogleAnalyticsEnabledRenderer ;

// search configs files in  in config and /etc/geocodit directories
if (! isset($_ENV['BOTK_CONFIGDIR'])) {
	if ( file_exists( __DIR__. '/../../config/geocodit.ini')) {
		$_ENV['BOTK_CONFIGDIR'] = realpath(__DIR__. '/../../config');
	} elseif ( is_dir('/etc/geocodit') ) {
		$_ENV['BOTK_CONFIGDIR'] = '/etc/geocodit';
	}
}
$config = Context::factory()->ns('geocodit');
$UA = $config->getValue( 'UA', '');
$penality = $config->getValue( 'penality',2);
$googleApiKeyNotSetWarning = $config->getValue( 'googleApiKey','')
	?:"<p>WARNING: google maps api key not available (continue at your risk)</p>";
$bingApiKeyNotSetWarning = $config->getValue( 'bingApiKey','')
	?:"<p>WARNING: bing maps api key not available, bing maps will return errors</p>";

// Enable Universal Analytics code
GoogleAnalyticsEnabledRenderer::$UniversalAnalyticsId = $UA;
$UASnippet=GoogleAnalyticsEnabledRenderer::GoogleAnalyticsSnippet();

?>
<!DOCTYPE html>
<html>
    <head>
		<meta charset='utf-8'>
		<link rel='stylesheet' type='text/css' href='http://linkeddata.center/resources/v4/css/doc.css'/>
		<style type='text/css'>
		    main {
		        font-family: monospace;
		        white-space: pre;
		    }
		</style>
        <title>GeocodIT demo</title>
    </head>
    <body>
      <header>
		<h1>GeocodIT demo</h1>

		<p>A system that manages italian geocoding knowledge base according W3C semantic web best practices and standards. 
			<a href ="https://github.com/linkeddatacenter/geocodit" target="_blank" >View on GitHub</a>
			</p>
		<div style="color: red"> <?=$googleApiKeyNotSetWarning?><?=$bingApiKeyNotSetWarning?> </div> 
	    <nav>
	       <div >
	        <dl>
				<dt>Help resources:</dt>
				<dd><a href ="http://linkeddata.center/help/business/cases/geocodit-v1" target="_blank">The GeocodIT case study article </a></dd>
			</dl>
	       </div>
	    </nav>
	   

	   
	  </header>
	  <div>
	  	<h2>Web Services (i.e. RESTful APIs)</h2>
	  		<table width="100%"  style="background-color: #8ECCC2; border: 2px solid gray;"  ><tr><td style="text-align: center;  padding: 15px;">
	  		<h3>WARNING</h3>
	  	  	<p>APIs' endpoints must be used only to support GeocodIT learning. To avoid abuses, a performance penality of <b><?=$penality?> seconds</b> is added to each call.</p>
	  	  	<p>To use GeocodIT APIs in a production enviroment <a href ="https://github.com/linkeddatacenter/geocodit" target="_blank" >clone the project</a>
	  	  		in  your (local |phisical |cloud) server.</p>
	  	  </td></tr></table>
	   
		<div >
			<br>
	  	  	 <p>Syntax: <code>api/geocode?q=<i>address to search</i>[&amp; trust=<i>provider choice</i>]</code>. Try it yourself: </p>
		     <code style="text-indent: 50px;">
		      	<form action="api/geocode" method="get">
	      			api/geocode?q=<input size="40" type="search" name="q" value="Via Montefiori 13, Esino Lario">
	      			&amp; trust=<select name="trust">
						<option>opendata</option>
						<option>ms</option>
						<option>google</option>
						<option>osm</option>
						<option>all</option>
						<option>geocodit</option>
						<option>geocoditOSM</option>
						<option>google_map</option>
						<option>bing_map</option>
						<option>openstreetmap</option>
					</select>    	
					<input type="submit" value="Try geocoder">
		      	</form>
		     </code>
		</div>
		<div>

	  	  	 <p>Syntax: <code>api/benchmark?q=<i>address to search</i></code>. Try it yourself: </p>
		     <code style="text-indent: 50px;">
		      	<form  action="api/benchmark" method="get">
	      			api/benchmark?q=<input size="40" type="search" name="q" value="Via Montefiori 13, Esino Lario">  	
					<input type="submit" value="Try geocoder">
		      	</form>
		      </code>
		</div>
				
	      <p>For better results, address should be expressed in the form: "&lt;Denominazione Urbanistica Generica (DUG)&gt; &lt;Denominazione Urbanistica Ufficiale(DUF)&gt; &lt;Numero civico&gt; , &lt;City&gt; " </p>

	   </div>
	   
	  <div>
	  	<script>
	  		function myopen(link,local){
	  			if( local) {
	  				url = link;
	  			} else {
	  				url = document.getElementById('endpoint').value + link;
	  			}
	  			newWindow =window.open(url, '_blank');
	  			newWindow.focus();
	  		}
	  	</script>
	  	<hr>
	  	<h2>Knowledge services (i.e. data APIs) :</h2>
	  	 <p>Enter your LinkedData.Center knowledge base endpoint here:  <input id='endpoint', size="40" value="https://hub1.linkeddata.center/demo"> . 
	  	 	Authorization required (use demo/demo)</p>
	      <ul>
	      		<li>View <a onclick="myopen('cpane/config', true);">Knowledge base configuration</a></li>
	      </ul>
	      <p>Here are some data examples:</p>
	      <ul>
	      		<li><a onclick="myopen('/table/gecodit:luoghi');">Numeri civici</a></li>
	      		<li><a onclick="myopen('/table/istat:comuni');">Comuni</a></li>
	      		<li><a onclick="myopen('/table/istat:provincie');">Provincie</a></li>
	      		<li><a onclick="myopen('/table/istat:regioni');">Regioni</a></li>
	      		<li>[<a onclick="myopen('/queries');">more...</a>]</li>
	      </ul>
	   </div>
	   <hr>
	  <div>
	  	<h2>Data gateways (from three stars data to RDF):</h2>
	      <ul>
	      		<li><a href="gw/farmacie">Farmacie</a></li>
	      		<li><a href="gw/parafarmacie">Parafarmacie</a></li>
	      </ul>
	   </div>
		<hr>
		<footer>
		<p>Power by: <a href="http://linkeddata.center/"><img width ="200" src="http://linkeddata.center/resources/v4/logo/Logo-colori-trasp_oriz-640x220.png"></a></p>
		<?=$UASnippet?>
		</footer>	   
    </body>
</html>
