<?php

/**
 * Migrator Admin Page.
 */
class MPP_User_Photos_Importer_Admin {

	/**
	 * MPP_User_Photos_Importer_Admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_js' ) );
	}

	/**
	 * Add Menu.
	 */
	public function add_menu() {
		add_submenu_page( mpp_admin()->get_menu_slug(), __( 'MPP Photos Importer', 'mpp-user-photos-importer' ), __( 'MPP Photos Importer', 'mpp-user-photos-importer' ), 'manage_options', 'user-photos-importer', array(
			$this,
			'view',
		) );
	}

	/**
	 * Load js on admin page.
	 */
	public function load_js() {
		wp_enqueue_script( 'mpp-user-photos-importer', plugin_dir_url( __FILE__ ) . 'mpp-upi-admin.js', array( 'jquery' ) );
	}

	/**
	 * Render admin view.
	 */
	public function view() {

		?>

        <div class='wrap'>
            <h2><?php _e( 'Import User Photos', 'mpp-user-photos-importer' );?></h2>

            <a href="#" class='button button-secondary' id='mpp_import_user_photos-start'><?php _e( 'Start Migration', 'mpp-user-photos-importer' );?></a>

            <div class='clear'></div>

            <div id='mpp_import_user_photos-log'>
            </div>
        </div>

        <style type='text/css'>

            #mpp_import_user_photos-log {
                background: #ccc;
                color: #333;
                padding: 10px;
                border: 1px solid #333;
            }

            #mpp_import_user_photos-log p {
                font-size: 13px;
                font-weight: bold;
                margin-bottom: 10px;
            }

        </style>
		<?php
	}

}

new MPP_User_Photos_Importer_Admin();
