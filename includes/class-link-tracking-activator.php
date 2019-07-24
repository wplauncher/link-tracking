<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    Link_Tracking
 * @subpackage Link_Tracking/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Link_Tracking
 * @subpackage Link_Tracking/includes
 * @author     Ben Shadle <benshadle@gmail.com>
 */
class Link_Tracking_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
			// add click and impression tables
			global $wpdb;
			$plugin_name_db_version = '1.0';
			$table_name = $wpdb->prefix . "link_tracking_clicks"; 
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						clicks mediumint(9) NOT NULL,
						post_id mediumint(9) NOT NULL,
						week timestamp NOT NULL,
						UNIQUE KEY id (id)
					) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			$table_name = $wpdb->prefix . "link_tracking_impressions"; 
			$sql2 = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				impressions mediumint(9) NOT NULL,
				post_id mediumint(9) NOT NULL,
				week timestamp NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";
			dbDelta( $sql2 );

			add_option( 'link_tracking_db_version', $plugin_name_db_version );

	}

}
