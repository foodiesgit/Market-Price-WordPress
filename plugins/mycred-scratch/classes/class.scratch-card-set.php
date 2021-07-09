<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Get Scratch Card Set Setup
 * @since 1.1.3
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Scratch_Card_Set' ) ) :
	final class myCRED_Scratch_Card_Set {

		public $set_id             = false;
		public $set_logo_id        = '';
		public $set_logo_url       = '';
		public $use_logo_as_bg     = false;

		public $cost               = 0;
		public $daily_maximum      = 0;
		public $minimum_scratch    = 0;
		public $structure          = array();

		public $log_templates      = array();
		public $message_templates  = array();
		public $coin               = array();

		public $ready              = false;
		public $setup              = array();
		public $cover_image_id     = '';
		public $cover_image        = '';
		public $template           = '';

		public $charge_point_type  = NULL;
		public $payout_point_type  = NULL;

		public $post               = NULL;
		public $mycred             = NULL;
		public $available_statuses = array();

		public $available          = true;
		public $excluded           = false;
		public $over_limit         = false;
		public $over_purchase_limit = false;
		public $can_afford         = false;
		public $unplayed           = 0;

		/**
		 * Constructor
		 * @version 1.0
		 */
		public function __construct( $set_id = NULL ) {

			if ( $set_id === NULL ) return false;

			$this->set_id = absint( $set_id );

			$this->populate();

		}

		/**
		 * Populate Set
		 * @version 1.0
		 */
		protected function populate() {

			$post = get_post( $this->set_id );
			if ( ! isset( $post->ID ) ) return false;

			$default = array(
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

			$this->setup             = get_post_meta( $post->ID, 'mycred_set_setup', true );
			if ( empty( $this->setup ) ) $this->setup = $default;

			$this->charge_point_type = get_post_meta( $post->ID, 'charge_point_type', true );
			if ( $this->charge_point_type == '' ) $this->charge_point_type = MYCRED_DEFAULT_TYPE_KEY;

			$this->payout_point_type = get_post_meta( $post->ID, 'payout_point_type', true );
			if ( $this->payout_point_type == '' ) $this->payout_point_type = MYCRED_DEFAULT_TYPE_KEY;

			$this->set_logo_id       = get_post_thumbnail_id( $post );
			if ( $this->set_logo_id != '' )
				$this->set_logo_url = $this->prep_url( wp_get_attachment_url( $this->set_logo_id ) );

			$this->use_logo_as_bg    = ( (int) get_post_meta( $post->ID, 'bg_logo', true ) === 1 ) ? true : false;

			$this->mycred            = mycred( $this->charge_point_type );
			$this->post              = $post;

			// Get cost
			$cost                    = get_post_meta( $post->ID, 'cost', true );
			if ( $cost == '' ) $cost = 0;
			$this->cost              = $this->mycred->number( $cost );

			// Daily Maximum
			$daily_maximum           = get_post_meta( $post->ID, 'daily_max', true );
			$this->daily_maximum     = absint( $daily_maximum );

			// Minimum Scratch Percentage
			$minimum = get_post_meta( $post->ID, 'minimum', true );
			if ( $minimum == '' ) $minimum = 50;
			$this->minimum_scratch = abs( $minimum );

			// Log templates
			$log_templates = array(
				'purchase' => get_post_meta( $post->ID, 'buy_log', true ),
				'payout'   => get_post_meta( $post->ID, 'win_log', true )
			);

			if ( $log_templates['purchase'] == '' ) $log_templates['purchase'] = '%post_title% card purchase';
			if ( $log_templates['payout'] == '' ) $log_templates['payout'] = '%post_title% card win';

			$this->log_templates     = $log_templates;

			// Messages
			$message_templates       = array(
				'winner'   => get_post_meta( $post->ID, 'win_message', true ),
				'nowin'    => get_post_meta( $post->ID, 'no_win_message', true ),
				'limit'    => get_post_meta( $post->ID, 'limit_message', true )
			);

			if ( $message_templates['limit'] == '' ) $message_templates['limit'] = 'You have reached your daily purchase limit';

			$this->message_templates = $message_templates;

			// Card Structure
			$structure               = array(
				'width'  => get_post_meta( $post->ID, 'width', true ),
				'height' => get_post_meta( $post->ID, 'height', true ),
				'top'    => get_post_meta( $post->ID, 'top', true ),
				'left'   => get_post_meta( $post->ID, 'left', true )
			);

			if ( $structure['width'] == '' ) $structure['width'] = 0;
			if ( $structure['height'] == '' ) $structure['height'] = 0;

			$this->structure         = $structure;

			// Coin
			$coin                    = array(
				'image'    => get_post_meta( $post->ID, 'coin_image', true ),
				'url'      => '',
				'diameter' => get_post_meta( $post->ID, 'diameter', true )
			);

			if ( $coin['image'] == '' ) $coin['image'] = $coin['url'] = plugins_url( 'assets/images/coin.png', MYCRED_SCRATCH );
			elseif ( is_numeric( $coin['image'] ) ) $coin['url'] = wp_get_attachment_url( $coin['image'] );
			elseif ( $coin['image'] != '' ) $coin['url'] = $coin['image']; 

			$coin['image']           = $this->prep_url( $coin['image'] );
			if ( $coin['diameter'] == '' || $coin['diameter'] < 10 ) $coin['diameter'] = 10;

			$this->coin              = $coin;

			// Cover Image
			$cover_image             = get_post_meta( $post->ID, 'cover_image', true );
			$this->cover_image_id    = $cover_image;
			if ( is_numeric( $this->cover_image_id ) ) $cover_image = wp_get_attachment_url( $this->cover_image_id );
			$this->cover_image       = $this->prep_url( $cover_image );

			// Template
			$this->template          = get_post_meta( $post->ID, 'template', true );

			// Check if ready
			if ( $this->structure['width'] > 0 && $this->structure['height'] > 0 && $this->cover_image != '' && ! empty( $this->setup[0]['attachment_ids'] ) && ! empty( $this->setup[1]['attachment_ids'] ) )
				$this->ready = true;

			// Available statuses for sets
			$this->available_statuses = array(
				'available' => __( 'Available', 'mycred' ),
				'onhold'    => __( 'On Hold', 'mycred' ),
				'soldout'   => __( 'Sold Out', 'mycred' ),
				'trash'     => __( 'Trashed', 'mycred' ),
				'draft'     => __( 'Draft', 'mycred' ),
			);

			$this->cards_remaining = mycred_get_remaining_cards_in_set( $this->set_id );
			$this->unplayed_cards  = mycred_get_remaining_card_count( $this->set_id );

		}

		/**
		 * Is Set Ready?
		 * @version 1.0
		 */
		public function is_set_ready() {
			return $this->ready;
		}

		/**
		 * Is Set Playable?
		 * @version 1.0
		 */
		public function is_set_playable() {

			$playable = true;

			if ( ! $this->is_set_ready() )
				$playable = false;

			elseif ( $this->get_status() == 'soldout' )
				$playable = false;

			return $playable;

		}

		/**
		 * Clone Set
		 * @version 1.0
		 */
		public function clone_set() {

			$clone                = array( 'post_type' => 'scratch_card_set' );
			$clone['post_title']  = $this->post->post_title;
			$clone['post_author'] = $this->post->post_author;
			$clone['post_status'] = 'draft';

			$clone_id             = wp_insert_post( $clone );

			if ( $clone_id === false || is_wp_error( $clone_id ) )
				return false;

			if ( $this->set_logo_id != '' ) {

				$clone                = get_post( $clone_id );
				set_post_thumbnail( $clone, $this->set_logo_id );

			}

			add_post_meta( $clone_id, 'mycred_set_setup',  $this->setup, true );
			add_post_meta( $clone_id, 'charge_point_type', $this->charge_point_type, true );
			add_post_meta( $clone_id, 'payout_point_type', $this->payout_point_type, true );
			add_post_meta( $clone_id, 'cost',              $this->cost, true );
			add_post_meta( $clone_id, 'daily_max',         $this->daily_maximum, true );
			add_post_meta( $clone_id, 'bg_logo',           ( $this->use_logo_as_bg ? 1 : 0 ), true );
			add_post_meta( $clone_id, 'minimum',           $this->minimum_scratch, true );
			add_post_meta( $clone_id, 'buy_log',           $this->log_templates['purchase'], true );
			add_post_meta( $clone_id, 'win_log',           $this->log_templates['payout'], true );
			add_post_meta( $clone_id, 'win_message',       $this->message_templates['winner'], true );
			add_post_meta( $clone_id, 'no_win_message',    $this->message_templates['nowin'], true );
			add_post_meta( $clone_id, 'limit_message',     $this->message_templates['limit'], true );
			add_post_meta( $clone_id, 'width',             $this->structure['width'], true );
			add_post_meta( $clone_id, 'height',            $this->structure['height'], true );
			add_post_meta( $clone_id, 'top',               $this->structure['top'], true );
			add_post_meta( $clone_id, 'left',              $this->structure['left'], true );
			add_post_meta( $clone_id, 'coin_image',        $this->coin['image'], true );
			add_post_meta( $clone_id, 'diameter',          $this->coin['diameter'], true );
			add_post_meta( $clone_id, 'cover_image',       $this->cover_image_id, true );
			add_post_meta( $clone_id, 'template',          $this->template, true );

			return apply_filters( 'mycred_scratch_cloned_set', $clone_id, $this );

		}

		/**
		 * Can Card Be Purchased?
		 * @version 1.0
		 */
		public function can_card_be_purchased() {

			return ( $this->cards_remaining > 0 ) ? true : false;

		}

		/**
		 * Is User Excluded?
		 * @version 1.0
		 */
		public function user_is_excluded( $user_id = NULL ) {

			// First we check exclusions by plugin settings
			$prefs    = mycred_scratch_settings();
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
			if ( ! $excluded && $this->mycred->exclude_user( $user_id ) )
				$excluded = true;

			// Make sure we are not excluded from the paid out point type (if different)
			if ( ! $excluded && $this->charge_point_type != $this->payout_point_type ) {

				$mycred = mycred( $this->payout_point_type );
				if ( $mycred->exclude_user( $user_id ) )
					$excluded = true;

			}

			return apply_filters( 'mycred_user_can_scratch', $excluded, $user_id, $this );

		}

		/**
		 * Get Users Todays Play Count
		 * @version 1.0
		 */
		public function get_users_todays_playcount( $user_id = NULL ) {

			$now   = current_time( 'timestamp' );
			$today = strtotime( 'today midnight', $now );

			global $wpdb;

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$this->mycred->log_table} WHERE ref IN ( %s, %s ) AND ref_id = %d AND user_id = %d AND time >= %d;", 'buy_scratch_card', 'free_scratch_card', $this->set_id, $user_id, $today ) );
			if ( $count === NULL ) $count = 0;

			return apply_filters( 'mycred_scratch_todays_play_count', $count, $user_id, $this );

		}

		/**
		 * User is over Purchase Limit?
		 * @version 1.0
		 */
		public function user_is_over_limit( $user_id = NULL ) {

			$over_limit = false;
			if ( $this->daily_maximum > 0 && $this->get_users_todays_playcount( $user_id ) >= $this->daily_maximum )
				$over_limit = true;

			return $over_limit;

		}

		/**
		 * User Can Afford to Buy?
		 * @version 1.0
		 */
		public function user_can_afford_to_buy( $user_id = NULL ) {

			$can_afford = true;

			if ( $this->cost > 0 ) {

				$balance = $this->mycred->get_users_balance( $user_id );
				if ( $balance < $this->cost )
					$can_afford = false;

			}

			return $can_afford;

		}

		/**
		 * Get Status
		 * @version 1.0
		 */
		public function get_status() {

			return $this->post->post_status;

		}

		/**
		 * Show Status
		 * @version 1.0
		 */
		public function show_status( $return = false ) {

			$status = $this->get_status();
			if ( array_key_exists( $status, $this->available_statuses ) )
				$status = $this->available_statuses[ $status ];

			if ( $return )
				return $status;

			echo $status;

		}

		/**
		 * Change Status
		 * @version 1.0
		 */
		public function change_status( $new_status = '' ) {

			if ( ! array_key_exists( $new_status, $this->available_statuses ) ) return false;

			remove_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );

			$update = wp_update_post( array( 'ID' => $this->set_id, 'status' => $new_status ) );

			add_action( 'save_post_scratch_card_set', 'mycred_save_scratch_card_set', 10, 2 );

			if ( $update == $this->set_id ) {

				clean_post_cache( $this->set_id );

				$this->post = get_post( $this->set_id );

				$update = true;

			}
			else $update = false;

			return $update;

		}

		/**
		 * Destroy Set
		 * @version 1.0
		 */
		public function destroy_set() {

			wp_delete_post( $this->set_id, true );

			global $wpdb, $scratch_card_set_db;

			$wpdb->delete(
				$scratch_card_set_db,
				array( 'set_id' => $this->set_id ),
				array( '%d' )
			);

		}

		/**
		 * Prep URL
		 * Make sure the URL is correct and enforce SSL if used.
		 * @version 1.1
		 */
		public function prep_url( $url = '' ) {

			if ( empty( $url ) ) return $url;

			$current_url = home_url( '/' );
			$current_url = str_replace( 'https://', 'http://', $current_url );

			// WPML Support for "Language URL format"
			if ( defined( 'WPML_PLUGIN_BASENAME' ) ) {

				global $sitepress;

				$lang_code   = $sitepress->get_current_language();
				if ( substr( $current_url, ( 0 - ( strlen( $lang_code ) + 1 ) ) ) == $lang_code . '/' )
					$current_url = str_replace( $lang_code . '/', '', $current_url );

			}

			$length      = strlen( $current_url );

			$image_url   = str_replace( 'https://', 'http://', $url );
			$image_url   = substr( $image_url, 0, $length );

			if ( $current_url != $image_url ) {
				$url = str_replace( 'https://', 'http://', $url );
				$url = str_replace( $image_url, $current_url, $url );
			}

			// If we want to force SSL
			$prefs = mycred_scratch_settings();
			if ( is_ssl() || ( array_key_exists( 'force_ssl', $prefs ) && $prefs['force_ssl'] == 1 ) )
				$url = str_replace( 'http://', 'https://', $url );

			return $url;

		}

		/**
		 * Get Card Set Title
		 * @version 1.0
		 */
		public function get_title() {

			$title = '';
			if ( isset( $this->post->post_title ) )
				$title = $this->post->post_title;

			return $title;

		}

		/**
		 * Show Card Set Title
		 * @version 1.0
		 */
		public function show_title( $return = false ) {

			$title = $this->get_title();
			$title = apply_filters( 'the_title', $title );

			if ( $return )
				return $title;

			echo $title;

		}

		/**
		 * Get Cost
		 * @version 1.0.1
		 */
		public function get_cost( $qty = 1 ) {

			if ( $this->cost == 0 ) return $this->mycred->zero();

			$qty  = abs( $qty );
			$cost = $qty * $this->cost;

			return $cost;

		}

		/**
		 * Show Cost
		 * @version 1.0.1
		 */
		public function show_cost( $qty = 1, $return = false ) {

			$cost = $this->get_cost( $qty );

			if ( $cost == $this->mycred->zero() )
				$cost = __( 'Free', 'mycred' );

			else
				$cost = sprintf( _x( '%s / play', '10 points / play', 'mycred' ), $this->mycred->format_creds( $cost ) );

			if ( $return )
				return $cost;

			echo $cost;

		}

		/**
		 * Get Daily Maximum Purchase
		 * @version 1.0
		 */
		public function get_max_play() {

			return $this->daily_maximum;

		}

		/**
		 * Show Daily Maximum
		 * @version 1.0
		 */
		public function show_daily_maximum( $return = false ) {

			$maximum = $this->get_max_play();
			if ( $maximum === 0 ) $maximum = __( 'Unlimited', 'mycred' );

			if ( $return )
				return $maximum;

			echo $maximum;

		}

		/**
		 * Get Log Template
		 * @version 1.0
		 */
		public function get_log_template( $id = '' ) {

			if ( ! array_key_exists( $id, $this->log_templates ) ) return '-';

			return $this->log_templates[ $id ];

		}

		/**
		 * Get Message
		 * @version 1.0
		 */
		public function get_message( $id = '' ) {

			if ( ! array_key_exists( $id, $this->message_templates ) ) return '';

			return $this->message_templates[ $id ];

		}

		/**
		 * Show Message
		 * @version 1.0
		 */
		public function show_message( $id = '', $return = false ) {

			$message = $this->get_message( $id );
			$message = $this->mycred->template_tags_post( $message, $this->set_id );
			$message = wptexturize( $message );

			if ( $return )
				return $message;

			echo $message;

		}

	}
endif;

/**
 * Get Scratch Card Set
 * @version 1.0
 */
if ( ! function_exists( 'mycred_scratch_card_set' ) ) :
	function mycred_scratch_card_set( $set_id = NULL ) {

		global $current_scratch_card_set;

		if ( isset( $current_scratch_card_set->set_id ) && $current_scratch_card_set->set_id == $set_id )
			return $current_scratch_card_set;

		$set = new myCRED_Scratch_Card_Set( $set_id );
		if ( $set->set_id === false ) return false;

		$current_scratch_card_set = $set;

		return $current_scratch_card_set;

	}
endif;
