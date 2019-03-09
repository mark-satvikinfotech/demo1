<?php
/**
 * Plugin Name: Hebrew Font
 * Plugin URI: http://www.webview.co.il/plugins/hebrew-font/
 * Description: Hebrew Font plugin provides an easy way to use Hebrew fonts on your WordPress website.
 * Version: 0.3
 * Author: Nilly Klein
 * Author URI: http://www.webView.co.il
 * License: GPL2
 */

// Globals
define('HF_PLUGIN_URL', plugins_url() . '/hebrew-font');


// Add fonts css for use with the visual editor
function hf_wp_mce_fonts_styles() {

   $font_url = 'http://fonts.googleapis.com/earlyaccess/alefhebrew.css';
   add_editor_style( str_replace( ',', '%2C', $font_url ) );

   $font_url = 'http://fonts.googleapis.com/earlyaccess/notosanshebrew.css';
   add_editor_style( str_replace( ',', '%2C', $font_url ) );

   $font_url = 'http://fonts.googleapis.com/earlyaccess/opensanshebrewcondensed.css';
   add_editor_style( str_replace( ',', '%2C', $font_url ) );

   $font_url = plugins_url( '/fonts/miriamclm/stylesheet.css', __FILE__ );
   add_editor_style( str_replace( ',', '%2C', $font_url ) );

   $font_url = plugins_url( '/fonts/comixno2/stylesheet.css', __FILE__ );
   add_editor_style( str_replace( ',', '%2C', $font_url ) );

   $font_url = plugins_url( '/fonts/nehama/stylesheet.css', __FILE__ );
   add_editor_style( str_replace( ',', '%2C', $font_url ) );
}
//add_action( 'init', 'hf_wp_mce_fonts_styles' );

function hf_wp_mce_add_css( $mce_css ) {
    if ( ! empty( $mce_css ) )
        $mce_css .= ',';

    $mce_css .=   '//fonts.googleapis.com/earlyaccess/alefhebrew.css,' .
                  '//fonts.googleapis.com/earlyaccess/notosanshebrew.css,' .
                  '//fonts.googleapis.com/earlyaccess/opensanshebrewcondensed.css,' .
                  plugins_url( '/fonts/miriamclm/stylesheet.css', __FILE__ ) . ',' .
                  plugins_url( '/fonts/comixno2/stylesheet.css', __FILE__ ) . ','. 
                  plugins_url( '/fonts/nehama/stylesheet.css', __FILE__ );

    return $mce_css;
}

add_filter( 'mce_css', 'hf_wp_mce_add_css' );

// Enqueue plugin css
function hf_enqueues() {     

        wp_enqueue_style('alefhebrew', 'http://fonts.googleapis.com/earlyaccess/alefhebrew.css');
        wp_enqueue_style('notosanshebrew', 'http://fonts.googleapis.com/earlyaccess/notosanshebrew.css');
        wp_enqueue_style('opensanshebrewcondensed', 'http://fonts.googleapis.com/earlyaccess/opensanshebrewcondensed.css');
        wp_enqueue_style('miriamclm', HF_PLUGIN_URL . '/fonts/miriamclm/stylesheet.css');
        wp_enqueue_style('comixno2', HF_PLUGIN_URL . '/fonts/comixno2/stylesheet.css');
        wp_enqueue_style('nehama', HF_PLUGIN_URL . '/fonts/nehama/stylesheet.css');
}
add_action('wp_enqueue_scripts', 'hf_enqueues');


// Enable font family drop down in the visual editor
function hf_wp_mce_buttons( $buttons ) {
        array_unshift( $buttons, 'fontselect' );
        return $buttons;
}
add_filter( 'mce_buttons_3', 'hf_wp_mce_buttons' );


// Add Hebrew fonts to the Fonts list
function hf_wp_mce_fonts_array( $initArray ) {
    $initArray['font_formats'] = 
        'Alef=Alef Hebrew;' .                
        'Andale Mono=andale mono,times;' .
        'Arial=arial,helvetica,sans-serif;' .
        'Arial Black=arial black,avant garde;' .
        'Book Antiqua=book antiqua,palatino;' .
        'Comic Sans MS=comic sans ms,sans-serif;' .
        'Comix No 2=comix_no2_clm;' .
        'Courier New=courier new,courier;' .
        'Georgia=georgia,palatino;' .
        'Helvetica=helvetica;' .
        'Impact=impact,chicago;' .
        'Lato=Lato;' .
        'Miriam=miriam_clm;' .       
        'Nehama=nehamaregular;' . 
        'Noto Sans Hebrew=Noto Sans Hebrew;' .                
        'Open Sans Hebrew Condensed=Open Sans Hebrew Condensed;' .                
        'Tahoma=tahoma,arial,helvetica,sans-serif;' .
        'Terminal=terminal,monaco;' .
        'Times New Roman=times new roman,times;' .
        'Trebuchet MS=trebuchet ms,geneva;' .
        'Verdana=verdana,geneva;' .
        'Webdings=webdings;' .
        'Wingdings=wingdings,zapf dingbats';
    return $initArray;
}
add_filter( 'tiny_mce_before_init', 'hf_wp_mce_fonts_array' );
