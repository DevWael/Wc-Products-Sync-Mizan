<?php

if ( ! wp_next_scheduled( 'psm_get_products_task_hookss' ) ) {
	add_action( 'init', 'psm_get_products_hourly_event' );
}

// run psm_get_products_task function hourly
function psm_get_products_hourly_event() {
	wp_schedule_event( time(), 'hourly', 'psm_get_products_task_hook' );
	PSM_Helpers::update_option( 'store_option', time() );
}

//create psm-get-products task every hour
add_action( 'psm_get_products_task_hook', 'psm_get_products_task' );
function psm_get_products_task() {
	wpqt_create_task( 'psm-get-products', 'psm-get-products' );
	PSM_Helpers::update_option( 'psm_task_created', time() );
}

function psm_insert_log( $product_id, $sync_status ) {
	global $wpdb;
	$wpdb->insert( $wpdb->prefix . 'psm_sync_log', array(
		'product_id' => $product_id,
		'status'     => $sync_status,
	) );
}

function psm_display_log_results() {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "psm_sync_log LIMIT 100;" ), ARRAY_A );
}

function psm_get_product_sync_log_results( $product_id ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "psm_sync_log WHERE product_id = '" . $product_id . "' LIMIT 100;" ), ARRAY_A );
}


add_action( 'init', function () {
//test api request
//	$nn = wp_remote_request( 'http://istorejed.gotdns.com:8010/api/ProdAPI/GetProducts', array(
//		'method'    => 'GET',
//		//'sslverify' => true,
//		'timeout'   => 1000,
//		'headers'   => array(
//			'Securitykey' => 'rvgU7mF8cWBn9b4o4KH3uKH9',
//		)
//	) );
//
//	if ( is_wp_error( $nn ) ) {
//		PSM_Helpers::Log( $nn, 'Error' );
//	} else {
//		PSM_Helpers::Log( wp_remote_retrieve_body( $nn ), 'Success' );
//	}

} );

//test all requests
//add_filter( 'http_request_args', function ( $params, $url ) {
//	PSM_Helpers::Log( $url );
//	PSM_Helpers::Log( $params );
//
//	add_filter( 'https_ssl_verify', '__return_false' );
//
//	return $params;
//}, 10, 2 );
