<?php

if ( ! wp_next_scheduled( 'psm_get_products_task' ) ) {
	add_action( 'init', 'psm_get_products_hourly_event' );
}

// run psm_get_products_task function hourly
function psm_get_products_hourly_event() {
	wp_schedule_event( time(), 'hourly', 'psm_get_products_task' );
}

//create psm-get-products task every hour
function psm_get_products_task(){
	wpqt_create_task( 'psm-get-products','ok' );
}
