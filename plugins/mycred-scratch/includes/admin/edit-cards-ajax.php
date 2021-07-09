<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * AJAX: Generate Scratch Cards
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_ajax_generate_scratch_cards' ) ) :
	function mycred_ajax_generate_scratch_cards() {

		// Security
		check_ajax_referer( 'mycred-generate-scratch-card-set', 'token' );

		$set_id    = absint( $_POST['setid'] );
		$setup_row = absint( $_POST['setup'] );
		$payout    = sanitize_text_field( $_POST['payout'] );
		$number    = sanitize_text_field( $_POST['number'] );

		$set       = mycred_scratch_card_set( $set_id );
		if ( $set === false )
			wp_send_json_error( 0 );

		if ( ! array_key_exists( $setup_row, $set->setup ) )
			wp_send_json_error( 0 );

		global $wpdb, $scratch_card_set_db;

		$setup_to_run = $set->setup[ $setup_row ];
		$total_needed = absint( $setup_to_run['number'] );

		// First we check how many cards for this row we have already created
		$rows_ready   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$scratch_card_set_db} WHERE set_id = %d AND setup_row = %d", $set_id, $setup_row ) );
		if ( $rows_ready === NULL ) $rows_ready = 0;

		// Rows already exists in db
		if ( $rows_ready > 0 ) {

			// All rows done, no need to continue
			if ( $rows_ready == $total_needed )
				wp_send_json_success( array( 'finishedrow' => true, 'html' => '<span class="dashicons dashicons-yes"></span>' ) );

			// Some rows have been added, lets run the remaining
			else
				$setup_to_run['number'] = $total_needed - $rows_ready;

		}

		// Enforce a maximum to prevent timeouts
		if ( $setup_to_run['number'] > MYCRED_MAX_CARD_CREATION )
			$setup_to_run['number'] = MYCRED_MAX_CARD_CREATION;

		// Prepreare backgrounds
		$backgrounds = $setup_to_run['attachment_ids'];
		$no_of_bgs   = count( $backgrounds );
		$completed   = 0;

		// Insert cards one by one
		for ( $i = 0; $i < $setup_to_run['number']; $i++ ) {

			// Select background
			if ( $no_of_bgs == 1 )
				$bg = $backgrounds[0];

			else
				$bg = $backgrounds[ mt_rand( 0, ( $no_of_bgs - 1 ) ) ];

			$bg_col      = 'attachment_id';
			$bg_col_prep = '%d';

			if ( ! is_numeric( $bg ) ) {
				$bg_col      = 'template';
				$bg_col_prep = '%s';
			}

			$wpdb->insert(
				$scratch_card_set_db,
				array(
					'set_id'    => $set_id,
					'setup_row' => $setup_row,
					$bg_col     => $bg,
					'payout'    => $setup_to_run['value']
				),
				array( '%d', '%d', $bg_col_prep, '%s' )
			);

			$completed++;

		}

		// Indicate to the javascript if this row needs to be run again or if we can continue on to the next one
		$finished = true;
		$html     = '<span class="dashicons dashicons-yes"></span>';
		if ( ( $completed + $rows_ready ) < $total_needed ) {

			$finished   = false;
			$done       = ( $completed + $rows_ready );
			$percentage = ( ( $done / $total_needed ) * 100 );
			$html       = number_format( $percentage, 0 );

			$color      = 'red';
			if ( $percentage > 90 )
				$color = 'green';
			elseif ( $percentage > 50 )
				$color = 'orange';

			$html       = '<div style="font-size: 10px; color: ' . $color . ';">' . $html . ' %</div>';

		}

		wp_send_json_success( array( 'finishedrow' => $finished, 'html' => $html ) );

	}
endif;

/**
 * AJAX: Toggle Logo Usage
 * @since 1.0.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_ajax_toggle_logo_as_bg' ) ) :
	function mycred_ajax_toggle_logo_as_bg() {

		// Security
		check_ajax_referer( 'mycred-toggle-scratch-card-logo-bg', 'token' );

		$set_id    = absint( $_POST['setid'] );
		$selection = absint( $_POST['logo'] );

		update_post_meta( $set_id, 'bg_logo', $selection );
		wp_send_json_success();

	}
endif;
