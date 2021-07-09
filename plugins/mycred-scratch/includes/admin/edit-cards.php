<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Scratch Card Title Text
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_card_title_text' ) ) :
	function mycred_scratch_card_title_text( $title ) {

		$screen = get_current_screen();

		if  ( isset( $screen->post_type ) && 'scratch_card_set' == $screen->post_type )
			$title = __( 'Enter a set title', 'mycred' );

		return $title;

	}
endif;

/**
 * Scratch Card Update Messages
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_card_set_update_messages' ) ) :
	function mycred_card_set_update_messages( $messages ) {

		$messages['scratch_card_set'] = array(
			0  => '',
			1  => __( 'Card Set Updated.', 'mycred' ),
			2  => 'Unsupported Action',
			3  => 'Unsupported Action',
			4  => __( 'Card Set Updated.', 'mycred' ),
			5  => 'Unsupported Action',
			6  => __( 'Card Set Updated.', 'mycred' ),
			7  => __( 'Card Set Updated.', 'mycred' ),
			8  => 'Unsupported Action',
			9  => 'Unsupported Action',
			10 => __( 'Card Set Updated.', 'mycred' )
		);

		return $messages;

	}
endif;

/**
 * Edit Cards Screen Styles
 * @since 1.0
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_edit_cards_admin_styles' ) ) :
	function mycred_edit_cards_admin_styles() {

		global $post;

		wp_enqueue_style( 'edit-scratch-cards' );
		wp_enqueue_style( 'mycred-scratch-bootstrap' );

		if ( in_array( $post->post_status, array( 'new', 'auto-draft', 'draft' ) ) ) {

			wp_localize_script(
				'edit-scratch-cards',
				'myCREDScratchCardEditor',
				array(
					'uploadertitle'      => esc_js( __( 'Scratch Card Backgrounds', 'mycred' ) ),
					'buttonlabel'        => esc_js( __( 'Use', 'mycred' ) ),
					'uploadercovertitle' => esc_js( __( 'Scratch Card Cover Image', 'mycred' ) ),
					'buttoncoverlabel'   => esc_js( __( 'Use as Cover', 'mycred' ) ),
					'changecoverlabel'   => esc_js( __( 'Change Cover Image', 'mycred' ) ),
					'uploadercointitle'  => esc_js( __( 'Scratch Card Coin Image', 'mycred' ) ),
					'buttoncoinlabel'    => esc_js( __( 'Use as Coin', 'mycred' ) ),
					'confirmpublish'     => esc_js( __( 'Are you sure you want to activate this set? Once activated, you can no longer make any further changes to the set!', 'mycred' ) ),
					'confirmtemplate'    => esc_js( __( 'Are you sure you want to load this template? The card layout, cover images and setup will be replaced. If you want to save your setup but look at a template, make sure you save your setup first before loading the template.', 'mycred' ) ),
					'confirmactivation'  => esc_js( __( 'Activating the set will NOT save any changes you might have made! Make sure you save your changes before you activate a card set!', 'mycred' ) ),
					'activationtoken'    => wp_create_nonce( 'mycred-generate-scratch-card-set' ),
					'confirmdestroy'     => esc_js( __( 'Are you sure you want to destroy this card set? All remaining cards will be permanently deleted. This can not be undone!', 'mycred' ) ),
					'generatorerror'     => esc_js( __( 'Something went wrong. Please check the field and try again.', 'mycred' ) )
				)
			);

			wp_enqueue_media();
			wp_enqueue_script( 'edit-scratch-cards' );
			wp_enqueue_script( 'mycred-scratch-card' );

		}

		else {

			wp_localize_script(
				'manage-scratch-cards',
				'myCREDScratchCardManager',
				array(
					'confirmonhold' => esc_js( __( 'Are you sure you want to put sale of this set on hold? Purchased cards that has not yet been scratched will still be available to users but no new cards can be purchased.', 'mycred' ) ),
					'onholdtoken'   => wp_create_nonce( 'mycred-card-set-onhold' )
				)
			);

			wp_enqueue_script( 'manage-scratch-cards' );
			wp_enqueue_script( 'mycred-scratch-card' );

		}

	}
endif;

/**
 * Scratch Card Set Metaboxes
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_set_metaboxes' ) ) :
	function mycred_scratch_card_set_metaboxes( $post ) {

		$card_set = mycred_scratch_card_set( $post->ID );
		$setup    = $card_set->setup;

		// Replace the bult-in "Publish" metabox with our own
		remove_meta_box( 'submitdiv', 'scratch_card_set', 'side' );

		// Remove slug metabox
		remove_meta_box( 'slugdiv', 'scratch_card_set', 'normal' );

		// Action metabox (available for all)
		add_meta_box( 'mycred-scratch-actions', __( 'Actions', 'mycred' ), 'mycred_scratch_card_submit_metabox', 'scratch_card_set', 'side', 'high' );

		// New card set creations
		if ( in_array( $card_set->get_status(), array( 'new', 'auto-draft', 'draft' ) ) ) {

			// Card set setup
			add_meta_box( 'mycred-scratch-card-setup', __( 'Setup', 'mycred' ), 'mycred_scratch_card_setup_metabox', 'scratch_card_set', 'normal', 'high' );

			// Templates
			add_meta_box( 'mycred-scratch-card-templates', __( 'Message Templates', 'mycred' ), 'mycred_scratch_card_templates_metabox', 'scratch_card_set', 'normal', 'high', array( 'button' => false ) );

			// Card design
			add_meta_box( 'mycred-scratch-card-design', __( 'Card Design', 'mycred' ), 'mycred_scratch_card_design_metabox', 'scratch_card_set', 'normal', 'default' );

			// Winnings
			add_meta_box( 'mycred-scratch-card-winnings', __( 'Winnings', 'mycred' ), 'mycred_scratch_card_winnings_metabox', 'scratch_card_set', 'side', 'core' );

			// Coin image
			add_meta_box( 'mycred-scratch-card-coin', __( 'Coin Image', 'mycred' ), 'mycred_scratch_card_coin_metabox', 'scratch_card_set', 'side', 'core' );

			// Cover image
			add_meta_box( 'mycred-scratch-card-cover', __( 'Cover Image', 'mycred' ), 'mycred_scratch_card_cover_metabox', 'scratch_card_set', 'side', 'core' );

			// Once we have saved once
			if ( $card_set->get_status() == 'draft' ) {

				if ( $card_set->ready )
					add_meta_box( 'mycred-scratch-card-preview', __( 'Preview', 'mycred' ), 'mycred_scratch_card_preview_metabox', 'scratch_card_set', 'normal', 'default' );

				// No win background
				add_meta_box( 'mycred-scratch-card-no-win', __( 'No Win Backgrounds', 'mycred' ), 'mycred_scratch_card_nowin_metabox', 'scratch_card_set', 'normal', 'low', array( 'set' => $setup[0], 'row' => 0 ) );

				// Winning backgrounds
				unset( $setup[0] );
				if ( count( $setup ) > 0 ) {
					foreach ( $setup as $row => $set ) {

						if ( $set['value'] == 0 ) continue;

						add_meta_box( 'mycred-scratch-card-win' . $row, sprintf( __( '%s Backgrounds', 'mycred' ), $card_set->mycred->format_creds( $set['value'] ) ), 'mycred_scratch_card_win_metabox', 'scratch_card_set', 'normal', 'low', array( 'set' => $set, 'row' => $row ) );

					}
				}

			}

		}

		// Metaboxes to show if the set is active
		elseif ( in_array( $card_set->get_status(), array( 'available', 'onhold', 'soldout' ) ) ) {

			// Templates
			add_meta_box( 'mycred-scratch-card-templates', __( 'Message Templates', 'mycred' ), 'mycred_scratch_card_templates_metabox', 'scratch_card_set', 'normal', 'high', array( 'button' => true ) );

			add_meta_box( 'mycred-scratch-card-stats', __( 'Cards in Circulation', 'mycred' ), 'mycred_scratch_card_stats_metabox', 'scratch_card_set', 'normal', 'high', array( '_post' => $card_set ) );
			add_meta_box( 'mycred-scratch-card-preview', __( 'Preview', 'mycred' ), 'mycred_scratch_card_preview_metabox', 'scratch_card_set', 'normal', 'high', array( '_post' => $card_set ) );

		}

	}
endif;

/**
 * Metabox: Submit
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_card_submit_metabox' ) ) :
	function mycred_scratch_card_submit_metabox( $post ) {

		$status = $post->post_status;
		$set = mycred_scratch_card_set( $post->ID );

?>
<div class="submitbox" id="submitpost">
	<div id="minor-publishing">
		<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
		<div style="display:none;">
		<?php submit_button( __( 'Save', 'mycred' ), 'button', 'save' ); ?>
		</div>

		<div id="minor-publishing-actions">

			<div id="save-action">
				<input type="submit" name="save" id="save-post" value="<?php esc_attr_e( 'Save Changes', 'mycred' ); ?>" class="button" />
				<span class="spinner"></span>
			</div>
			<div class="clear"></div>

			<?php if ( $status == 'draft' || $status == 'auto-draft' ) : ?>

			<p><span class="description"><?php _e( 'Once a set is activated, no further changes will be allowed. Please make sure you double check everything before activating this set.', 'mycred' ); ?></span></p>

			<?php else : ?>

			<ul>
				<li><div id="current-status"><?php _e( 'Current Status', 'mycred' ); ?>: <strong><?php echo mycred_display_scratc_card_set_status( $status ); ?></strong></div></li>
				<li><div id="setup-cost"><?php printf( __( 'Card Price: %s', 'mycred' ), '<strong>' . $set->show_cost( 1, true ) . '</strong>' ); ?></div></li>
				<li><div id="setup-limit"><?php printf( __( 'Purchase Limit: %s', 'mycred' ), '<strong>' . ( $set->daily_maximum > 0 ? sprintf( _n( '1 / day', '%d / day', $set->daily_maximum, 'mycred' ), $set->daily_maximum ) : __( 'Unlimited', 'mycred' ) ) . '</strong>' ); ?></div></li>
				<?php if ( $set->charge_point_type != $set->payout_point_type ) : ?>
				<li><div id="charge-type"><?php printf( __( 'Charged: %s', 'mycred' ), '<strong>' . mycred_get_point_type_name( $set->charge_point_type, false ) . '</strong>' ); ?></div></li>
				<li><div id="payout-type"><?php printf( __( 'Paid: %s', 'mycred' ), '<strong>' . mycred_get_point_type_name( $set->payout_point_type, false ) . '</strong>' ); ?></div></li>
				<?php else : ?>
				<li><div id="charge-type"><?php printf( __( 'Point Type: %s', 'mycred' ), '<strong>' . mycred_get_point_type_name( $set->charge_point_type, false ) . '</strong>' ); ?></div></li>
				<?php endif; ?>
			</ul>

			<?php endif; ?>

			<div class="clear"></div>
		</div><!-- #minor-publishing-actions -->

		<?php if ( $status == 'draft' || $status == 'auto-draft' ) : ?>

		<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( $status ); ?>" />
		<input type="hidden" name="post_status" value="draft" />

		<input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="public" />
		<input type="hidden" name="visibility" value="public" />

		<?php endif; ?>

		<div class="clear"></div>
	</div>
	<div id="major-publishing-actions">

		<?php if ( current_user_can( "delete_post", $set->set_id ) && $status == 'draft' ) : $delete_url = add_query_arg( array( 'post_type' => 'scratch_card_set', 'destroy-set' => $set->set_id ), admin_url( 'edit.php' ) ); ?>

		<div id="delete-action">
			<a class="submitdelete deletion" id="mycred-destroy-scratch-card-set" href="<?php echo esc_url( $delete_url ); ?>"><?php echo esc_attr( __( 'Destroy Card Set', 'mycred' ) ); ?></a>
		</div>

		<?php endif; ?>

		<div id="publishing-action">
			<span class="spinner"></span>

			<?php if ( $status == 'draft' && $set->ready ) : ?>

			<input type="submit" data-setid="<?php echo $set->set_id; ?>" id="activate-new-scratch-card-set" class="button button-primary primary button-large" value="<?php _e( 'Activate', 'mycred' ); ?>" />

			<?php elseif ( $status == 'onhold' ) : ?>

			<input type="hidden" name="mycred-change-status" value="available" />
			<?php submit_button( __( 'Reactivate', 'mycred' ), 'primary button-large', 'publish', false ); ?>

			<?php elseif ( $status == 'soldout' ) : ?>

			<?php submit_button( __( 'Restart', 'mycred' ), 'primary button-large', 'publish', false ); ?>

			<?php elseif ( $status == 'available' ) : ?>

			<input type="submit" data-setid="<?php echo $set->set_id; ?>" id="put-card-set-on-hold" class="button button-primary primary button-large" value="<?php _e( 'Put On Hold', 'mycred' ); ?>" />

			<?php endif; ?>

		</div>
		<div class="clear"></div>
	</div>
</div>
<?php

	}
endif;

/**
 * Metabox: Card Setup
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_setup_metabox' ) ) :
	function mycred_scratch_card_setup_metabox( $post ) {

		$set = mycred_scratch_card_set( $post->ID );

?>
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="form-group">
			<label for="mycred-scratch-charge-point-type"><?php _e( 'Charged Point Type', 'mycred' ); ?></label>
			<?php mycred_scratch_point_type_dropdown( 'charge_point_type', 'mycred-scratch-charge-point-type', $set->charge_point_type ); ?>
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="form-group">
			<label for="mycred-scratch-payout-point-type"><?php _e( 'Paid Out Point Type', 'mycred' ); ?></label>
			<?php mycred_scratch_point_type_dropdown( 'payout_point_type', 'mycred-scratch-payout-point-type', $set->payout_point_type ); ?>
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="form-group">
			<label for="mycred-scratch-cost"><?php _e( 'Cost', 'mycred' ); ?></label>
			<input type="text" name="scratch_card_setup[cost]" id="mycred-scratch-cost" value="<?php echo esc_attr( $set->cost ); ?>" />
			<div><span class="description"><?php _e( 'Use zero for free plays.', 'mycred' ); ?></span></div>
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="form-group">
			<label for="mycred-scratch-daily-max"><?php _e( 'Max. Purchases / Day', 'mycred' ); ?></label>
			<input type="number" name="scratch_card_setup[daily_max]" id="mycred-scratch-daily-max" value="<?php echo absint( $set->daily_maximum ); ?>" />
			<div><span class="description"><?php _e( 'Use zero for unlimited.', 'mycred' ); ?></span></div>
		</div>
	</div>
</div>
<?php

	}
endif;

/**
 * Design Metabox
 * @since 1.0.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_card_design_metabox' ) ) :
	function mycred_scratch_card_design_metabox( $post ) {

		$set = mycred_scratch_card_set( $post->ID );

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

		<div class="form-group">
			<label for="mycred-scratch-template"><?php _e( 'Design Template', 'mycred' ); ?></label>
			<?php mycred_scratch_template_dropdown( 'scratch_card_setup[template]', 'mycred-scratch-template', $set->template ); ?>
		</div>

	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="mycred-scratch-width"><?php _e( 'Card Width', 'mycred' ); ?></label>
					<input type="text" name="scratch_card_setup[width]" size="5" id="mycred-scratch-width" value="<?php echo esc_attr( $set->structure['width'] ); ?>" size="8" /> px
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="mycred-scratch-height"><?php _e( 'Card Height', 'mycred' ); ?></label>
					<input type="text" name="scratch_card_setup[height]" size="5" id="mycred-scratch-height" value="<?php echo esc_attr( $set->structure['height'] ); ?>" size="8" /> px
				</div>
			</div>
		</div>

	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
			<label for="mycred-scratch-minimum"><?php _e( 'Minimum Scratch', 'mycred' ); ?></label>
			<input type="number" name="scratch_card_setup[minimum]" id="mycred-scratch-minimum" min="1" max="100" step="1" size="10" value="<?php echo esc_attr( $set->minimum_scratch ); ?>" /> %
			<div><span class="description"><?php _e( 'The amount of cover a user must scratch for a payout.', 'mycred' ); ?></span></div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
			<label for="mycred-scratch-diameter"><?php _e( 'Scratch Diameter', 'mycred' ); ?></label>
			<input type="number" name="scratch_card_setup[diameter]" id="mycred-scratch-diameter" min="1" max="100" step="1" size="10" value="<?php echo esc_attr( $set->coin['diameter'] ); ?>" /> px
			<div><span class="description"><?php _e( 'The brush diameter when scratching.', 'mycred' ); ?></span></div>
		</div>
	</div>
</div>
<?php

	}
endif;

/**
 * Coin Metabox
 * @since 1.0.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_card_coin_metabox' ) ) :
	function mycred_scratch_card_coin_metabox( $post ) {

		$set = mycred_scratch_card_set( $post->ID );

?>
<div class="row">
	<div class="col-lg-12">
		<div id="mycred-scratch-coin-image">
			<div>
<?php

if ( $set->coin['url'] != '' ) {

	echo '<img src="' . $set->coin['url'] . '" alt="" /><input type="hidden" name="scratch_card_setup[coin_image]" id="mycred-scratch-coin-image" value="' . esc_attr( $set->coin['image'] ) . '" />';

}

?>
			</div>
		</div>
		<button type="button" id="change-coin-image" class="button"><?php _e( 'Change Image', 'mycred' ); ?></button>
	</div>
</div>
<?php

	}
endif;

/**
 * Cover Image Metabox
 * @since 1.0.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_card_cover_metabox' ) ) :
	function mycred_scratch_card_cover_metabox( $post ) {

		$set = mycred_scratch_card_set( $post->ID );

?>
<div class="row">
	<div class="col-lg-12">
		<div id="scratch-cover-image-wrapper">
			<div style="margin-bottom: 12px;"><span class="description"><?php _e( 'This is the image the user scratch away.', 'mycred' ); ?></span></div>
<?php

		if ( $set->cover_image != '' ) {

			echo '<img src="' . $set->cover_image . '" alt="" /><input type="hidden" name="scratch_card_setup[cover_image]" id="mycred-scratch-cover-image" value="' . esc_attr( $set->cover_image_id ) . '" />';

		}

?>
		</div>
		<button type="button" id="set-scratch-cover-image" class="button"><?php if ( $set->cover_image != '' ) _e( 'Change Cover Image', 'mycred' ); else _e( 'Set Cover Image', 'mycred' ); ?></button>
	</div>
</div>
<?php

	}
endif;

/**
 * Metabox: Winnings
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_winnings_metabox' ) ) :
	function mycred_scratch_card_winnings_metabox( $post ) {

		$card_set = mycred_scratch_card_set( $post->ID );
		$mycred   = $card_set->mycred;
		if ( $card_set->charge_point_type != $card_set->payout_point_type )
			$mycred = mycred( $card_set->payout_point_type );

?>
<table class="table" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th id="scratch-set-payout" class="scratch-set-payout"><?php _e( 'Payout', 'mycred' ); ?></th>
			<th id="scratch-set-number" class="scratch-set-number"><?php _e( 'Quantity', 'mycred' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php

		$total = 0;
		foreach ( $card_set->setup as $row => $set ) {

			$total = $total + $set['number'];

?>
		<tr class="scratch-payout-row" id="mycredcardpayoutrow<?php echo $row; ?>">
			<td class="scratch-set-payout">
				<input type="text"<?php if ( $row == 0 ) echo ' readonly="readonly"'; ?> name="mycred_scratch_set[<?php echo $row; ?>][value]" size="6" value="<?php if ( $row != 0 ) echo $mycred->number( $set['value'] ); else echo 'No win'; ?>" placeholder="<?php echo $mycred->zero(); ?>" />
			</td>
			<td class="scratch-set-number">
				<input type="number" min="0" step="1" name="mycred_scratch_set[<?php echo $row; ?>][number]" size="6" value="<?php echo absint( $set['number'] ); ?>" placeholder="0" />
			</td>
			<td class="text-center scratch-set-equals"><?php if ( $row != 0 ) : ?><button type="button" class="button button-small remove-scratch-setup-button">-</button><?php endif; ?></td>
		</tr>
<?php

		}

?>

		<tr class="scratch-payout-total">
			<td class="">
				<strong><?php _e( 'Total Cards', 'mycred' ); ?></strong>
			</td>
			<td class="scratch-set-number">
				<strong><?php echo absint( $total ); ?></strong>
			</td>
			<td class="text-center scratch-set-equals"><button type="button" id="add-new-scratch-payout-row" class="button button-primary button-small" data-rows="<?php echo count( $card_set->setup ); ?>" data-format="<?php echo $mycred->zero(); ?>">+</button></td>
		</tr>
	</tbody>
</table>
<?php

	}
endif;

/**
 * Metabox: Card Preview
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_preview_metabox' ) ) :
	function mycred_scratch_card_preview_metabox( $post ) {

		$set  = mycred_scratch_card_set( $post->ID );

		$args = array(
			'id'         => 'mycred-new-scratch-card-preview',
			'background' => $set->setup[0]['attachment_ids'][0],
			'cover'      => $set->cover_image,
			'coin'       => $set->coin['url'],
			'minimum'    => $set->minimum_scratch,
			'diameter'   => $set->coin['diameter'],
			'width'      => $set->structure['width'],
			'height'     => $set->structure['height']
		);

?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">

		<?php mycred_display_scratch_card_preview( $args ); ?>

	</div>
</div>
<?php

	}
endif;

/**
 * Logo BG Option
 * @since 1.0.3
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_logo_options' ) ) :
	function mycred_scratch_card_logo_options( $content, $post_id ) {

		if ( get_post_type( $post_id ) == 'scratch_card_set' ) {

			$set = get_post_meta( $post_id, 'bg_logo', true );
			if ( $set == '' ) $set = 0;

			ob_start();

?>
<p style="margin: 0 0 0 0;"><label for="mycred-scratch-bg-logo"><input type="checkbox" name="bg_logo" id="mycred-scratch-bg-logo"<?php checked( $set, 1 ); ?> value="1" /> <?php _e( 'Show logo as background.', 'mycred' ); ?></label></p>
<script type="text/javascript">
jQuery(function($){

	$( '#mycred-scratch-bg-logo' ).change(function(){

		$(this).blur();

		var checkboxelement = $(this);
		var usebglogo       = 0;
		if ( $(this).is( ':checked' ) )
			usebglogo = 1;

		$.ajax({
			type        : "POST",
			data        : {
				action : 'toggle_scratch_logo_bg',
				token  : '<?php echo wp_create_nonce( 'mycred-toggle-scratch-card-logo-bg' ); ?>',
				setid  : <?php echo $post_id; ?>,
				logo   : usebglogo
			},
			dataType    : "JSON",
			url         : ajaxurl,
			beforeSend : function(){
				checkboxelement.attr( 'disabled', 'disabled' );
			},
			success    : function( response ) {
				console.log( response );
				checkboxelement.removeAttr( 'disabled' );
				if ( ! response.success && usebglogo === 1 )
					checkboxelement.removeAttr( 'checked' );
			}
		});

	});

});
</script>
<?php

			$output = ob_get_contents();
			ob_end_clean();

			$content .= $output;

		}

		return $content;

	}
endif;



/**
 * Metabox: Message Templates
 * @since 1.0.3
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_templates_metabox' ) ) :
	function mycred_scratch_card_templates_metabox( $post, $atts ) {

		$show_button = $atts['args']['button'];
		$set         = mycred_scratch_card_set( $post->ID );

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="mycred-scratch-buy-log"><?php _e( 'Purchase / Free Play Log Template', 'mycred' ); ?></label>
					<input type="text" name="scratch_card_templates[buy_log]" id="mycred-scratch-buy-log" value="<?php echo esc_attr( $set->log_templates['purchase'] ); ?>" style="width:99%;" />
					<div><span class="description"><?php _e( 'The log template used when a user buys a card or plays a free card.', 'mycred' ); ?> <?php echo $set->mycred->available_template_tags( array( 'general', 'post' ) ); ?></span></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="mycred-scratch-win-log"><?php _e( 'Payout Log Template', 'mycred' ); ?></label>
					<input type="text" name="scratch_card_templates[win_log]" id="mycred-scratch-win-log" value="<?php echo esc_attr( $set->log_templates['payout'] ); ?>" style="width:99%;" />
					<div><span class="description"><?php _e( 'The log template used when a user wins.', 'mycred' ); ?> <?php echo $set->mycred->available_template_tags( array( 'general', 'post' ) ); ?></span></div>
				</div>
			</div>
		</div>

	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="mycred-scratch-win-message"><?php _e( 'Win Message', 'mycred' ); ?></label>
					<input type="text" name="scratch_card_templates[win_message]" id="mycred-scratch-win-message" value="<?php echo esc_attr( $set->message_templates['winner'] ); ?>" style="width:99%;" />
					<div><span class="description"><?php _e( 'Message shown when a user scratches a winning card.', 'mycred' ); ?> <?php echo $set->mycred->available_template_tags( array( 'general', 'amount' ) ); ?></span></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="mycred-scratch-no-win-message"><?php _e( 'No Win Message', 'mycred' ); ?></label>
					<input type="text" name="scratch_card_templates[no_win_message]" id="mycred-scratch-no-win-message" value="<?php echo esc_attr( $set->message_templates['nowin'] ); ?>" style="width:99%;" />
					<div><span class="description"><?php _e( 'Message shown when a user has scratched a card with no payout.', 'mycred' ); ?> <?php echo $set->mycred->available_template_tags( array( 'general' ) ); ?></span></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="mycred-scratch-limit-message"><?php _e( 'Reached Limit Message', 'mycred' ); ?></label>
					<input type="text" name="scratch_card_templates[limit_message]" id="mycred-scratch-limit-message" value="<?php echo esc_attr( $set->message_templates['limit'] ); ?>" style="width:99%;" />
					<div><span class="description"><?php _e( 'Message shown when a user has reached their daily play limit. Ignored if no limit is enforced.', 'mycred' ); ?> <?php echo $set->mycred->available_template_tags( array( 'general' ) ); ?></span></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php

	}
endif;

/**
 * Metabox: Card No Win Images
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_nowin_metabox' ) ) :
	function mycred_scratch_card_nowin_metabox( $post, $atts ) {

		$setup = $atts['args']['set'];
		$row   = $atts['args']['row'];

?>
<div class="mycred-scratch-image-wrapper row">
<?php

		if ( ! empty( $setup['attachment_ids'] ) ) {
			foreach ( $setup['attachment_ids'] as $i => $attachment_id ) {

				$attachment_image = $attachment_id;
				if ( is_numeric( $attachment_id ) ) {
					$attachment_image = wp_get_attachment_url( $attachment_id );
					if ( $attachment_image === false ) {

?>
	<div class="mycred-scratch-attached-image col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<pre><?php print_r( $attachment_id ); ?></pre>
	</div>
<?php

					}
				}

?>
	<div class="mycred-scratch-attached-image col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<div class="remove-attached-image"><div class="dashicon dashicons-no dashicons-before"></div></div>
		<input type="hidden" name="mycred_scratch_set[0][attachment_ids][]" id="mycred-scratch-nowin-id<?php echo $i; ?>" value="<?php echo esc_attr( $attachment_id ); ?>" />
		<img src="<?php echo $attachment_image; ?>" alt="" />
	</div>
<?php

			}
		}

?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 add-new-button-row"><button type="button" id="add-new-no-win-image" class="button button-primary" data-row="<?php echo $row; ?>"><?php _e( 'Add Images', 'mycred' ); ?></button></div>
</div>
<?php

	}
endif;

/**
 * Metabox: Win Images
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_win_metabox' ) ) :
	function mycred_scratch_card_win_metabox( $post, $atts ) {

		$setup = $atts['args']['set'];
		$row   = $atts['args']['row'];

?>
<div class="mycred-scratch-image-wrapper row">
<?php

		if ( ! empty( $setup['attachment_ids'] ) ) {
			foreach ( $setup['attachment_ids'] as $i => $attachment_id ) {

				$attachment_image = $attachment_id;
				if ( is_numeric( $attachment_id ) ) {
					$attachment_image = wp_get_attachment_url( $attachment_id );
					if ( $attachment_image === false ) continue;
				}

?>
	<div class="mycred-scratch-attached-image col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<div class="remove-attached-image"><div class="dashicon dashicons-no dashicons-before"></div></div>
		<input type="hidden" name="mycred_scratch_set[<?php echo $row; ?>][attachment_ids][]" id="mycred-scratch-win<?php echo $row; ?>-id<?php echo $i; ?>" value="<?php echo esc_attr( $attachment_id ); ?>" />
		<img src="<?php echo $attachment_image; ?>" alt="" />
	</div>
<?php

			}
		}

?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 add-new-button-row"><button type="button" id="add-new-win-image" class="button button-primary" data-row="<?php echo $row; ?>"><?php _e( 'Add Images', 'mycred' ); ?></button></div>
</div>
<?php

	}
endif;

/**
 * Metabox: Card Stats
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_card_stats_metabox' ) ) :
	function mycred_scratch_card_stats_metabox( $post ) {

		$card_set = mycred_scratch_card_set( $post->ID );
		$mycred   = $card_set->mycred;
		if ( $card_set->charge_point_type != $card_set->payout_point_type )
			$mycred = mycred( $card_set->payout_point_type );

?>
<table class="table" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="cardset-col-"><?php _e( 'Card Value', 'mycred' ); ?></th>
			<th class="cardset-col-"><?php _e( 'Cards Created', 'mycred' ); ?></th>
			<th class="cardset-col-"><?php _e( 'Cards Purchased', 'mycred' ); ?></th>
			<th class="cardset-col-"><?php _e( 'Cards Played', 'mycred' ); ?></th>
			<th class="cardset-col-"><?php _e( 'Cards Remaining', 'mycred' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php

		$total_created = $total_purchased = $total_played = $total_remaining = 0;
		foreach ( $card_set->setup as $row => $set ) {

			$cards_in_set    = absint( $set['number'] );
			$remaining       = mycred_get_remaining_card_count( $card_set->set_id, $row );
			$purchased       = mycred_get_purchased_card_count( $card_set->set_id, $row );
			$played          = $cards_in_set - $remaining;

			$total_created   = $total_created + $cards_in_set;
			$total_purchased = $total_purchased + $purchased;
			$total_played    = $total_played + $played;
			$total_remaining = $total_remaining + $remaining;

?>
		<tr>
			<td class="cardset-col-"><?php if ( $row != 0 ) echo $mycred->format_creds( $set['value'] ); else _e( 'No win', 'mycred' ); ?></td>
			<td class="cardset-col-"><?php echo $cards_in_set; ?></td>
			<td class="cardset-col-"><?php echo absint( $purchased ); ?></td>
			<td class="cardset-col-"><?php echo absint( $cards_in_set - $remaining ); ?></td>
			<td class="cardset-col-"><?php echo absint( $remaining ); ?></td>
		</tr>
<?php

		}

?>
	</tbody>
	<tfoot>
		<tr>
			<td class="cardset-col-"><strong><?php _e( 'Total', 'mycred' ); ?></strong></td>
			<td class="cardset-col-"><strong><?php echo $total_created; ?></strong></td>
			<td class="cardset-col-"><strong><?php echo $total_purchased; ?></strong></td>
			<td class="cardset-col-"><strong><?php echo $total_played; ?></strong></td>
			<td class="cardset-col-"><strong><?php echo $total_remaining; ?></strong></td>
		</tr>
	</tfoot>
</table>
<p><span class="description"><?php _e( 'Purchased cards are cards that has been paid for but not yet scratched or cards that has been given to a user by an administrator and not yet scratched by the user. A card is considered "played" once it has been scratched.', 'mycred' ); ?></span></p>
<script type="text/javascript">
jQuery(function($) {

	$( '#titlewrap input#title' ).attr( 'disabled', 'disabled' );

});
</script>
<?php

	}
endif;

/**
 * Save Scretch Card
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_save_scratch_card_set' ) ) :
	function mycred_save_scratch_card_set( $post_id, $post ) {

		if ( ! current_user_can( 'edit_others_posts' ) ) return;

		if ( isset( $_POST['bg_logo'] ) ) {

			update_post_meta( $post_id, 'bg_logo', 1 );

		}

		if ( isset( $_POST['scratch_card_templates'] ) ) {

			$buy_log = sanitize_text_field( $_POST['scratch_card_templates']['buy_log'] );
			update_post_meta( $post_id, 'buy_log', $buy_log );

			$win_log = sanitize_text_field( $_POST['scratch_card_templates']['win_log'] );
			update_post_meta( $post_id, 'win_log', $win_log );

			$win_message = sanitize_text_field( $_POST['scratch_card_templates']['win_message'] );
			update_post_meta( $post_id, 'win_message', $win_message );

			$no_win_message = sanitize_text_field( $_POST['scratch_card_templates']['no_win_message'] );
			update_post_meta( $post_id, 'no_win_message', $no_win_message );

			$win_message = sanitize_text_field( $_POST['scratch_card_templates']['limit_message'] );
			update_post_meta( $post_id, 'limit_message', $win_message );

		}

		if ( $post->post_status == 'draft' && isset( $_POST['charge_point_type'] ) && isset( $_POST['scratch_card_setup'] ) && ! isset( $_POST['ready-to-activate-set'] ) ) {

			$charge_point_type = sanitize_key( $_POST['charge_point_type'] );
			if ( $charge_point_type == '' )
				$charge_point_type = 'mycred_default';

			update_post_meta( $post_id, 'charge_point_type', $charge_point_type );

			$payout_point_type = sanitize_key( $_POST['payout_point_type'] );
			if ( $payout_point_type == '' )
				$payout_point_type = 'mycred_default';

			update_post_meta( $post_id, 'payout_point_type', $payout_point_type );

			$mycred = mycred( $payout_point_type );

			$cost = $mycred->number( $_POST['scratch_card_setup']['cost'] );
			update_post_meta( $post_id, 'cost', $cost );

			$daily_max = absint( $_POST['scratch_card_setup']['daily_max'] );
			update_post_meta( $post_id, 'daily_max', $daily_max );

			$template       = sanitize_text_field( $_POST['scratch_card_setup']['template'] );
			$template_setup = mycred_load_scratch_card_template( $template );

			// Custom setup or requested template does not exist
			if ( $template == '' || ( $template != '' && $template_setup === false ) ) {

				delete_post_meta( $post_id, 'template' );

				$minimum = absint( $_POST['scratch_card_setup']['minimum'] );
				update_post_meta( $post_id, 'minimum', $minimum );

				$diameter = absint( $_POST['scratch_card_setup']['diameter'] );
				update_post_meta( $post_id, 'diameter', $diameter );

				$width = absint( $_POST['scratch_card_setup']['width'] );
				update_post_meta( $post_id, 'width', $width );

				$height = absint( $_POST['scratch_card_setup']['height'] );
				update_post_meta( $post_id, 'height', $height );

				if ( array_key_exists( 'coin_image', $_POST['scratch_card_setup'] ) ) {

					if ( is_numeric( $_POST['scratch_card_setup']['coin_image'] ) )
						$coin_image = absint( $_POST['scratch_card_setup']['coin_image'] );
					else
						$coin_image = sanitize_text_field( $_POST['scratch_card_setup']['coin_image'] );

					update_post_meta( $post_id, 'coin_image', $coin_image );

				}

				if ( array_key_exists( 'cover_image', $_POST['scratch_card_setup'] ) ) {

					if ( isset( $_POST['scratch_card_setup']['cover_image'] ) ) {

						if ( is_numeric( $_POST['scratch_card_setup']['cover_image'] ) )
							$cover_image = absint( $_POST['scratch_card_setup']['cover_image'] );
						else
							$cover_image = sanitize_text_field( $_POST['scratch_card_setup']['cover_image'] );

						update_post_meta( $post_id, 'cover_image', $cover_image );

					}

				}

				$setup = (array) get_post_meta( $post_id, 'mycred_set_setup', true );
				if ( isset( $_POST['mycred_scratch_set'] ) ) {

					$setup   = array();
					$counter = 0;
					foreach ( $_POST['mycred_scratch_set'] as $row => $set ) {

						$number = absint( $set['number'] );
						if ( $counter == 0 && $number == 0 && apply_filters( 'mycred_scratch_everyonewins', false, $post_id ) === false ) continue;

						$attachment_ids = array();
						if ( array_key_exists( 'attachment_ids', $set ) ) {

							foreach ( $set['attachment_ids'] as $id ) {

								if ( is_numeric( $id ) )
									$id = absint( $id );
								else
									$id = sanitize_text_field( $id );

								if ( $id === 0 || $id == '' ) continue;
								$attachment_ids[] = $id;

							}

						}

						$new_setup = array();
						$new_setup['value']          = ( ( $counter > 0 ) ? $mycred->number( $set['value'] ) : 0 );
						$new_setup['number']         = $number;
						$new_setup['attachment_ids'] = $attachment_ids;

						$setup[ $counter ] = $new_setup;

						$counter ++;

					}

					if ( $counter == 0 )
						$setup = array(
							array(
								'number'         => 0,
								'value'          => 0,
								'attachment_ids' => array()
							),
							array(
								'number'         => 0,
								'value'          => 0,
								'attachment_ids' => array()
							)
						);

				}

				update_post_meta( $post_id, 'mycred_set_setup', $setup );

			}

			// Use of template
			else {

				if ( $template_setup !== false ) {

					update_post_meta( $post_id, 'minimum',          $template_setup['minimum_scratch'] );
					update_post_meta( $post_id, 'diameter',         $template_setup['brush_diameter'] );
					update_post_meta( $post_id, 'width',            $template_setup['card_width'] );
					update_post_meta( $post_id, 'height',           $template_setup['card_height'] );
					update_post_meta( $post_id, 'cover_image',      $template_setup['cover_image'] );
					update_post_meta( $post_id, 'mycred_set_setup', $template_setup['setup'] );

					if ( $template_setup['coin_image'] != '' )  update_post_meta( $post_id, 'coin_image',  $template_setup['coin_image'] );
					if ( $template_setup['buy_log'] != '' )     update_post_meta( $post_id, 'buy_log',     $template_setup['buy_log'] );
					if ( $template_setup['win_log'] != '' )     update_post_meta( $post_id, 'win_log',     $template_setup['win_log'] );
					if ( $template_setup['win_message'] != '' ) update_post_meta( $post_id, 'win_message', $template_setup['win_message'] );

				}

			}

		}

		elseif ( isset( $_POST['ready-to-activate-set'] ) && $_POST['ready-to-activate-set'] == 1 ) {

			remove_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );
			wp_update_post( array( 'ID' => $post_id, 'post_status' => 'available' ) );
			add_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );

		}

		elseif ( in_array( $post->post_status, array( 'available', 'onhold', 'soldout', 'publish', 'future' ) ) ) {

			if ( isset( $_POST['mycred-change-status'] ) ) {

				global $wpdb;

				$new_status = sanitize_key( $_POST['mycred-change-status'] );

				switch ( $new_status ) {

					case 'onhold' :

						remove_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );
						wp_update_post( array( 'ID' => $post_id, 'post_status' => 'onhold' ) );
						add_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );

					break;

					case 'available' :

						remove_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );
						wp_update_post( array( 'ID' => $post_id, 'post_status' => 'available' ) );
						add_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );

					break;

				}

			}

			return;

		}

	}
endif;
