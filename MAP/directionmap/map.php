<script src="http://staging.technocratshorizons.com/city2city/assets/js/jquery.min.js"></script>
<script>
$(document).ready(function() {
	$('#add-more-cities').click(function() {
		var $div = $('div[id^="stopover"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
		var $klon = $div.clone().prop('id', 'stopover'+num );
		$klon.find('.stopover_date').attr('id','');
		$klon.find('.sec-title').css('display','none');
		$klon.find('.remove-stoper').css('display','block');
		
		$klon.find('.stopover_date').removeClass('hasDatepicker').datepicker({
			minDate: 0,
			maxDate: "+2Y",
			dateFormat: 'mm/dd/yy',
		});
		$klon.find('input').val('');
		$div.after( $klon ); 
	});
});

$(document).on('click','.remove-stoper',function(){
	var current = $(this);
	$('#DeleteStoper').modal({
		backdrop: 'static',
		keyboard: false
    })
    .one('click', '.yes-alert-delete', function(e) {
		current.parent().remove();
		calculateAndDisplayRoute(directionsService, directionsDisplay);
    });
});

$(document).on('focus','.location', function() {
	$location_input = $(this);
	var options = {
		componentRestrictions: {
			country: 'us'
		}
	};
	autocomplete = new google.maps.places.Autocomplete($location_input.get(0), options);
	autocomplete.addListener('place_changed', fillInAddress);
});

var directionsService ='';
var directionsDisplay ='';
var map ='';

function fillInAddress() {
	
	var breakdown = 0;
	var result = autocomplete.getPlace();
	for(var i = 0; i < result.address_components.length; i += 1) {
		var addressObj = result.address_components[i];
		for(var j = 0; j < addressObj.types.length; j += 1) {
			if (addressObj.types[j] === 'locality') {
				breakdown = 1;
				$location_input.parent().find('.input_hidden').val(addressObj.long_name);
				$('#dynamic_departure').html($("input[name='DepartureCity']").val());
				$('#dynamic_arrival').html($("input[name='ArrivalCity']").val());
				
				
				var departureaddress = $("input[name='DepartureAddress']").val();
				var arrivaladdress = $("input[name='ArrivalAddress']").val();
				if(departureaddress!='' && arrivaladdress!='') {
					$.ajax({
						type:"POST",
						url: "http://mserver/codes_current/amit/MAP/directionmap/map.php/DistanceCalculateInAjax",
						dataType: 'json',
						data: {'DepartureAddress': departureaddress,'ArrivalAddress': arrivaladdress},
						dataType: 'json',
						success: function(resp) {
							$('#dst-km').html(resp.distance);
							$('#duration-day').html(resp.drivingtime);
						}
					});
				}
			}
			if (addressObj.types[j] === 'administrative_area_level_1') {
				$location_input.parent().find('.input_hidden_state').val(addressObj.long_name);	
			}
		}
	}
	if(breakdown == 0) {
		$location_input.parent().find('.input_hidden').val('');
	}
	calculateAndDisplayRoute(directionsService, directionsDisplay);
}

function initMap() {
	directionsService  = new google.maps.DirectionsService;
	directionsDisplay = new google.maps.DirectionsRenderer;
	map = new google.maps.Map(document.getElementById('routemap'), {
		zoom: 5,
		center: {lat:10, lng: -1.4163},
		draggable: !("ontouchend" in document),
		preserveViewport: true
	});
	directionsDisplay.setMap(map);				
}

function calculateAndDisplayRoute(directionsService, directionsDisplay) {
	
	var waypts = []; var destination = '';
	/* $('.route').each(function () {
		if($(this).val() != '') {
			waypts.push({
				location: $(this).val(),
				stopover: true
			});
		}
	}) */

	if(document.getElementById('autocomplete2').value != '') {
		destination = document.getElementById('autocomplete2').value;
	}else if(document.getElementById('start').value != '') {
		destination = document.getElementById('start').value;
	}else{
		destination = document.getElementById('defaultl').value;
	}

	directionsService.route({
		origin: document.getElementById('defaultl').value,
		destination: destination,
		waypoints: waypts,
		optimizeWaypoints: true,
		travelMode: 'DRIVING'
	}, function(response, status) {
		if (status === 'OK') {
			directionsDisplay.setDirections(response);
			var route = response.routes[0];
		} 
		else {
		
		}
	});
}
window.onload=function() { calculateAndDisplayRoute(directionsService, directionsDisplay);	 }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBi8WzTrO14PRzaziKEwF2LEnE_7rwKBPM&libraries=places&callback=initMap"></script>
<div id="routemap" class="adjustment" style="height:300px;width:100%;max-width:800px;">
</div>
<style>
input {
  margin-bottom: 13px;
  max-width: 350px;
  padding: 10px;
  width: 100%;
}
.main_wrap {
  margin-top: 20px;
  max-width: 800px;
  width: 100%;
}
.main_wrap_one {
  float: left;
  width: 50%;
}
</style>
<div class="main_wrap">
<div class="main_wrap_one">
	<label class="inout">
	Current location<br/>
	<input id="start" name="DepartureAddress" class="custom-field form-control location" placeholder="(Address, city, station, street or town, postcode)" value="" type="text">
	<input id="defaultl" name="defaultl" value="252 Broadway Tacoma, WA 98402" type="hidden">
	</label>
</div>
<div class="main_wrap_one">
	<label>
	Destination location<br/>
	<input name="ArrivalAddress" class="custom-field form-control location" placeholder="(Address, city, station, street or town, postcode)" id="autocomplete2" value="" type="text">
	</label>
	</div>
</div>
<?php 

function DistanceCalculateInAjax() {
	/* 	echo '<pre>';
		print_r($_REQUEST);
		echo '</pre>';
		die; */
		if($_REQUEST){
			
			$startinglocation = $this->input->post('DepartureAddress');
			$endlocation = $this->input->post('ArrivalAddress');
			
			$this->config->load('config_google');
			$googleKey = $this->config->item('api_key');
			
			$startinglocation = str_replace(' ','+',$startinglocation);
			$endlocation = str_replace(' ','+',$endlocation);
		
			$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=$startinglocation&destinations=$endlocation&key=$googleKey";
			$url = str_replace(' ','',$url);
			 
			$distance_data = file_get_contents($url);
			$distance_data = json_decode($distance_data); 
			$response = $distance_data->rows[0]->elements;
			
			if($response[0]->status == "OK") {
				
				$distance = ($response[0]->distance->value)/1000;
				$distance = sprintf ("%.2f",$distance);
				$drivingtime = $response[0]->duration->text;
			}
			else {
				$distance = '0.00';
				$drivingtime = '';
			}	
			$msg = array('distance'=>$distance,'drivingtime'=>$drivingtime);
			echo json_encode($msg); die;
		}
	}

?>

			