<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Purchases Admin Menu
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_admin_menu_scratch_purchases' ) ) :
	function mycred_admin_menu_scratch_purchases() {

		if ( myred_get_available_set_count() == 0 ) return;

		$pages   = array();

		$pages[] = add_submenu_page(
			'edit.php?post_type=scratch_card_set',
			__( 'Purchase Log', 'mycred' ),
			__( 'Purchase Log', 'mycred' ),
			'edit_users',
			'scratch-cards-purchases',
			'mycred_scratch_purchases_admin_screen'
		);

		foreach ( $pages as $page ) {
			add_action( 'admin_print_styles-' . $page, 'mycred_scratch_purchases_admin_styles' );
			add_action( 'load-' . $page,               'mycred_scratch_purchases_admin_load' );
		}

	}
endif;

/**
 * Purchases Admin Styles
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_purchases_admin_styles' ) ) :
	function mycred_scratch_purchases_admin_styles() {

?>
<style type="text/css">
th#cardbuyer { width: 30%; }
th#cardset { width: 30%; }
th#time { width: 25%; }
th#payment { width: auto; }
</style>
<?php

	}
endif;

/**
 * Purchases Admin Load
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_purchases_admin_load' ) ) :
	function mycred_scratch_purchases_admin_load() {

		// Handle entries per page updates
		if ( isset( $_REQUEST['wp_screen_options']['option'] ) && isset( $_REQUEST['wp_screen_options']['value'] ) ) {
			
			if ( $_REQUEST['wp_screen_options']['option'] == 'mycred-scratch-payouts' ) {
				$value = absint( $_REQUEST['wp_screen_options']['value'] );
				mycred_update_user_meta( get_current_user_id(), 'mycred-scratch-purchases', '', $value );
			}

		}

		// Handle bulk actions
		if ( isset( $_GET['bulkaction'] ) ) {

			// Delete log entries
			if ( $_GET['bulkaction'] == 'delete' && isset( $_GET['entry'] ) ) {

				global $wpdb, $mycred;

				$deleted   = 0;
				$purchases = $_GET['entry'];
				if ( ! empty( $purchases ) ) {
					foreach ( $purchases as $entry_id ) {

						$wpdb->delete(
							$mycred->log_table,
							array( 'id' => $entry_id ),
							array( '%d' )
						);

						$deleted ++;

					}
				}

				$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'page' => 'scratch-cards-purchases', 'deleted' => $deleted ), admin_url( 'edit.php' ) );
				wp_safe_redirect( $url );
				exit;

			}

			// Export log entries
			elseif ( $_GET['bulkaction'] == 'export' && isset( $_GET['entry'] ) ) {

				$search_args             = ( function_exists( 'mycred_get_search_args' ) ? mycred_get_search_args( array( 'page', 'bulkaction', 'point_type', 'entry' ) ) : array() );

				$entry_ids = array();
				foreach ( $_GET['entry'] as $id ) {
					$id = trim( $id );
					if ( $id == '' || absint( $id ) == 0 ) continue;
					$entry_ids[] = $id;
				}

				$search_args['entry_id'] = array( 'ids' => $entry_ids, 'compare' => 'IN' );

				$search_args['ref']      = 'buy_scratch_card,free_scratch_card';
				$search_args['number']   = -1;

				$export_file_name        = 'mycred-scratch';
				if ( array_key_exists( 'ref_id', $search_args ) )
					$export_file_name .= '-' . get_the_title( $search_args['ref_id'] );

				$export_file_name       .= '-purchases.csv';
				$export_file_name        = strtolower( str_replace( ' ', '-', $export_file_name ) );
				$export_file_name        = apply_filters( 'mycred_scratch_export_file', $export_file_name, $search_args );

				$export_headers          = array( 'EntryID', 'UserID', 'UserEmail', 'Date', 'CardSet', 'SetID', 'PointType', 'Payment' );
				$export_headers          = apply_filters( 'mycred_scratch_export_columns', $export_headers, $search_args );

				$export                  = new myCRED_Query_Log( $search_args );

				if ( $export->num_rows > 0 ) {

					// Load parseCSV
					if ( ! class_exists( 'parseCSV' ) )
						require_once myCRED_ASSETS_DIR . 'libs/parsecsv.lib.php';

					$export_data = mycred_scratch_prep_entries_for_export( $export->results, $export_headers );

					$csv         = new parseCSV();
					$csv->output( true, $export_file_name, $export_data, $export_headers );

					exit;

				}

			}

		}

		// Delete specific entry
		elseif ( isset( $_GET['delete'] ) ) {

			$entry_id = absint( $_GET['delete'] );
			if ( $entry_id > 0 ) {

				global $wpdb, $mycred;

				$wpdb->delete(
					$mycred->log_table,
					array( 'id' => $entry_id ),
					array( '%d' )
				);

				$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'page' => 'scratch-cards-purchases', 'deleted' => 1 ), admin_url( 'edit.php' ) );
				wp_safe_redirect( $url );
				exit;

			}

		}

		$args = array(
			'label'   => __( 'Entries', 'mycred' ),
			'default' => 10,
			'option'  => 'mycred-scratch-purchases'
		);
		add_screen_option( 'per_page', $args );

	}
endif;

/**
 * Purchases Admin Screen
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_purchases_admin_screen' ) ) :
	function mycred_scratch_purchases_admin_screen() {

		if ( ! current_user_can( 'edit_users' ) ) wp_die( 'Access Denied' );

		$screen             = get_current_screen();
		$search_args        = ( function_exists( 'mycred_get_search_args' ) ? mycred_get_search_args( array( 'page', 'bulkaction', 'point_type', 'entry' ) ) : array() );

		$per_page           = mycred_get_user_meta( get_current_user_id(), 'mycred-scratch-purchases', '', true );
		if ( $per_page == '' ) $per_page = 10;

		$search_args['ref'] = 'buy_scratch_card,free_scratch_card';

		// Entries per page
		if ( ! array_key_exists( 'number', $search_args ) )
			$search_args['number'] = absint( $per_page );

		$log                = new myCRED_Query_Log( $search_args );
	
		$log->is_admin      = true;
		$log->headers       = array(
			'cb'        => '',
			'cardbuyer' => __( 'User', 'mycred' ),
			'time'      => __( 'Date', 'mycred' ),
			'cardset'   => __( 'Card Set', 'mycred' ),
			'payment'   => __( 'Payment', 'mycred' )
		);

		add_filter( 'mycred_log_cardbuyer', 'mycred_scratch_log_column_cardbuyer', 10, 2 );
		add_filter( 'mycred_log_cardset', 'mycred_scratch_log_column_cardset', 10, 2 );
		add_filter( 'mycred_log_payment', 'mycred_scratch_log_column_payment', 10, 2 );

?>
<div class="wrap">
	<h1><?php _e( 'Purchase Log', 'mycred' ); ?></h1>
<?php

		// Indicate log entry deletions
		if ( isset( $_GET['deleted'] ) ) {

			if ( $_GET['deleted'] == 0 )
				echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'No entries were deleted', 'mycred' ) . '</p></div>';
			else
				echo '<div id="message" class="notice notice-success is-dismissible"><p>' . sprintf( _n( '1 entry deleted', '%d entries deleted', $_GET['deleted'], 'mycred' ), $_GET['deleted'] ) . '</p></div>';

		}

		// Filter by dates
		$log->filter_dates( admin_url( 'edit.php?post_type=scratch_card_set&page=scratch-cards-purchases' ) );

?>

	<form method="get" action="">
		<input type="hidden" name="post_type" value="scratch_card_set" />
		<input type="hidden" name="page" value="scratch-cards-purchases" />
		<input type="hidden" name="ref" value="buy_scratch_card,free_scratch_card" />
<?php

		if ( array_key_exists( 'user', $search_args ) )
			echo '<input type="hidden" name="user" value="' . esc_attr( $search_args['user'] ) . '" />';

		if ( array_key_exists( 's', $search_args ) )
			echo '<input type="hidden" name="s" value="' . esc_attr( $search_args['s'] ) . '" />';

		if ( isset( $_GET['ref'] ) )
			echo '<input type="hidden" name="show" value="' . esc_attr( $_GET['ref'] ) . '" />';

		if ( isset( $_GET['show'] ) )
			echo '<input type="hidden" name="show" value="' . esc_attr( $_GET['show'] ) . '" />';

		if ( array_key_exists( 'order', $search_args ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $search_args['order'] ) . '" />';

		if ( array_key_exists( 'paged', $search_args ) )
			echo '<input type="hidden" name="paged" value="' . esc_attr( $search_args['paged'] ) . '" />';

		if ( $log->num_rows > 0 ) {

			$log->search();

			$bulk_actions = array(
				'-1'     => __( 'Bulk Actions', 'mycred' ),
				'delete' => __( 'Delete', 'mycred' ),
				'export' => __( 'Export', 'mycred' )
			);

?>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select name="bulkaction" id="bulk-action-selector-top">
<?php

			foreach ( $bulk_actions as $action_id => $label )
				echo '<option value="' . $action_id . '">' . $label . '</option>';

?>
				</select>
				<input type="submit" class="button action" id="doaction" value="<?php _e( 'Apply', 'mycred' ); ?>" />
			</div>
<?php

			if ( myred_get_available_set_count() > 0 ) :

				$card_sets = mycred_get_available_card_sets();

?>
			<div class="alignleft actions">
				<select name="ref_id" id="card-sets-top">
					<option value=""><?php _e( 'Filter by Card Set', 'mycred' ); ?></option>
<?php

				foreach ( $card_sets as $set_id => $set )
					echo '<option value="' . $set_id . '">' . $set->get_title() . '</option>';

?>
				</select>
<?php

				echo '<select name="order" id="myCRED-order-filter"><option value="">' . __( 'Show in order', 'mycred' ) . '</option>';
				foreach ( array( 'ASC' => __( 'Ascending', 'mycred' ), 'DESC' => __( 'Descending', 'mycred' ) ) as $value => $label ) {

					echo '<option value="' . $value . '"';
					if ( ! isset( $_GET['order'] ) && $value == 'DESC' ) echo ' selected="selected"';
					elseif ( isset( $_GET['order'] ) && $_GET['order'] == $value ) echo ' selected="selected"';
					echo '>' . $label . '</option>';

				}
				echo '</select>';

?>
				<input type="text" class="form-control" name="user" id="myCRED-user-filter" size="22" placeholder="<?php _e( 'User ID, Username, Email or Nicename', 'mycred' ); ?>" value="<?php echo ( ( isset( $_GET['user'] ) ) ? esc_attr( $_GET['user'] ) : '' ); ?>" />
				<input type="submit" class="button action" id="doaction" value="<?php _e( 'Filter', 'mycred' ); ?>" />
			</div>
<?php

			endif;

			$log->navigation( 'top' );

?>
		</div>
<?php

		}

?>

		<?php $log->display(); ?>

		<div class="tablenav bottom">

			<?php $log->table_nav( 'bottom', false ); ?>

		</div>

	</form>
</div>
<?php

	}
endif;

/**
 * Log Column: Card Buyer
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_log_column_cardbuyer' ) ) :
	function mycred_scratch_log_column_cardbuyer( $content, $log_entry ) {

		$username = sprintf( __( 'Deleted User. ID: %s', 'mycred' ), $log_entry->user_id );
		$user     = get_userdata( $log_entry->user_id );
		if ( isset( $user->ID ) )
			$username = $user->display_name;

		$username = '<strong>' . $username . '</strong>';

		

		$actions   = array();
		$actions[] = '<span class="user"><a href="' . admin_url( 'edit.php?post_type=scratch_card_set&page=scratch-cards-purchases&user=' . $log_entry->user_id ) . '">' . __( 'Filter by User', 'mycred' ) . '</a></span>';
		$actions[] = '<span class="delete"><a href="' . admin_url( 'edit.php?post_type=scratch_card_set&page=scratch-cards-purchases&delete=' . $log_entry->id ) . '">' . __( 'Delete', 'mycred' ) . '</a></span>';

		$actions   = apply_filters( 'mycred-scratch-payout-log-actions', $actions, $log_entry );

		if ( ! empty( $actions ) ) {

			$row_actions = '<div class="row-actions">' . implode( ' | ', $actions ) . '</div>';
			$username   .= $row_actions;

		}

		return $username;

	}
endif;

/**
 * Log Column: Card Set
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_log_column_cardset' ) ) :
	function mycred_scratch_log_column_cardset( $content, $log_entry ) {

		$set_id    = $log_entry->ref_id;
		$set_title = get_the_title( $set_id );
		if ( $set_title == '' ) $set_title = sprintf( __( 'Set Deleted. Set ID: %s', 'mycred' ), $set_id );

		return $set_title;

	}
endif;

/**
 * Log Column: Payment
 * @since 2.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_log_column_payment' ) ) :
	function mycred_scratch_log_column_payment( $content, $log_entry ) {

		$payment = __( 'Free', 'mycred' );
		if ( $log_entry->ref == 'buy_scratch_card' )
			$payment = sprintf( '%s %s', abs( $log_entry->creds ), mycred_get_point_type_name( $log_entry->ctype, ( $log_entry->creds == 1 ? false : true ) ) );

		return $payment;

	}
endif;
