<?php

class PSM_Sync_Tasks {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		$this->get_all_products_task();
	}

	public function get_all_products_task() {
		if ( function_exists( 'wpqt_register_queue' ) ) {
			wpqt_register_queue( 'psm-get-products', array(
				'callback'  => array( $this, 'get_products' ),
				'processor' => 'cron',
				'retry'     => 5,
				'bulk'      => false,
			) );

			wpqt_register_queue( 'psm-update-products-tasks', array(
				'callback'  => array( $this, 'prepare_update_products_tasks' ),
				'processor' => 'cron',
				'retry'     => 3,
				'bulk'      => false,
			) );

			wpqt_register_queue( 'psm-update-products', array(
				'callback'  => array( $this, 'update_products' ),
				'processor' => 'cron',
				'retry'     => 3,
				'bulk'      => false,
			) );
		}
	}

	public function get_products( $data, $queue ) {
		$mizan = new Mizan_API();
		$mizan->request();
		if ( $mizan->errors ) {
			//there are errors
			PSM_Helpers::update_option( 'store_list_error', 'error' );

			return false;
		} elseif ( $mizan->result ) {
			//PSM_Helpers::delete_option( 'store_list' );
			PSM_Helpers::update_option( 'store_list', $mizan->result );
			wpqt_create_task( 'psm-update-products-tasks', 'psm-update-products-tasks' );

			return true;
		}
		PSM_Helpers::update_option( 'store_list_error2', 'error' );

		return false;
	}

	public function prepare_update_products_tasks( $data, $queue ) {
		$store_products = PSM_Helpers::get_option( 'store_list' );
		if ( $store_products ) {
			$store_products_array = json_decode( $store_products, true );
			$i                    = 1;
			$products             = array();
			foreach ( $store_products_array as $store_product ) {
				$products[] = $store_product;
				if ( $i % 5 == 0 ) {
					//divide all products into 5 products in each task array
					wpqt_create_task( 'psm-update-products', wp_json_encode( $products ) );
					$products = array();
				}
				$i ++;
			}

			return true;
		}

		return false;
	}

	//update stock quantity for each product
	public function update_products( $data, $queue ) {
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
	}

}

new PSM_Sync_Tasks;
