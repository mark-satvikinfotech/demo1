<?php


class Coupon_Campaigns_Definitions {

    function coupon_table_schema() {

		 global $wpdb;

	    $table_name =$wpdb->prefix ."coupon_campaigns_users";
	    $charset_collate = $wpdb->get_charset_collate();
	    $sql = "CREATE TABLE $table_name (
	            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,

                `coupon_code` varchar(255) NOT NULL,
 
                `name` varchar(255) NOT NULL,

                `email` varchar(255) NOT NULL,

                  `ip` varchar(255) NOT NULL,

                `campaign_id` BIGINT(20) UNSIGNED NOT NULL,

               `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               `source` varchar(255) NOT NULL,

                 PRIMARY KEY  (id)


	          ) $charset_collate; ";


	          require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	         dbDelta($sql);

	           $table_name =$wpdb->prefix ."coupan_code";
	            $charset_collate = $wpdb->get_charset_collate();
	          $sql1 ="CREATE TABLE $table_name (
	            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,

                  `campaign_id` BIGINT(20) UNSIGNED NOT NULL,

                  `coupon_code` varchar(255) NOT NULL,

                  `status` tinyint unsigned NOT NULL DEFAULT '0',
           
                  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 

                  PRIMARY KEY  (id)



	          ) $charset_collate; ";

	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta($sql1);
	}

}


	// run the install scripts upon plugin activation
