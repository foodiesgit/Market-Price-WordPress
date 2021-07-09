<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Add Scratch Card Set Post Type
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_add_cards_post_type' ) ) :
	function mycred_add_cards_post_type() {

		$labels = array(
			'name'                  => _x( 'Scratch Card Sets', 'Post Type General Name', 'mycred' ),
			'singular_name'         => _x( 'Scratch Card', 'Post Type Singular Name', 'mycred' ),
			'menu_name'             => __( 'Scratch Cards', 'mycred' ),
			'name_admin_bar'        => __( 'Scratch Cards', 'mycred' ),
			'archives'              => __( 'Cards Archive', 'mycred' ),
			'parent_item_colon'     => __( 'Parent Card:', 'mycred' ),
			'all_items'             => __( 'All Sets', 'mycred' ),
			'add_new_item'          => __( 'Add New Set', 'mycred' ),
			'add_new'               => __( 'Add New', 'mycred' ),
			'new_item'              => __( 'New Set', 'mycred' ),
			'edit_item'             => __( 'Edit Set', 'mycred' ),
			'update_item'           => __( 'Update Set', 'mycred' ),
			'view_item'             => __( 'View Set', 'mycred' ),
			'search_items'          => __( 'Search scratch cards', 'mycred' ),
			'not_found'             => __( 'No scratch card sets found', 'mycred' ),
			'not_found_in_trash'    => __( 'No scratch card sets found in Trash', 'mycred' ),
			'featured_image'        => __( 'Card Set Logo', 'mycred' ),
			'set_featured_image'    => __( 'Add a logo to this set', 'mycred' ),
			'remove_featured_image' => __( 'Remove logo', 'mycred' ),
			'use_featured_image'    => __( 'Use as Logo', 'mycred' ),
			'insert_into_item'      => __( 'Insert into set', 'mycred' ),
			'uploaded_to_this_item' => __( 'Uploaded to this set', 'mycred' ),
			'items_list'            => __( 'Scratch Card list', 'mycred' ),
			'items_list_navigation' => __( 'Scratch Card list navigation', 'mycred' ),
			'filter_items_list'     => __( 'Filter scratch card set list', 'mycred' ),
		);
		$args = array(
			'label'                 => __( 'Scratch Cards', 'mycred' ),
			'description'           => __( 'myCRED scratch card sets.', 'mycred' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'thumbnail' ),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 110,
			'menu_icon'             => 'dashicons-tickets-alt',
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => false,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'page',
			'register_meta_box_cb'  => 'mycred_scratch_card_set_metaboxes'
		);
		register_post_type( 'scratch_card_set', apply_filters( 'mycred_register_scratch_cards', $args ) );

		register_post_status( 'available', array(
			'label'                     => __( 'Available', 'post' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Available <span class="count">(%s)</span>', 'Available <span class="count">(%s)</span>' ),
		) );

		register_post_status( 'onhold', array(
			'label'                     => __( 'On Hold', 'post' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>' ),
		) );

		register_post_status( 'soldout', array(
			'label'                     => __( 'Sold Out', 'post' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Sold Out <span class="count">(%s)</span>', 'Sold Out <span class="count">(%s)</span>' ),
		) );

	}
endif;

/**
 * Display Scratch Card Set Status
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_display_scratc_card_set_status' ) ) :
	function mycred_display_scratc_card_set_status( $status = '' ) {

		$display = __( 'Unknown', 'mycred' );
		if ( $status == 'available' )
			$display = __( 'Available', 'mycred' );

		elseif ( $status == 'onhold' )
			$display = __( 'On Hold', 'mycred' );

		elseif ( $status == 'soldout' )
			$display = __( 'Sold Out', 'mycred' );

		elseif ( $status == 'trash' )
			$display = __( 'Trashed', 'mycred' );

		elseif ( $status == 'draft' )
			$display = __( 'Draft', 'mycred' );

		return $display;

	}
endif;

/**
 * Display Scratch Card Preview
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_display_scratch_card_preview' ) ) :
	function mycred_display_scratch_card_preview( $atts ) {

		extract( shortcode_atts( array(
			'id'         => 'mycred-new-scratch-card',
			'background' => '',
			'cover'      => '',
			'coin'       => '',
			'minimum'    => 50,
			'diameter'   => 10,
			'width'      => 0,
			'height'     => 0
		), $atts ) );

		if ( is_numeric( $background ) )
			$background_url = wp_get_attachment_url( $background );
		else
			$background_url = $background;

		if ( is_numeric( $cover ) )
			$cover_url = wp_get_attachment_url( $cover );
		else
			$cover_url = $cover;

		if ( is_numeric( $coin ) )
			$coin_url = wp_get_attachment_url( $coin );
		elseif ( $coin == '' )
			$coin_url = plugins_url( 'assets/images/coin.png', MYCRED_SCRATCH );
		else
			$coin_url = $coin;

?>
<style type="text/css">
#<?php echo $id; ?> { display: block; margin: 0 auto; padding: 0 0 0 0; width:<?php echo absint( $width ); ?>px !important; height:<?php echo absint( $height ); ?>px !important; }
#<?php echo $id; ?> canvas { display: block; position: inherit; }
</style>
<button type="button" class="button button-small" id="reset-scratch-card-preview" style="float:right;"><?php _e( 'Reset Preview', 'mycred' ); ?></button>
<div id="<?php echo $id; ?>" class="mycred-scratch-card" style="width:<?php echo absint( $width ); ?>px;height:<?php echo absint( $height ); ?>px;"></div>
<script type="text/javascript">
function completed( element )  {

	instructions = document.getElementById('reset-scratch-card-preview');
	instructions.addEventListener('click', restartPreview);

	jQuery(function($) {

		alert( '<?php _e( 'The card has been scratched.', 'mycred' ); ?>' );

	});

};
function restartPreview() {
	instructions.removeEventListener('click', restartPreview, false);
	document.getElementById( "<?php echo esc_attr( $id ); ?>" ).restart();
	document.getElementById( "<?php echo esc_attr( $id ); ?>" ).className = '';
	isMouseDown = false;
}

if (window.getSelection) {
	if (window.getSelection().empty) {  // Chrome
		window.getSelection().empty();
	}
	else if (window.getSelection().removeAllRanges) {  // Firefox
		window.getSelection().removeAllRanges();
	}
}
else if (document.selection) {  // IE?
	document.selection.empty();
}

window.onload = function() {

	instructions = document.getElementById('reset-scratch-card-preview');
	instructions.addEventListener('click', restartPreview);

	createScratchCard({
		'container'  : document.getElementById( "<?php echo esc_attr( $id ); ?>" ), 
		'background' : '<?php echo esc_url( $background_url ); ?>', 
		'foreground' : '<?php echo esc_url( $cover_url ); ?>', 
		'coin'       : '<?php echo esc_url( $coin_url ); ?>',
		'percent'    : <?php echo absint( $minimum ); ?>,
		'thickness'  : <?php echo absint( $diameter ); ?>,
		'callback'   : 'completed'
	});

}
</script>
<?php

	}
endif;

/**
 * Load Scratch Card
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_load_scratch_card' ) ) :
	function mycred_load_scratch_card( $card = NULL, $user_id, $flavour = 'default', $multi = 0, $set = false ) {

		$background_url = mycred_get_scratch_background( $card->attachment_id, $card->template );

		if ( $set === false ) {
			$card_sets   = mycred_get_available_card_sets( $user_id );
			$set         = ( array_key_exists( $card->set_id, $card_sets ) ? $card_sets[ $card->set_id ] : false );
		}

		ob_start();

?>
<div id="mycred-the-card<?php echo $card->set_id; ?>" class="mycred-scratch-card" style="width:<?php echo absint( $set->structure['width'] ); ?>px !important; height:<?php echo absint( $set->structure['height'] ); ?>px !important; margin: 0 auto;"></div>
<script type="text/javascript">
var mycredscratchcard<?php echo $card->set_id; ?>;

var mycred_scratch_completed<?php echo $card->id; ?> = function() {

	var collect  = {
		action  : 'claim-scratch-card',
		token   : '<?php echo wp_create_nonce( 'mycred-scratch-claim-scratch-card' ); ?>',
		player  : <?php echo $user_id; ?>,
		playid  : <?php echo $card->set_id; ?>,
		itemid  : <?php echo $card->id; ?>,
		flavour : '<?php echo $flavour; ?>',
		multi   : <?php echo $multi; ?>
	};

	mycred_submit_play( collect, mycred_play_handler_default, false, myCREDPlay.delay );

}

if (window.getSelection) {
	if (window.getSelection().empty) {  // Chrome
		window.getSelection().empty();
	}
	else if (window.getSelection().removeAllRanges) {  // Firefox
		window.getSelection().removeAllRanges();
	}
}
else if (document.selection) {  // IE?
	document.selection.empty();
}

jQuery(function($) {

	mycredscratchcard<?php echo $card->set_id; ?> = createScratchCard({
		'container'  : document.getElementById( 'mycred-the-card<?php echo $card->set_id; ?>' ), 
		'background' : '<?php echo esc_url( $background_url ); ?>', 
		'foreground' : '<?php echo esc_url( $set->cover_image ); ?>', 
		'coin'       : '<?php echo esc_url( $set->coin['url'] ); ?>',
		'percent'    : <?php echo $set->minimum_scratch; ?>,
		'thickness'  : <?php echo $set->coin['diameter']; ?>,
		'callback'   : 'mycred_scratch_completed<?php echo $card->id; ?>'
	});

});
</script>
<div class="mycred-play-delay-timer"></div>
<?php

		$content = ob_get_contents();
		ob_end_clean();

		return $content;

	}
endif;

/**
 * Get Backround
 * @since 1.0
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_get_scratch_background' ) ) :
	function mycred_get_scratch_background( $attachment_id = 0, $template = '' ) {

		$background = $template;
		if ( $attachment_id > 0 ) {

			$background = wp_get_attachment_url( $attachment_id );
			if ( $background === false || $background === NULL || $background == '' )
				$background = $template;

		}

		if ( $background != '' ) {

			$correct_host = parse_url( home_url( '/' ), PHP_URL_HOST );
			$used_host    = parse_url( $background, PHP_URL_HOST );

			// Website URL has changed since the cards were created.
			if ( ( $correct_host !== false && $correct_host !== NULL ) && ( $used_host !== false && $used_host !== NULL ) && $correct_host != $used_host ) {

				// Attempt to fix
				$background = str_replace( $used_host, $correct_host, $background );

			}

		}

		if ( is_ssl() && $background != '' )
			$background = str_replace( 'http://', 'https://', $background );

		return apply_filters( 'mycred_get_scratch_background', $background, $attachment_id, $template );

	}
endif;

/**
 * Get Available Set Count
 * Counts the total number of card sets that is currently active.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'myred_get_available_set_count' ) ) :
	function myred_get_available_set_count() {

		global $wpdb;

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'scratch_card_set' AND post_status = 'available';" );
		if ( $count === NULL ) $count = 0;

		return $count;

	}
endif;

/**
 * Get Card Set IDs
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'myred_get_card_set_ids' ) ) :
	function myred_get_card_set_ids() {

		global $wpdb;

		$ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'scratch_card_set' AND post_status IN ( 'available', 'onhold' );" );

		return $ids;

	}
endif;

/**
 * Get Available Card Sets
 * @since 1.1
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_available_card_sets' ) ) :
	function mycred_get_available_card_sets( $user_id = NULL ) {

		global $my_scratch_cards;

		if ( is_array( $my_scratch_cards ) && ! empty( $my_scratch_cards ) ) return $my_scratch_cards;

		$set_ids   = myred_get_card_set_ids();
		if ( empty( $set_ids ) ) return false;

		$available = array();
		foreach ( $set_ids as $set_id ) {

			$set             = mycred_scratch_card_set( $set_id );
			if ( $set === false || ! $set->is_set_playable() ) continue;

			if ( $user_id !== NULL ) {

				$set->unplayed            = mycred_get_users_unplaid_count( $user_id, $set_id );
				$set->can_afford          = $set->user_can_afford_to_buy( $user_id );
				$set->over_purchase_limit = $set->user_is_over_limit( $user_id );
				$set->excluded            = $set->user_is_excluded( $user_id );

				if ( $set->over_purchase_limit || ( ! $set->can_afford && $set->unplayed == 0 ) ) {

					$set->available = false;

				}

			}

			$available[ $set_id ] = $set;

		}

		if ( empty( $available ) ) $available = false;

		return $available;

	}
endif;

/**
 * Get Purchased Card Count
 * Counts the total number of cards that has been purchased but not yet
 * scratched.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_purchased_card_count' ) ) :
	function mycred_get_purchased_card_count( $post_id, $setup_row = NULL ) {

		global $wpdb, $scratch_card_set_db;

		if ( $setup_row !== NULL ) {

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$scratch_card_set_db} WHERE set_id = %d AND setup_row = %d AND user_id != 0;", $post_id, $setup_row ) );
			if ( $count === NULL ) $count = 0;

		}
		else {

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$scratch_card_set_db} WHERE set_id = %d AND user_id != 0;", $post_id ) );
			if ( $count === NULL ) $count = 0;

		}

		return $count;

	}
endif;

/**
 * Get Remaining Card Count
 * Returns the number of cards that remain to be purchased.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_remaining_card_count' ) ) :
	function mycred_get_remaining_card_count( $post_id, $setup_row = NULL ) {

		global $wpdb, $scratch_card_set_db;

		if ( $setup_row !== NULL ) {

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$scratch_card_set_db} WHERE set_id = %d AND setup_row = %d AND user_id = 0;", $post_id, $setup_row ) );
			if ( $count === NULL ) $count = 0;

		}

		else {

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$scratch_card_set_db} WHERE set_id = %d AND user_id = 0;", $post_id ) );
			if ( $count === NULL ) $count = 0;

		}

		return $count;

	}
endif;

/**
 * Get Random Card ID
 * Picks a random card id from the card set table.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_random_card_id' ) ) :
	function mycred_get_random_card_id( $post_id ) {

		global $wpdb, $scratch_card_set_db;

		$prefs     = mycred_scratch_settings();
		$picked_id = false;

		// Should never happen but need to be sure there are cards left to pick from
		$remaining = mycred_get_remaining_card_count( $post_id );
		if ( $remaining == 0 ) {

			remove_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );
			wp_update_post( array( 'ID' => $post_id, 'status' => 'soldout' ) );
			add_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );

			return false;

		}

		// If the SQL function RAND() is disabled
		if ( $prefs['disabled_random'] == 1 ) {

			$card_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$scratch_card_set_db} WHERE set_id = %d AND user_id = 0;", $post_id ) );
			if ( ! empty( $card_ids ) ) {

				$random    = mt_rand( 0, count( $card_ids ) - 1 );
				$picked_id = isset( $card_ids[ $random ] ) ? $card_ids[ $random ] : false;

			}

		}

		// Use RAND()
		else {

			$picked_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$scratch_card_set_db} WHERE set_id = %d AND user_id = 0 ORDER BY RAND() LIMIT 1;", $post_id ) );
			if ( $picked_id === NULL )
				$picked_id = false;

		}

		return $picked_id;

	}
endif;

/**
 * User Can Scratch
 * Checks if a given user ID can play the nominated scratch card set.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_user_can_scratch' ) ) :
	function mycred_user_can_scratch( $user_id, $card_set ) {

		// First we check exclusions by plugin settings
		$prefs   = mycred_scratch_settings();
		$excluded = false;

		if ( ! empty( $prefs['exclude_roles'] ) ) {

			$role = mycred_scratch_get_users_role( $user_id );
			if ( in_array( $role, $prefs['exclude_roles'] ) )
				$excluded = true;

		}

		if ( ! $excluded && ! empty( $prefs['exclude_ids'] ) ) {

			if ( in_array( $user_id, $prefs['exclude_ids'] ) )
				$excluded = true;

		}

		// Make sure we are not excluded form the point type we charge plays in
		if ( ! $excluded && $card_set->mycred->exclude_user( $user_id ) )
			$excluded = true;

		// Make sure we are not excluded from the paid out point type (if different)
		if ( ! $excluded && $card_set->charge_point_type != $card_set->payout_point_type ) {

			$mycred = mycred( $card_set->payout_point_type );
			if ( $mycred->exclude_user( $user_id ) )
				$excluded = true;

		}

		return apply_filters( 'mycred_user_can_scratch', $excluded, $user_id, $card_set );

	}
endif;

/**
 * Get Users Unplayed Count
 * Returns the number of cards that belongs to a user but has not yet been played.
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_users_unplaid_count' ) ) :
	function mycred_get_users_unplaid_count( $user_id, $post_id ) {

		global $wpdb, $scratch_card_set_db;

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$scratch_card_set_db} WHERE set_id = %d AND user_id = %d;", $post_id, $user_id ) );
		if ( $count === NULL ) $count = 0;

		return apply_filters( 'mycred_users_unplayed_count', $count, $user_id, $post_id );

	}
endif;

/**
 * Get Remaining Card Count
 * Return the number of cards that are remaining in the database.
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_remaining_cards_in_set' ) ) :
	function mycred_get_remaining_cards_in_set( $set_id ) {

		global $wpdb, $scratch_card_set_db;

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$scratch_card_set_db} WHERE set_id = %d;", $set_id ) );
		if ( $count === NULL ) $count = 0;

		return apply_filters( 'mycred_cards_remaining_in_set_count', $count, $set_id );

	}
endif;

/**
 * Get A Card from user
 * Returns a card id for a user.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_an_unplayed_card_id' ) ) :
	function mycred_get_an_unplayed_card_id( $user_id, $post_id ) {

		global $wpdb, $scratch_card_set_db;

		$card_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$scratch_card_set_db} WHERE set_id = %d AND user_id = %d LIMIT 1;", $post_id, $user_id ) );
		if ( $card_id === NULL ) $card_id = 0;

		return $card_id;

	}
endif;
