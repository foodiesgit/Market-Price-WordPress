jQuery(function($) {

	var completedrows = [];
	var setuprows     = $( '#mycred-scratch-card-winnings table tbody tr.scratch-payout-row' );

	/**
	 * Remove the submitdiv metabox
	 * This is a temporary fix for the PIKLIST plugin that insist on attaching
	 * metaboxes on all custom post types.
	 * @since 1.0
	 * @version 1.0
	 */
	$(document).ready(function(){
		$( '#poststuff #submitdiv' ).remove();
	});

	/**
	 * Recount Set Total
	 * @since 1.0
	 * @version 1.0
	 */
	function mycred_scratch_recount_set_total() {

		var totalelement = $( '.scratch-payout-total .scratch-set-number strong' );
		var totalinset   = 0;

		$( '#mycred-scratch-card-winnings table tr.scratch-payout-row .scratch-set-number input' ).each(function(){

			var rowtotal = $(this).val();
			if ( rowtotal == '' ) rowtotal = 0;
			rowtotal = parseInt( rowtotal );

			totalinset = totalinset + rowtotal;

		});

		totalelement.empty().text( totalinset );

	};

	/**
	 * Insert Payout Row
	 * @since 1.0
	 * @version 1.0
	 */
	function mycred_scratch_cards_insert_row( index, scratchsetid, buttonel ) {

		var thisresultsbox = $( '#mycredcardpayoutrow' + index + ' td.scratch-set-equals' );

		$.ajax({
			type        : "POST",
			data        : {
				action : 'generate_scratch_cards',
				token  : myCREDScratchCardEditor.activationtoken,
				setid  : scratchsetid,
				payout : $( '#mycredcardpayoutrow' + index + ' td.scratch-set-payout input' ).val(),
				number : $( '#mycredcardpayoutrow' + index + ' td.scratch-set-number input' ).val(),
				setup  : index
			},
			dataType    : "JSON",
			url         : ajaxurl,
			beforeSend  : function() {

				console.log( 'Started row ' + index );

			},
			success     : function( response ) {

				if ( response.success ) {

					// Row finished
					if ( response.data.finishedrow ) {

						thisresultsbox.empty().html( response.data.html );
						completedrows[ index ] = 'OK';

						// If we are not on the final row continue with the next one
						if ( index < ( setuprows.length - 1 ) ) {

							console.log( 'Finished row ' + index );

							mycred_scratch_cards_insert_row( ( index + 1 ), scratchsetid, buttonel );
							return true;

						}

						// Final row finished
						else {

							if ( completedrows.length == setuprows.length ) {

								console.log( 'All rows were successfully created.' );

								buttonel.after( '<input type="hidden" name="ready-to-activate-set" value="1" />' );

								$( 'form#post' ).submit();

							}

							else if ( completedrows.length < setuprows.length ) {

								alert( myCREDScratchCardEditor.generatorerror );

								$( 'form#post input, form#post select' ).removeAttr( 'readonly', 'readonly' );
								$( 'form#post input[type=submit], form#post button' ).removeAttr( 'disabled', 'disabled' );

							}

						}

					}

					// Row not finished, we need to run again
					else {

						thisresultsbox.empty().html( response.data.html );
						console.log( 'The current row is not yet finished. Run again.' );

						mycred_scratch_cards_insert_row( index, scratchsetid, buttonel );
						return true;

					}

				}
				else {

					thisresultsbox.empty().html( '<span class="dashicons dashicons-no"></span>' );

				}

			},
			error       : function( jqXHR, textStatus, errorThrown ) {

				console.log( jqXHR + ':' + textStatus + ':' + errorThrown );
				thisresultsbox.empty();
				return false;

			}
		});

	};

	/**
	 * Update Total
	 * When a winning input field changes we need to update the total.
	 * @since 1.0
	 * @version 1.0
	 */
	$( '#mycred-scratch-card-winnings table tr.scratch-payout-row .scratch-set-number input' ).change(function(){

		mycred_scratch_recount_set_total();

	});

	/**
	 * Add New Row
	 * Add new payout row.
	 * @since 1.0
	 * @version 1.0
	 */
	$( '#add-new-scratch-payout-row' ).click(function(){

		var numberformat = $(this).data( 'format' );
		var setuprows    = $( '#mycred-scratch-card-winnings table tr.scratch-payout-row' );
		var totalrows    = setuprows.length;

		$( '#mycred-scratch-card-winnings table tr.scratch-payout-row .scratch-set-payout input' ).each(function( index ){

			$(this).attr( 'name', 'mycred_scratch_set[' + index + '][value]' );

		});

		$( '#mycred-scratch-card-winnings table tr.scratch-payout-row .scratch-set-number input' ).each(function( index ){

			$(this).attr( 'name', 'mycred_scratch_set[' + index + '][number]' );

		});

		var rowtemplate = '<tr class="scratch-payout-row"> \
			<td class="scratch-set-payout"> \
				<input type="text" name="mycred_scratch_set[' + totalrows + '][value]" size="6" value="" placeholder="' + numberformat + '" \/> \
			<\/td> \
			<td class="scratch-set-number"> \
				<input type="number" name="mycred_scratch_set[' + totalrows + '][number]" min="0" step="1" size="6" value="0" \/> \
			<\/td> \
			<td class="text-center scratch-set-equals"><button type="button" class="button button-small remove-scratch-setup-button">-<\/button><\/td> \
		<\/tr>';

		$( '#mycred-scratch-card-winnings table tr.scratch-payout-total' ).before( rowtemplate );

	});

	/**
	 * Remove Payout Row
	 * Handle removal of a payout row.
	 * @since 1.0
	 * @version 1.0
	 */
	$( '#mycred-scratch-card-winnings' ).on( 'click', '.remove-scratch-setup-button', function(e){

		e.preventDefault();

		$(this).parent().parent().slideUp().remove();

		$( '#mycred-scratch-card-winnings table tr.scratch-payout-row .scratch-set-payout input' ).each(function( index ){

			$(this).attr( 'name', 'mycred_scratch_set[' + index + '][value]' );

		});

		$( '#mycred-scratch-card-winnings table tr.scratch-payout-row .scratch-set-number input' ).each(function( index ){

			$(this).attr( 'name', 'mycred_scratch_set[' + index + '][number]' );

		});

		mycred_scratch_recount_set_total();

	});

	/**
	 * Set Cover Image
	 * @since 1.0
	 * @version 1.0
	 */
	$( '#set-scratch-cover-image' ).click(function(e){

		var coverwrapper = $( '#scratch-cover-image-wrapper' );
		var coverbutton  = $(this);

		e.preventDefault();

		scratch_cover_uploader = wp.media.frames.file_frame = wp.media({
			title    : myCREDScratchCardEditor.uploadercovertitle,
			button   : {
				text     : myCREDScratchCardEditor.buttoncoverlabel
			},
			multiple : false
		});

		scratch_cover_uploader.on( 'select', function(){

			attachments = scratch_cover_uploader.state().get('selection').first().toJSON();

			var coverimage = '<img src="' + attachments.url + '" alt="" \/><input type="hidden" name="scratch_card_setup[cover_image]" id="mycred-scratch-cover-image" value="' + attachments.id + '" \/>';
			coverwrapper.empty().html( coverimage );
			coverbutton.text( myCREDScratchCardEditor.changecoverlabel );

		});

		scratch_cover_uploader.open();

	});

	/**
	 * Set Coin Image
	 * @since 1.0
	 * @version 1.0
	 */
	$( '#change-coin-image' ).click(function(e){

		var coinwrapper = $( '#mycred-scratch-coin-image div' );

		e.preventDefault();

		scratch_coin_uploader = wp.media.frames.file_frame = wp.media({
			title    : myCREDScratchCardEditor.uploadercointitle,
			button   : {
				text     : myCREDScratchCardEditor.buttoncoinlabel
			},
			multiple : false
		});

		scratch_coin_uploader.on( 'select', function(){

			attachments = scratch_coin_uploader.state().get('selection').first().toJSON();

			var coinimage = '<img src="' + attachments.url + '" alt="" \/><input type="hidden" name="scratch_card_setup[coin_image]" id="mycred-scratch-coin-image" value="' + attachments.id + '" \/>';
			coinwrapper.empty().html( coinimage );

		});

		scratch_coin_uploader.open();

	});

	/**
	 * Remove Image
	 * @since 1.0
	 * @version 1.0
	 */
	$( '#postbox-container-2' ).on( 'click', '.dashicon', function(e){

		$(this).parent().parent().remove();

	});

	/**
	 * Add Image
	 * @since 1.0
	 * @version 1.0
	 */
	$( '.mycred-scratch-image-wrapper button' ).on( 'click', function(e){

		var button     = $(this).parent().parent();
		var imagegroup = $(this).data( 'row' );

		e.preventDefault();

		scratch_background_uploader = wp.media.frames.file_frame = wp.media({
			title    : myCREDScratchCardEditor.uploadertitle,
			button   : {
				text     : myCREDScratchCardEditor.buttonlabel
			},
			multiple : true
		});

		scratch_background_uploader.on( 'select', function(){

			attachments = scratch_background_uploader.state().get('selection');
			attachments.each(function( el, index ){

				var selectedbg = el.toJSON();

				console.log( index );
				console.log(  );

				var ScratchCardImageTemplate = '<div class="mycred-scratch-attached-image col-lg-3 col-md-3 col-sm-3 col-xs-12"><div class="remove-attached-image"><div class="dashicon dashicons-no dashicons-before"><\/div><\/div><input type="hidden" name="mycred_scratch_set[' + imagegroup + '][attachment_ids][]" id="mycred-scratch-win' + imagegroup + '-id' + selectedbg.id + '" value="' + selectedbg.id + '" \/><img src="' + selectedbg.url + '" alt="" \/><\/div>';

				button.prepend( ScratchCardImageTemplate );

			});

		});

		scratch_background_uploader.open();

	});

	/**
	 * Confirm Publication
	 * @since 1.0
	 * @version 1.0
	 */
	$( 'input#publish' ).click(function(e){

		e.preventDefault();

		if ( ! confirm( myCREDScratchCardEditor.confirmpublish ) )
			return false;

		else
			return true;

	});

	/**
	 * Load Template
	 * @since 1.0
	 * @version 1.0
	 */
	$( 'select#mycred-scratch-template' ).change(function(e){

		e.preventDefault();

		var selectedtemplate = $(this).find( ':selected' );
		if ( selectedtemplate === undefined ) return false;

		if ( selectedtemplate.val() == '' ) return false;

		if ( confirm( myCREDScratchCardEditor.confirmtemplate ) ) {

			$( 'form#post input#save-post' ).click();

			$( 'form#post input, form#post select' ).attr( 'readonly', 'readonly' );
			$( 'form#post input[type=submit], form#post button' ).attr( 'disabled', 'disabled' );

		}
		else {

			selectedtemplate.removeAttr( 'selected' );

		}

	});

	/**
	 * Activate New Set
	 * @since 1.0
	 * @version 1.0
	 */
	$( 'input#activate-new-scratch-card-set' ).click(function(e){

		e.preventDefault();

		if ( ! confirm( myCREDScratchCardEditor.confirmactivation ) )
			return false;

		var formsubmitbutton = $(this);

		console.log( 'Total found rows: ' + setuprows.length );

		$( 'form#post input, form#post select' ).attr( 'readonly', 'readonly' );
		$( 'form#post input[type=submit], form#post button' ).attr( 'disabled', 'disabled' );

		$( '#mycred-scratch-card-winnings table td button' ).hide();
		formsubmitbutton.blur();

		$( '#mycredcardpayoutrow0 td.scratch-set-equals' ).empty().html( '<small>0 %</small>' );

		mycred_scratch_cards_insert_row( 0, formsubmitbutton.data( 'setid' ), formsubmitbutton );

	});

	/**
	 * Destroy New Set
	 * @since 1.0
	 * @version 1.0
	 */
	$( '#mycred-destroy-scratch-card-set' ).click(function(){

		if ( ! confirm( myCREDScratchCardEditor.confirmdestroy ) )
			return false;

		$( 'form#post input, form#post select' ).attr( 'readonly', 'readonly' );
		$( 'form#post input[type=submit], form#post button' ).attr( 'disabled', 'disabled' );

		return true;

	});

});