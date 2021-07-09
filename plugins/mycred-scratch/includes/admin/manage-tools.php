<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Tools Admin Menu
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_admin_menu_scratch_tools' ) ) :
	function mycred_admin_menu_scratch_tools() {

		$pages = array();

		$pages[] = add_submenu_page(
			'edit.php?post_type=scratch_card_set',
			__( 'Tools', 'mycred' ),
			__( 'Tools', 'mycred' ),
			'edit_users',
			'scratch-cards-tools',
			'mycred_scratch_tools_admin_screen'
		);

		foreach ( $pages as $page ) {
			add_action( 'admin_print_styles-' . $page, 'mycred_scratch_tools_admin_styles' );
			add_action( 'load-' . $page,               'mycred_scratch_tools_admin_load' );
		}

	}
endif;

/**
 * Tools Admin Styles
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_tools_admin_styles' ) ) :
	function mycred_scratch_tools_admin_styles() {



	}
endif;

/**
 * Tools Admin Load
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_tools_admin_load' ) ) :
	function mycred_scratch_tools_admin_load() {



	}
endif;

/**
 * Tools Admin Screen
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_tools_admin_screen' ) ) :
	function mycred_scratch_tools_admin_screen() {

		if ( ! current_user_can( 'edit_users' ) ) wp_die( 'Access Denied' );

		global $wp_roles;

		$prefs = mycred_scratch_settings();

?>
<div class="wrap">
	<h1><?php _e( 'Tools', 'mycred' ); ?></h1>
	<h3>Changed Domain Name</h3>
	<p>This tool allows you to sync the card URLs in your database with your websites URL. Use this when you change your websites domain name and if you have cards made from templates. Cards that uses custom images that you uploaded to your website will always show the correct URL.</p>
	<p><button type="button" id="mycred-scratch-update-urls" class="button button-secondary">Run Tool</button></p>
	<p>&nbsp;</p>
	<p><?php echo plugins_url( '/', MYCRED_SCRATCH ); ?>
	<h3>Delete Everything</h3>
	<p>This tool allows you to delete all card sets and scratch cards in your database. This can not be undone!</p>
	<p><button type="button" id="mycred-scratch-delete-everything" class="button button-secondary">Run Tool</button></p>
</div>
<?php

	}
endif;
