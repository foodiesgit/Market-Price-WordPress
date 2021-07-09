<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Settings Admin Menu
 * @since 1.0
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_admin_menu_scratch_settings' ) ) :
	function mycred_admin_menu_scratch_settings() {

		$pages = array();

		$pages[] = add_submenu_page(
			'edit.php?post_type=scratch_card_set',
			__( 'Settings', 'mycred' ),
			__( 'Settings', 'mycred' ),
			'edit_users',
			'scratch-cards-settings',
			'mycred_scratch_settings_admin_screen'
		);

		foreach ( $pages as $page ) {
			add_action( 'admin_print_styles-' . $page, 'mycred_scratch_settings_admin_styles' );
			add_action( 'load-' . $page,               'mycred_scratch_settings_admin_load' );
		}

	}
endif;

/**
 * Settings Admin Styles
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_settings_admin_styles' ) ) :
	function mycred_scratch_settings_admin_styles() {



	}
endif;

/**
 * Settings Admin Load
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_settings_admin_load' ) ) :
	function mycred_scratch_settings_admin_load() {



	}
endif;

/**
 * Settings Admin Screen
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_settings_admin_screen' ) ) :
	function mycred_scratch_settings_admin_screen() {

		if ( ! current_user_can( 'edit_users' ) ) wp_die( 'Access Denied' );

		global $wp_roles;

		$prefs = mycred_scratch_settings();

?>
<div class="wrap">
	<h1><?php _e( 'Settings', 'mycred' ); ?> <a href="http://codex.mycred.me/chapter-iv/games/scratch-cards-add-on/" target="_blank" class="page-title-action"><?php _e( 'Documentation', 'mycred' ); ?></a></h1>
<?php

		if ( isset( $_GET['settings-updated'] ) )
			echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Settings updated.', 'mycred' ) . '</p></div>';

?>
	<p></p>
	<form method="post" action="options.php">

		<?php settings_fields( 'mycred-scratch-card' ) ?>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">MySQL RAND()</th>
					<td>
						<label for="mycred-scratch-card-prefs-disable-rand"><input type="checkbox" name="mycred_scratch_card_prefs[disabled_random]" id="mycred-scratch-card-prefs-disable-rand" <?php checked( $prefs['disabled_random'], 1 ); ?> value="1" /> <?php _e( 'The MySQL RAND() function has been disabled on this site. Use alternative random script.', 'mycred' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="mycred-scratch-card-prefs-load"><?php _e( 'Load Delay', 'mycred' ); ?></label></th>
					<td>
						<input type="text" name="mycred_scratch_card_prefs[load_delay]" id="mycred-scratch-card-prefs-load" value="<?php echo absint( $prefs['load_delay'] ); ?>" /> second 
						<p><span class="description"><?php _e( 'Option to delay the play field loading a new card once the user finished scratching their current card. Use zero for no delay. ', 'mycred' ); ?></span></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Force SSL', 'mycred' ); ?></th>
					<td>
						<label for="mycred-scratch-card-prefs-force-ssl"><input type="checkbox" name="mycred_scratch_card_prefs[force_ssl]" id="mycred-scratch-card-prefs-force-ssl" <?php checked( $prefs['force_ssl'], 1 ); ?> value="1" /> <?php _e( 'Force all card images to be loaded over https instead of http. Enable this if you recently enabled SSL on your website and you have available scratch cards.', 'mycred' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Excludes', 'mycred' ); ?></th>
					<td>
						<p><strong><?php _e( 'Exclude by Role', 'mycred' ); ?></strong></p>
						<p><span class="description"><?php _e( 'Check all roles that are not allowed to play scratch cards.', 'mycred' ); ?></span></p>
						<ul>
<?php

		$roles = $wp_roles->get_names();
		foreach ( $roles as $role_id => $role_name ) {

			$checked = '';
			if ( in_array( $role_id, $prefs['exclude_roles'] ) )
				$checked = 'checked="checked"';

			echo '<li><label for=""><input type="checkbox" name="mycred_scratch_card_prefs[exclude_roles][]" id="mycred-scratch-card-prefs-exclude-roles" ' . $checked . ' value="' . $role_id . '" /> ' . esc_attr( $role_name ) . '</label></li>';

		}

?>
						</ul>
						<p>&nbsp;</p>
						<p><strong><?php _e( 'Exclude by ID', 'mycred' ); ?></strong></p>
						<p><span class="description"><?php _e( 'Comma separated list of user IDs to exclude from playing scratch cards.', 'mycred' ); ?></span></p>
						<input type="text" class="code ltr" size="80" name="mycred_scratch_card_prefs[exclude_ids]" id="mycred-scratch-card-prefs-exclude-ids" value="<?php echo implode( ',', $prefs['exclude_ids'] ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<p>&nbsp;</p>
		<h3><?php _e( 'Shortcode Templates', 'mycred' ); ?></h3>
		<p><?php _e( 'The following templates will be used when users for one reason or another, can not play.', 'mycred' ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e( 'Visitors', 'mycred' ); ?></th>
					<td>
						<p><span class="description"><?php _e( 'Shown when someone views any shortcode and are not logged in.', 'mycred' ); ?></span></p>
						<?php wp_editor( $prefs['template_visitors'], 'mycredscratchcardtemplatevisitors', array( 'textarea_rows' => 15, 'textarea_name' => 'mycred_scratch_card_prefs[template_visitors]' ) ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Excluded', 'mycred' ); ?></th>
					<td>
						<p><span class="description"><?php _e( 'Shown when a users is excluded form playing.', 'mycred' ); ?></span></p>
						<?php wp_editor( $prefs['template_excluded'], 'mycredscratchcardtemplateexcluded', array( 'textarea_rows' => 15, 'textarea_name' => 'mycred_scratch_card_prefs[template_excluded]' ) ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Insufficient Funds', 'mycred' ); ?></th>
					<td>
						<p><span class="description"><?php _e( 'Shown when a users can not afford to buy a new card.', 'mycred' ); ?></span></p>
						<?php wp_editor( $prefs['template_insufficient'], 'mycredscratchcardtemplateinsifficent', array( 'textarea_rows' => 15, 'textarea_name' => 'mycred_scratch_card_prefs[template_insufficient]' ) ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Sold Out', 'mycred' ); ?></th>
					<td>
						<p><span class="description"><?php _e( 'Shown when a card set is sold out.', 'mycred' ); ?></span></p>
						<?php wp_editor( $prefs['template_soldout'], 'mycredscratchcardtemplatesoldout', array( 'textarea_rows' => 15, 'textarea_name' => 'mycred_scratch_card_prefs[template_soldout]' ) ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'On Hold', 'mycred' ); ?></th>
					<td>
						<p><span class="description"><?php _e( 'Shown when a card set is put on hold.', 'mycred' ); ?></span></p>
						<?php wp_editor( $prefs['template_onhold'], 'mycredscratchcardtemplateonhold', array( 'textarea_rows' => 15, 'textarea_name' => 'mycred_scratch_card_prefs[template_onhold]' ) ); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<?php submit_button( __( 'Save Changes', 'mycred' ), 'primary', 'save-changes', true ); ?>

	</form>
</div>
<?php

	}
endif;
