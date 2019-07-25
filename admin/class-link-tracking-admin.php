<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    Link_Tracking
 * @subpackage Link_Tracking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Link_Tracking
 * @subpackage Link_Tracking/admin
 * @author     Ben Shadle <benshadle@gmail.com>
 */
class Link_Tracking_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('init', array( $this, 'register_custom_post_types' ));
		add_action('add_meta_boxes_link_tracking_links', array( $this, 'setupCustomPostTypeMetaboxes' ));
		add_action( 'save_post_link_tracking_links', array( $this, 'saveCustomPostTypeMetaBoxData') );
		add_shortcode( 'link_tracking', array( $this, 'linkTrackingShortcode' ));
		
		// UPdate the columns shown on hte products edit.php file - so we also have cost, inventory and product id
		add_filter('manage_link_tracking_links_posts_columns' , array($this,'link_tracking_links_columns'));

		// this fills in the columns that were created with each individual post's value
		add_action( 'manage_link_tracking_links_posts_custom_column' , array($this,'fill_link_tracking_links_columns'), 10, 2 );
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/link-tracking-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $typenow;
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

		if( $typenow == 'link_tracking_links' ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/link-tracking-admin.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'google-charts', 'https://www.gstatic.com/charts/loader.js', array(), $this->version, false );
			wp_enqueue_media();
		}

	}
//STEP 2: Create the Register Custom Post Types Method
public function register_custom_post_types(){
	$LinkArgs = array(
			'label'=>'Link Tracking Link',
			'labels'=>
				array(
					'name'=>'Links',
					'singular_name'=>'Link',
					'add_new'=>'Add Link',
					'add_new_item'=>'Add New Link',
					'edit_item'=>'Edit Link',
					'new_item'=>'New Link',
					'view_item'=>'View Link',
					'search_items'=>'Search Link',
					'not_found'=>'No Links Found',
					'not_found_in_trash'=>'No Links Found in Trash',
					'menu_name' => 'Link Tracking',
					'name_admin_bar'     => 'Link Tracking',
				),
			'public'=>false,
			'description'=>'A Link you can track', 
			'exclude_from_search'=>true,
			'show_ui'=>true,
			'menu_position'=>26,
			'menu_icon'=>"dashicons-admin-links",
			'supports'=>array('title', 'custom_fields'));
 
	// Post type, $args - the Post Type string can be MAX 20 characters
	register_post_type( 'link_tracking_links', $LinkArgs );
}
public function linkTrackingShortcode( $atts, $content = "" ) {
	//[link_tracking link='29'][/link_tracking]
	$a = shortcode_atts( array(
			'link'=>'',	
			'classes'=>'',
			'style'=>'',
			'hidden'=>'',					
			), $atts );
	// link is the post id
	$href = get_post_meta($a['link'], $this->plugin_name.'_url', true );
	$link_text = get_post_meta($a['link'], $this->plugin_name.'_link_text', true );
	$target = get_post_meta($a['link'], $this->plugin_name.'_target', true );

	return '<a class="link_tracking_link '.esc_attr($a['classes']).'" data-postid="'.esc_attr($a['link']).'" target="'.esc_attr($target).'" href="'.esc_attr($href).'" style="'.esc_attr($a['style']).'">'.$link_text.'</a>';
	}
	public function link_tracking_links_columns($columns){
			// Remove Author and Comments from Columns and Add Cost, Inventory and Product Id
			unset(
				$columns['wpseo-score'],
				$columns['wpseo-title'],
				$columns['wpseo-metadesc'],
				$columns['wpseo-focuskw']
			);
			return array(
							 'cb' => '<input type="checkbox" />',
							 'title' => __('Title'),
							 'clicks' => __('Clicks'),
							 'impressions' => __('Impressions'),
							 'link_id' =>__( 'Link ID'),
					 'date' =>__( 'Date')
					 );
				//return $columns;
		}
	public function fill_link_tracking_links_columns( $column, $post_id ) {
		// Fill in the columns with meta box info associated with each post
	switch ( $column ) {
	case 'clicks' :
		echo get_post_meta( $post_id , $this->plugin_name.'_clicks' , true ); 
		break;
	case 'impressions' :
			echo get_post_meta( $post_id , $this->plugin_name.'_impressions' , true ); 
			break;
	case 'link_id' :
			echo $post_id; 
			break;
		}
}
	public function setupCustomPostTypeMetaboxes(){
		add_meta_box('link_tracking_links_data_meta_box', 'Link Tracking Data', array($this,'link_tracking_links_data_meta_box'), 'link_tracking_links', 'normal','high' );
		add_meta_box('link_tracking_links_shortcode_meta_box', 'Link Tracking Shortcode', array($this,'link_tracking_links_shortcode_meta_box'), 'link_tracking_links', 'normal','high' );
		}
		public function link_tracking_links_shortcode_meta_box($post){
			echo '<div class="link_tracking_links_field_containers">';
			echo '<ul class="link_tracking_links_data_metabox">';
			
		echo '<li><code class="link-tracking-links_code">[link_tracking link="'.$post->ID.'"][/link_tracking]</code></li>';
			echo '</ul></div>';
		
		}
		public function get_last_5_weeks(){
			$start = strtotime('4 mondays ago');
			$end = strtotime('monday this week');
			$data['first_week'] = date('Y-m-d H:i:s', $start);
			$data['last_week'] = date('Y-m-d H:i:s', $end);
			return $data;
		}
		public function get_historical_tracking($data){
			/*require_once(plugin_dir_path( dirname(__FILE__) ) . 'public/class-link-tracking-public.php');*/
			$Link_Tracking_Public = new Link_Tracking_Public($this->plugin_name, $this->version);
			$first_last_data = $this->get_last_5_weeks();
			$weekly_data = array('first_week'=>$first_last_data['first_week'], 'last_week'=>$first_last_data['last_week'], 'post_id' => $data['post_id']);

			$weekly_clicks = $Link_Tracking_Public->get_clicks($weekly_data);
			$weekly_impressions = $Link_Tracking_Public->get_impressions($weekly_data);
			return array('clicks'=>$weekly_clicks, 'impressions'=>$weekly_impressions);
		}
	public function link_tracking_links_data_meta_box($post){
			// Add a nonce field so we can check for it later.
			wp_nonce_field( $this->plugin_name.'_meta_box', $this->plugin_name.'_links_meta_box_nonce' );
	
			echo '<div class="link_tracking_links_field_containers">';
			//show clicks/impressions by week #
			$data = array('post_id'=>$post->ID);
			$historical_tracking = $this->get_historical_tracking($data);
			//echo var_dump($historical_tracking);
			if($historical_tracking && isset($historical_tracking['clicks'])){
				foreach($historical_tracking['clicks'] AS $key=>$value){
					$weekly_data[] = "['".date('m-d-Y',strtotime($historical_tracking['clicks'][$key]->week))."', '".$historical_tracking['clicks'][$key]->clicks."', '".$historical_tracking['impressions'][$key]->impressions."']";
				}
			}
			/*$weekly_string = '["07-22-2019","33","333"]';*/
			if(isset($weekly_data)){
				$weekly_string = implode(',',$weekly_data);
			} else {
				$weekly_string = '';
			}
			
			if($weekly_string){
					echo "<div><div id='link_tracking_columnchart_material'></div><script type='text/javascript'>
					var weekly_string = $weekly_string;
					console.log(weekly_string);
					google.charts.load('current', {'packages':['bar']});
					google.charts.setOnLoadCallback(drawChart);

					function drawChart() {
						var data = google.visualization.arrayToDataTable([
							['Week of', 'Clicks', 'Impressions'],".$weekly_string."
						]);

						var options = {
							chart: {
								title: 'Link Tracking',
								subtitle: 'Clicks and Impressions by Week',
							},
							bars: 'vertical',
							vAxis: {format: 'decimal'},
							height: 400,
							colors: ['#4d6dc3', '#8097d4']
						};

						var chart = new google.charts.Bar(document.getElementById('link_tracking_columnchart_material'));

						chart.draw(data, google.charts.Bar.convertOptions(options));
					}
				</script></div>";
			}
			echo '<ul class="link_tracking_links_data_metabox">';
			
			echo '<li><label for="'.$this->plugin_name.'_url">';
			_e( 'Link', $this->plugin_name.'_url' );
			echo '</label>';
			echo '<button class="button link-tracking-media-button">Upload File</button>';
			$args = array (
									'type'      => 'input',
						'subtype'	  => 'text',
						'id'	  => $this->plugin_name.'_url',
						'name'	  => $this->plugin_name.'_url',
						'required' => 'required="required"',
						'get_options_list' => '',
						'value_type'=>'normal',
						'wp_data' => 'post_meta',
						'post_id'=> $post->ID
							);
					// this gets the post_meta value and echos back the input
			$this->link_tracking_render_settings_field($args);
			echo '</li>';
			echo '<li><label for="'.$this->plugin_name.'_link_text">';
			_e( 'Link Text', $this->plugin_name.'_link_text' );
			echo '</label>';
			$args = array (
									'type'      => 'input',
						'subtype'	  => 'text',
						'id'	  => $this->plugin_name.'_link_text',
						'name'	  => $this->plugin_name.'_link_text',
						'required' => 'required="required"',
						'get_options_list' => '',
						'value_type'=>'normal',
						'wp_data' => 'post_meta',
						'post_id'=> $post->ID
							);
					// this gets the post_meta value and echos back the input
			$this->link_tracking_render_settings_field($args);
			echo '</li>';
			echo '<li><label for="'.$this->plugin_name.'_target">';
			_e( 'Target', $this->plugin_name.'_target' );
			echo '</label>';
			unset($args);
	  	$args = array (
	              'type'      => 'select',
				  'subtype'	  => '',
				  'id'	  => $this->plugin_name.'_target',
				  'name'	  => $this->plugin_name.'_target',
				  'required' => 'required="required"',
				  'get_options_list' => 'get_target_list',
				  'value_type'=>'normal',
				  'wp_data' => 'post_meta',
					'post_id'=> $post->ID
	          );
					// this gets the post_meta value and echos back the input
			$this->link_tracking_render_settings_field($args);
			echo '</li>';
			echo '<li><label for="'.$this->plugin_name.'_clicks">';
			_e( 'Total Clicks', $this->plugin_name.'_clicks' );
			echo '</label>';
			$args = array (
									'type'      => 'input',
						'subtype'	  => 'number',
						'id'	  => $this->plugin_name.'_clicks',
						'name'	  => $this->plugin_name.'_clicks',
						'disabled' => 'true',
						'get_options_list' => '',
						'value_type'=>'normal',
						'wp_data' => 'post_meta',
						'post_id'=> $post->ID
							);
					// this gets the post_meta value and echos back the input
			$this->link_tracking_render_settings_field($args);
			echo '</li><li><label for="'.$this->plugin_name.'_impressions">';
			_e( 'Total Impressions', $this->plugin_name.'_impressions' );
			echo '</label>';
			unset($args);
				$args = array (
									'type'      => 'input',
						'subtype'	  => 'number',
						'id'	  => $this->plugin_name.'_impressions',
						'name'	  => $this->plugin_name.'_impressions',
						'disabled' => 'true',
						'get_options_list' => '',
						'value_type'=>'normal',
						'wp_data' => 'post_meta',
						'post_id'=> $post->ID
							);
			// this gets the post_meta value and echos back the input
			$this->link_tracking_render_settings_field($args);
		echo '</li>';
			echo '</ul></div>';
		
		}
		public function get_target_list(){
			/*_blank	Opens the linked document in a new window or tab
_self	Opens the linked document in the same frame as it was clicked (this is default)
_parent	Opens the linked document in the parent frame
_top	Opens the linked document in the full body of the window*/
			$list = array(		
				0 => array('value'=> '_self', 'name' => 'Open in the same frame as it was clicked '),					
				1 => array('value'=> '_blank', 'name' => 'Open in a new window or tab'),						  
									2 => array('value'=> '_parent', 'name' => 'Open in the parent frame'),
									3 => array('value'=> '_top', 'name' => 'Open in the full body of the window'),	  
							);
			return $list;
		}
		
		public function link_tracking_render_settings_field($args) {
			if($args['wp_data'] == 'option'){
				$wp_data_value = get_option($args['name']);
			} elseif($args['wp_data'] == 'post_meta'){
				$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
			}
			
			switch ($args['type']) {
				case 'select':
					// get the options list array from the get_options_list array value
					$wp_data_list = call_user_func(array('Link_Tracking_Admin', $args['get_options_list']), $args);
					//$wp_data_list = $this->$args['get_options_list']($args);
					foreach($wp_data_list AS $o){
						$value = ($args['value_type'] == 'serialized') ? serialize($o) : $o['value'];
						$select_options .= ($value == $wp_data_value) ? '<option selected="selected" value=\''.esc_attr($value).'\'>'.$o['name'].'</option>' : '<option value=\''.esc_attr($value).'\'>'.$o['name'].'</option>';
					}
					if(isset($args['disabled'])){
						// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo '<select id="'.$args['id'].'_disabled" disabled name="'.$args['name'].'_disabled">'.$select_options.'</select><input type="hidden" id="'.$args['id'].'" name="'.$args['name'].'" value="' . esc_attr($wp_data_value) . '" />';
					} else {
						$display = (isset($args['display'])) ? 'style="display:'.$args['display'].';"' : '';
						$attr_value = (isset($args['attr_value'])) ? 'data-value="'.esc_attr($wp_data_value).'"' : '';
						echo '<select '.$attr_value.' '.$display.' id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'">'.$select_options.'</select>';
						
					}
					
					break;
				case 'input':
					$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
					if($args['subtype'] != 'checkbox'){
						$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
						$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
						$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
						$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
						$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
						if(isset($args['disabled'])){
							// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
							echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
						} else {
							echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
						}
						/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/
						
					} else {
						$checked = ($value) ? 'checked' : '';
						echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
					}
					break;
				case 'wysiwyg':
				$args = array(
					'textarea_name' => $args['name'],
					'media_buttons' => $args['media_buttons'],
					); 
					wp_editor( $wp_data_value, $args['id'],$args); 
					break;
				default:
					# code...
					break;
			}
		}
		public function saveCustomPostTypeMetaBoxData( $post_id ) {
			/*
			 * We need to verify this came from our screen and with proper authorization,
			 * because the save_post action can be triggered at other times.
			 */
		
			// Check if our nonce is set.
			if ( ! isset( $_POST['link-tracking_links_meta_box_nonce'] ) ) {
				return;
			}
	
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['link-tracking_links_meta_box_nonce'], 'link-tracking_meta_box' ) ) {
				return;
			}
	
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
	
			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			
			// Make sure that it is set.
			if ( !isset( $_POST[$this->plugin_name.'_url'] ) &&  !isset( $_POST[$this->plugin_name.'_target'] ) &&  !isset( $_POST[$this->plugin_name.'_link_text'] )) {
				return;
			}
			
			/* OK, it's safe for us to save the data now. */
	
			// Sanitize user input.
			$url = sanitize_text_field( $_POST[$this->plugin_name."_url"]);
			$link_text = sanitize_text_field( $_POST[$this->plugin_name."_link_text"]);
			$target = sanitize_text_field( $_POST[$this->plugin_name."_target"]);
			
			update_post_meta($post_id, $this->plugin_name.'_url',$url);	
			update_post_meta($post_id, $this->plugin_name.'_link_text',$link_text);	
			update_post_meta($post_id, $this->plugin_name.'_target',$target);	
		
		}
		public function get_yes_no_list(){
			$yesNoList = array(		
						0 => array('value'=> 'no', 'name' => 'No'),
						1 => array('value'=> 'yes', 'name' => 'Yes'),	  
									
							);
			return $yesNoList;
		}
}
