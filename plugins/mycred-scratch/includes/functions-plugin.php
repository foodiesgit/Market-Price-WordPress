<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Get Settings
 * @since 1.0
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_scratch_settings' ) ) :
	function mycred_scratch_settings() {

		$default = array(
			'disabled_random'       => 0,
			'load_delay'            => 0,
			'force_ssl'             => 0,
			'exclude_roles'         => array(),
			'exclude_ids'           => array(),
			'template_visitors'     => 'Please login to play this game',
			'template_onhold'       => 'This scratch card is currently unavailable',
			'template_soldout'      => 'This scratch card has sold out',
			'template_excluded'     => 'I am sorry but you can not play this game',
			'template_insufficient' => 'Insufficient funds'
		);

		$settings = get_option( 'mycred_scratch_card_prefs', $default );

		return wp_parse_args( $settings, $default );

	}
endif;

/**
 * Sanitize Settings
 * @since 1.0
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_sanitize_scratch_settings' ) ) :
	function mycred_sanitize_scratch_settings( $data ) {

		global $wp_roles;

		$new                    = array();
		$new['disabled_random'] = ( isset( $data['disabled_random'] ) ) ? 1 : 0;
		$new['load_delay']      = absint( $data['load_delay'] );
		$new['force_ssl']       = ( isset( $data['force_ssl'] ) ) ? 1 : 0;

		$exclude_roles          = array();
		$available_roles        = $wp_roles->get_names();

		if ( isset( $data['exclude_roles'] ) && is_array( $data['exclude_roles'] ) && ! empty( $data['exclude_roles'] ) ) {
			foreach ( $data['exclude_roles'] as $role ) {

				$role = sanitize_key( $role );
				if ( $role != '' && array_key_exists( $role, $available_roles ) )
					$exclude_roles[] = $role;

			}
		}
		$new['exclude_roles']   = $exclude_roles;

		$exclude_ids = array();
		if ( ! empty( $data['exclude_ids'] ) ) {

			if ( ! is_array( $data['exclude_ids'] ) )
				$ids = explode( ',', $data['exclude_ids'] );
			else
				$ids = $data['exclude_ids'];

			foreach ( $ids as $user_id ) {

				$user_id = absint( sanitize_text_field( $user_id ) );
				if ( $user_id > 0 )
					$exclude_ids[] = $user_id;

			}

		}
		$new['exclude_ids'] = $exclude_ids;

		$new['template_visitors']     = wp_kses_post( $data['template_visitors'] );
		$new['template_excluded']     = wp_kses_post( $data['template_excluded'] );
		$new['template_insufficient'] = wp_kses_post( $data['template_insufficient'] );
		$new['template_soldout']      = wp_kses_post( $data['template_soldout'] );
		$new['template_onhold']       = wp_kses_post( $data['template_onhold'] );

		$new['template_visitors']     = balancetags( $new['template_visitors'] );
		$new['template_excluded']     = balancetags( $new['template_excluded'] );
		$new['template_insufficient'] = balancetags( $new['template_insufficient'] );
		$new['template_soldout']      = balancetags( $new['template_soldout'] );
		$new['template_onhold']       = balancetags( $new['template_onhold'] );

		return $new;

	}
endif;

/**
 * Select Point Type from Select Dropdown
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_point_type_dropdown' ) ) :
	function mycred_scratch_point_type_dropdown( $name = '', $id = '', $selected = '', $return = false ) {

		$types  = mycred_get_types();
		$output = '';

		if ( count( $types ) == 1 ) {

			$mycred = mycred();

			$output .= '<input type="hidden" name="' . $name . '" id="' . $id . '" value="mycred_default" /><p class="static-form-control">' . $mycred->plural() . '</p>';

		}

		else {

			$output .= '<select name="' . $name . '" id="' . $id . '">';

			foreach ( $types as $type => $label ) {

				if ( $type == 'mycred_default' ) {
					$_mycred = mycred( $type );
					$label   = $_mycred->plural();
				}

				$output .= '<option value="' . $type . '"';
				if ( $selected == $type ) $output .= ' selected="selected"';
				$output .= '>' . $label . '</option>';

			}

			$output .= '</select>';

		}

		if ( $return )
			return $output;

		echo $output;
	}
endif;

/**
 * Install DB

	id                  unique id
	set_id              card set post type id 
	user_id             user id, set once a user buys the card 
	template            if a template is used for bg, the template name
	attachment_id       if an attachment is used for bg, the attachment file id
	payout              the payout amount

 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_install_db' ) ) :
	function mycred_scratch_install_db() {

		if ( get_option( 'mycred_scratch_card_db', false ) != '1.0.1' ) {

			global $wpdb;

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$wpdb->hide_errors();

			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				if ( ! empty( $wpdb->charset ) )
					$collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";
				if ( ! empty( $wpdb->collate ) )
					$collate .= " COLLATE {$wpdb->collate}";
			}

			// Log structure
			$sql = "
				id                  INT(11) NOT NULL AUTO_INCREMENT, 
				set_id              INT(11) DEFAULT 0, 
				setup_row           INT(11) DEFAULT 0, 
				user_id             INT(11) DEFAULT 0, 
				template            LONGTEXT DEFAULT '', 
				attachment_id       INT(11) DEFAULT 0, 
				payout              LONGTEXT DEFAULT '',
				PRIMARY KEY  (id), 
				UNIQUE KEY id (id)"; 

			// Insert table
			$table = $wpdb->prefix . 'scratch_cards';
			dbDelta( "CREATE TABLE IF NOT EXISTS {$table} ( " . $sql . " ) $collate;" );

			update_option( 'mycred_scratch_card_db', '1.0.1' );

		}

	}
endif;

/**
 * Get Users Role
 * Returns an array with the key and label for the given users
 * role. Returns false if user has no role or if the user id is missing.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_get_users_role' ) ) :
	function mycred_scratch_get_users_role( $user_id = NULL ) {

		if ( $user_id === NULL ) return false;

		$user = new WP_User( $user_id );
		if ( ! is_array( $user->roles ) ) return false;

		$wp_roles = new WP_Roles;
		$names    = $wp_roles->get_names();
		$out      = array();

		foreach ( $user->roles as $role ) {
			if ( isset( $names[ $role ] ) )
				$out[ $role ] = $names[ $role ];
		}

		return $out;

	}
endif;

/**
 * Prep Entries for Export
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_prep_entries_for_export' ) ) :
	function mycred_scratch_prep_entries_for_export( $data = NULL, $headers = array() ) {

		$results = array();
		if ( $data === NULL || ! is_array( $data ) || empty( $data ) || empty( $headers ) ) return $results;

		foreach ( $data as $entry ) {

			$row  = array();
			$user = get_userdata( $entry->user_id );
			foreach ( $headers as $header_id ) {

				$content = '';

				if ( $header_id == 'EntryID' ) {
					$content = $entry->id;
				}

				elseif ( $header_id == 'UserID' ) {
					$content = $entry->user_id;
				}

				elseif ( $header_id == 'UserEmail' ) {
					$content = 'n/a';
					if ( isset( $user->user_email ) )
						$content = $user->user_email;
				}

				elseif ( $header_id == 'Date' ) {
					$content = date( 'Y-m-d', $entry->time );
				}

				elseif ( $header_id == 'PointType' ) {
					$content = mycred_get_point_type_name( $entry->ctype, false );
				}

				elseif ( $header_id == 'Payment' || $header_id == 'Payout' ) {
					$content = abs( $entry->creds );
				}

				elseif ( $header_id == 'CardSet' ) {
					$content = get_the_title( $entry->ref_id );
					if ( $content == '' )
						$content = 'n/a';
				}

				elseif ( $header_id == 'SetID' ) {
					$content = abs( $entry->ref_id );
				}

				else {
					$content = apply_filters( 'mycred-scratch-export-column-' . $header_id, $content, $entry );
				}

				$row[ $header_id ] = $content;

			}
			$results[] = $row;

		}

		return $results;

	}
endif;
