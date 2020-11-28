<?php
add_action( 'init', 'psm_get_products_hourly_event' );
// run psm_get_products_task function hourly
function psm_get_products_hourly_event() {
	if ( false === as_next_scheduled_action( 'psm_every_min_task' ) ) {
		as_schedule_recurring_action( time(), HOUR_IN_SECONDS, 'psm_every_hour_task', array(), 'mizan_sync' );
	}
}

//create psm-get-products task every hour
add_action( 'psm_every_hour_task', 'psm_get_products' );
function psm_get_products() {
	PSM_Helpers::Log( 'get products', 'Success' );
	$mizan = new Mizan_API();
	$mizan->request();
	if ( $mizan->errors ) {
		//there are errors
		psm_insert_log( 'Failed to connect', 0 );//log product_id from store as success
		PSM_Helpers::update_option( 'store_list_error', 'error' );

		return false;
	} elseif ( $mizan->result ) {
		PSM_Helpers::Log( 'products loaded', 'Success' );
		//PSM_Helpers::delete_option( 'store_list' );
		PSM_Helpers::update_option( 'store_list', $mizan->result );
//		wpqt_create_task( 'psm-update-products-tasks', 'psm-update-products-tasks' );
		as_schedule_single_action( time(), 'psm_prepare_update_products_tasks', array(), 'mizan_sync' );

		return true;
	}

	return false;
}

add_action( 'psm_prepare_update_products_tasks', 'psm_prepare_update_products_tasks_run' );
function psm_prepare_update_products_tasks_run() {
	PSM_Helpers::Log( 'create updating tasks', 'Success' );
	$store_products = PSM_Helpers::get_option( 'store_list' );
	if ( $store_products ) {
		$store_products_array = json_decode( $store_products, true );
		$i                    = 1;
		$products             = array();
		foreach ( $store_products_array as $store_product ) {
			$products[] = $store_product;
			if ( $i % 5 == 0 ) {
				//divide all products into 5 products in each task array
//				wpqt_create_task( 'psm-update-products', wp_json_encode( $products ) );
				as_schedule_single_action( time(), 'psm_update_products', array( 'products_data' => wp_json_encode( $products ) ), 'mizan_sync' );
				$products = array();
			}
			$i ++;
		}

		return true;
	}

	return false;
}

add_action( 'psm_update_products', function ( $data ) {
	PSM_Helpers::Log( 'update products', 'Success' );
	if ( $data ) {
		$products = json_decode( $data, true );
		if ( is_array( $products ) ) {
			foreach ( $products as $product ) {
				$product_sku = $product['p_prodidco'];
				$product_obj = PSM_Helpers::get_product_by_sku( $product_sku );
				if ( $product_obj ) {
					$product_obj->set_stock_quantity( $product['p_curnbals'] ); //update stock quantity
					psm_insert_log( $product_obj->get_id(), 1 );//log product_id from store as success
				} else {
					psm_insert_log( $product['p_prodidco'], 0 ); //log product sku from api as failed
				}
			}
		} else {
			return false;
		}

		return true;
	}

	return false;
} );


function psm_insert_log( $product_id, $sync_status ) {
	PSM_Helpers::Log( 'db log', 'Success' );
	global $wpdb;
	$wpdb->insert( $wpdb->prefix . 'psm_sync_log', array(
		'product_id' => $product_id,
		'status'     => $sync_status,
	) );
}

function psm_display_log_results() {
	global $wpdb;

	return $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "psm_sync_log ORDER BY `id` DESC LIMIT 200;" , ARRAY_A );
}

function psm_get_product_sync_log_results( $product_id ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "psm_sync_log WHERE product_id = '" . $product_id . "' LIMIT 100;" ), ARRAY_A );
}
