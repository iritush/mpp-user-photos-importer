<?php

/**
 * Plugin Name: MediaPress User Photos Importer
 * Version: 1.0.0-alpha
 * Author: Irit adjusted from BuddyDev BP Gallery Migrator
 */

/**
 * Load the plugin files.
 */
function mpp_load_user_photos_importer() {
	$path = plugin_dir_path( __FILE__ );

	require_once $path . 'admin/mpp-upi-admin.php';
	require_once $path . 'admin/mpp-upi-ajax.php';
	require_once $path . 'core/class-mpp-user-photos-importer.php';
}
add_action( 'mpp_loaded', 'mpp_load_user_photos_importer' );
