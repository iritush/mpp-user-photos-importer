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
	public function logger($text_to_add) {
		try {
			$logfile = '';
			if(!is_file($logfile)){
				touch($logfile);
			}
			$logcontent = file_get_contents($logfile);
			$logcontent .= $text_to_add;
			file_put_contents($logfile, $logcontent);
		}
		catch  (exception $e) {
			print "failed with exception " . $e . "\n";
		}
		return;
	}

	/**
	 * Call it to start migration
	 *
	 * @return boolean|\WP_Error
	 */
	public function start() {
		$csv_file = '';
		$csv = array_map('str_getcsv', file($csv_file));
		$photos_path = '';
		$current_user = '';
		$user_image_map_array = array ();
		$i=0;
		$total_users = 0;
		$total_imported=0;
		$total_skipped_users = 0;
		$total_failed_imports = 0;
		$this->logger ("New Import\n");

		foreach ($csv as $entry) {
			if ($entry[0] != $current_user) {
				$current_user = $entry[0];
				$this->logger("current user: " . $current_user . "\n");
				$i=0;
				$total_users++;
				$current_user_found = true;
				$the_user = get_user_by('login', $current_user);
				if (!$the_user) {
					$this->logger ("couldn't find user " . $current_user . " skipping\n");
					$current_user_found = false;
					$total_skipped_users++;
				}
				else {
					$user_image_map_array[$current_user]['userid'] = $the_user->ID;
					$gallery_id = mpp_create_gallery( array(
						'creator_id'	 => $user_image_map_array[$current_user]['userid'],
						'title'        => 'My Photos',
						// 'description'  => $caption,
						'status'		 => 'private',
						'component'		 => 'members',
						'component_id'	 => $user_image_map_array[$current_user]['userid'],
						'type'			 => 'photo',
						) );
						if (!$gallery_id) {
							$this->logger ("Failed to create gallery for " . $current_user . "skipping\n");
							$current_user_found = false;
							$total_skipped_users++;
						}
						else {
							$user_image_map_array[$current_user]['galleryid'] = $gallery_id;
						}
				}
			}
			if ($entry[0] == $current_user && $current_user_found) {
				$this->logger("Importing image for user " . $current_user . "\n");
				$user_image_map_array[$current_user][$i] = array();					
				$user_image_map_array[$current_user][$i]['townname'] = $entry[1];
				$user_image_map_array[$current_user][$i]['filename'] = $photos_path . '/LG_' . $entry[2];
				$user_image_map_array[$current_user][$i]['datetaken'] = $entry[3];
				$user_image_map_array[$current_user][$i]['caption'] = $entry[4];
				$user_image_map_array[$current_user][$i]['imagetitle'] = $entry[1] . "-" . $entry[3];
				
				$result = mpp_import_file( $user_image_map_array[$current_user][$i]['filename'], $user_image_map_array[$current_user]['galleryid'], array(
					'description'  => $user_image_map_array[$current_user][$i]['caption'],
					'title' => $user_image_map_array[$current_user][$i]['imagetitle'],
					'author' => $user_image_map_array[$current_user]['userid'],
					'user_id' => $user_image_map_array[$current_user]['userid'],
					));
				if (is_wp_error( $result )) {
					$this->logger ("Failed to import file " . $user_image_map_array[$current_user][$i]['filename'] . "for user " . $current_user . " gallery " . $user_image_map_array[$current_user]['galleryid'] . ": " . $result);
					$total_failed_imports++;
				}
				else {
					$this->logger ("Successfuly imported " . $file_name . " for user " . $user_image_map_array[$current_user]['userid'] . " into gallery id " . $gallery_id . "\n");
					$total_imported++;
				}
				$i++;
			}		
		}
		$this->logger ("Completed with a total of " . $total_imported . " successful imports. " . $total_failed_imports . " failed imports, " . $total_skipped_users . " skipped users\n");
		return ("done");
	}
}