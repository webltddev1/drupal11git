(function ($, Drupal) {
  Drupal.behaviors.d9custom2 = {
    attach: function (context, settings) {
			
			
      // console.log('me');
			
			// Hotel full page image slider
			if( $("article.full .field--name-field-slideshow,article.full .field--name-field-prod-slideshow").is('div') ){
				$(once('slideshw', 'article.full .field--name-field-slideshow,article.full .field--name-field-prod-slideshow', context)).slick({
				// $("article.full .field--name-field-slideshow,article.full .field--name-field-prod-slideshow", context).slick({
					infinite: true,
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false,
					arrows: true,
					autoplay: true,
					// centerMode: true,
					// centerPadding: '60px',
					// cssEase: 'cubic-bezier(0.600, -0.280, 0.735, 0.045)',
				});
				
				$('article.full .field--name-field-slideshow,article.full .field--name-field-prod-slideshow').on('beforeChange', function(event, slick, currentSlide, nextSlide){
					$('.slider-count .count-current').html(nextSlide + 1);
				});
			}
			
			
			// Homepage slideshow
			if( $("#block-d9bootstrap-slideshowblock .slideshow").is('div') ){
				$(once('slideshww', '#block-d9bootstrap-slideshowblock .slideshow', context)).slick({
				// $("#block-d9bootstrap-slideshowblock .slideshow", context).slick({
					infinite: true,
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: true,
					arrows: true,
					autoplay: true,
					appendArrows: '#slide-slick-nav',
					appendDots: '#slide-slick-nav',
					// centerMode: true,
					// centerPadding: '60px',
					// appendArrows: '#block-categoryblock .directional-nav',
					// cssEase: 'cubic-bezier(0.600, -0.280, 0.735, 0.045)',
				});
			}
			
			
			// Category home block
			if( $(".block-cats-block .categoryblock").is('div') ){
				$(once('catblck', '.block-cats-block .categoryblock .inner', context)).slick({
				// $(".block-cats-block .categoryblock .inner", context).slick({
					infinite: false,
					slidesToShow: 4,
					slidesToScroll: 1,
					dots: false,
					arrows: true,
					autoplay: false,
					responsive: [
						{
							breakpoint: 800,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1,
							}
						},
						{
							breakpoint: 500,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						},
					]
				});
			}
			// Homepage content
			if( $(".region-content .categoryblock").is('div') ){
				$(once('catblckk', '.region-content .categoryblock .inner', context)).slick({
				// $(".region-content .categoryblock .inner", context).slick({
					infinite: false,
					slidesToShow: 4,
					slidesToScroll: 1,
					dots: false,
					arrows: true,
					autoplay: false,
					responsive: [
						{
							breakpoint: 800,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1,
							}
						},
						{
							breakpoint: 500,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						},
					]
				});
			}
			
			// alert('x1'); // WILL NOT BE USED
			if( $("#categorylistingform").is('form') ){
				$(".form-checkbox").change(function() {
								$("#loada").show();
								$("#categorylistingform").submit();
				});
			}
			

			
			

			
			
			
			/* product accordeon */
			// $(once('titleclick', '.field--name-field-titre', context)).click(function(){
				// $(this).toggleClass("openn");
				// $(this).next().slideToggle();
			// });
			
			// carousel with variable width
			// if( $('#block-imagesblock .advimages').is('div') ){
				// $("#block-imagesblock .advimages", context).slick({
					// infinite: true,
					// slidesToShow: 3,
					// slidesToScroll: 3,
					// dots: true,
					// // centerMode: true,
					// // centerPadding: '60px',
					// // appendArrows: '#block-categoryblock .directional-nav',
					// // cssEase: 'cubic-bezier(0.600, -0.280, 0.735, 0.045)',
				// });
			// }
			
			
			// // carousel with variable width
			// if( $('.field--name-field-slides .field--item').is('div') ){
				// $(".field--name-field-slides", context).slick({
					// infinite: true,
					// slidesToShow: 1,
					// slidesToScroll: 1,
					// dots: false,
					// arrows: true,
					// autoplay: true,
					// // centerMode: true,
					// // centerPadding: '60px',
					// // appendArrows: '#block-categoryblock .directional-nav',
					// // cssEase: 'cubic-bezier(0.600, -0.280, 0.735, 0.045)',
				// });
			// }
			// console.log('ss3ss5');
			
			
			
      // function d7GridSize(){ // Carousel number of elements based on width
        // $x = 4;
        // if( window.innerWidth < 475 ) $x = 1;
        // else if( window.innerWidth < 660 ) $x = 3;
        // else if( window.innerWidth < 1200 ) $x = 4;
        
        // return $x;
      // }
      
			/*
      if( $('#diaporama').is('div') ){
        $('#diaporama .slideshow', context).slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: true,
					fade: false,
					variableWidth: false,
					autoplay: true,
					appendArrows: '#diaporama .directional-nav',
					// cssEase: 'cubic-bezier(0.600, -0.280, 0.735, 0.045)',
        });
      }
			*/
        
        // articlesslideblock.slick({
          // slidesToShow: 1,
          // slidesToScroll: 1,
          // // asNavFor: side,
          // arrows: false,
          // autoplay: true,
          // dots: true,
          // centerMode: false,
          // infinite: true,
          // autoplaySpeed: 4500,
          // useTransform: true,
          // asNavFor: '#block-articlesslideblock .article-images-inner .articles-img'
          // // focusOnSelect: true,
          
        // });
        
        
      
		// circuit croisiers, price change on date change
		if( $("#edit-departure-dates").is("select") ){
			var dep_id = $("#edit-departure-dates").val();
			// console.log(dep_id);
			var dep_prices = drupalSettings.d9offers.departure_prices;
			var dep_price = dep_prices[dep_id];
			// console.log(dep_price);
			$(".apartirde .apd").html( addCommas(dep_price) + ' €');
		}
		$("#edit-departure-dates").change(function(){
			var dep_id = $(this).val();
			// console.log(dep_id);
			var dep_prices = drupalSettings.d9offers.departure_prices;
			var dep_price = dep_prices[dep_id];
			// console.log(dep_price);
			$(".apartirde .apd").html( addCommas(dep_price) + ' €');
		});
		
		
		
		
function addCommas(nStr)
{
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ' ' + '$2');
    }
    return x1 + x2;
}
      
    }//END here
  };
})(jQuery, Drupal);