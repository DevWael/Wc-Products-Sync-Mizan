<?php
/**
 * Plugin Name: Wc Products Sync Mizan
 * Plugin URI: https://github.com/DevWael/Wc-Products-Sync-Mizan
 * Description: Sync WC Products With Mizan API.
 * Version: 1.0
 * Author: AhmadWael
 * Author URI: https://github.com/DevWael
 * License: GPL2
 */

define( 'PSM_DIR', plugin_dir_path( __FILE__ ) );
define( 'PSM_URI', plugin_dir_url( __FILE__ ) );

//check if woocommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	include PSM_DIR . 'inc/wp-queue-tasks/wp-queue-tasks.php'; //tasks engine
	include PSM_DIR . 'inc/PSM_Helpers.php'; //helper functions
	include PSM_DIR . 'inc/Mizan_Api.php'; //Mizan API
	include PSM_DIR . 'inc/functions.php'; //Hooked functions
	include PSM_DIR . 'inc/PSM_Sync_Tasks.php'; //Tasks Queue
	include PSM_DIR . 'inc/admin_page.php'; //admin page
}
