gs.geolocation = {

	/**
	 * Initialize default view
	 */
	init: function() {
		gs.geolocation.getPosition();
	},

	/**
	 *	Get current GPS position
	 */
	getPosition: function() {
		// Check if browser supports geolocation
		if(!navigator.geolocation) {
			console.warn("Geolocation not supported by the browser");
			return;
		}
		// Get location once
		navigator.geolocation.getCurrentPosition(function(position) {
			if(position.accuracy > 20) { // accurate to 20 meters
				console.warn("Position is too inaccurate; accuracy="+position.accuracy);
				return;
			} else {
				var latitude = position.coords.latitude;
				var longitude = position.coords.longitude;
				$.post('/user/geolocation', {latitude:latitude, longitude:longitude}, function(data) {
					console.log(data);
					gs.setSession(data);
				});
				console.log('Sending ' + latitude + ', ' + longitude);
			}
		
		},
		function(error) {
			switch(error.code) {
				case error.PERMISSION_DENIED:
					console.error("User denied the request for Geolocation.");
					break;
				case error.POSITION_UNAVAILABLE:
					console.error("Location information is unavailable.");
					break;
				case error.TIMEOUT:
					console.error("The request to get user location timed out.");
					break;
				case error.UNKNOWN_ERROR:
					console.error("An unknown error occurred.");
					break;
			}
		},
		{
			timeout: 30000, // Report error if no position update within 30 seconds
			maximumAge: 30000, // Use a cached position up to 30 seconds old
			enableHighAccuracy: true // Enabling high accuracy tells it to use GPS if it's available  
		});
	}
}

gs.geolocation.init();