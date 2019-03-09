<?php

/*
Description and Documentation: http://www.gilluminate.com/2011/12/23/host-your-own-custom-wordpress-plugin-updater/
Author: Jason Gill @gilluminate


This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//Exclude from WP updates
function gill_updates_exclude_wphelp7vik( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}

add_filter( 'http_request_args', 'gill_updates_exclude_wphelp7vik', 5, 2 );


//Returns current plugin info.
function gill_plugin_get_wphelp7vik($i) {
	global $this_file_wphelp7vik;
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( $this_file_wphelp7vik ) ) );
	$plugin_file = basename( ( $this_file_wphelp7vik ) );
	return $plugin_folder[$plugin_file][$i];
}

/*//check for update twice a day (same schedule as normal WP plugins)
register_activation_hook($this_file_wphelp7vik, 'gill_check_activation_wphelp7vik');
add_action('gill_check_event_wphelp7vik', 'gill_check_update_wphelp7vik');

function gill_check_activation_wphelp7vik() {
    wp_schedule_event(time(), 'every5minute', 'gill_check_event_wphelp7vik');

}

 */
add_action('admin_init', 'gill_check_activation_wphelp7vik');

function gill_check_activation_wphelp7vik() {
    gill_check_update_wphelp7vik();
}
function gill_check_update_wphelp7vik() {
	global $wp_version;
	global $this_file_wphelp7vik;
	global $update_check_wphelp7vik;
	$plugin_folder = plugin_basename( dirname( $this_file_wphelp7vik ) );
	$plugin_file = basename( ( $this_file_wphelp7vik ) );
	if ( defined( 'WP_INSTALLING' ) ) return false;

	$response = wp_remote_get( $update_check_wphelp7vik );
        if( is_wp_error( $response ) ) {        
            $response = file_get_contents( $update_check_wphelp7vik );
        }else{
            $response = $response['body'];
        }
        if(empty($response))
            return;
	list($version, $url) = explode('|', $response);       
	if(gill_plugin_get_wphelp7vik("Version") == $version) return false;
	$plugin_transient = get_site_transient('update_plugins');
	$a = array(
		'slug' => $plugin_folder,
		'new_version' => $version,
		'url' => gill_plugin_get_wphelp7vik("AuthorURI"),
		'package' => $url
	);
	$o = (object) $a;
	$plugin_transient->response[$plugin_folder.'/'.$plugin_file] = $o;
	set_site_transient('update_plugins', $plugin_transient);
     
}

//remove cron task upon deactivation
//register_deactivation_hook($this_file_wphelp7vik, 'gill_check_deactivation_wphelp7vik');
function gill_check_deactivation_wphelp7vik() {
	wp_clear_scheduled_hook('gill_check_event_wphelp7vik');
}
