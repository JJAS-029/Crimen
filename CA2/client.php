<html>
    <head>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin=""/>
        <style>
            #mapa{
                height: 100%;
                width: 100%;
            }
        </style>
    </head>
    <body>
        <h1>Geoserver User</h1>
        <div id='mapa'></div>
		<div>
        <?php
$dbconn = pg_connect("host=70.35.196.78  dbname=sigdis user=sigdis password=sigdis")
or die('No se ha podido conectar: ' . pg_last_error());

// Realizando una consulta SQL
$query = 'SELECT * FROM jjas.invit';
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
echo "<table>\n";
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}    
echo "</table>\n";
?>
        </div>
    </body>

<script src="https://code.jquery.com/jquery-3.2.1.js" ></script>

<script src="https://code.jquery.com/jquery-3.2.1.js" ></script>
     <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
 integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
 crossorigin=""></script>

 <script>
    var mymap = L.map('mapa').setView([19.271091, -99.659437], 10);
    L.Map.addInitHook('addHandler', 'tilt', L.TiltHandler);


	var positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="https://carto.com/attribution">CARTO</a>'
    }).addTo(mymap);
    
    var owsrootUrl = 'http://74.208.211.167:8080/geoserver/jjas/ows';
 
		var defaultParameters = {
			service: 'WFS',
			version: '1.0.0',
		        request: 'GetFeature',
			typeName: 'jjas:ZMVT_peli',
			maxFeatures:50,
			outputFormat: 'application/json',
 
		};
		var parameters = L.Util.extend(defaultParameters);
 
		var URL = owsrootUrl + L.Util.getParamString(parameters);
			
		$.ajax({
			url: URL,
			success: function (data) {
				var geojson = new L.geoJson(data, {
					style: {"color":"#2ECCFA","weight":2},
					onEachFeature: function(feature, layer){
						layer.bindPopup("Has hecho click en " + feature.properties.nom_mun +" "+ feature.properties.peligro);
					}}
				).addTo(mymap);
			}
		});
			 
</script>
</html>