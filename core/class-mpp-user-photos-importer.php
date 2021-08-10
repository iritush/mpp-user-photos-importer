<?php
/**
 * Migrator class to migrating BP Gallery Media/Gallery to MediaPress Gallery
 */
class MPP_User_Photos_Importer {

	/**
	 * MPP_User_Photos_Importer constructor.
	 */
	public function __construct() {
		
	}

	/**
	 * Call it to start migration
	 *
	 * @return boolean|\WP_Error
	 */
	public function start() {
		$path = plugin_dir_path( __FILE__ );
		$home = getenv("HOME");
		$photos_path = $home . '/public_html/photos';
		$logfile = $path . '/log.txt';
		$csv_file = '';
		if(!is_file($logfile)){
			touch($logfile);
		}
		$logcontent = file_get_contents($logfile);
		$logcontent .= "Starting\n";
		file_put_contents($logfile, $logcontent);

		$csv = array_map('str_getcsv', file($path . '/' . $csv_file));
		$user_gallery_map = array ();
		$total_imported = 0;
		$total_failed_imports = 0;
		$chunk_csv = array_chunk($csv, 2, true);
		foreach ($chunk_csv as $sub_csv) {
			foreach ($sub_csv as $entry) {
			$username = $entry[0];
			$the_user = get_user_by('login', $username);
			$userid = $the_user->ID;
			$town_name = $entry[1];
			$file_name = $photos_path . '/LG_' . $entry[2];
			$date_taken = $entry[3];
			$caption = $entry[4];
			$image_title = $town_name . "-" . $date_taken;
			if ($user_gallery_map[$userid]) {
				$gallery_id = $user_gallery_map[$userid];
			}
			else {
				$gallery_id = mpp_create_gallery( array(
				'creator_id'	 => $userid,
				'title'        => 'My Photos',
            	'description'  => $caption,
				'status'		 => 'private',
				'component'		 => 'members',
				'component_id'	 => $userid,
				'type'			 => 'photo',
				) );
				$user_gallery_map[$userid] = $gallery_id;
			}

		if ($gallery_id) {
			$result = mpp_import_file( $file_name, $gallery_id, array(
            	'description'  => $caption,
				'title' => $image_title,
				'author' => $userid,
				'user_id' => $userid,
				));
			if (is_wp_error( $result )) {
				$logcontent = file_get_contents($logfile);
            	$logcontent .= "Failed to import file with the following error: " . $result;
            	file_put_contents($logfile, $logcontent);
				$total_failed_imports++;
			}
			else {
				$logcontent = file_get_contents($logfile);
            	$logcontent .= "Successfuly imported " . $file_name . " for user " . $userid . " into gallery id " . $gallery_id . "\n";
            	file_put_contents($logfile, $logcontent);
				$total_imported++;
			}
		}
		else {
			$logcontent = file_get_contents($logfile);
			$logcontent .= "Failed to create pr retrieve gallery id for user " . $userid;
			file_put_contents($logfile, $logcontent);
			}
		}
		}
		$message = "Completed with a total of " . $total_imported . " successful imports and " . $total_failed_imports . " failed imports";
		$logcontent = file_get_contents($logfile);
		$logcontent .= $message;
		file_put_contents($logfile, $logcontent);
		return ($message);
	}
}