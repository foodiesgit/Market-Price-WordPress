<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Play API: JSON Handler
 * Convers an array of arguments into a JSON object to be used in ajax calls.
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_play_json' ) ) :
	function mycred_play_json( $_args = array() ) {

		$args          = wp_parse_args( $_args, array(
			'id'         => 0,
			'empty'      => 0,
			'element'    => '',
			'html'       => '',
			'field'      => '',
			'newlabel'   => '',
			'newbalance' => '',
			'decimals'   => 0,
			'message'    => '',
			'multi'      => 0
		) );

		$args['id']    = absint( $args['id'] );
		$args['empty'] = absint( $args['empty'] );
		$args['multi'] = absint( $args['multi'] );

		$args = apply_filters( 'mycred_play_api_json', $args, $_args );

		wp_send_json( $args );

	}
endif;

/**
 * Play API: Get Flavour
 * Converts generic names into the supported flavours by the API.
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_play_get_flavour' ) ) :
	function mycred_play_get_flavour( $theme = '' ) {

		if ( ! in_array( $theme, array( 'grey', 'blue', 'green', 'gold', 'red', 'purple', 'plain', 'inverse' ) ) )
			$theme = 'plain';

		$flavour = $theme;
		if ( $theme == 'blue' )
			$flavour = 'primary';

		elseif ( $theme == 'green' )
			$flavour = 'action';

		elseif ( $theme == 'gold' )
			$flavour = 'highlight';

		elseif ( $theme == 'red' )
			$flavour = 'caution';

		elseif ( $theme == 'purple' )
			$flavour = 'royal';

		return apply_filters( 'mycred_api_get_flavour', $flavour, $theme );

	}
endif;

/**
 * Play API: Play Button
 * Generates a play button which triggers the Play API.
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_play_button' ) ) :
	function mycred_play_button( $id = '', $label = '', $args = array(), $block_button = false, $button_size = 'large' ) {

		$id    = sanitize_text_field( $id );
		$label = do_shortcode( $label );
		$args  = wp_parse_args( $args, array(
			'id'      => 0,
			'do'      => 0,
			'item'    => 0,
			'token'   => '',
			'flavour' => 'plain',
			'multi'   => 0
		) );

		$args['id']      = absint( $args['id'] );
		$args['do']      = sanitize_text_field( $args['do'] );
		$args['item']    = absint( $args['item'] );
		$args['token']   = sanitize_text_field( $args['token'] );
		$args['multi']   = absint( $args['multi'] );
		$args['flavour'] = sanitize_text_field( $args['flavour'] );

		$button_classes  = array( 'button', 'button-box', 'button-' . $args['flavour'] );

		if ( $block_button === true )
			$button_classes[] = 'button-block';

		if ( $button_size != '' && in_array( $button_size, array( 'tiny', 'small', 'large', 'jumbo', 'giant' ) ) )
			$button_classes[] = 'button-' . $button_size;

		if ( strlen( $id ) > 0 )
			$id = 'id="' . $id . '"';

		$button = '<button type="button" class="' . implode( ' ', apply_filters( 'mycred_play_api_button_classes', $button_classes, $args ) ) . '" ' . $id;

		foreach ( $args as $data => $value )
			$button .= ' data-' . $data . '="' . $value . '"';

		$button .= '>' . $label . '</button>';

		return apply_filters( 'mycred_play_api_button', $button, $id, $label, $args, $block_button, $button_size );

	}
endif;

/**
 * Play API: Check Request
 * Checks a request to be valid.
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_play_check_request' ) ) :
	function mycred_play_check_request( $id = '' ) {

		check_ajax_referer( $id, 'token' );

		// Minimum requirements
		if ( ! isset( $_POST['player'] ) || absint( $_POST['player'] ) === 0 )
			die(0);

		// This should never occur but just in case
		if ( ! is_user_logged_in() )
			die(0);

		// Check for spam
		if ( function_exists( 'mycred_force_singular_session' ) && mycred_force_singular_session( $_POST['player'], $id ) )
			die(0);

		do_action( 'mycred_play_api_check_request' );

	}
endif;

/**
 * Play API: Get Request
 * Converts POST items into an sanitized array.
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_play_get_request' ) ) :
	function mycred_play_get_request() {

		$args = array();
		foreach ( $_POST as $key => $value ) {

			if ( in_array( $key, array( 'action', 'token' ) ) ) continue;

			if ( in_array( $key, array( 'playid', 'item', 'user_id', 'multi' ) ) )
				$value = absint( $value );

			elseif ( is_string( $value ) )
				$value = sanitize_text_field( $value );

			if ( $key === 'player' )
				$key = 'user_id';

			$args[ $key ] = $value;

		}

		return apply_filters( 'mycred_play_api_get_request', $args );

	}
endif;

/**
 * Play API: Enqueue All
 * Enqueues all script and styles. Should be used to enqueue scripts and styles for the front-end
 * playfield.
 * @since 1.1
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_play_enqueue_all' ) ) :
	function mycred_play_enqueue_all( $args = array() ) {

		global $post;

		$args = shortcode_atts( array(
			'app'    => esc_url( isset( $post->ID ) ? get_permalink( $post->ID ) : home_url( '/' ) ),
			'player' => get_current_user_id(),
			'delay'  => 0
		), $args );

		wp_enqueue_style( 'mycred-play' );

		wp_localize_script(
			'mycred-play',
			'myCREDPlay',
			$args
		);

		wp_enqueue_script( 'mycred-play' );

	}
endif;

/**
 * Play API: Register Assets
 * Registers all front end scripts and styles used by the play API.
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_play_register_assets' ) ) :
	function mycred_play_register_assets() {

		// Styles
		wp_register_style( 'ladda-themeless',   plugins_url( 'assets/libs/ladda/ladda-themeless.min.css', MYCRED_SCRATCH ), array(), MYCRED_SCRATCH_VERSION, 'all' );
		wp_register_style( 'mycred-play',       plugins_url( 'assets/libs/play-api/mycred-play.css', MYCRED_SCRATCH ),      array( 'ladda-themeless' ), MYCRED_SCRATCH_VERSION . '.1', 'all' );

		// Scripts
		wp_register_script( 'jquery-numerator', plugins_url( 'assets/libs/jquery-numerator.js', MYCRED_SCRATCH ),           array( 'jquery' ), '0.2.1' );
		wp_register_script( 'ladda-spin',       plugins_url( 'assets/libs/ladda/spin.min.js', MYCRED_SCRATCH ),             array(), '1.0.0' );
		wp_register_script( 'ladda',            plugins_url( 'assets/libs/ladda/ladda.min.js', MYCRED_SCRATCH ),            array( 'ladda-spin' ), '1.0.0' );
		wp_register_script( 'mycred-play',      plugins_url( 'assets/libs/play-api/mycred-play.js', MYCRED_SCRATCH ),       array( 'jquery-numerator', 'ladda' ), MYCRED_SCRATCH_VERSION, true );

	}
endif;
add_action( 'init', 'mycred_play_register_assets' );
