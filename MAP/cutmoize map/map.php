
<?php

$locations = '{"title": "title", "lat": "30.5437048", "lng": "-98.40756650000003","description": "fdasf"},{"title": "title", "lat": "32.8995451", "lng": "-96.74300900000003","description": "fdasf"},{"title": "title", "lat": "30.2430081", "lng": "-97.7229097","description": "fdasf"},';

?>


<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAODS9XrZ6B-JFmbx4d71V5nznMAjctqOM&sensor=false"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script type="text/javascript" src="richmarker.js"></script>
<!--script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyBHuNR-fHV-HvWVWrJvjv9XsGe7NhL8t9c" type="text/javascript"></script-->
<style>
	.img{
		    position: relative;
    height: 60px;
    width: 60px;
    display: block;
	}
	.img img{
		max-height:100%;
		height:auto;
		position: absolute;
		left: 0;
		right: 0;
		margin: auto;
	}
	.markerHTML{		
		color:#fff;
		border-radius:30px;
		height:60px;
		width:60px;
		background-size:contain;
	}
	.markerHTML .count{
		padding:10px;
		text-align:center;
		position: absolute;
		top: 0;
		right: 0;
		left: 0;
		margin: auto;
	}
	.markerHTML .title{
		position: absolute;
		left: 0;
		right: 0;
		margin: auto;
		text-align: center;
		color: #000;
		font-size: 14px;
		font-weight: bold;		
		text-transform: capitalize;
		letter-spacing: 1px;
		top:-17px;
	}
	#map div{
		box-shadow:none !important;
	}
	/* Removing Google map watermark and Terms and Conditions */
	.gmnoprint a, .gmnoprint span {
    display:none;
}
.gmnoprint div {
    background:none !important;
}
a[href^="http://maps.google.com/maps"]{display:none !important}
a[href^="https://maps.google.com/maps"]{display:none !important}
</style>
<script type="text/javascript">	
	var markers = [
		<?php echo $locations; ?>
    ];
    window.onload = function () {
        LoadMap();
    }
	
	var styles =[
          {
            elementType: 'geometry',  /**/
            stylers: [{color: '#e7e7e8'}]
          },
          {
            elementType: 'labels.icon',
            stylers: [{visibility: 'off'}]
          },
          {
            elementType: 'labels.text.fill',
            stylers: [{color: '#bcbdc0'}]
          },		
          {
            elementType: 'labels.text.stroke',
            stylers: [{color: '#000'}]
          },
          {
            featureType: 'administrative.land_parcel',
            elementType: 'labels.text.fill',
            stylers: [{color: '#bdbdbd'}]
          },
          {
            featureType: 'poi',
            elementType: 'geometry',
            stylers: [{color: '#eeeeee'}]
          },
          {
            featureType: 'poi',
            elementType: 'labels.text.fill',
            stylers: [{color: '#757575'}]
          },
          {
            featureType: 'poi.park',
            elementType: 'geometry',
            stylers: [{color: '#e5e5e5'}]
          },
          {
            featureType: 'poi.park',
            elementType: 'labels.text.fill',
            stylers: [{color: '#9e9e9e'}]
          },
          {
            featureType: 'road',
            elementType: 'geometry',
            stylers: [{color: '#bcbdc0'}]
          },
          {
            featureType: 'road.arterial',
            elementType: 'labels.text.fill',
            stylers: [{color: '#757575'}]
          },
          {
            featureType: 'road.highway',
            elementType: 'geometry',
            stylers: [{color: '#dadada'}]
          },
          {
            featureType: 'road.highway',
            elementType: 'labels.text.fill',
            stylers: [{color: '#616161'}]
          },
          {
            featureType: 'road.local',
            elementType: 'labels.text.fill',
            stylers: [{color: '#000'}]
          },
          {
            featureType: 'transit.line',
            elementType: 'geometry',
            stylers: [{color: '#e5e5e5'}]
          },
          {
            featureType: 'transit.station',
            elementType: 'geometry',
            stylers: [{color: '#eeeeee'}]
          },
          {
            featureType: 'water',
            elementType: 'geometry',
            stylers: [{color: '#c9c9c9'}]
          },
          {
            featureType: 'water',
            elementType: 'labels.text.fill',
            stylers: [{color: '#9e9e9e'}]
          }
        ];
    function LoadMap() {
        var mapOptions = {
            center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
            // zoom: 8, //Not required.
            mapTypeId: google.maps.MapTypeId.ROADMAP,
			styles: styles		
        };
        var infoWindow = new google.maps.InfoWindow();
        var map = new google.maps.Map(document.getElementById("map"), mapOptions);
		
        //Create LatLngBounds object.
        var latlngbounds = new google.maps.LatLngBounds();
		var count = 1;
        for (var i = 0; i < markers.length; i++) {
            var data = markers[i]
            var myLatlng = new google.maps.LatLng(data.lat, data.lng);
            var marker = new RichMarker({
                position: myLatlng,
                map: map,
                title: data.title,				
				content:'<div class="markerHTML"><span class="img"><img src="marker.png" /><span class="count">'+count+'</span></span><br/><span class="title">'+data.title+'</span></div>'
            });
			
            (function (marker, data) {
                google.maps.event.addListener(marker, "click", function (e) {
                    infoWindow.setContent("<div style = 'width:220px;min-height:40px'>" + data.description + "</div>");
                    infoWindow.open(map, marker);
                });
            })(marker, data);
 
            //Extend each marker's position in LatLngBounds object.
            latlngbounds.extend(marker.position);
			count++;
        }
				
	
        //Get the boundaries of the Map.
        var bounds = new google.maps.LatLngBounds();
 
        //Center map and adjust Zoom based on the position of all markers.
        map.setCenter(latlngbounds.getCenter());
        map.fitBounds(latlngbounds);		
    }
</script>

<div id="map" style="height:300px;width:100%"></div>
			