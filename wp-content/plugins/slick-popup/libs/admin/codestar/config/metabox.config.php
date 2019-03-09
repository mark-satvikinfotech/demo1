<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// METABOX OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options      = array();
global $post_types;
$dependancy = array( '_splite_meta_override', '==', 'true' );
// -----------------------------------------
// Page Side Metabox Options               -
// -----------------------------------------
$options[]    = array(
	'id'        => '_splite_page_options',
	'title'     => 'Slick Popup Lite',
	'post_type' => 'page',
	'context'   => 'side',
	'priority'  => 'default',
	'sections'  => array(
		array(
			'name'   => 'Overrides',
			'fields' => array(

				array(
				  'id'      => '_splite_meta_override',
				  'type'    => 'switcher',
				  'title'   => 'Override Defaults?',
				  'label'   => 'Use different form for this page?',
				  'default' => false
				),

				array(
					'id'            => '_splite_meta_form_id',
					'type'           => 'select',
					'title'          => 'Choose CF7 Form',
					'dependency'   => $dependancy,
					'options'        => 'posts',
					'query_args'     => array(
						'post_type'    => 'wpcf7_contact_form',
						'orderby'      => 'post_date',
						'order'        => 'DESC',
						'posts_per_page' => -1
					),
					'default_option' => 'Select a post',
				),		
				
				array(
				  'id'            => '_splite_meta_side_button',
				  'type'          => 'text',
				  'title'   	  => 'Side Button Text',
				  'dependency'   => $dependancy,
				  'attributes'    => array(
					'placeholder' => ''
				  )
				),
				
				array(
				  'id'            => '_splite_meta_popup_heading',
				  'type'          => 'text',
				  'title'   	  => 'Popup Heading',
				  'dependency'   => $dependancy,
				  'attributes'    => array(
					'placeholder' => ''
				  )
				),
				
				array(
					'id'            => '_splite_meta_cta',
					'type'      => 'textarea',
					'title'     => 'CTA Text',
					'dependency'   => $dependancy,
					'info'      => 'Change the call-to-action text above form for this page.',
				),
				
			),
		),
	)
);

CSFramework_Metabox::instance( $options );
