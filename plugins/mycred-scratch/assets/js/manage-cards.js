jQuery(function($) {

	/**
	 * 
	 * @since 1.0
	 * @version 1.0
	 */
	$( '#put-card-set-on-hold' ).click(function(e){

		e.preventDefault();

		if ( confirm( myCREDScratchCardManager.confirmonhold ) ) {

			$( 'form#post input, form#post select' ).attr( 'readonly', 'readonly' );
			$( 'form#post input[type=submit], form#post button' ).attr( 'disabled', 'disabled' );
			$(this).after( '<input type="hidden" name="mycred-change-status" value="onhold" />' );
			$( 'form#post' ).submit();

		}

	});

	/**
	 * 
	 * @since 1.0
	 * @version 1.0
	 */
	$( 'a.mycred-destroy-scratch-card-set' ).click(function(){

		if ( ! confirm( myCREDScratchCardManager.confirmdestroy ) )
			return false;

		return true;

	});

});