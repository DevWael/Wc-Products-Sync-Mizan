<?php

class PSM_Admin_Interface {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu_item' ) );
	}

	public function admin_menu_item() {
		add_menu_page( 'Mizan Sync', 'Mizan Sync', 'manage_options',
			'psm-mizan-sync', array( $this, 'admin_page_content' ), 'dashicons-controls-repeat', 15 );
	}

	public function admin_page_content() {
		echo '<div class="wrap">';


		echo 'hello world';


		echo '</div>';
	}

}

new PSM_Admin_Interface();
