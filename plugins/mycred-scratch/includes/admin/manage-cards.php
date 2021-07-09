<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Manage Card Screen Actions
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_admin_actions' ) ) :
	function mycred_scratch_card_admin_actions() {

		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != 'scratch_card_set' || ! current_user_can( 'edit_users' ) ) return;

		// Change Card Set Status
		if ( isset( $_GET['change-status-to'] ) ) {

			$new_status = sanitize_key( $_GET['change-status-to'] );
			$post_id    = absint( $_GET['set_id'] );
			$set        = mycred_scratch_card_set( $post_id );

			if ( $set->get_status() != '' ) {

				$new_status = $set->change_status( $new_status ) ? $new_status : 'failed';

				$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'changed-status' => $new_status ), admin_url( 'edit.php' ) );
				wp_safe_redirect( $url );
				exit;

			}

		}

		// Destroy Card Set
		elseif ( isset( $_GET['destroy-set'] ) ) {

			$post_id = absint( $_GET['destroy-set'] );
			$set     = mycred_scratch_card_set( $post_id );

			if ( $set->get_status() != '' ) {

				$set->destroy_set();

				$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'destroyed-set' => 1 ), admin_url( 'edit.php' ) );
				wp_safe_redirect( $url );
				exit;

			}

		}

		// Clone Card Set
		elseif ( isset( $_GET['clone-set'] ) ) {

			$post_id    = absint( $_GET['clone-set'] );
			$set        = mycred_scratch_card_set( $post_id );

			$url    = add_query_arg( array( 'post_type' => 'scratch_card_set', 'cloned-set' => 1 ), admin_url( 'edit.php' ) );
			if ( ! $set->clone_set() )
				$url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'cloned-set' => 0 ), admin_url( 'edit.php' ) );

			wp_safe_redirect( $url );
			exit;

		}

	}
endif;

/**
 * Manage Cards Screen Admin Notices
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_admin_update_notices' ) ) :
	function mycred_scratch_card_admin_update_notices() {

		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'scratch_card_set' && isset( $_GET['changed-status'] ) ) {

			if ( $_GET['changed-status'] == 'available' )
				echo '<div id="message" class="notice notice-success is-dismissible"><p>' . __( 'Card Set Reactivated.', 'mycred' ) . '</p></div>';

			elseif ( $_GET['changed-status'] == 'onhold' )
				echo '<div id="message" class="notice notice-info is-dismissible"><p>' . __( 'Card Set put On Hold.', 'mycred' ) . '</p></div>';

		}

		elseif ( isset( $_GET['destroyed-set'] ) ) {

			echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'Card Set permanently deleted.', 'mycred' ) . '</p></div>';

		}

		elseif ( isset( $_GET['cloned-set'] ) ) {

			if ( $_GET['cloned-set'] == 1 )
				echo '<div id="message" class="notice notice-success is-dismissible"><p>' . __( 'Set successfully cloned.', 'mycred' ) . '</p></div>';
			else
				echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'Failed to clone card set.', 'mycred' ) . '</p></div>';

		}

	}
endif;

/**
 * Manage Cards Screen Styles
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_manage_cards_admin_styles' ) ) :
	function mycred_manage_cards_admin_styles() {

		wp_enqueue_style( 'manage-scratch-cards' );

		wp_localize_script(
			'manage-scratch-cards',
			'myCREDScratchCardManager',
			array(
				'confirmonhold'  => esc_js( __( 'Are you sure you want to put sale of this set on hold? Purchased cards that has not yet been scratched will still be available to users but no new cards can be purchased.', 'mycred' ) ),
				'confirmdestroy' => esc_js( __( 'Are you sure you want to destroy this card set? All remaining cards will be permanently deleted. This can not be undone!', 'mycred' ) )
			)
		);

		wp_enqueue_script( 'manage-scratch-cards' );

	}
endif;

/**
 * Scratch Cards Admin Column Headers
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_card_admin_column_headers' ) ) :
	function mycred_scratch_card_admin_column_headers( $default ) {

		$columns = array();

		$columns['cb']        = $default['cb'];
		$columns['title']     = $default['title'];
		$columns['logo']      = __( 'Logo', 'mycred' );
		$columns['postid']    = __( 'Set ID', 'mycred' );
		$columns['status']    = __( 'Status', 'mycred' );
		$columns['cards']     = __( 'Setup', 'mycred' );
		$columns['remaining'] = __( 'Remaining', 'mycred' );

		return $columns;

	}
endif;

/**
 * Scratch Cards Admin Column Content
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_admin_column_content' ) ) :
	function mycred_scratch_card_admin_column_content( $column_name, $post_id ) {

		$set = mycred_scratch_card_set( $post_id );

		if ( $column_name == 'status' ) {

			$set->show_status();

		}

		elseif ( $column_name == 'postid' ) {

			echo '<code>' . absint( $post_id ) . '</code>';

		}

		elseif ( $column_name == 'logo' ) {

			if ( $set->set_logo_url == '' )
				_e( 'Not used', 'mycred' );
			else
				echo '<img src="' . $set->set_logo_url . '" alt="" />';

		}

		elseif ( $column_name == 'cards' ) {

			if ( $set->get_status() == 'draft' ) {

				echo '-';

			}
			else {

?>
<div class="scratch-card-setup">
<?php

			$mycred = $set->mycred;
			if ( $set->charge_point_type != $set->payout_point_type )
				$mycred = mycred( $set->payout_point_type );

			$total = 0;
			if ( ! empty( $set->setup ) ) {
				foreach ( $set->setup as $row => $set ) {

					if ( $row == 0 )
						$value = __( 'No win', 'mycred' );
					else
						$value = $mycred->format_creds( $set['value'] );

					echo '<div class="card-set"><span class="value">' . $value . '</span><span class="sep">=</span><span class="count">' . $set['number'] . '</span></div>';
					$total = $total + $set['number'];

				}
			}

?>
	<div class="card-set"><span class="value"><strong><?php _e( 'Total', 'mycred' ); ?></strong></span><span class="sep">&nbsp;</span><span class="count"><strong><?php echo $total; ?></strong></span></div>
</div>
<?php

			}

		}

		elseif ( $column_name == 'remaining' ) {

			if ( $set->get_status() == 'draft' ) {

				echo '-';

			}
			else {

?>
<div class="scratch-card-setup">
<?php

				$mycred = $set->mycred;
				if ( $set->charge_point_type != $set->payout_point_type )
					$mycred = mycred( $set->payout_point_type );

				$total = 0;
				if ( ! empty( $set->setup ) ) {
					foreach ( $set->setup as $row => $set ) {

						if ( $row == 0 )
							$value = __( 'No win', 'mycred' );
						else
							$value = $mycred->format_creds( $set['value'] );

						$remaining = mycred_get_remaining_card_count( $post_id, $row );

						echo '<div class="card-set"><span class="value">' . $value . '</span><span class="sep">=</span><span class="count">' . $remaining . '</span></div>';
						$total = $total + $remaining;

					}
				}

?>
	<div class="card-set"><span class="value"><strong><?php _e( 'Total', 'mycred' ); ?></strong></span><span class="sep">&nbsp;</span><span class="count"><strong><?php echo $total; ?></strong></span></div>
</div>
<?php

			}

		}

	}
endif;

/**
 * Admin Row Actions
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_card_admin_row_actions' ) ) :
	function mycred_scratch_card_admin_row_actions( $actions, $post ) {

		if ( isset( $post->post_type ) && $post->post_type == 'scratch_card_set' ) {

			unset( $actions['inline hide-if-no-js'] );

			if ( in_array( $post->post_status, array( 'available', 'onhold', 'soldout' ) ) ) {

				unset( $actions['trash'] );

				$clone_url  = add_query_arg( array( 'post_type' => 'scratch_card_set', 'clone-set' => $post->ID ), admin_url( 'edit.php' ) );
				$actions['clone']  = '<a href="' . esc_url( $clone_url ) . '" class="mycred-clone-scratch-card-set">' . __( 'Clone Set', 'mycred' ) . '</a>';

				$delete_url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'destroy-set' => $post->ID ), admin_url( 'edit.php' ) );
				$actions['delete'] = '<a href="' . esc_url( $delete_url ) . '" class="mycred-destroy-scratch-card-set">' . __( 'Destroy Set', 'mycred' ) . '</a>';

			}

			elseif ( $post->post_status == 'trash' )
				unset( $actions['untrash'] );

		}

		return $actions;

	}
endif;
