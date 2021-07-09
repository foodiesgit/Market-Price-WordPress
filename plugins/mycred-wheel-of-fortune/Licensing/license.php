<?php
define('mycred_fortunewheel_SLUG', 'mycred-wheel-of-fortune');
define('mycred_fortunewheel_VERSION', '1.0');

//license system
add_filter( 'pre_set_site_transient_update_plugins', 'check_for_plugin_update_mycred_fortunewheel' , 80 );
/**
 * Plugin Update Check
 * @since 1.0
 * @version 1.1
 */
function check_for_plugin_update_mycred_fortunewheel( $checked_data ) {

	global $wp_version;

	if ( empty( $checked_data->checked ) )
		return $checked_data;

	$args = array(
		'slug'    => mycred_fortunewheel_SLUG,
		'version' => mycred_fortunewheel_VERSION,
		'site'    => site_url()
	);
	$request_string = array(
		'body'       => array(
			'action'     => 'version', 
			'request'    => serialize( $args ),
			'api-key'    => md5( get_bloginfo( 'url' ) )
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	// Start checking for an update
	$response = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

	if ( ! is_wp_error( $response ) ) {

		$result = maybe_unserialize( $response['body'] );

		if ( is_object( $result ) && ! empty( $result ) )
			$checked_data->response[ mycred_fortunewheel_SLUG . '/' . mycred_fortunewheel_SLUG . '.php' ] = $result;

	}

	return $checked_data;

}

add_filter( 'plugins_api', 'plugin_api_call_mycred_fortunewheel', 80, 3 );

/**
 * Plugin New Version Update
 * @since 1.0
 * @version 1.1
 */
function plugin_api_call_mycred_fortunewheel( $result, $action, $args ) {
  
	global $wp_version;

	if ( ! isset( $args->slug ) || ( $args->slug != mycred_fortunewheel_SLUG ) )
		return $result;

	// Get the current version
	$args = array(
		'slug'    => mycred_fortunewheel_SLUG,
		'version' => mycred_fortunewheel_VERSION,
		'site'    => site_url()
	);
	 
	$request_string = array(
		'body'       => array(
			'action'     => 'info', 
			'request'    => serialize( $args ),
			'api-key'    => md5( get_bloginfo( 'url' ) )
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);
	

	$request = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );
	
	if ( ! is_wp_error( $request ) )
		$result = maybe_unserialize( $request['body'] );

	if ( $result->license_expires != '' )
		update_option( 'mycred-premium-' . mycred_fortunewheel_SLUG . '-expires', $result->license_expires );

	if ( $result->license_renew != '' )
		update_option( 'mycred-premium-' . mycred_fortunewheel_SLUG . '-renew',   $result->license_renew );

	return $result;

}

add_filter( 'plugin_row_meta',                       'plugin_view_info_mycred_fortunewheel' , 80, 3 );

/**
 * Plugin View Info
 * @since 1.1
 * @version 1.0
 */
function plugin_view_info_mycred_fortunewheel( $plugin_meta, $file, $plugin_data ) {

	if ( $file != plugin_basename( mycred_fortunewheel ) ) return $plugin_meta;

	$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
		esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . mycred_fortunewheel_SLUG .
		'&TB_iframe=true&width=600&height=550' ) ),
		esc_attr( __( 'More information about this plugin', mycred_fortunewheel_SLUG ) ),
		esc_attr( 'myCred Wheel of Fortune' ),
		__( 'View details', mycred_fortunewheel_SLUG )
	);

	$url     = str_replace( array( 'https://', 'http://' ), '', get_bloginfo( 'url' ) );
	$expires = get_option( 'mycred-premium-' . mycred_fortunewheel_SLUG . '-expires', '' );
	if(empty($expires)){
		$args = new stdClass;
		$args->slug = mycred_fortunewheel_SLUG;
		$args->version = mycred_fortunewheel_VERSION;
		$args->site = site_url();
		
		$action = '';
		$result = '';   
		plugin_api_call_mycred_fortunewheel( $result, $action, $args );
		
		$expires = get_option( 'mycred-premium-' . mycred_fortunewheel_SLUG . '-expires', '' );
	}
	if ( $expires != '' ) {

		if ( $expires == 'never' )
			$plugin_meta[] = 'Unlimited License';

		elseif ( absint( $expires ) > 0 ) {

			$days = ceil( ( $expires - current_time( 'timestamp' ) ) / DAY_IN_SECONDS );
			if ( $days > 0 )
				$plugin_meta[] = sprintf(
					'License Expires in <strong%s>%s</strong>',
					( ( $days < 30 ) ? ' style="color:red;"' : '' ),
					sprintf( _n( '1 day', '%d days', $days ), $days )
				);

			$renew = get_option( 'mycred-premium-' . mycred_fortunewheel_SLUG . '-renew', '' );
			if ( $days < 30 && $renew != '' )
				$plugin_meta[] = '<a href="' . esc_url( $renew ) . '" target="_blank" class="">Renew License</a>';

		}

	}

	else $plugin_meta[] = '<a href="http://mycred.me/about/terms/#product-licenses" target="_blank">No license found for - ' . $url . '</a>';

	return $plugin_meta;

}