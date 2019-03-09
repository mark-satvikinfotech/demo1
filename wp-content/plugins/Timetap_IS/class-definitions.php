<?php

	class Product_Goals_Definitions {


		function product_goal_table_schema() {

			
			global $wpdb;
	    $table_name =$wpdb->prefix ."product_goals";
	    $charset_collate = $wpdb->get_charset_collate();
	    $sql = "CREATE TABLE $table_name (
	            `id` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `pro_ser_id` INT(30) NOT NULL,
                `pro_ser_name` varchar(500) NOT NULL,
                `camp_id` varchar(30) NOT NULL,
                `camp_name` varchar(500) NOT NULL,
                `goal_id` varchar(255) NOT NULL,
                `goal_name` varchar(500) NOT NULL,
                 PRIMARY KEY  (id)
	          ) $charset_collate; ";
	          require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	         dbDelta($sql);

		}

	}

