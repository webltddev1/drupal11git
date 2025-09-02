(function ($, Drupal) {
  Drupal.behaviors.d9custom = {
    attach: function (context, settings) {
      // console.log('me');
      
      function d7GridSize(){ // Carousel number of elements based on width
        $x = 4;
        if( window.innerWidth < 475 ) $x = 1;
        else if( window.innerWidth < 660 ) $x = 3;
        else if( window.innerWidth < 1200 ) $x = 4;
        
        return $x;
      }
      
      if( $('#diaporama').is('div') ){
        $('#diaporama').flexslider({
          selector: ".slideshow > li",
          animation: "slide",
          directionNav: true,
          touch: true,
          animationLoop: true,
          controlNav: false,
          itemMargin: 0,
          minItems: 0,
          slideshow: true,
          pauseOnHover: true,
          animationSpeed: 500,
          slideshowSpeed: 5000,
        });
      }
	  
	  
	  
	  
	  
	 console.log('insode');
	
    function initingMap(latitude, longitude, id) {
        var coordinates = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
        var map = new google.maps.Map(document.getElementById(id), {
            zoom: 15, // Adjust the zoom level as needed
			disableDefaultUI: true,
            center: coordinates
        });
        var marker = new google.maps.Marker({
            position: coordinates,
            map: map
        });
    }
	
	if( $('.mapme').is('div') ){
		$('.mapme').each(function(){
			var i = $(this).attr('id');
			var latitude = $(this).attr('lat');
			var longitude = $(this).attr('lon');
			
			
			console.log(latitude);
			console.log(longitude);
			
			initingMap(latitude, longitude, i);
		});
	}
			
      
    }//END here
  };
})(jQuery, Drupal);