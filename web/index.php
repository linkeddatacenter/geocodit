<?php
require '../vendor/autoload.php';

DEFINE('DEMO_ENDPOINT', 'https://hub1.linkeddata.center/demo');

// search configs files in  in config and /etc/geocodit directories
if (! isset($_ENV['BOTK_CONFIGDIR'])) {
	if ( file_exists( __DIR__. '/../config/geocodit.ini')) {
		$_ENV['BOTK_CONFIGDIR'] = realpath(__DIR__. '/../config');
	} elseif ( is_dir('/etc/geocodit') ) {
		$_ENV['BOTK_CONFIGDIR'] = '/etc/geocodit';
	}
}
$config = \BOTK\Context\Context::factory()->ns('geocodit');
$penality = $config->getValue( 'penality',2);
$endpoint = $config->getValue( 'endpoint', DEMO_ENDPOINT);
$defaultAddress = $config->getValue('defaultAddress','Via Montefiori 13, Esino Lario');

// test google maps and bing maps credentials
$googleApiKeyNotSetWarning = $config->getValue( 'googleApiKey','')
	?'':"<li style='color: red'>WARNING: google maps api key not available (continue at your risk)</li>";
$bingApiKeyNotSetWarning = $config->getValue( 'bingApiKey','')
	?'':"<li style='color: red' >WARNING: bing maps api key not available, bing maps will return errors</li>";

// Enable Universal Analytics code
$UASnippet=\Geocodit\View\GoogleAnalyticsEnabledRenderer::GoogleAnalyticsSnippet($config->getValue( 'UA', ''));

$passwordHint = ($endpoint==DEMO_ENDPOINT)?' (demo/demo)':'';

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
		<div >
			Service status:
			<ul>
				<li>knowledge base endpoint:  <?=$endpoint?></li>
				<li>execution APIs penality:  <?=$penality?> sec.</li>
				<?=$googleApiKeyNotSetWarning?>
				<?=$bingApiKeyNotSetWarning?>
			</ul>  
		</div> 
	    <nav>
	       <div >
	        <dl>
				<dt>Help resources:</dt>
				<dd><a href ="http://linkeddata.center/help/business/cases/geocodit-v1" target="_blank">The GeocodIT case study article </a></dd>
				<dd><a href ="profile.html" target="_blank">GeocodIT language profile</a></dd>
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
	  	  	 <p>Geocoder API syntax: <code>api/geocode?q=<i>address to search</i>[&amp; trust=<i>provider choice</i>]</code>*. Try it yourself: </p>
		    
		      	<form style="text-indent: 50px;" action="api/geocode" method="get">
	      			<code >api/geocode?q=</code><input size="30" type="search" name="q" value="<?=$defaultAddress?>">
	      			 <code >&amp;trust=</code><select name="trust">
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
					<input type="submit" value="Try GeocodIT  geocoder">
		      	</form>
		     
		</div>
		<div>
			<br>
	  	  	 <p>Benchmark API syntax: <code>api/benchmark?q=<i>address to search</i></code>*. Try it yourself: </p>
		     
		      	<form style="text-indent: 50px;" action="api/benchmark" method="get">
	      			<code>api/benchmark?q=</code><input size="30" type="search" name="q" value="<?=$defaultAddress?>">  	
					<input type="submit" value="Try GeocodIT benchmark">
		      	</form>
  
		</div>
		  <br>	
	      <p>* For better results, address should be expressed in the form: "&lt;Denominazione Urbanistica Generica (DUG)&gt; &lt;Denominazione Urbanistica Ufficiale(DUF)&gt; &lt;Numero civico&gt; , &lt;City&gt; " </p>

	   </div>
	   
	  <div>
	  	<hr>
	  	<h2>Knowledge services (i.e. data APIs) :</h2>
	      
	      <p>Credential required<?=$passwordHint?>.</p>
	      <ul>
	      		<li>View <a target="_blank" href="<?=$endpoint?>">Knowledge base</a> configuration. [<a target="_blank" href="kees.ttl">KEES file</a>].</li>
	      		<li><a target="_blank" href="<?=$endpoint?>/table/gecodit:luoghi">Numeri civici</a></li>
	      		<li><a target="_blank" href="<?=$endpoint?>/table/istat:comuni">Comuni</a></li>
	      		<li><a target="_blank" href="<?=$endpoint?>/table/istat:provincie">Provincie</a></li>
	      		<li><a target="_blank" href="<?=$endpoint?>/table/istat:regioni">Regioni</a></li>
	      		
	      		<li>[<a target="_blank" href="<?=$endpoint?>/cpanel">more...</a>]</li>
	      </ul>
	   </div>
	   <hr>
	  <div>
	  	<h2>Linked data gateways:</h2>
	  	<p>Some examples of web resources that translate CSV open data into GeocodIT Linked Open Data:</p>
	      <ul>
	      		<li><a href="gw/farmacie">Farmacie</a> (source Ministero della Salute)</li>
	      		<li><a href="gw/parafarmacie">Parafarmacie</a> (source Ministero della Salute)</li>
	      </ul>
	   </div>
		<hr>
		<footer>
		<p>Power by: <a href="http://linkeddata.center/"><img width ="200" src="http://linkeddata.center/resources/v4/logo/Logo-colori-trasp_oriz-640x220.png"></a></p>
		<?=$UASnippet?>
		</footer>	   
    </body>
</html>
