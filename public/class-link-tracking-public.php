<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    Link_Tracking
 * @subpackage Link_Tracking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Link_Tracking
 * @subpackage Link_Tracking/public
 * @author     Ben Shadle <benshadle@gmail.com>
 */
class Link_Tracking_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'wp_ajax_nopriv_link_tracking_clicks', array($this,'track_clicks'));
		
		add_action( 'wp_ajax_link_tracking_clicks', array($this,'track_clicks'));
		add_action( 'wp_ajax_nopriv_link_tracking_impressions', array($this,'track_impressions'));
		
		add_action( 'wp_ajax_link_tracking_impressions', array($this,'track_impressions'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Link_Tracking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Link_Tracking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/link-tracking-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Link_Tracking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Link_Tracking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$clickNonce = wp_create_nonce( "link_tracking_click" );
		$impressionNonce = wp_create_nonce( "link_tracking_impression" );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/link-tracking-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'link_tracking_ajax_object', 
		  	array( 
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'click_nonce'=> $clickNonce,
				'impression_nonce'=> $impressionNonce
			) 
		  );

	}
	public function track_clicks(){		
		$nonce = sanitize_text_field($_REQUEST['security']);
		//$user_id = intval($_REQUEST['uid']);
		// verify the self created nonce - don't use wp_verify_nonce bc it checks the referring url and it is different 
		
		//if(!current_user_can( 'administrator' ) || $payment_processor_nonce != $nonce){
		//wp_set_current_user( $user_id );
		if(!wp_verify_nonce($nonce,'link_tracking_click')){
			$data['response'] = 'error'.__LINE__;
			$data['vars'] = $_POST;
			// Set content type
			header('Content-type: application/json');

			// Prevent caching
			header('Expires: 0');
			echo json_encode($data);
			exit();
		}
		$post_id = sanitize_text_field($_POST['post_id']);
		$post_exists = get_post($post_id);
		if($post_exists){
			$clicks = get_post_meta($post_id, $this->plugin_name.'_clicks', true );
			if(!$clicks){
				$clicks = 1;
			} else {
				$clicks = $clicks+1;
			}
			update_post_meta($post_id, $this->plugin_name.'_clicks',$clicks);
		} else {
			$clicks = '';
		}
		$existingData = array('post_id' => $post_id);
		$clicks_table = $this->update_clicks($existingData);

		$data['response'] = 'success';
		$data['clicks'] = $clicks;
		// Set content type
		header('Content-type: application/json');

		// Prevent caching
		header('Expires: 0');
		echo json_encode($data);
		/* close connection */
		exit();	
	}
	public function get_current_week(){
		$time = strtotime('last monday');
		$current_week = date('Y-m-d', $time);
		
		return $current_week;
	}
	public function get_current_week_full(){
		$time = strtotime('last monday');
		$current_week = date('Y-m-d H:i:s', $time);
		return $current_week;
	}
	/**
	 * Params - first_week, last_week, post_id
	 *
	 * @since    1.0.0
	 */
	public function get_clicks($data){
		global $wpdb;
		// Get the wpmerchant db version to make sure the functions below are= compatible with the db version
    	$plugin_name_db_version = get_option( $this->plugin_name.'_db_version' );
		$table_name = $wpdb->prefix . 'link_tracking_clicks';
		// Make sure the select statement is compatible with the db version		
		$post_id = $data['post_id'];
		if(!isset($data['first_week'])){
			$week = $this->get_current_week();
			$clicks = $wpdb->get_row("SELECT * FROM $table_name WHERE post_id = '$post_id' and week LIKE '%$week%'");
		} else {
			$first_week = $data['first_week'];
			$last_week = $data['last_week'];
			$clicks = $wpdb->get_results("SELECT * FROM $table_name WHERE post_id = '$post_id' and week >= '$first_week' and week <= '$last_week' ORDER BY week DESC");
		}
		
		return $clicks;
	}
	public function update_clicks($existingData){
		global $wpdb;
		// Get the wpmerchant db version to make sure the functions below are= compatible with the db version
    	$plugin_name_db_version = get_option( $this->plugin_name.'_db_version' );
		$table_name = $wpdb->prefix . 'link_tracking_clicks';
		$clicks = $this->get_clicks($existingData);
		if(isset($clicks)){
			// insert into clicks record
			$post_id = $clicks->post_id;
			$week = $clicks->week;
			$where = array( 'post_id' => $post_id, 'week' => $week);
			// Make sure the insert statement is compatible with the db version		
			$sql = $wpdb->update( 
				$table_name, 
				array( 
					'clicks' => $clicks->clicks+1
				),
				$where
			);
		} else {
			// add clicks record
			$week = $this->get_current_week();
			$sql = $wpdb->insert( 
				$table_name, 
				array( 
					'clicks' => 1,
					'post_id' => $existingData['post_id'],
					'week' => $week,
				)
			);
		}
		
		return $sql;
	}
	/**
	 * Params - first_week, last_week, post_id
	 *
	 * @since    1.0.0
	 */
	public function get_impressions($data){
		global $wpdb;
		// Get the wpmerchant db version to make sure the functions below are= compatible with the db version
    	$plugin_name_db_version = get_option( $this->plugin_name.'_db_version' );
		$table_name = $wpdb->prefix . 'link_tracking_impressions';
		// Make sure the select statement is compatible with the db version		
		$post_id = $data['post_id'];
		if(!isset($data['first_week'])){
			$week = $this->get_current_week();
			$impressions = $wpdb->get_row("SELECT * FROM $table_name WHERE post_id = '$post_id' and week LIKE '%$week%'");
		} else {
			$first_week = $data['first_week'];
			$last_week = $data['last_week'];
			$impressions = $wpdb->get_results("SELECT * FROM $table_name WHERE post_id = '$post_id' and week >= '$first_week' and week <= '$last_week' ORDER BY week DESC");
		}
		
		return $impressions;
	}
	public function update_impressions($existingData){
		global $wpdb;
		// Get the wpmerchant db version to make sure the functions below are= compatible with the db version
    	$plugin_name_db_version = get_option( $this->plugin_name.'_db_version' );
		$table_name = $wpdb->prefix . 'link_tracking_impressions';
		$impressions = $this->get_impressions($existingData);
		if(isset($impressions)){
			// insert into clicks record
			$post_id = $impressions->post_id;
			$week = $impressions->week;
			$where = array( 'post_id' => $post_id, 'week' => $week);
			// Make sure the insert statement is compatible with the db version		
			$sql = $wpdb->update( 
				$table_name, 
				array( 
					'impressions' => $impressions->impressions+1
				),
				$where
			);
		} else {
			// add clicks record
			$week = $this->get_current_week();
			$sql = $wpdb->insert( 
				$table_name, 
				array( 
					'impressions' => 1,
					'post_id' => $existingData['post_id'],
					'week' => $week,
				)
			);
		}
		
		return $sql;
	}
	public function track_impressions(){		
		$nonce = sanitize_text_field($_REQUEST['security']);
		//$user_id = intval($_REQUEST['uid']);
		// verify the self created nonce - don't use wp_verify_nonce bc it checks the referring url and it is different 
		
		//if(!current_user_can( 'administrator' ) || $payment_processor_nonce != $nonce){
		//wp_set_current_user( $user_id );
		if(!wp_verify_nonce($nonce,'link_tracking_impression')){
			$data['response'] = 'error'.__LINE__;
			$data['vars'] = $_POST;
			// Set content type
			header('Content-type: application/json');

			// Prevent caching
			header('Expires: 0');
			echo json_encode($data);
			exit();
		}
		$post_id = sanitize_text_field($_POST['post_id']);
		$post_exists = get_post($post_id);
		if($post_exists){
			$impressions = get_post_meta($post_id, $this->plugin_name.'_impressions', true );
			if(!$impressions){
				$impressions = 1;
			} else {
				$impressions = $impressions+1;
			}
			update_post_meta($post_id, $this->plugin_name.'_impressions',$impressions);
		} else {
			$impressions = '';
		}
		// update impression count for the week
		$existingData = array('post_id' => $post_id);
		$impression_table = $this->update_impressions($existingData);

		$data['response'] = 'success';
		$data['impressions'] = $impressions;
		// Set content type
		header('Content-type: application/json');

		// Prevent caching
		header('Expires: 0');
		echo json_encode($data);
		/* close connection */
		exit();	
	}
}
