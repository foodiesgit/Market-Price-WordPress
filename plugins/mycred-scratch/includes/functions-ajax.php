<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * AJAX: Load Field
 * @since 1.0
 * @version 1.2
 */
if ( ! function_exists( 'mycred_scratch_ajax_load_field' ) ) :
	function mycred_scratch_ajax_load_field( $set = false, $return = false ) {

		extract( mycred_play_get_request() );

		$buttons     = array();
		$set_id      = absint( $playid );

		if ( ! isset( $set->set_id ) ) {

			$card_sets = mycred_get_available_card_sets( $user_id );

			// Make sure card set is playable (checks card set status, checks for unplayed cards if sold out, checks for daily limits and if user is excluded)
			$set       = ( array_key_exists( $set_id, $card_sets ) ? $card_sets[ $set_id ] : false );

		}

		mycred_ajax_check_card_set_playable( $user_id, $set );

		/*
		mycred_play_json( array(
			'id'       => $set_id,
			'empty'    => 1,
			'message'  => 'Debug',
			'element'  => '#mycredplayfield' . $set_id,
			'field'    => '<pre>' . print_r( $set, true ) . '</pre>',
			'multi'    => 0
		) );
		*/

		$prefs       = mycred_scratch_settings();
		$remaining   = '<small>' . __( 'Unlimited plays.', 'mycred' ) . '</small>';

		// If there is a daily max, indicate it
		if ( $set->daily_maximum > 0 )
			$remaining = '<small>' . sprintf( _n( 'Max. 1 purchase / day', 'Max. %d purchases / day', $set->daily_maximum, 'mycred' ), $set->daily_maximum ) . '</small>';

		if ( ! $set->over_purchase_limit ) {

			// If play is free - add play button
			if ( $set->cost == 0 ) {

				$buttons[] = '<div class="mycred-play-meta">' . mycred_play_button( 'mycredbuyacard' . $set->set_id, __( 'Free', 'mycred' ), array( 'do' => 'buy-scratch-card', 'id' => $set->set_id, 'token' => wp_create_nonce( 'mycred-scratch-buy-scratch-card' ), 'flavour' => $flavour ), true ) . '</div>';
				$cost      = __( 'Free', 'mycred' );

			}

			// If user can afford to play - add buy button
			elseif ( $set->can_afford ) {

				$buttons[] = '<div class="mycred-play-meta">' . mycred_play_button( 'mycredbuyacard' . $set->set_id, __( 'Buy', 'mycred' ), array( 'do' => 'buy-scratch-card', 'id' => $set->set_id, 'token' => wp_create_nonce( 'mycred-scratch-buy-scratch-card' ), 'flavour' => $flavour ), true ) . '</div>';

			}

		}
		elseif ( $set->unplayed == 0 )
			$buttons[] = $set->get_message( 'limit' );

		if ( $set->unplayed > 0 ) {

			$card_id   = mycred_get_an_unplayed_card_id( $user_id, $set->set_id );
			$unplayed  = __( 'Play', 'mycred' ) . '<span class="count-bubble button button-raised button-circle button-plain button-tiny">' . $set->unplayed . '</span>';
			$buttons[] = '<div class="mycred-play-meta">' . mycred_play_button( 'mycredplayacard' . $set->set_id, $unplayed, array( 'do' => 'new-scratch-card', 'id' => $set->set_id, 'item' => $card_id, 'token' => wp_create_nonce( 'mycred-scratch-new-scratch-card' ), 'flavour' => $flavour ), true ) . '</div>';

		}

		$card_wrapper_class = array( 'mycred-play-item', 'play-item' . $set->set_id );
		$card_wrapper_style = array( 'height: ' . $set->structure['height'] . 'px;', 'width: ' . $set->structure['width'] . 'px;', 'min-height: ' . $set->structure['height'] . 'px !important;' );
		$card_set_title     = '<span class="set-title">' . $set->post->post_title . '</span>';

		if ( $set->set_logo_url != '' ) {

			if ( $set->use_logo_as_bg ) {
				$card_wrapper_style[] = "background: url('" . esc_url( $set->set_logo_url ) . "') no-repeat center center;";
			}
			else {
				$card_wrapper_class[] = 'inline';
				$card_wrapper_class[] = 'empty';
				$card_set_title       = '<img src="' . esc_url( $set->set_logo_url ) . '" alt="' . esc_attr( $set->post->post_title ) . '" class="mycred-item-logo" />' . $card_set_title;
			}

		}
		else {
			$card_wrapper_class[] = 'empty';
		}

		ob_start();

?>
<div class="<?php echo implode( ' ', $card_wrapper_class ); ?>" style="<?php echo implode( ' ', $card_wrapper_style ); ?>">
	<div class="mycred-play-item-wrap">
		<div class="mycred-play-item-title"><?php echo $card_set_title; ?></div>
		<div class="mycred-play-item-cost"><?php $set->show_cost(); ?></div>
		<div class="mycred-play-item-actions"><?php echo implode( '', $buttons ); ?></div>
	</div>
</div>
<?php

		$content = ob_get_contents();
		ob_end_clean();

		if ( $return )
			return $content;

		mycred_play_json( array(
			'id'       => $set_id,
			'empty'    => 1,
			'element'  => '#mycredplayfield' . $set_id,
			'field'    => $content
		) );

	}
endif;

/**
 * AJAX: Load Fields
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_ajax_load_fields' ) ) :
	function mycred_scratch_ajax_load_fields( $theset = false, $return = false ) {

		extract( mycred_play_get_request() );

		$user_id   = get_current_user_id();
		$card_sets = mycred_get_available_card_sets( $user_id );

		if ( $card_sets === false )
			mycred_play_json( array(
				'id'       => 0,
				'empty'    => 1,
				'element'  => '#mycredplayfield0',
				'field'    => '<p>' . __( 'No scratch cards available at this time', 'mycred' ) . '</p>'
			) );

		ob_start();

		echo '<ul>';

		// Find the tallest card
		$highest = 0;
		foreach ( $card_sets as $set_id => $set ) {
			if ( $set->structure['height'] > $highest )
				$highest = $set->structure['height'];
		}

		foreach ( $card_sets as $set_id => $set ) {

			$buttons = array();

			if ( $theset !== false && $theset->set_id == $set->set_id )
				$set = $theset;

			if ( ! $set->over_purchase_limit ) {

				// If play is free - add play button
				if ( $set->cost == 0 ) {

					$buttons[] = '<div class="mycred-play-meta">' . mycred_play_button( 'mycredbuyacard' . $set->set_id, __( 'Free', 'mycred' ), array( 'do' => 'buy-scratch-card', 'id' => $set->set_id, 'token' => wp_create_nonce( 'mycred-scratch-buy-scratch-card' ), 'flavour' => $flavour ), true ) . '</div>';

				}

				elseif ( $set->can_afford )
					$buttons[] = '<div class="mycred-play-meta">' . mycred_play_button( 'mycredbuyacard' . $set->set_id, __( 'Buy', 'mycred' ), array( 'do' => 'buy-scratch-card', 'id' => $set->set_id, 'token' => wp_create_nonce( 'mycred-scratch-buy-scratch-card' ), 'flavour' => $flavour, 'multi' => 1 ), true ) . '</div>';

			}

			if ( $set->unplayed > 0 ) {
				$card_id   = mycred_get_an_unplayed_card_id( $user_id, $set->set_id );
				$label     = __( 'Play', 'mycred' ) . '<span class="count-bubble button button-raised button-circle button-inverse button-tiny">' . $set->unplayed . '</span>';
				$buttons[] = '<div class="mycred-play-meta">' . mycred_play_button( 'mycredplayacard' . $set->set_id, $label, array( 'do' => 'new-scratch-card', 'id' => $set->set_id, 'item' => $card_id, 'token' => wp_create_nonce( 'mycred-scratch-new-scratch-card' ), 'flavour' => $flavour, 'multi' => 1 ), true ) . '</div>';
			}

			$card_wrapper_class = array( 'mycred-play-item', 'play-item' . $set->set_id );
			$card_wrapper_style = array( 'height: ' . $set->structure['height'] . 'px;', 'width: ' . $set->structure['width'] . 'px;', 'min-height: ' . $set->structure['height'] . 'px !important;' );

			$card_set_title     = '<span class="set-title">' . $set->post->post_title . '</span>';
			if ( $set->set_logo_url != '' ) {

				if ( $set->use_logo_as_bg ) {
					$card_wrapper_style[] = "background: url('" . esc_url( $set->set_logo_url ) . "') no-repeat center center;";
				}
				else {
					$card_wrapper_class[] = 'inline';
					$card_wrapper_class[] = 'empty';
					$card_set_title       = '<img src="' . esc_url( $set->set_logo_url ) . '" alt="' . esc_attr( $set->post_title ) . '" class="mycred-item-logo" />' . $card_set_title;
				}

			}
			else {
				$card_wrapper_class[] = 'empty';
			}

			if ( ! in_array( 'inline', $card_wrapper_class ) && $set->structure['height'] <= 250 )
				$card_wrapper_class[] = 'inline';

			if ( $set->structure['height'] < $highest ) {
				$diff = $highest - $set->structure['height'];
				if ( $diff / 2 > 0 )
					$card_wrapper_style[] = 'margin-top: ' . absint( $diff / 2 ) . 'px;';
			}

?>
<li style="<?php if ( $set->structure['height'] < $highest ) echo 'min-height: ' . ( $highest + 12 ) . 'px;'; ?>">
	<div class="<?php echo implode( ' ', $card_wrapper_class ); ?>" style="<?php echo implode( ' ', $card_wrapper_style ); ?>">
		<div class="mycred-play-item-wrap">
			<div class="mycred-play-item-title"><?php echo $card_set_title; ?></div>
			<div class="mycred-play-item-cost"><?php $set->show_cost(); ?></div>
			<div class="mycred-play-item-actions"><?php echo implode( '', $buttons ); ?></div>
		</div>
	</div>
</li>
<?php

		}

		echo '</ul>';

		$content = ob_get_contents();
		ob_end_clean();

		if ( $return )
			return $content;

		mycred_play_json( array(
			'id'       => 0,
			'empty'    => 1,
			'element'  => '#mycredplayfield0',
			'field'    => $content,
			'message'  => __( 'Select a card', 'mycred' ),
			'multi'    => 1
		) );

	}
endif;

/**
 * AJAX: Buy A Card
 * @since 1.0
 * @version 1.3
 */
if ( ! function_exists( 'mycred_scratch_ajax_buy_a_card' ) ) :
	function mycred_scratch_ajax_buy_a_card() {

		extract( mycred_play_get_request() );

		global $wpdb, $scratch_card_set_db;

		$set_id      = absint( $playid );
		$card_sets   = mycred_get_available_card_sets( get_current_user_id() );

		$set         = ( array_key_exists( $set_id, $card_sets ) ? $card_sets[ $set_id ] : false );
		if ( $set === false || ! $set->available ) {
			mycred_play_json( array(
				'id'       => $set_id,
				'empty'    => 1,
				'message'  => '',
				'element'  => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
				'field'    => wptexturize( __( 'Could not complete purchase. Please reload the page and try again.', 'mycred' ) ),
				'multi'    => $multi
			) );
		}

		$prefs                = mycred_scratch_settings();
		$play_a_card          = false;

		// Pick a card at random
		$newly_picked_card_id = mycred_get_random_card_id( $set_id );

		// If we can not pick a new card at random, we have no cards left to play.
		if ( $newly_picked_card_id === false ) {

			mycred_play_json( array(
				'id'       => $set_id,
				'empty'    => 1,
				'message'  => '',
				'element'  => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
				'field'    => wptexturize( $prefs['template_soldout'] ),
				'multi'    => $multi
			) );

		}

		// Assign card ownership to the player
		$wpdb->update(
			$scratch_card_set_db,
			array( 'user_id' => $user_id ),
			array( 'id'      => $newly_picked_card_id ),
			array( '%d' ),
			array( '%d' )
		);

		$mycred      = $set->mycred;
		$balance     = $mycred->get_users_balance( $user_id );

		// Free card
		if ( $set->get_cost() == $mycred->zero() ) {

			// Insert a log entry so we can enforce limits
			$wpdb->insert(
				$mycred->log_table,
				array(
					'ref'     => 'free_scratch_card',
					'ref_id'  => $set_id,
					'user_id' => $user_id,
					'creds'   => 0,
					'ctype'   => $set->charge_point_type,
					'time'    => current_time( 'timestamp' ),
					'entry'   => $set->log_templates['purchase']
				),
				array( '%s', '%d', '%d', '%d', '%s', '%d', '%s' )
			);

			$play_a_card = true;
			$balance     = '';

		}

		// Else if we can afford
		elseif ( $set->can_afford ) {

			// Attempt charge
			$charged = $mycred->add_creds(
				'buy_scratch_card',
				$user_id,
				0 - $set->get_cost(),
				$set->log_templates['purchase'],
				$set_id,
				array( 'ref_type' => 'post' ),
				$set->charge_point_type
			);

			// Charge was successfull
			if ( $charged ) {

				$play_a_card = true;
				if ( $set->charge_point_type == $set->payout_point_type )
					$balance = $balance - $set->cost;
				else
					$balance = '';

			}

			// Declined, release card
			else {

				$wpdb->update(
					$scratch_card_set_db,
					array( 'user_id' => 0 ),
					array( 'id' => $newly_picked_card_id ),
					array( '%d' ),
					array( '%d' )
				);

			}

		}

		// If a card should be played
		if ( $play_a_card ) {

			// Get the requested card object
			$selected_card = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$scratch_card_set_db} WHERE id = %d;", $newly_picked_card_id ) );

			// Get the rendered card as a string
			$rendered_card = mycred_load_scratch_card( $selected_card, $user_id, $flavour, $multi, $set );

			// Render message
			$message       = $set->show_message( $set->log_templates['purchase'], $set_id );

			mycred_play_json( array(
				'id'         => $set_id,
				'empty'      => 1,
				'element'    => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
				'field'      => $rendered_card,
				'message'    => $message,
				'decimals'   => $mycred->format['decimals'],
				'newbalance' => $balance,
				'multi'      => $multi
			) );

		}

		// Payment failed probably due to lack of funds
		mycred_play_json( array(
			'id'       => $set_id,
			'empty'    => 1,
			'message'  => '',
			'element'  => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
			'field'    => wptexturize( $prefs['template_insufficient'] ),
			'multi'    => $multi
		) );

	}
endif;

/**
 * AJAX: Play A Card
 * @since 1.0
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_scratch_ajax_play_a_card' ) ) :
	function mycred_scratch_ajax_play_a_card() {

		extract( mycred_play_get_request() );

		$set_id  = absint( $playid );
		$play_id = absint( $itemid );

		global $wpdb, $scratch_card_set_db;

		// Get the card
		$selected_card = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$scratch_card_set_db} WHERE id = %d AND user_id = %d;", $play_id, $user_id ) );

		// Upps, the card was not found
		if ( ! isset( $selected_card->id ) ) {

			mycred_play_json( array(
				'id'       => $set_id,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
				'field'    => '<p>' . __( 'Card not found. Please reload this page and try again.', 'mycred' ) . '</p>'
			) );

		}

		// Get the rendered card as a string
		$rendered_card = mycred_load_scratch_card( $selected_card, $user_id, $flavour, $multi );

		mycred_play_json( array(
			'id'       => $selected_card->set_id,
			'empty'    => 1,
			'element'  => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $selected_card->set_id ),
			'field'    => $rendered_card,
			'multi'    => $multi
		) );

	}
endif;

/**
 * AJAX: Payout card
 * @since 1.0
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_scratch_ajax_payout' ) ) :
	function mycred_scratch_ajax_payout() {

		extract( mycred_play_get_request() );

		$card_id   = absint( $itemid );
		$set_id    = absint( $playid );
		$card_sets = mycred_get_available_card_sets( $user_id );

		global $wpdb, $scratch_card_set_db;

		// Get the set
		$set       = ( array_key_exists( $set_id, $card_sets ) ? $card_sets[ $set_id ] : false );

		// Get the card
		$card      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$scratch_card_set_db} WHERE id = %d AND user_id = %d;", $card_id, $user_id ) );

		// Upps, the card does not exists.
		if ( ! isset( $card->id ) )
			mycred_play_json( array(
				'id'       => $set_id,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
				'message'  => __( 'Card not found. Please reload this page and try again.', 'mycred' )
			) );

		// Default to no-win
		$winnings = $set->show_message( 'nowin', true );
		$winnings = apply_filters( 'mycred_scratch_no_win', $winnings, $set_id, $user_id );
		$balance  = '';

		$set->unplayed --;
		if ( $set->unplayed < 0 ) $set->unplayed = 0;

		// The card has a payout
		if ( is_numeric( $card->payout ) && $card->payout > 0 ) {

			do_action( 'mycred_scratch_card_payout', $card, $set, $user_id );

			// Load myCRED with the point type set for payouts (even if just one type is used)
			$mycred    = ( $set->charge_point_type != $set->payout_point_type ) ? mycred( $set->payout_point_type ) : $set->mycred;
			$balance   = $mycred->get_users_balance( $user_id, $set->payout_point_type );
			$data      = array( 'ref_type' => 'post', 'cardid' => $card->id );

			// Make sure user only gets paid out once!
			$been_paid = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$mycred->log_table} WHERE ref = 'win_scratch_card' AND user_id = %d AND ctype = %s AND data = %s;", $user_id, $set->payout_point_type, serialize( $data ) ) );

			// No results = no payout
			if ( $been_paid === NULL ) {

				// Attempt payout
				$payout = $mycred->add_creds(
					'win_scratch_card',
					$user_id,
					$card->payout,
					$set->log_templates['payout'],
					$set_id,
					$data,
					$set->payout_point_type
				);

				/**
				 * Something declined the payout.
				 * Probably causes:
				 * - User just got excluded
				 * - myCRED customization (code snippet in theme) is declining the transaction
				 * - Database was not reached
				 */
				if ( ! $payout ) {

					if ( $multi === 0 )
						$start = mycred_scratch_ajax_load_field( $set, true );
					else
						$start = mycred_scratch_ajax_load_fields( $set, true );

					mycred_play_json( array(
						'id'       => $set_id,
						'empty'    => 1,
						'message'  => __( 'Could not payout. Error 2', 'mycred' ),
						'element'  => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
						'field'    => $start,
						'multi'    => $multi
					) );

				}

				// Player was successfully paid
				else {

					// Render the winnings message
					$winnings = $mycred->template_tags_amount( $set->show_message( 'winner', true ), $card->payout );

					// Update balance
					if ( $set->payout_point_type == $set->charge_point_type )
						$balance = $mycred->number( $balance + $card->payout );

					// Do not change balance if we got paid out in a different point type
					else
						$balance = '';

				}

			}

			// This should not occur but just in case it does, reload the play field and give an error message
			else {

				if ( $multi === 0 )
					$start = mycred_scratch_ajax_load_field( $set, true );
				else
					$start  = mycred_scratch_ajax_load_fields( $set, true );

				mycred_play_json( array(
					'id'       => $set_id,
					'empty'    => 1,
					'message'  => __( 'You have already been paid for this card.', 'mycred' ),
					'element'  => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
					'field'    => $start,
					'multi'    => $multi
				) );

			}

		}

		// Remove card to prevent future use
		$wpdb->delete(
			$scratch_card_set_db,
			array( 'id' => $card->id ),
			array( '%d' )
		);

		// Flush?
		$wpdb->flush();

		do_action( 'mycred_scratch_card_used', $card, $set, $user_id );

		// Check if we have cards left to play
		$remaining = mycred_get_remaining_card_count( $set_id );

		$prefs     = mycred_scratch_settings();

		// No more cards! Change status to "Sold Out".
		if ( $remaining == 0 ) {

			$set->change_status( 'soldout' );

			$start = wptexturize( $prefs['template_soldout'] );

		}

		// There are cards left to play
		else {

			if ( $multi === 0 )
				$start = mycred_scratch_ajax_load_field( $set, true );
			else
				$start = mycred_scratch_ajax_load_fields( $set, true );

		}

		mycred_play_json( array(
			'id'         => $set_id,
			'empty'      => 1,
			'message'    => $winnings,
			'element'    => '#mycredplayfield' . ( ( $multi === 1 ) ? 0 : $set_id ),
			'field'      => $start,
			'newbalance' => ( ( $winnings == '' ) ? '' : $balance ),
			'multi'      => $multi
		) );

	}
endif;

/**
 * Check if Card Set is Playable
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_ajax_check_card_set_playable' ) ) :
	function mycred_ajax_check_card_set_playable( $user_id, $set ) {

		$prefs = mycred_scratch_settings();

		if ( $set === false )
			mycred_play_json( array(
				'id'       => 0,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . 0,
				'field'    => '<div class="central-jumbo">' . __( 'Invalid ID', 'mycred' ) . '</div>'
			) );

		// Nothing to show for visitors
		if ( ! is_user_logged_in() )
			mycred_play_json( array(
				'id'       => $set->set_id,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . $set->set_id,
				'field'    => wptexturize( $prefs['template_visitors'] )
			) );

		// Either we gave the wrong id or the card set is no longer available or draft
		if ( ! $set->is_set_ready() )
			mycred_play_json( array(
				'id'       => $set->set_id,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . $set->set_id,
				'field'    => '<div class="central-jumbo">' . __( 'Invalid ID', 'mycred' ) . '</div>'
			) );

		// Sold out
		if ( $set->get_status() == 'soldout' && $set->unplayed == 0 )
			mycred_play_json( array(
				'id'       => $set->set_id,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . $set->set_id,
				'field'    => wptexturize( $prefs['template_soldout'] )
			) );

		// Nothing left to play, the set should be soldout
		if ( $set->cards_remaining == 0 && $set->unplayed == 0 && $set->get_status() != 'soldout' ) {

			$set->change_status( 'soldout' );

			mycred_play_json( array(
				'id'       => $set->set_id,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . $set->set_id,
				'field'    => wptexturize( $prefs['template_soldout'] )
			) );

		}

		// On Hold
		elseif ( $set->get_status() == 'onhold' )
			mycred_play_json( array(
				'id'       => $set->set_id,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . $set->set_id,
				'field'    => wptexturize( $prefs['template_onhold'] )
			) );

		// Check for exclusions
		if ( $set->user_is_excluded( $user_id ) )
			mycred_play_json( array(
				'id'       => $set->set_id,
				'empty'    => 1,
				'element'  => '#mycredplayfield' . $set->set_id,
				'field'    => wptexturize( $prefs['template_excluded'] )
			) );

		return true;

	}
endif;
