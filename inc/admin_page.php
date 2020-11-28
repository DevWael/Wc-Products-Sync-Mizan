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
		$sync_results = psm_display_log_results();
		?>
        <style>
            #sync-data {
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 70%;
            }

            #sync-data td, #sync-data th {
                border: 1px solid #ddd;
                padding: 5px;
                transition: 300ms;
            }

            #sync-data tr:nth-child(even) {
                background-color: #dcdcdc;
            }

            #sync-data tr:hover {
                background-color: #d0d0d0;
            }

            #sync-data th {
                padding-top: 12px;
                padding-bottom: 12px;
                text-align: left;
                background-color: #424242;
                color: white;
            }
        </style>
        <h3>
            Products Sync Details
        </h3>
        <table id="sync-data">
            <tr>
                <th>#</th>
                <th>Message</th>
                <th>Sync Date</th>
                <th>Status</th>
            </tr>
			<?php $i = 1;
			foreach ( $sync_results as $result ) { ?>
                <tr>
                    <td><?php echo esc_html( $i ) ?></td>
                    <td><?php
						if ( is_numeric( $result['product_id'] ) ) {
							$product = wc_get_product( $result['product_id'] );
							if ( $product ) {
								echo $product->get_title();
						    } else {
								echo esc_html( $result['product_id'] );
							}
						} else {
							echo esc_html( $result['product_id'] );
						}
						?></td>
                    <td><?php echo esc_html( $result['sync_date'] ) ?></td>
                    <td><?php echo esc_html( $result['status'] ) == 1 ? 'Successful' : 'failed'; ?></td>
                </tr>
				<?php $i ++;
			} ?>
        </table>
		<?php


		echo '</div>';
	}

}

new PSM_Admin_Interface();
