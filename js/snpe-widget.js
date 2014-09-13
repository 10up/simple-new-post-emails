(function($){
	$('#snpe_send').on('change', function(e){
		var $this = $(this),
			checked = ! $this.prop('checked'); // store the previous state

		// display spinner

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: $('.snpe-options').serialize(),
			success: function(r) {
				if ( true === r.success ) {
					$this.prop( 'checked', r.data );
				} else {
					$this.prop( 'checked', checked);
				}

				// hide spinner
			}
		})
	})
})(jQuery);
