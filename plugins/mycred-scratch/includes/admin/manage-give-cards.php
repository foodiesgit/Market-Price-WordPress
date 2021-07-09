<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Giveout Admin Menu
 * @since 1.0
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_admin_menu_scratch_giveout' ) ) :
	function mycred_admin_menu_scratch_giveout() {

		if ( myred_get_available_set_count() == 0 ) return;

		$pages   = array();

		$pages[] = add_submenu_page(
			'edit.php?post_type=scratch_card_set',
			__( 'Give Card', 'mycred' ),
			__( 'Give Card', 'mycred' ),
			'edit_users',
			'mycred-scratch-cards-giveout',
			'mycred_scratch_giveout_admin_screen'
		);

		foreach ( $pages as $page ) {
			add_action( 'admin_print_styles-' . $page, 'mycred_scratch_giveout_admin_styles' );
			add_action( 'load-' . $page,               'mycred_scratch_giveout_admin_load' );
		}

	}
endif;

/**
 * Giveout Admin Styles
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_giveout_admin_styles' ) ) :
	function mycred_scratch_giveout_admin_styles() {

?>
<style type="text/css">
th#user { width: 40%; }
th#cards { width: 40%; }
th#number { width: 20%; }
td select, td input { width: 99%; }
</style>
<?php

	}
endif;

/**
 * Giveout Admin Load
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_giveout_admin_load' ) ) :
	function mycred_scratch_giveout_admin_load() {

		if ( ! isset( $_POST['give_scratch_card'] ) || ! wp_verify_nonce( $_POST['give_scratch_card']['token'], 'mycred-give-scratch-card' ) ) return;

		global $wpdb, $scratch_card_set_db;

		$recipient = false;
		$user_id   = sanitize_text_field( $_POST['give_scratch_card']['user'] );
		if ( is_numeric( $user_id ) ) {

			$_user = get_userdata( $user_id );
			if ( isset( $_user->ID ) )
				$recipient = $_user->ID;

		}

		elseif ( is_email( $user_id ) ) {

			$_user = get_user_by( 'email', $user_id );
			if ( isset( $_user->ID ) )
				$recipient = $_user->ID;

		}

		if ( ! $recipient ) {

			$_user = get_user_by( 'login', $user_id );
			if ( isset( $_user->ID ) )
				$recipient = $_user->ID;

		}

		// Make sure we have nominated a user that exists
		if ( ! $recipient ) {

			$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'page' => 'mycred-scratch-cards-giveout', 'error' => 1 ), admin_url( 'edit.php' ) );
			wp_safe_redirect( $url );
			exit;

		}

		$set_id    = absint( $_POST['give_scratch_card']['card'] );
		$card_sets = mycred_get_available_card_sets( $recipient );
		$set       = ( array_key_exists( $set_id, $card_sets ) ? $card_sets[ $set_id ] : false );

		// Make sure the card set exists
		if ( $set === false || ! $set->ready ) {

			$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'page' => 'mycred-scratch-cards-giveout', 'error' => 2 ), admin_url( 'edit.php' ) );
			wp_safe_redirect( $url );
			exit;

		}

		// Make sure the card set is active
		if ( $set->get_status() != 'available' ) {

			$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'page' => 'mycred-scratch-cards-giveout', 'error' => 3 ), admin_url( 'edit.php' ) );
			wp_safe_redirect( $url );
			exit;

		}

		// Make sure cards remain for the selected card set
		if ( $set->cards_remaining == 0 ) {

			$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'page' => 'mycred-scratch-cards-giveout', 'error' => 4 ), admin_url( 'edit.php' ) );
			wp_safe_redirect( $url );
			exit;

		}

		global $wpdb, $scratch_card_set_db;

		$number = absint( $_POST['give_scratch_card']['number'] );
		if ( $number == 0 ) $number = 1;

		// Pick one random card
		if ( $number == 1 ) {

			$random_card = mycred_get_random_card_id( $set_id );

			$wpdb->update(
				$scratch_card_set_db,
				array( 'user_id' => $recipient ),
				array( 'id'      => $random_card ),
				array( '%d' ),
				array( '%d' )
			);

		}

		// Pick multiple random cards
		else {

			for ( $i = 0; $i < $number; $i++ ) {

				$random_card = mycred_get_random_card_id( $set_id );

				$wpdb->update(
					$scratch_card_set_db,
					array( 'user_id' => $recipient ),
					array( 'id'      => $random_card ),
					array( '%d' ),
					array( '%d' )
				);

			}

		}

		$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'page' => 'mycred-scratch-cards-giveout', 'completed' => $number ), admin_url( 'edit.php' ) );
		wp_safe_redirect( $url );
		exit;

	}
endif;

/**
 * Giveout Admin Screen
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_giveout_admin_screen' ) ) :
	function mycred_scratch_giveout_admin_screen() {

		if ( ! current_user_can( 'edit_users' ) ) wp_die( 'Access Denied' );

		global $wpdb;

		$users = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users};" );
		$cards = $wpdb->get_results( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_status = 'available' AND post_type = 'scratch_card_set';" );

?>
<div class="wrap">
	<h1><?php _e( 'Give Cards', 'mycred' ); ?></h1>
<?php

		if ( isset( $_GET['completed'] ) && $_GET['completed'] != '' )
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf( _n( 'Card successfully given to user.', '%d Cards successfully given to user.', absint( $_GET['completed'] ), 'mycred' ), absint( $_GET['completed'] ) ) . '</p></div>';

		elseif ( isset( $_GET['error'] ) && $_GET['error'] != '' ) {

			if ( $_GET['error'] == 1 )
				echo '<div id="message" class="error notice"><p>' . __( 'Could not find a user with the details you provided. Please check and try again.', 'mycred' ) . '</p></div>';

			elseif ( $_GET['error'] == 2 )
				echo '<div id="message" class="error notice"><p>' . __( 'The selected card set could not be found.', 'mycred' ) . '</p></div>';

			elseif ( $_GET['error'] == 3 )
				echo '<div id="message" class="error notice"><p>' . __( 'The selected card set is not available.', 'mycred' ) . '</p></div>';

			elseif ( $_GET['error'] == 4 )
				echo '<div id="message" class="error notice"><p>' . __( 'The selected card set has no cards remaining.', 'mycred' ) . '</p></div>';

		}

?>
	<p><?php _e( 'Here you can give away cards to your users without them needing to pay for it. Once a card has been given to a user, they can scratch it immediately.', 'mycred' ); ?></p>
	<form id="give-out-cards" method="post">
		<input type="hidden" name="give_scratch_card[token]" value="<?php echo wp_create_nonce( 'mycred-give-scratch-card' ); ?>" />
		<table class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<th id="user" class="manage-column column-user column-primary"><?php _e( 'User', 'mycred' ); ?></th>
					<th id="cards" class="manage-column column-cards"><?php _e( 'Card Set', 'mycred' ); ?></th>
					<th id="number" class="manage-column column-number"><?php _e( 'Number', 'mycred' ); ?></th>
				</tr>
			</thead>
			<tbody id="the-list">
				<tr>
					<td class="column-user">

						<?php if ( $users > 100 ) : ?>
						<input type="text" class="code" size="30" required name="give_scratch_card[user]" id="mycred-give-cards-to-user" placeholder="<?php _e( 'User ID, email or username', 'mycred' ); ?>" value="" />
						<?php else : ?>
						<select name="give_scratch_card[user]" id="mycred-give-cards-to-user">
<?php

		$all_users = $wpdb->get_results( "SELECT ID, display_name, user_login FROM {$wpdb->users};" );
		foreach ( $all_users as $user ) {

			echo '<option value="' . $user->ID . '">';

			$name = $user->display_name;
			if ( $name == '' )
				$name = $user->user_login;

			echo $name . '</option>';

		}

?>
						</select>
						<?php endif; ?>

					</td>
					<td class="column-cards">
						<?php if ( empty( $cards ) ) : ?>
						<?php _e( 'No active card sets found.', 'mycred' ); ?>
						<?php else : ?>
						<select name="give_scratch_card[card]" id="mycred-give-cards-of-kind">
<?php

		foreach ( $cards as $card_set ) {

			$remaining = mycred_get_remaining_card_count( $card_set->ID );
			$title = sprintf( _x( '%s ( %d Remaining )', '%s = Card Set Title %d = Number of cards remaining', 'mycred' ), $card_set->post_title, $remaining );

			echo '<option value="' . $card_set->ID . '">' . esc_attr( $title ) . '</option>';

		}

?>
						</select>
						<?php endif; ?>
					</td>
					<td class="column-number">
						<input type="number" name="give_scratch_card[number]" id="mycred-give-cards-number" size="4" min="1" value="1" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php if ( ! empty( $cards ) ) : ?><p class="alignright"><input type="submit" id="mycred-submit-give-card-form" class="button button-primary" value="<?php _e( 'Give Card', 'mycred' ); ?>" /></p><?php endif; ?>
	</form>
</div>
<script type="text/javascript">
jQuery(function($) {

	$( 'form#give-out-cards' ).submit(function(e){

		$( '#mycred-submit-give-card-form' ).attr( 'disabled', 'disabled' ).val( '<?php echo esc_js( __( 'Working ...', 'mycred' ) ); ?>' );

	});

});
</script>
<?php

	}
endif;
