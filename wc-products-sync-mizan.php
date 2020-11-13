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

include PSM_DIR . 'inc/wp-queue-tasks/wp-queue-tasks.php'; //tasks engine
include PSM_DIR . 'inc/admin_page.php'; //admin page
