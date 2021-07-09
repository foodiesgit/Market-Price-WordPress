/**
 * myCRED Playfield API
 * @version 1.0.2
 */
var myCREDPlayFields   = [];

var mycred_submit_play = function( play, success_callback, ctx, delay ) {

	var delaytimer;
	if ( typeof delay === 'undefiend' )
		delay = 0;

	jQuery.ajax({
		type       : "POST",
		data       : play,
		dataType   : "JSON",
		url        : myCREDPlay.app,
		success    : function( response ) {

			if ( response.id === undefined )
				location.reload();

			if ( parseInt( delay ) > 0 ) {

				var count  = parseInt( delay );
				jQuery( '.mycred-play-delay-timer' ).text( count );
				delaytimer = setInterval(function(){
					if ( count <= 0 ) {
						clearInterval( delaytimer );
						success_callback( response, ctx );
					}
					else {
						count --;
						jQuery( '.mycred-play-delay-timer' ).text( count );
					}
				}, 1000 );

			}
			else {
				success_callback( response, ctx );
			}

		}
	});

};

var mycred_play_handle_balance = function( response, button ) {

	var balanceelement = jQuery( response.element + ' .mycred-play-balance span.balance' );
	var currentbalance = balanceelement.text();
	var decimals       = parseInt( response.decimals );

	// Float
	if ( decimals > 0 ) {

		currentbalance = parseFloat( currentbalance );
		finalAmount    = parseFloat( response.newbalance );

		var decimal_factor = decimals === 0 ? 1 : Math.pow( 10, decimals );

		balanceelement.prop( 'number', currentbalance ).numerator({
			toValue    : finalAmount,
			fromValue  : currentbalance,
			rounding   : decimals,
			duration   : 2000,
			onStart    : function(){
				balanceelement.parent( 'button' ).addClass( 'button-glow' );
			},
			onComplete : function(){
				balanceelement.parent( 'button' ).removeClass( 'button-glow' );
			}
		});

	}
	// Int
	else {

		currentbalance = parseInt( currentbalance );
		finalAmount    = parseInt( response.newbalance  );

		balanceelement.prop( 'number', currentbalance ).numerator({
			toValue    : finalAmount,
			fromValue  : currentbalance,
			duration   : 2000,
			onStart    : function(){
				balanceelement.parent( 'button' ).addClass( 'button-glow' );
			},
			onComplete : function(){
				balanceelement.parent( 'button' ).removeClass( 'button-glow' );
			}
		});

	}

};

var mycred_play_handler_default = function( response, button ) {

	if ( button === undefined ) button = false;

	if ( response.empty == 1 )
		jQuery( response.element + ' .mycred-play-field' ).empty().append( response.field );
	else
		jQuery( response.element + ' .mycred-play-field' ).append( response.field );

	if ( response.message !== null && response.message.length !== 0 ) {

		jQuery( response.element + ' .mycred-play-title' ).fadeOut(function(){
			jQuery( response.element + ' .mycred-play-title' ).empty().text( response.message ).fadeIn();
		});

	}

	if ( response.newbalance.length !== 0 )
		mycred_play_handle_balance( response, button );

	if ( button === false ) return true;

	if ( response.newlabel != '' )
		button.text( response.newlabel ).removeAttr( 'disabled' );

	if ( response.glow == 1 )
		button.addClass( 'button-glow' );
	else
		button.removeClass( 'button-glow' );

};

jQuery(function($) {

	$(document).ready(function(){

		// Load all play fields
		$( 'body .mycred-play-field-wrapper' ).each(function(index,item){

			var playfield = $(item);
			if ( playfield.hasClass( 'skip-load' ) ) return true;

			var autoload  = {
				action  : playfield.data( 'do' ),
				token   : playfield.data( 'token' ),
				player  : myCREDPlay.player,
				playid  : playfield.data( 'id' ),
				itemid  : playfield.data( 'item' ),
				flavour : playfield.data( 'flavour' ),
				multi   : playfield.data( 'multi' )
			};

			mycred_submit_play( autoload, mycred_play_handler_default, false );

		});

		// Bind click for all buttons
		$( '.mycred-play-field-wrapper' ).on( 'click', 'button.button', function(e){

			var playbutton = $(this);
			if ( playbutton.hasClass( 'button-fake' ) ) return true;

			var handler    = mycred_play_handler_default;

			// do-html="elementid" - html content is expected to be returned, element id to replace content with
			if ( playbutton.data( 'handler' ) !== undefined )
				handler = playbutton.data( 'handler' );

			var newplay    = {
				action  : playbutton.data( 'do' ),
				token   : playbutton.data( 'token' ),
				player  : myCREDPlay.player,
				playid  : playbutton.data( 'id' ),
				itemid  : playbutton.data( 'item' ),
				flavour : playbutton.data( 'flavour' ),
				multi   : playbutton.data( 'multi' )
			};

			mycred_submit_play( newplay, handler, playbutton );

		});

	});

});