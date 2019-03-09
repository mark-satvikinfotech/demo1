<?php
/*
Plugin Name: Coupon Campaigns
Plugin URI: https://www.hikebranding.com/
Description:Helps to display coupons to visitors and Facebook messengers using ManyChat.
Version: 1.0.1
Author: Hikebranding
Author URI: https://www.hikebranding.com/
License: GPL3
Text Domain: coupon-campaigns
Domain Path: /languages
*/


require_once('config.php');

// If this file is called directly, abort.

if ( ! defined( 'ABSPATH' ) ) exit;


class Coupon_Campaigns{

    protected static $instance = null;

    //private $plugin_version = '1.0.0';

   // private $db_version = '1.0.0';


    public static function instance() {

            if ( is_null ( self::$_instance ) ) {

                self::$_instance = new self;

               self::$instance->load_required_files(); 

             self::$instance->cpt= new Coupon_Campaigns_Custom_Post_Types();

            }

            return self::$_instance;

        }

        public function __construct() {

        $this->define_constants();

        $this->load_plugin_definitions();

        $this->init();

        $this->load_required_files();

        }


        private function define_constants() {

        define( 'Coupon_Campaigns_PLUGIN_BASENAME', plugin_basename(__FILE__) );

        define( 'Coupon_Campaigns_PLUGIN_FILE', __FILE__ );

        define( 'Coupon_Campaigns_PLUGIN_SLUG', 'coupon-campaigns' );

        define( 'Coupon_Campaigns_PLUGIN_TITLE', 'coupon campaigns' );

        define( 'Coupon_Campaigns_PLUGIN_URL', plugin_dir_url(__FILE__) );

        define( 'Coupon_Campaigns_PLUGIN_PATH', plugin_dir_path(__FILE__) );

        }

        public function load_plugin_definitions() {

            include_once( 'includes/class-definitions.php' );

        }

        public function load_required_files() {

        require_once IFE_PLUGIN_DIR . 'includes/class-custom-post-types.php';

        include_once( 'includes/class-custom-post-types.php' );

        include_once( 'includes/class-custom-fields.php' );

        include_once( 'includes/class-shortcode.php' );

        include_once( 'includes/class-coupon.php' );

        if( is_admin() ) {

            include_once( 'includes/class-admin.php' );

        }

     }



       public function init(){

            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

            register_activation_hook( __FILE__, array( $this, 'activate' ) );

            register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        }

           public function activate( $network_wide ) {

             if ( ! $this->dependencies() ) {

            die($this->error_notice());

        }


                }

   
          public function deactivate( $network_wide ) {

            flush_rewrite_rules();

        }

}


if($_REQUEST['ismanychat']=="Y"){



include_once('manychatwebhook.php');

}


include_once dirname( __FILE__ ).'/includes/class-definitions.php';

register_activation_hook( __FILE__, array( 'Coupon_Campaigns_Definitions', 'coupon_table_schema' ) );


    add_shortcode( 'shortcodename', 'register_plugin_post_types' );

 function register_plugin_post_types() {

// Set UI labels for Custom Post Type

     $labels = array(

        'name'                => _x( 'Campaigns', 'Post Type General Name', 'twentythirteen' ),

        'singular_name'       => _x( 'Campaign', 'Post Type Singular Name', 'twentythirteen' ),

        'menu_name'           => __( 'Campaigns', 'twentythirteen' ),

        'parent_item_colon'   => __( 'Parent Movie', 'twentythirteen' ),

        'all_items'           => __( 'All Campaigns', 'twentythirteen' ),

        'add_new_item'        => __( 'Add New Campaign', 'twentythirteen' ),

        'add_new'             => __( 'Add New', 'twentythirteen' ),

        'search_items'        => __( 'Search Campaign', 'twentythirteen' ),

        'not_found'           => __( 'Not Found', 'twentythirteen' ),

        'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),

    );

// Set other options for Custom Post Type

    $args = array(
        'label'               => __( 'campaigns', 'twentythirteen' ),
        'description'         => __( 'Campaign', 'twentythirteen' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'genres' ),

        /* A hierarchical CPT is like Pages and can have

        * Parent and child items. A non-hierarchical CPT

        * is like Posts.

        */ 

        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => false,
        'capability_type'     => 'page',

    );
    // Registering your Custom Post Type
    register_post_type( 'campaign', $args );
}


/* Hook into the 'init' action so that the function

* Containing our post type registration is not 

* unnecessarily executed. 

*/

add_action( 'init', 'register_plugin_post_types', 0 );

$coupon_flag = get_option( 'coupon_flag' ); 


 if($coupon_flag=='Y'){

    add_action( 'admin_init', 'add_campaign_meta_boxes' );


        function add_campaign_meta_boxes() {

            add_meta_box("campaign_contact_meta", "Campaign Details", "add_contact_details_campaign_meta_box", "campaign", "normal", "low");

               // add_meta_box( 'example_meta', __( 'Example Title', 'example-meta' ), 'add_contact_details_campaign_meta_box', 'campaign' );

        }

    }else{

add_action('admin_notices', 'my_custom_notice');

        
            function my_custom_notice()
            {
                    global $current_screen;

                    if ( 'campaign' == $current_screen->post_type )
                        {
                            echo "<h1 class='licenseclass'>Please Enter Licensekey</h1>";

                        }
            }

    }


function add_contact_details_campaign_meta_box()

{

    global $post;
    $custom = get_post_custom( $post->ID );
    $campaign_id = $post->ID ;
    $pr_url_link = $custom["pr_url_link"][0];
    $product_name = $custom["product_name"][0];
    $text_button = $custom["text_button"][0];
    $name_required=get_post_meta( $post->ID, 'name_required', true );
    $unique_email=get_post_meta( $post->ID, 'unique_email', true );
    $unique_ip=get_post_meta( $post->ID, 'unique_ip', true );
    $email_from = $custom["email_from"][0];
    $subject = $custom["subject"][0];
    $message = $custom["message"][0];
    $couponcodes1 = $custom["couponcodes"][0];
    $thpopmessage = $custom["thpopmessage"][0];
    date_default_timezone_set('Asia/Kolkata');
     $time1=date('Y-m-d');
    global $wpdb;
    $table_name = $wpdb->prefix . "coupan_code";
    $wpdb->delete( $table_name, array( 'status' => '0' , 'campaign_id'=>$campaign_id) );
//$campaign_id
    $text = trim($couponcodes1); // remove the last \n or whitespace character
    //$text1 = nl2br($text);
    $results = $wpdb->get_results( "SELECT coupon_code FROM $table_name WHERE status = '1'" );
   $couponcodes = explode(PHP_EOL, $text);
    foreach($couponcodes as $couponcode){

        $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE  coupon_code = '$couponcode'" );
        if(empty($results)){
            $wpdb->insert($table_name, array('campaign_id' => $campaign_id, 'coupon_code' => $couponcode, 'status' => '0', 'time' => $time1) );
        }
    }  
    $ademail_cp=get_post_meta( $post->ID, 'ademail_cp', true );
    $ademail_last_cp=get_post_meta( $post->ID, 'ademail_last_cp', true );
    $cp_limit = $custom["cp_limit"][0];
    $admin_email = $custom["admin_email"][0];

    //$meta_radio= $custom["meta-radio"][0];
   // $user_thpop_required=get_post_meta( $post->ID, 'user_thpop_required', true );
   // $user_th_required=get_post_meta( $post->ID, 'user_th_required', true );
    $thlink = $custom["thlink"][0];
        $example_stored_meta = get_post_meta( $post->ID );
    $shortcode = '[HB id="' . $campaign_id . '"]';

    ///$text="$shortcode";

    //echo "$shortcode";

    ?>
   <!-- <p>Shortcode to display box on page: <?php echo "$shortcode" ?> </p> -->
    <style>.width99 {width:99%;}</style>
    <p>
        <label>Link:</label><br />
        <input type="text" name="pr_url_link" value="<?php echo $pr_url_link; ?>" class="width99"/>
    </p>
    <p>
        <label>Product name:</label><br />
        <input type="text" name="product_name" value="<?php echo $product_name; ?>" class="width99" />
    </p>
    <p>
        <label>Text on Button:</label><br />
        <input type="text" name="text_button" value="<?php echo $text_button; ?>" class="width99" />
    </p>
    <p>
    <input type="checkbox" name="name_required" value="yes" <?php echo (($name_required=='yes') ? 'checked="checked"': '');?>/> Add 'Name' field to submission form
    </p>
    <p>
    <input type="checkbox" name="unique_email" value="yes" <?php echo (($unique_email=='yes') ? 'checked="checked"': '');?>/> Allow one promo code per email address Optional
    </p>

    <p>
    <input type="checkbox" name="unique_ip" value="yes" <?php echo (($unique_ip=='yes') ? 'checked="checked"': '');?>/> Allow one promo code per IP Address

    </p>
    </br><h1>Email Message</h1>
    <p>
        <label>Email From:</label><br />
        <input type="text" name="email_from" value="<?php echo $email_from; ?>" class="width99" />
    </p>

    <p>
        <label>Subject Line:</label><br />
        <input type="text" name="subject" value="<?php echo $subject; ?>" class="width99" />
    </p>
    <p>
        <label>Message:</label><br />
        <textarea rows="5" name="message" class="width99"><?php echo $message; ?></textarea>
    </p>
      
      <p class="notes">To show Product name use  [[PRODUCT]] ,</br>
     To show Coupon Code use [[COUPON_CODE]] ,</br>
     To show Product link use [[LINK]]</p>

      <p>
        <label>Coupon Codes:</label><br />
        <textarea rows="10" name="couponcodes" class="width99"><?php echo $couponcodes1; ?></textarea>
    </p>
    <p>
    <input type="checkbox" name="ademail_cp" value="yes" <?php echo (($ademail_cp=='yes') ? 'checked="checked"': '');?>/> Email me every time coupon sent

    </p>
    <p>

    <input type="checkbox" name="ademail_last_cp" value="yes" <?php echo (($ademail_last_cp=='yes') ? 'checked="checked"': '');?>/> Email me after last coupon sent,and campaign stopped
    </p>
    <p>
        <label>Limit:</label><br />
        <input type="text" name="cp_limit" value="<?php echo $cp_limit; ?>" class="" />
    </p>
    <p>
        <label>My email address is:</label><br />
        <input type="text" name="admin_email" value="<?php echo $admin_email; ?>" class="width99" />
    </p>
    </br><h1>After visitor submit email address</h1>

    <p>
    <label for="meta-radio-one">
     <input type="radio" name="meta-radio" id="meta-radio-one" value="radio-one" <?php if ( isset ( $example_stored_meta['meta-radio'] ) ) checked( $example_stored_meta['meta-radio'][0], 'radio-one' ); ?>>
            <?php _e( 'Display Generic "Thank You" Pop-up Message', 'campaign_contact_meta' )?>
    </label>
    </p>

    <p>
        <label>Write message to show on Thank-you Popup:</label><br />
        <textarea rows="5" name="thpopmessage" class="width99"><?php echo $thpopmessage; ?></textarea>
    </p>

    <p>
     <label for="meta-radio-two">
        <input type="radio" name="meta-radio" id="meta-radio-two" value="radio-two" <?php if ( isset ( $example_stored_meta['meta-radio'] ) ) checked( $example_stored_meta['meta-radio'][0], 'radio-two' ); ?>>
            <?php _e( 'Send User To "Thank You" Page', 'campaign_contact_meta' )?>
     </label>
     <input type="text" name="thlink" value="<?php echo $thlink; ?>" class="thlink" />
    </p>


   <p><h2 class="displaysortcodeclass">Shortcode to display Coupon:</h2><div class="sortcodeclass">[HB-ThankYou]</div></p>
    <p><h2 class="displaysortcodeclass">Shortcode to display box on page:</h2> <div class="sortcodeclass"><?php echo "$shortcode" ?></div></p>

    <input type="hidden" id="post_ID" name="post_ID" value="<?php echo (int) $campaign_id; ?>" />



           <?php


             global $wpdb;
            $table_name7 = $wpdb->prefix . "coupon_campaigns_users";
            $customers = $wpdb->get_results("SELECT * FROM $table_name7 WHERE campaign_id = '$campaign_id'");

            echo  "<h2 class='tabletitle'>Coupons sent</h2>";

            echo "<table id='tableid' width='100%' border=1>";

            //echo  "<caption>Coupons sent</caption>";


            echo "<tr>";
            echo "<th>Name</th> <th>Email</th>  <th>CouponbCode</th> <th>Time sent</th> <th>Source</th> <th>Action</th>";
            echo "</tr>";
            foreach($customers as $customer){
                ?>
            <tr>
            <td><?php echo $customer->name ?></td>
            <td><?php echo $customer->email ?></td>
            <td><?php echo $customer->coupon_code ?></td>
            <td><?php echo $customer->time ?></td>
          
            <td><?php echo $customer->source ?></td>

            <td><input type="button" value="delete" onClick="reply_click(<?php echo $customer->id; ?>)" class='delete' ></span>

            </tr>
            <?php
            }
            ?>
            <table>

<?php


  }

    add_action('wp_ajax_deletecutomer', 'deletecutomer' );
    add_action('wp_ajax_nopriv_deletecutomer', 'deletecutomer' );
    function deletecutomer(){

        $id = $_POST['id'];

        global $wpdb;
        $table_name = $wpdb->prefix . "coupon_campaigns_users";
        //echo "string";
      $wpdb->delete($table_name, array('id'=>$id) );



    }








    function save_campagin_custom_fields(){
      global $post;
      if 
( $post )

  {
    update_post_meta($post->ID, "pr_url_link", @$_POST["pr_url_link"]);
    update_post_meta($post->ID, "product_name", @$_POST["product_name"]);
    update_post_meta($post->ID, "text_button", @$_POST["text_button"]);
    update_post_meta($post->ID, "name_required", @$_POST["name_required"]);
    update_post_meta($post->ID, "unique_email", @$_POST["unique_email"]);
    update_post_meta($post->ID, "unique_ip", @$_POST["unique_ip"]);
    update_post_meta($post->ID, "subject", @$_POST["subject"]);
    update_post_meta($post->ID, "email_from", @$_POST["email_from"]);
    update_post_meta($post->ID, "message", @$_POST["message"]);
    update_post_meta($post->ID, "couponcodes", @$_POST["couponcodes"]);
    update_post_meta($post->ID, "ademail_cp", @$_POST["ademail_cp"]);
    update_post_meta($post->ID, "cp_limit", @$_POST["cp_limit"]);
    update_post_meta($post->ID, "ademail_last_cp", @$_POST["ademail_last_cp"]);
    update_post_meta($post->ID, "admin_email", @$_POST["admin_email"]);
    update_post_meta($post->ID, "thlink", @$_POST["thlink"]);
    update_post_meta( $post->ID, 'meta-radio', @$_POST[ 'meta-radio' ] );
    update_post_meta($post->ID, "thpopmessage", @$_POST["thpopmessage"]);

  }

 }


add_action( 'save_post', 'save_campagin_custom_fields' );
function load_testimonials($a){

    $args = array(
        "post_type" => "campaign",
        "id"       =>  $post->ID
    );
    if( isset( $a['rand'] ) && $a['rand'] == true ) {
        $args['orderby'] = 'rand';

    }
    if( isset( $a['max'] ) ) {
        $args['posts_per_page'] =(int) $a['max'];
    }
    if( $a['id'] ) {
    $posts_in = array_map( 'intval', explode( ',',$a['id'] ) );
    $args['post__in'] = $posts_in;
    }
    //getting all testimonials
    $posts = get_posts($args);
       echo '<div id="testimonials" class="flexslider">';
       foreach($posts as $post)
       {
            $form = '';
            if ( ! empty( $post->post_content ) ) { echo '<p>'.$post->post_content.'<br />'; }
            $form .="<h2>GET Your Coupon Code</h2>";
            $form .="<div id='popupmessage'></div>";
            $form .= "<form action='' method='POST' name='signupform' id='signupform'>";
            if($post->name_required=='yes'){
            $form .= "Name:<input type='text' name='name' id='name' value=''>";
            }
            $form .= "Email:<input type='email' name='email' required id='email' value=''>";
            $form .= "<input type='hidden' value='$post->ID' name='campagin_id' id='campagin_id'>";
            $form .= "<input type='button' id='coupon-submit' class='couponsubmitclass' value='$post->text_button'>";
            $form .= "</form>";
            return $form;
       }  
}

    add_shortcode("HB","load_testimonials");
    add_filter('widget_text', 'do_shortcode');
    

    //Get form data using ajax
    add_action('wp_ajax_sendmail', 'sendmail' );
    add_action('wp_ajax_nopriv_sendmail', 'sendmail' );
    function sendmail($name='',$email='',$ip='',$postid=''){
       
        if($postid == ""){
            $postid = $_POST['campagin_id'];
        }
 
        if($name == ""){
            $name=$_POST['name'];
        }
        
        if($email == ""){
        $email=$_POST['email'];
          }

          



        
       function getUserIpAddr(){

            if(!empty($_SERVER['HTTP_CLIENT_IP'])){

                //ip from share internet

                $ip = $_SERVER['HTTP_CLIENT_IP'];

            }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){

                //ip pass from proxy

                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

            }else{

                $ip = $_SERVER['REMOTE_ADDR'];

            }

            return $ip;

        }

        if($ip == ""){
        $ip=getUserIpAddr();

        }

        

    $unique_email= get_post_meta( $postid, 'unique_email', true );


        


    $email_count = 0;

    $data['email_count']='';

    if($unique_email=="yes"){

        global $wpdb;

        $table_name5 = $wpdb->prefix . "coupon_campaigns_users";

        //echo "SELECT email FROM $table_name5 WHERE email = '$email'";

        if($email != ""){

            $results5 = $wpdb->get_results( "SELECT COUNT(email) as email_count FROM $table_name5 WHERE email = '$email'");

            $email_count = $results5[0]->email_count;

            if($email_count != 0){

                $data['email_count']='Sorry, one Coupon code allowed per email';

            }

        }
    }

    $unique_ip= get_post_meta( $postid, 'unique_ip', true );

    $ip_count = 0;

    $data['ip_count']='';

    if($unique_ip=="yes"){

        global $wpdb;

        $table_name5 = $wpdb->prefix . "coupon_campaigns_users";

        //echo "SELECT email FROM $table_name5 WHERE email = '$email'";

        if($ip != ""){

            $results6 = $wpdb->get_results( "SELECT COUNT(ip) as ip_count FROM $table_name5 WHERE ip = '$ip'");

            $ip_count = $results6[0]->ip_count;


            if($_REQUEST['ismanychat']=="Y"){
            if($ip_count != 0){

                $data['ip_count']='Sorry, one Coupon code allowed per account';

            }
        }

            else{

                $data['ip_count']='Sorry, one Coupon code allowed per ip';
            }

        }

    }


        $limit= get_post_meta( $postid, 'cp_limit', true );

        //echo $limit;

        $message=get_post_meta( $postid, 'message', true );

        //echo $message;

        global $wpdb;

        date_default_timezone_set('Asia/Kolkata');

        $time1=date('Y-m-d');

        $table_name = $wpdb->prefix . "coupan_code";

        $results = $wpdb->get_results( "SELECT coupon_code FROM $table_name WHERE campaign_id = '$postid' AND status ='1' AND time='$time1' ");

        $coupancount=count($results); 


            if($coupancount < $limit && $email_count == '0' && $ip_count == '0'){

            $table_name2 = $wpdb->prefix . "coupan_code";

            $coupancode_result = $wpdb->get_results( "SELECT coupon_code FROM $table_name2 WHERE campaign_id = '$postid' AND status ='0' limit 1 ");

             global $wpdb;
             $coupon_result=$coupancode_result[0]->coupon_code;
             if($name == ""){
             $name=$_POST['name'];
             }

             if($email == ""){

             $email=$_POST['email'];
             }

             if($ip == ""){
              $ip=getUserIpAddr();

             }

             if($postid == ""){
             $postid = $_POST['campagin_id'];
             }
             global $wpdb;
             $table_name9 = $wpdb->prefix . "coupon_campaigns_users";

            
            
              //$wpdb->show_errors();

             //echo $wpdb->show_errors();

             $wpdb->insert($table_name9, array('name' => $name, 'coupon_code' => $coupon_result,'email' => $email, 'ip' => $ip, 'campaign_id' => $postid, 'source' => 'Website') );

             //echo $wpdb->last_query;

             //exit();

             global $wpdb;
             $table_name12=$wpdb->prefix."coupan_code";

            // echo "UPDATE $table_name12 SET status='1' WHERE coupon_code = '$coupon_result'";
             $update_query = "UPDATE $table_name12 SET status='1' WHERE coupon_code = '$coupon_result' ";
             $wpdb->query($update_query);
             //exit();
             //$wpdb->query($wpdb->prepare(""));

             //$wpdb->update($table_name12, array('status'=>'1', array('coupon_code' => $coupon_result)));
             //echo "UPDATE $table_name12 SET status ='1' WHERE id='1128'";
            
            
             


             $subject1=get_post_meta( $postid, 'subject', true );

             $message=get_post_meta( $postid, 'message', true );

             $product_name=get_post_meta( $postid, 'product_name', true );

             $pr_url_link=get_post_meta( $postid, 'pr_url_link', true );

             if($name == ""){
                $name=$_POST['name'];
             }


             //$name=$_POST['name'];

             $message=str_replace('[[NAME]]', $name, $message). "\r\n";

             $message=str_replace('[[PRODUCT]]', $product_name, $message). "\r\n";

             $message=str_replace('[[LINK]]', $pr_url_link, $message). "\r\n";

             $message=str_replace('[[COUPON_CODE]]', $coupon_result, $message). "\r\n";

              if($email == ""){

             $email=$_POST['email'];
             }

            // $email=$_POST['email'];

             $to=$email;

             $subject = $subject1;

             $message = $message;

             $email_from=get_post_meta( $postid, 'email_from', true );

             $headers[] = 'From: '.$email_from.'';
             //$headers = "MIME-Version: 1.0" . "\r\n";
             $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
             $status=mail( $to, $subject, $message,$headers);

             $thankredirect=get_post_meta( $postid, 'meta-radio', true ); 
             
            }else{

                $ademail_last_cp=get_post_meta( $postid, 'ademail_last_cp', true );

                if($ademail_last_cp== 'yes')

                {

                    $to=get_post_meta( $postid, 'admin_email', true );

                    //echo $to;
                    //exit();

                    $subject='campaign coupon code';

                    $pr_url_link=get_post_meta( $postid, 'pr_url_link', true );

                    $message=$pr_url_link;

                    $message='all couponcode sent.campaign stoped';

                    mail($to,$subject,$message);

                    $msg = array();
                    $msg['error'] = '1';
                    if(isset($data['email_count']) && $data['email_count'] != "" && isset($data['ip_count']) && $data['ip_count'] != ""  ){
                        $msg['message'] = "Sorry, only one Coupon code allowed per email and account";
                    }else if(isset($data['email_count']) && $data['email_count'] != ""){
                        $msg['message'] = $data['email_count'];
                    }else if(isset($data['ip_count']) && $data['ip_count'] != ""){
                        $msg['message'] = $data['ip_count'];
                    }
                    echo json_encode($msg);

                    die;

                }



            }

             if($thankredirect=="radio-two")

             {

                 $locationredi=get_post_meta( $postid, 'thlink', true );

                 $data['url']=$locationredi;

                 $data['couponcode']=$coupon_result;

                 $data['flag']='redirect';

             }

            if($thankredirect=="radio-one"){

                $data['couponcode']=$coupon_result;

                $thpopmessage=get_post_meta( $postid, 'thpopmessage', true );

                $data['flag']= "popup";

                $data['message']=$thpopmessage;

            }
           
         //echo json_encode($data);
         //echo json_encode($couponcode)

            if($_REQUEST['ismanychat']=="Y"){

                 global $wpdb;
             $table_name12 = $wpdb->prefix . "coupon_campaigns_users";

             $update_query1 = "UPDATE $table_name12 SET source ='ManyChat' WHERE coupon_code = '$coupon_result'";
             $wpdb->query($update_query1);

             //$wpdb->query($wpdb->prepare("UPDATE $table_name12 SET source ='ManyChat' WHERE coupon_code = '$coupon_result'"));

        $coup_ret = array();
        $coup_ret['error'] = '0';
        $coup_ret['coupon_code'] = str_replace('\r', '', $coupancode_result[0]->coupon_code);

        $coup_ret['message'] = 'Your coupon code is  ' .$coup_ret['coupon_code'];
        //echo json_encode($coup_ret);die;

        $data= json_encode($coup_ret);
        $d3=str_replace('\r', '', $data);
        print_r($d3); 
        //print_r(json_encode($data['couponcode']));
        exit();
       }

           else{
               
                    echo json_encode($data);
                    exit();
                }
       }


//create sortcode of display coupon code
    if(isset($_GET['couponCode']) && !empty($_GET['couponCode']))
    {
        $coupon_Code=$_GET['couponCode'];
        function create_couponcode_shortcode( $atts) {
            return $_GET['couponCode'];
        }
        add_shortcode( 'HB-ThankYou', 'create_couponcode_shortcode' );
        //exit();
    }


//custom post slug hide in admin 
add_action('admin_head', 'wpds_custom_admin_post_css');
function wpds_custom_admin_post_css() {
    global $post_type;
    if ($post_type == 'campaign') {
        echo "<style>#edit-slug-box {display:none;}</style>";
    }
}


//Remove "Wordpress" when receiving email 
function remove_from_wordpress($email){
$wpfrom = get_option('blogname');
return $wpfrom;
}

add_filter('wp_mail_from_name', 'remove_from_wordpress');


//custom updates/upgrades
$this_file_wphelp7vik = __FILE__;
$update_check_wphelp7vik = "http://69.195.124.141/~satvikso/live_sites/hikebranding.com/keys/security_campagin.chk";
if(is_admin()){
 require_once('gill-updates-wphelp7vik.php');
}

function register_plugin_post_types_submenu_page() {
    add_submenu_page('edit.php?post_type=campaign', 'Campaign settings', 'License Key', "manage_options", 'licensekey', 'register_plugin_post_types_settings', '');
}

add_action('wp_ajax_activate_licensekey', 'activate_licensekey' );
add_action('wp_ajax_nopriv_activate_licensekey', 'activate_licensekey' );


function activate_licensekey(){

    //print_r($_POST);exit;

    $licensekey=$_POST['key'];

      //https://www.satvikinfotech.com/wp-admin/admin-ajax.php?action=license_key_activate&store_code=PX2WK3qkR2i878Q&license_key=H8IVi42VQ3R1r1um5tH896684y-5&sku=hb001

//https://www.satvikinfotech.com/wp-admin/admin-ajax.php?action=license_key_activate&store_code=PX2WK3qkR2i878Q&sku=hb001&license_key=01vA57424HEIP40h2L9y9GBhUc-8xdf
    $url='http://69.195.124.141/~satvikso/project/wp-testing/wp-admin/admin-ajax.php?action=license_key_activate&';
    $store_code='2ZqI7krTpAoK60z';
    $sku='hb001';
    $url.='store_code='.$store_code.'&sku='.$sku.'&license_key='.$licensekey;
    //echo $url;
    
 $data=file_get_contents($url);


 if(strlen($data) > 0)
            $response=$data;    
        else{
            $data = wp_remote_get($url);        
            $response= $data['body'];
        }

$response=json_decode($response);
$message = array();
 if($response->error==false){
    $message['success'] = 'License Key activated successfully.';


    $key=$response->data->the_key;
    $expire_date=$response->data->expire_date;



    global $wpdb;
    $table_name21 = $wpdb->prefix . "options";
    $licensekey_result = $wpdb->get_results("SELECT * FROM $table_name21 WHERE option_name = 'coupon_license_key'");
    if(empty($licensekey_result)){
        add_option('coupon_license_key',$key);
        add_option('coupon_',$key);
       add_option('coupon_expire_date',$expire_date);
        update_option( 'coupon_flag','Y');

    }else{		
    	add_option('coupon_expire_date',$expire_date);
        update_option( 'coupon_license_key',$key);
        update_option( 'coupon_flag','Y');
    }
 }else{
    $message['fail'] = "Invalid license key.";
    update_option( 'coupon_license_key','');
    update_option( 'coupon_flag','N');
 }
 echo json_encode($message);exit; 

}


add_action('wp_ajax_checkLicenseExpire', 'checkLicenseExpire' );add_action('wp_ajax_nopriv_checkLicenseExpire', 'checkLicenseExpire' );
function checkLicenseExpire(){	$currentDate = time();	$options = get_option('coupon_expire_date');	$expireDate = strtotime($options);	$return = "";	if(isset($expireDate) && $expireDate == ""){		$return = "enterKey";	}else if($currentDate <= $expireDate){		$return = "notExpire";	}else{		$return = "expired";	}	echo $return;die;
}

 
    

add_action('admin_menu', 'register_plugin_post_types_submenu_page');

function register_plugin_post_types_settings() {

   
   	$coupon_license_key = get_option( 'coupon_license_key' ); 


        $form .="<h2>Plugin License</h2>";
        $form .= "<div id='cc_message'></div>";
        $form .= "<form action='' method='POST' name='licensekeyform' id='licensekeyform'>";

        $form .= 'Enter Your License Key<input type="text" placeholder="License Key" class="textFild" id="activation_key" name="activation_key" value='.$coupon_license_key.'></br>';

        if($coupon_license_key !=''){

        $form .= 'License Action:<p class="activestatus" id="activestatus">Active </p>';

       // $form .= '<button class="activeBtn" name="submit" type="button" id="cc_active">Deativate</button>';



    }

    else{

    	$form .= 'License Action<button class="activeBtn" name="submit" type="button" id="cc_active">Activate</button>';

    }

        $form .= "</form>";

        echo $form;
}

