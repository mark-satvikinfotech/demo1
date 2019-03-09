<?php
   /*
   Plugin Name: Urdu Keyboard
   Plugin URI: https://wordpress.org/plugins/urdu-keyboard/
   Description: This Urdu Keyoboard (Phonetic) for WordPress, enables you to easily type Urdu in WordPress without installing Urduphonetic keyboard in your system.
   Version: 1.2
   Author: Zayed Baloch
   Author URI: https://www.mozahid.com/
   License: GPL2
   */

defined('ABSPATH') or die("No script kiddies please!");
define( 'RLUK_VERSION',   '1.2' );
define( 'RLUK_URL', plugins_url( '', __FILE__ ) );
define( 'RL_TEXTDOMAIN',  'rl_urdu_keyboard' );

class RadLabs_Urdu_Keyboard {

  function __construct() {
    add_action( 'wp_loaded', array( $this, 'init') );
  }

  public function init() {
    add_action( 'admin_menu', array( $this, 'com_radlabs_urduwriting_adminpanel') );
    add_action( 'admin_init', array( $this, 'add_editor_buttons' ) );
    add_action( 'admin_footer', array( $this, 'popup' ) );

    // styling
    add_action( 'admin_print_styles', array( $this, 'admin_styles') );
    add_action( 'admin_print_styles',  array( $this, 'admin_scripts') );

    add_filter( 'enter_title_here',  array( $this, 'radlabs_urdu_writing_title_text_input') );

    // Default WordPress post editor to HTML
    add_filter( 'wp_default_editor', create_function('', 'return "html";') );

  }

 /*
  * Register admin css
  */
  public function admin_styles() {
      wp_enqueue_style( 'rluk-admin', RLUK_URL . '/assets/style/urdustyle.css', array(), RLUK_VERSION, 'all' );
  }

  /*
   * Register admin scripts
   */
  public function admin_scripts() {
      wp_enqueue_script( 'rluk-editor', RLUK_URL . '/assets/script/UrduEditor.js', array( 'jquery' ), RLUK_VERSION, true );
      wp_enqueue_script( 'rluk-init', RLUK_URL . '/assets/script/init.js', array( 'jquery' ), RLUK_VERSION, true );
  }

  /*
   * Add buttons to TimyMCE
   */
  function add_editor_buttons() {
    // add shortcode button
    add_action( 'media_buttons', array( $this, 'add_keyboard_help_button' ), 10 );
  }

  /*
   * Add button to TimyMCE
   */

  public function add_keyboard_help_button( $page = null, $target = null ) {
    ?>
      <a href="#TB_inline?width=640&amp;height=600&amp;inlineId=rluk-wrap" id="urdu_keyboard_help" class="thickbox button" title="<?php _e( 'Urdu Keyboard Help', RL_TEXTDOMAIN ); ?>" data-page="<?php echo $page; ?>" data-target="<?php echo $target; ?>">
        <img src="<?php echo RLUK_URL . "/assets/images/ic-keyboard.png";?>" alt="" />
      </a>
    <?php
  }

  /*
   * TB window Popup
   */

  public function popup() {
  ?>
    <div id="rluk-wrap" style="display:none">
      <div class="rluk">
        <img src="<?php echo RLUK_URL . "/assets/images/keyboard-layout.jpg";?>" alt="" style="max-width: 100%;"/>
      </div>
    </div>

    <?php
  }

  /*
   * Post Title placeholder
   */
  public function radlabs_urdu_writing_title_text_input( $title ){
     return $title = 'اب آپ اردو میں لکھ سکتے ہیں';
  }

  /*
   * Plugin Setting (Information) page.
   */
  public function com_radlabs_urduwriting_adminpanel(){
    add_menu_page( 'Urdu Keyboard for WordPress', 'Urdu Keyboard', 'manage_options', 'urdu_options', array($this, 'rluk_options_page'), RLUK_URL . '/assets/images/admin-icon.png', 101 );
  }

  public function rluk_options_page(){
      ?>
      <div class="wrap">
      <h2>Urdu Keyboard</h2>
      <div class="rluk_keyboard">
        <h3>Phonetic Layout</h3>
        <img src="<?php echo RLUK_URL . "/assets/images/keyboard-layout.jpg";?>" alt="" style="max-width: 100%;"/>
      </div>
      <div class="rluk_content">
        <h3>How to use</h3>
        <h4>This Urdu Keyoboard (Phonetic) for WordPress, enables you to easily type Urdu in WordPress without installing Urduphonetic keyboard in your system.</h4>

        <h4>Pressing <code>Ctrl + Space</code> to change keyboard writing from <code>English to Urdu</code> or <code>Urdu to English</code>.</h4>

        <h4>In this version, Urdu keyboard is enabled only on Posts and Pages <code>Title</code>, <code>Content</code>, <code>Excerpt</code> and <code>Tags</code> </h4>

        <h3>For support & queries:</h3>
        <h4><a href="mailto:wp@radlabs.biz">Contact Us</a></h4>

        <hr/>
        <h4>Powered by: <a href="http://www.radlabs.biz/">Rad Labs</a></h4>
        <p><i>Special Thanks to: UrduWeb for support on Urdu Editor</i></p>

        <hr/>
        <h3>Compatible Browsers:</h3>
        <p><img src="<?php echo RLUK_URL . "/assets/images/ic-browsers.png";?>" alt=""/></p>
      </div>
      </div>
      <?php
  }

}

new RadLabs_Urdu_Keyboard();