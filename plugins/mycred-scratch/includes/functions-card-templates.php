<?php
if ( ! defined( 'MYCRED_SCRATCH_VERSION' ) ) exit;

/**
 * Get Card Templates
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_scratch_card_templates' ) ) :
	function mycred_get_scratch_card_templates() {

		return apply_filters( 'mycred_scratch_card_templates', array(
			'fireworks' => array(
				'label'               => __( 'Fireworks', 'mycred' ),
				'minimum_scratch'     => 75,
				'brush_diameter'      => 15,
				'card_width'          => 300,
				'card_height'         => 400,
				'coin_image'          => '', // optional
				'cover_image'         => plugins_url( 'assets/images/templates/fireworks/fireworks-cover.png', MYCRED_SCRATCH ),
				'buy_log'             => '', // optional
				'win_log'             => '', // optional
				'win_message'         => '', // optional
				'setup'               => array(
					array(
						'number'         => 100,
						'value'          => 0,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/fireworks/fireworks-nowin.png', MYCRED_SCRATCH )
						)
					),
					array(
						'number'         => 10,
						'value'          => 10,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/fireworks/fireworks-win.png', MYCRED_SCRATCH )
						)
					)
				)
			),
			'starburst' => array(
				'label'               => __( 'Starburst', 'mycred' ),
				'minimum_scratch'     => 45,
				'brush_diameter'      => 15,
				'card_width'          => 400,
				'card_height'         => 250,
				'coin_image'          => '', // optional
				'cover_image'         => plugins_url( 'assets/images/templates/starburst/starburst-cover.png', MYCRED_SCRATCH ),
				'buy_log'             => '', // optional
				'win_log'             => '', // optional
				'win_message'         => '', // optional
				'setup'               => array(
					array(
						'number'         => 100,
						'value'          => 0,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/starburst/starburst-nowin1.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/starburst/starburst-nowin2.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/starburst/starburst-nowin3.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/starburst/starburst-nowin4.png', MYCRED_SCRATCH )
						)
					),
					array(
						'number'         => 90,
						'value'          => 100,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/starburst/starburst-win.png', MYCRED_SCRATCH )
						)
					)
				)
			),
			'treasure' => array(
				'label'               => __( 'Treasure Cove', 'mycred' ),
				'minimum_scratch'     => 75,
				'brush_diameter'      => 15,
				'card_width'          => 300,
				'card_height'         => 400,
				'coin_image'          => '', // optional
				'cover_image'         => plugins_url( 'assets/images/templates/treasure/treasure-cover.png', MYCRED_SCRATCH ),
				'buy_log'             => '', // optional
				'win_log'             => '', // optional
				'win_message'         => '', // optional
				'setup'               => array(
					array(
						'number'         => 200,
						'value'          => 0,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/treasure/treasure-nowin.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/treasure/treasure-nowin1.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/treasure/treasure-nowin2.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/treasure/treasure-nowin3.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/treasure/treasure-nowin4.png', MYCRED_SCRATCH )
						)
					),
					array(
						'number'         => 100,
						'value'          => 10,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/treasure/treasure-win10.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/treasure/treasure-win10-2.png', MYCRED_SCRATCH ) 
						)
					),
					array(
						'number'         => 100,
						'value'          => 50,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/treasure/treasure-win50.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/treasure/treasure-win50-2.png', MYCRED_SCRATCH ) 
						)
					),
					array(
						'number'         => 50,
						'value'          => 100,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/treasure/treasure-win100.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/treasure/treasure-win100-2.png', MYCRED_SCRATCH ) 
						)
					),
					array(
						'number'         => 10,
						'value'          => 500,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/treasure/treasure-win500.png', MYCRED_SCRATCH ), 
							plugins_url( 'assets/images/templates/treasure/treasure-win500-2.png', MYCRED_SCRATCH ) 
						)
					),
					array(
						'number'         => 1,
						'value'          => 1000,
						'attachment_ids' => array(
							plugins_url( 'assets/images/templates/treasure/treasure-win1000.png', MYCRED_SCRATCH )
						)
					)
				)
			)
		) );

	}
endif;

/**
 * Load Card Template
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_load_scratch_card_template' ) ) :
	function mycred_load_scratch_card_template( $template = '' ) {

		$defaults = mycred_get_scratch_card_templates();

		if ( array_key_exists( $template, $defaults ) )
			return $defaults[ $template ];

		return false;

	}
endif;

/**
 * Select Template Dropdown
 * @since 1.0
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_scratch_template_dropdown' ) ) :
	function mycred_scratch_template_dropdown( $name = '', $id = '', $selected = '', $return = false ) {

		$templates = mycred_get_scratch_card_templates();

		$output    = '<select name="' . $name . '" id="' . $id . '">';

		$output   .= '<option value=""';
		if ( $selected == '' ) $output .= ' selected="selected"';
		$output   .= '>' . __( 'Custom Template', 'mycred' ) . '</option>';

		foreach ( $templates as $template_id => $template ) {

			$output .= '<option value="' . $template_id . '"';
			if ( $selected == $template_id ) $output .= ' selected="selected"';
			$output .= '>' . $template['label'] . '</option>';

		}

		$output  .= '</select>';

		if ( $return )
			return $output;

		echo $output;

	}
endif;
