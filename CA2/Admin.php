<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="robots" content="noindex, nofollow" />
    <meta
      name="viewport"
      content="initial-scale=1,maximum-scale=1,user-scalable=no"
    />
    <script src="https://api.tiles.mapbox.com/mapbox-gl-js/v2.0.0/mapbox-gl.js"></script>
    <link
      href="https://api.tiles.mapbox.com/mapbox-gl-js/v2.0.0/mapbox-gl.css"
      rel="stylesheet"
    />
    <style>
      body {
        margin: 0;
        padding: 0;
      }
      h2,
      h3 {
        margin: 10px;
        font-size: 1.2em;
      }
      h3 {
        font-size: 1em;
      }
      p {
        font-size: 0.85em;
        margin: 10px;
        text-align: left;
      }
      .map-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.8);
        margin-right: 20px;
        font-family: Arial, sans-serif;
        overflow: auto;
        border-radius: 3px;
      }
      #map {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 100%;
      }
      #features {
        top: 0;
        height: 100px;
        margin-top: 20px;
        width: 250px;
      }
      #legend {
        padding: 10px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        line-height: 18px;
        height: 150px;
        margin-bottom: 40px;
        width: 100px;
      }
      .legend-key {
        display: inline-block;
        border-radius: 20%;
        width: 10px;
        height: 10px;
        margin-right: 5px;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <div class="map-overlay" id="features">
      <h2>GeoCrime Analysis</h2>
      <div id="pd"><p>Coloca el cursor sobre un municipio</p></div>
    </div>
    <div class="map-overlay" id="legend"></div>

    <script>
     mapboxgl.accessToken = 'pk.eyJ1IjoiYnJpYW5qMTAxMSIsImEiOiJja2lpM2pkYmMwODBnMnltdmZuNGFrcGU2In0.1MpWFYYGZZ5b0orS5V5aZg';

var map = new mapboxgl.Map({
  container: 'map', // container id
  style: 'mapbox://styles/brianj1011/ckiqxraaw01v517n7rx7hts43', // url del estilo
center: [-99.659437 , 19.271091],
        zoom: 9.5 
});

//JS Distancia

var distanceContainer = document.getElementById('distance');
 
// GeoJSON object to hold our measurement features
var geojson = {
'type': 'FeatureCollection',
'features': []
};
 
// Used to draw a line between points
var linestring = {
'type': 'Feature',
'geometry': {
'type': 'LineString',
'coordinates': []
}
};

  //
//Distancia

    map.on('load', function () {
        map.addSource('geojson', {
            'type': 'geojson',
            'data': geojson
        });

        // Add styles to the map
        map.addLayer({
            id: 'measure-points',
            type: 'circle',
            source: 'geojson',
            paint: {
                'circle-radius': 5,
                'circle-color': '#000'
            },
            filter: ['in', '$type', 'Point']
        });
        map.addLayer({
            id: 'measure-lines',
            type: 'line',
            source: 'geojson',
            layout: {
                'line-cap': 'round',
                'line-join': 'round'
            },
            paint: {
                'line-color': '#000',
                'line-width': 2.5
            },
            filter: ['in', '$type', 'LineString']
        });

        map.on('click', function (e) {
            var features = map.queryRenderedFeatures(e.point, {
                layers: ['measure-points']
            });

            // Remove the linestring from the group
            // So we can redraw it based on the points collection
            if (geojson.features.length > 1) geojson.features.pop();

            // Clear the Distance container to populate it with a new value
            distanceContainer.innerHTML = '';

            // If a feature was clicked, remove it from the map
            if (features.length) {
                var id = features[0].properties.id;
                geojson.features = geojson.features.filter(function (point) {
                    return point.properties.id !== id;
                });
            } else {
                var point = {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'Point',
                        'coordinates': [e.lngLat.lng, e.lngLat.lat]
                    },
                    'properties': {
                        'id': String(new Date().getTime())
                    }
                };

                geojson.features.push(point);
            }

            if (geojson.features.length > 1) {
                linestring.geometry.coordinates = geojson.features.map(
                    function (point) {
                        return point.geometry.coordinates;
                    }
                );

                geojson.features.push(linestring);

                // Populate the distanceContainer with total distance
                var value = document.createElement('pre');
                value.textContent =
                    'Total distance: ' +
                    turf.length(linestring).toLocaleString() +
                    'km';
                distanceContainer.appendChild(value);
            }

            map.getSource('geojson').setData(geojson);
        });
    });

    map.on('mousemove', function (e) {
        var features = map.queryRenderedFeatures(e.point, {
            layers: ['measure-points']
        });
        // UI indicator for clicking/hovering a point on the map
        map.getCanvas().style.cursor = features.length
            ? 'pointer'
            : 'crosshair';
    });


//Densidad
map.on('load', function() {
  var layers = ['0 = Neutro', '1 = Bajo', '2 = Medio', '3 = Alto'];
var colors = ['#fef0d9', '#fdcc8a', '#fc8d59', '#e34a33'];// the rest of the code will go in here
  for (i = 0; i < layers.length; i++) {
  var layer = layers[i];
  var color = colors[i];
  var item = document.createElement('div');
  var key = document.createElement('span');
  key.className = 'legend-key';
  key.style.backgroundColor = color;

  var value = document.createElement('span');
  value.innerHTML = layer;
  item.appendChild(key);
  item.appendChild(value);
  legend.appendChild(item);
}
});

//Mover el cursor
map.on('mousemove', function(e) {
  var states = map.queryRenderedFeatures(e.point, {
    layers: ['ZMVT_PELIGRO']
  });

  if (states.length > 0) {
    document.getElementById('pd').innerHTML = '<h3><strong>' + states[0].properties.nom_mun + '</strong></h3><p><strong><em>' + states[0].properties.lisa_cl + '</strong> Grado de incidencia delictiva</em></p>';
  } else {
    document.getElementById('pd').innerHTML = '<p>Mueve el cursor!</p>';
  }
});

//Forma del cursor predeterminado
map.getCanvas().style.cursor = 'default';

//Limites territoriales de la ZMVT
map.fitBounds([[-102.177,23.287, -98.803,21.220], [-100.721,24.308, -98.432,22.221]]);

    </script>
  </body>
</html>