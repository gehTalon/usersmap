

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Users Map</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
  <script src="https://code.jquery.com/jquery-1.8.3.js"></script>			
  <script type='text/javascript' src='https://nxtaction.com/includes/calendar/libs/jquery-ui-1.8.11.custom.min.js'></script>
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRywapjev4IP4kFBmZ7tgbkSHnG1aDR1A&libraries=places&sensor=false"></script>
		
  <style>
    .main-box{
        margin-top:40px;
    }
    .row.content {height: 1500px}
    
    .sidenav {
      background-color: #f1f1f1;
      height: 100%;
    }

    .map-box{
        margin-top:10px;
    }

    #main-btn{
        display:none;
    }
   
    footer {
      background-color: #555;
      color: white;
      padding: 15px;
    }

    
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height: auto;} 
    }
  </style>
  <script>
    var locations = [];
    var map;
    var infowindow;
    var tempLocation;
    var tempLat;
    var tempLng;

    function storeLocationToFile()
    {
        $.ajax({
            type: "POST",
            url: 'storeLocations.php',
            data: {data:locations},
           
        }).then(
           
            function(response)
            {
                
              
            },
           
            function()
            {
                alert('There was some error!');
            }
        );
    }

    function loadLocations(){
       
        $.ajax({
            type: "GET",
            url: 'getLocations.php',
        
        }).then(
           
            function(response)
            {
                var jsonData = JSON.parse(response);

                locations = jsonData.locations;
                console.log(locations);
                var marker, i;

                for (i = 0; i < locations.length; i++) {  
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i].lat, locations[i].lng),
                    map: map
                });

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                    infowindow.setContent(locations[i].name);
                    infowindow.open(map, marker);
                    }
                })(marker, i));

                }
              
            },
           
            function()
            {
                alert('There was some error!');
            }
        );
    }
   
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            
        }
       
    }
    function showPosition(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

       
        initialize(lat,lng);
        
       
    
    }

    function openAddLocationForm(location,lat,lng)
    {
       $("#main-btn").click(); 
       tempLocation = location;
       tempLat = lat;
       tempLng = lng;
    }

    function addLocation()
    {
        var title = $("#location-add").val();
        var lat = $("#lat-add").val();
        var lng = $("#lng-add").val();

        var marker, i;

        
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lng),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function(marker) {
            return function() {
            infowindow.setContent(title);
            infowindow.open(map, marker);
            }
        })(marker));

        locations.push({"name":title,"lat":lat,"lng":lng});
        $("#location-add").val('');
          
        storeLocationToFile();


    }

    function placeMarker() {
            var title = $("#location-new").val();
            var marker = new google.maps.Marker({
                position: tempLocation, 
                map: map
            });

            google.maps.event.addListener(marker, 'click', (function(marker) {
                    return function() {
                    infowindow.setContent(title);
                    infowindow.open(map, marker);
                    }
            })(marker));
            locations.push({"name":title,"lat":tempLat,"lng":tempLng});
            $("#location-new").val('');
          
            storeLocationToFile();
    }
      
function initialize(lat = 16.94501681158705,lng = 121.46134915654419) {
     
      var mapOptions = {
          center: new google.maps.LatLng(lat,lng),
          zoom: 5,
          mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      map = new google.maps.Map(document.getElementById('map_canvas'),
          mapOptions);

      var input = document.getElementById('google_location');
      
      var autocomplete = new google.maps.places.Autocomplete(input);
      
      autocomplete.bindTo('bounds', map);

      infowindow = new google.maps.InfoWindow();
      var marker = new google.maps.Marker({
          map: map
      });

      google.maps.event.addListener(map, 'click', function(event) {
      
            openAddLocationForm(event.latLng,event.latLng.lat(),event.latLng.lng());
      });

      google.maps.event.addListener(autocomplete, 'place_changed', function() {
          infowindow.close();
          var place = autocomplete.getPlace();
          
          if (place.geometry.viewport) {
              map.fitBounds(place.geometry.viewport);
          } else {
              map.setCenter(place.geometry.location);
              map.setZoom(17);  // Why 17? Because it looks good.
          }

          var image = new google.maps.MarkerImage(
                  place.icon,
                  new google.maps.Size(71, 71),
                  new google.maps.Point(0, 0),
                  new google.maps.Point(17, 34),
                  new google.maps.Size(35, 35));
          marker.setIcon(image);
          marker.setPosition(place.geometry.location);

          var address = '';
          if (place.address_components) {
              address = [
                  (place.address_components[0] && place.address_components[0].short_name || ''),
                  (place.address_components[1] && place.address_components[1].short_name || ''),
                  (place.address_components[2] && place.address_components[2].short_name || '')
              ].join(' ');
          }

          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
          infowindow.open(map, marker);
      });

      
      loadLocations();
     
  }
  google.maps.event.addDomListener(window, 'load', getLocation);
  
  
  </script>
</head>
<body>

<div class="container-fluid main-box">
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" id="main-btn" data-target="#pinLocation">Open Modal</button>
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal"  data-target="#addLocation">Add Location</button>
  <div class="modal fade" id="pinLocation" role="dialog">
        <div class="modal-dialog">
        
      
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Pin new Location</h4>
            </div>
            <div class="modal-body">
            <form action="/action_page.php">
                <div class="form-group">
                    <label for="title">Name:</label>
                    <input type="text" class="form-control" id="location-new">
                </div>
              
            </form>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-default" data-dismiss="modal" onclick="placeMarker()">Save Location</button>
            </div>
        </div>
        
        </div>
  </div>

  <div class="modal fade" id="addLocation" role="dialog">
        <div class="modal-dialog">
        
      
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add New Location</h4>
            </div>
            <div class="modal-body">
            <form action="/action_page.php">
                <div class="form-group">
                    <label for="title">Name:</label>
                    <input type="text" class="form-control" id="location-add">
                </div>
                <div class="form-group">
                    <label for="lat">Lat:</label>
                    <input type="text" class="form-control" id="lat-add">
                </div>
                <div class="form-group">
                    <label for="lng">Lng:</label>
                    <input type="text" class="form-control" id="lng-add">
                </div>
              
            </form>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-default" data-dismiss="modal" onclick="addLocation()">Save Location</button>
            </div>
        </div>
        
        </div>
  </div>

  <div class="row content">
    

    <div class="col-12 map-box ">
   
        <div class="input-group">
        <input type="text" class="form-control" id="google_location" name="google_location" placeholder="Search Location..">
        <span class="input-group-btn">
          <button class="btn btn-default" type="button">
            <span class="glyphicon glyphicon-search"></span>
          </button>
        </span>
      </div>
        <div id="map_canvas" style=" height: 100vh; margin-top: 10px; position: relative; background-color: rgb(229, 227, 223); overflow: hidden; margin-right: 0px; width: 100%;" class="well well-small"> </div>
	  
    </div>
  </div>
</div>

</body>
</html>
