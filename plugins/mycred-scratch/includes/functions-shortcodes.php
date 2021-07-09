<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Shortcode: Scratch Cards
 * @since 1.0
 * @version 1.2
 */
if ( ! function_exists( 'mycred_scratch_cards_shortcode' ) ) :
	function mycred_scratch_cards_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'set'          => '',
			'user_id'      => 'current',
			'theme'        => 'gold',
			'show_balance' => 1,
			'bgcolor'      => '#f1f1f1'
		), $atts ) );

		global $mycred_load_playfield;

		$prefs = mycred_scratch_settings();

		// Nothing to show for visitors
		if ( ! is_user_logged_in() )
			return wptexturize( $prefs['template_visitors'] );

		if ( $user_id == 'current' || $user_id == '' )
			$user_id = get_current_user_id();

		$card_sets   = mycred_get_available_card_sets( $user_id );
		$set_id      = $set;
		$set         = ( array_key_exists( $set_id, $card_sets ) ? $card_sets[ $set_id ] : false );

		// Either we gave the wrong id or the card set is no longer available or draft
		if ( $set === false || ! $set->ready )
			return '<p>Invalid card set id</p>';

		// Sold out
		if ( $set->get_status() == 'soldout' )
			return wptexturize( wpautop( $prefs['template_soldout'] ) );

		// On Hold
		elseif ( $set->get_status() == 'onhold' )
			return wptexturize( wpautop( $prefs['template_onhold'] ) );

		// Check for exclusions
		if ( $set->user_is_excluded( $user_id ) )
			return wptexturize( wpautop( $prefs['template_excluded'] ) );

		$height       = $set->structure['height'] . 'px';
		$theme        = mycred_play_get_flavour( $theme );
		$button_class = 'button button-tiny button-' . $theme . ' button-rounded button-uppercase';

		ob_start();

?>
<style type="text/css">
#mycredplayfield<?php echo $set->set_id; ?> { position: relative; display: block; float: none; clear: both; width: 100%; background-color: <?php echo esc_attr( $bgcolor ); ?>; margin-bottom: 24px; }
#mycredplayfield<?php echo $set->set_id; ?> .mycred-play-field { width: 100%; text-align: center; min-height: <?php echo $height; ?>; }
</style>

<div id="mycredplayfield<?php echo $set->set_id; ?>" class="mycred-play-field-wrapper" data-do="load-scratch-card" data-item="0" data-token="<?php echo wp_create_nonce( 'mycred-scratch-load-scratch-card' ); ?>" data-id="<?php echo $set->set_id; ?>" data-flavour="<?php echo $theme; ?>">

	<div class="mycred-play-bar">

		<div class="mycred-play-title myd"><?php printf( _x( 'Scratch Card: %s', 'Scratch cards name', 'mycred' ), '<span>' . $set->post->post_title ) . '</div>'; ?></div>
		<?php if ( absint( $show_balance ) === 1 ) : ?>
		<div class="mycred-play-balance myd pull-right">
			<button type="button" class="button button-tiny button-disabled button-rounded button-uppercase button-fake"><?php if ( $set->mycred->before != '' ) echo $set->mycred->before . ' '; echo '<span class="balance">' . $set->mycred->format_number( $set->mycred->get_users_balance( $user_id ) ) . '</span>'; if ( $set->mycred->after != '' ) echo ' ' . $set->mycred->after; ?></button>
		</div>
		<?php endif; ?>
		<div class="clear clearfix"></div>

	</div>
	<div class="mycred-play-field">
		<div class="mycred-scratch-card-wrap" style="text-align: center;">
			<div class="loading-card-set" style="line-height: <?php echo $height; ?>; height: <?php echo $height; ?>; width: <?php echo $set->structure['width']; ?>px; margin: 0 auto;"><?php _e( 'loading ...', 'mycred' ); ?></div>
		</div>
	</div>

</div>
<?php

		$content = ob_get_contents();
		ob_end_clean();

		$mycred_load_playfield = true;

		return $content;

	}
endif;

/**
 * Shortcode: All Scratch Cards
 * @since 1.1
 * @version 1.1
 */
if ( ! function_exists( 'mycred_all_scratch_cards_shortcode' ) ) :
	function mycred_all_scratch_cards_shortcode( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'user_id'      => 'current',
			'theme'        => 'gold',
			'show_balance' => 1,
			'balance_label' => __( 'Your Balance:', 'mycred' ),
			'height'       => '',
			'bgcolor'      => '#f1f1f1',
			'ctype'        => MYCRED_DEFAULT_TYPE_KEY
		), $atts ) );

		global $mycred_load_playfield;

		$prefs  = mycred_scratch_settings();

		// Nothing to show for visitors
		if ( ! is_user_logged_in() )
			return wptexturize( $prefs['template_visitors'] );

		if ( $user_id == 'current' || $user_id == '' )
			$user_id = get_current_user_id();

		if ( $ctype == '' ) $ctype = MYCRED_DEFAULT_TYPE_KEY;

		$mycred = mycred( $ctype );
		if ( $mycred->exclude_user( $user_id ) ) return wptexturize( $prefs['template_excluded'] );

		$theme  = mycred_play_get_flavour( $theme );

		if ( substr( $height, -2, 0 ) != 'px' && substr( $height, -1, 0 ) != '%' )
			$height . 'px';

		ob_start();

?>
<div id="mycredplayfield0"<?php if ( $height != '' ) echo ' style="min-height: ' . $height . ';"'; ?> class="mycred-play-field-wrapper" data-do="load-scratch-cards" data-item="0" data-multi="1" data-token="<?php echo wp_create_nonce( 'mycred-scratch-load-scratch-cards' ); ?>" data-id="0" data-flavour="<?php echo $theme; ?>">

	<div class="mycred-play-bar">

		<div class="mycred-play-title myd"></div>
		<?php if ( absint( $show_balance ) === 1 ) : ?>
		<div class="mycred-play-balance myd pull-right"><?php if ( $balance_label != '' ) echo '<span class="balance-label">' . $balance_label . ' </span>'; if ( $mycred->before != '' ) echo $mycred->before . ' '; echo '<span class="balance">' . $mycred->format_number( $mycred->get_users_balance( $user_id, $ctype ) ) . '</span>'; if ( $mycred->after != '' ) echo ' ' . $mycred->after; ?></div>
		<?php endif; ?>
		<div class="clear clearfix"></div>

	</div>
	<div class="mycred-play-field">
		<div class="mycred-scratch-card-wrap">
			<div class="loading-card-set" style="line-height: <?php if ( $height != '' ) echo $height; else echo '100px'; ?>; text-align: center;"><?php _e( 'loading ...', 'mycred' ); ?></div>
		</div>
	</div>

</div>
<?php

		$content = ob_get_contents();
		ob_end_clean();

		$mycred_load_playfield = true;

		return $content;

	}
endif;

/**
 * Shortcode: Winning History
 * @since 1.0
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_scratch_history_shortcode' ) ) :
	function mycred_scratch_history_shortcode( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'user_id'   => 'current',
			'number'    => '',
			'type'      => MYCRED_DEFAULT_TYPE_KEY,
			'show_nav'  => 0,
			'inlinenav' => 0,
			'login'     => ''
		), $atts ) );

		return do_shortcode( '[mycred_history user_id="' . $user_id . '" ref="win_scratch_card" number=' . absint( $number ) . ' login="' . $login . '" type="' . $type . '" show_nav=' . $show_nav . ' inlinenav=' . $inlinenav . ']' . $content . '[/mycred_history]' );

	}
endif;

/**
 * Shortcode: Remaining Cards
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_scratch_cards_remaining_shortcode' ) ) :
	function mycred_scratch_cards_remaining_shortcode( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'set' => ''
		), $atts ) );

		$prefs = mycred_scratch_settings();
		$set   = mycred_scratch_card_set( $set );

		if ( $set === false || ! $set->ready )
			return 'Invalid card set id';

		if ( $set->get_status() == 'soldout' ) return $content;

		ob_start();

?>
<div class="table-responsive">
	<table class="table table-condensed scratch-card-setup-table" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class="mycred-scratch-table-column mycred-scratch-card-value"><?php _e( 'Card Value', 'mycred' ); ?></th>
				<th class="mycred-scratch-table-column mycred-scratch-card-remaining"><?php _e( 'Cards Left', 'mycred' ); ?></th>
			</tr>
		</thead>
		<tbody>
<?php

				$mycred = $set->mycred;
				if ( $set->charge_point_type != $set->payout_point_type )
					$mycred = mycred( $set->payout_point_type );

				$total = 0;
				if ( ! empty( $set->setup ) ) {
					foreach ( $set->setup as $row => $setup ) {

						if ( $row == 0 )
							$value = __( 'No win', 'mycred' );
						else
							$value = $mycred->format_creds( $setup['value'] );

						$remaining = mycred_get_remaining_card_count( $set->set_id, $row );

						echo '<tr><td class="mycred-scratch-card-value">' . $value . '</td><td class="mycred-scratch-table-column mycred-scratch-card-remaining">' . $remaining . '</td></tr>';
						$total = $total + $remaining;

					}
				}

?>
		</tbody>
		<tfoot>
			<tr>
				<th class="mycred-scratch-table-column mycred-scratch-card-value"><?php _e( 'Total', 'mycred' ); ?></th>
				<th class="mycred-scratch-table-column mycred-scratch-card-remaining"><?php echo $total; ?></th>
			</tr>
		</tfoot>
	</table>
</div>
<?php

		$content = ob_get_contents();
		ob_end_clean();

		return $content;

	}
endif;
