<?php

/**
 * Ajax Handler.
 */
class MPP_User_Photos_Importer_Ajax_Handler {

	/**
	 * MPP_User_Photos_Importer_Ajax_Handler constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_mpp_import_user_photos', array( $this, 'import_photos' ) );

	}

	/**
	 * Migrates Media
	 */
	public function import_photos() {

		// if ( ! function_exists( 'bp_get_gallery' ) ) {
		// 	wp_send_json( array(
		// 		'error'   => 1,
		// 		'message' => __( 'Please activate BP Gallery and try again!', 'mpp-bp-gallery-migrator' ),
		// 	) );
		// }

		$migrator = new  MPP_User_Photos_Importer();
		// start migration.
		$result = $migrator->start();

		if ( is_wp_error( $result ) ) {

			wp_send_json( array(
				'error'   => 1,
				'message' => $result->get_error_message(),
			) );
		}
		else {
			wp_send_json( array(
				'success'   => 1,
				'message'   => sprintf( "=> %d", $result),
			) );
		}

		exit( 0 );
	}
}

new MPP_User_Photos_Importer_Ajax_Handler();
