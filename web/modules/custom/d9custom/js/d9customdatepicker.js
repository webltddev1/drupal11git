(function ($, Drupal) {
  Drupal.behaviors.d9customdatepicker = {
    attach: function (context, settings) {
		$(once('daterranger', '#edit-periode,#edit-periode--2', context)).daterangepicker({
			minDate: moment(),
			autoUpdateInput: false,
			locale: {
				format: "DD/MM/YYYY",
				cancelLabel: 'Clear'
			}
		});
		
		$('#edit-periode,#edit-periode--2').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
		});
		
    }//END here
  };
})(jQuery, Drupal);