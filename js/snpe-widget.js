(function($){
	$('#snpe_send').on('change', function(e){
		var $this = $(this),
			checked = ! $this.prop('checked'), // store the previous state
			spinner = $(this).siblings('.spinner');

		$this.hide();
		spinner.css( 'display', 'inline-block' );

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: snpe_vars.ajaxurl,
			data: $('.snpe-options').serialize(),
			success: function(r) {
				if ( true === r.success ) {
					$this.prop( 'checked', r.data );
				} else {
					$this.prop( 'checked', checked);
				}

				spinner.hide();
				$this.show();
			}
		})
	})
})(jQuery);
