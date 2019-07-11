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
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);
		add_action('add_meta_boxes_link_tracking_links', array( $this, 'setupCustomPostTypeMetaboxes' ));
		add_action( 'save_post_link_tracking_links', array( $this, 'saveCustomPostTypeMetaBoxData') );
		add_shortcode( 'link_tracking', array( $this, 'linkTrackingShortcode' ));

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/link-tracking-admin.js', array( 'jquery' ), $this->version, false );

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
					'not_found_in_trash'=>'No Links Found in Trash'
				),
			'public'=>true,
			'description'=>'A Link you can track', 
			'exclude_from_search'=>true,
			'show_ui'=>true,
			'show_in_menu'=>$this->plugin_name,
			'supports'=>array('title', 'custom_fields'),
			'taxonomies'=>array('category','post_tag'));
 
	// Post type, $args - the Post Type string can be MAX 20 characters
	register_post_type( 'link_tracking_Links', $LinkArgs );
}
public function linkTrackingShortcode( $atts, $content = "" ) {
	$a = shortcode_atts( array(
			'link'=>'',	
			'classes'=>'',
			'style'=>'',
			'hidden'=>'',					
			), $atts );
	$href = get_post_meta($a['link'], $this->plugin_name.'_url', true );
	$link_text = get_post_meta($a['link'], $this->plugin_name.'_link_text', true );
	$target = get_post_meta($a['link'], $this->plugin_name.'_target', true );

	return '<a class="link_tracking_link '.esc_attr($a['classes']).'" target="'.esc_attr($target).'" href="'.esc_attr($href).'" style="'.esc_attr($a['style']).'">'.$link_text.'</a>';
	}
public function addPluginAdminMenu() {
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	add_menu_page( 'Link Tracking', 'Link Tracking', 'administrator', $this->plugin_name, array( $this, 'display_plugin_admin_dashboard' ), plugin_dir_url( FILE ) . "data:image/svg+xml;base64,PHN2ZyBoZWlnaHQ9JzMwMHB4JyB3aWR0aD0nMzAwcHgnICBmaWxsPSIjMDAwMDAwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NCA2NCIgeD0iMHB4IiB5PSIwcHgiPjxwYXRoIGQ9Ik0zOCw0NUgyNmExLDEsMCwwLDAtMSwxdjRhMSwxLDAsMCwwLDEsMUgzOGExLDEsMCwwLDAsMS0xVjQ2QTEsMSwwLDAsMCwzOCw0NVptLTEsNEgyN1Y0N0gzN1oiPjwvcGF0aD48cGF0aCBkPSJNMzIsMzlBMTEsMTEsMCwxLDAsMjEsMjgsMTEuMDEzLDExLjAxMywwLDAsMCwzMiwzOVptMC0yYTguOTczLDguOTczLDAsMCwxLTguNzY4LTExSDI1djdoMlYyNmgydjNoMlYyNmgydjdoMlYyNmgydjNoMlYyNmgxLjc2OEE4Ljk3Myw4Ljk3MywwLDAsMSwzMiwzN1ptMC0xOGE5LDksMCwwLDEsOC4wNSw1SDIzLjk1QTksOSwwLDAsMSwzMiwxOVoiPjwvcGF0aD48cGF0aCBkPSJNNi4yOTMsNi43MDcsMTkuNDI5LDE5Ljg0M0ExNC45MjksMTQuOTI5LDAsMCwwLDE3LjU1OCwyNEgxNGExLDEsMCwwLDAtMSwxVjYxYTEsMSwwLDAsMCwxLDFINTBhMSwxLDAsMCwwLDEtMVYyNWExLDEsMCwwLDAtMS0xSDQ2LjQ0MmExNC45NjEsMTQuOTYxLDAsMCwwLTIyLjYtOC41NzFMMTAuNzA3LDIuMjkzYTEsMSwwLDAsMC0xLjQxNCwwbC0zLDNBMSwxLDAsMCwwLDYuMjkzLDYuNzA3Wk0yNyw2MFY1NUgzN3Y1Wk00OSwyNlY2MEgzOVY1NGExLDEsMCwwLDAtMS0xSDI2YTEsMSwwLDAsMC0xLDF2NkgxNVYyNmgyLjE0OWExNSwxNSwwLDEsMCwyOS43LDBabS00LDJBMTMsMTMsMCwxLDEsMzIsMTUsMTMuMDE1LDEzLjAxNSwwLDAsMSw0NSwyOFpNMTAsNC40MTQsMjIuMjI5LDE2LjY0M2ExNS4yMjMsMTUuMjIzLDAsMCwwLTEuNTg2LDEuNTg2TDguNDE0LDZaIj48L3BhdGg+PHJlY3QgeD0iMjkiIHk9IjMxIiB3aWR0aD0iMiIgaGVpZ2h0PSIyIj48L3JlY3Q+PHJlY3QgeD0iMzciIHk9IjMxIiB3aWR0aD0iMiIgaGVpZ2h0PSIyIj48L3JlY3Q+PC9zdmc+", 26 );
	}
	public function setupCustomPostTypeMetaboxes(){
		add_meta_box('link_tracking_links_data_meta_box', 'Meta Box Data', array($this,'link_tracking_links_data_meta_box'), 'link_tracking_links', 'normal','high' );
		}
	public function link_tracking_links_data_meta_box($post){
			// Add a nonce field so we can check for it later.
			wp_nonce_field( $this->plugin_name.'_meta_box', $this->plugin_name.'_links_meta_box_nonce' );
	
			echo '<div class="link_tracking_links_field_containers">';
			echo '<ul class="link_tracking_links_data_metabox">';
		
			echo '<li><label for="'.$this->plugin_name.'_url">';
			_e( 'URL', $this->plugin_name.'_url' );
			echo '</label>';
			$args = array (
									'type'      => 'input',
						'subtype'	  => 'url',
						'id'	  => $this->plugin_name.'_url',
						'name'	  => $this->plugin_name.'_url',
						'required' => 'required="required"',
						'get_options_list' => '',
						'value_type'=>'normal',
						'wp_data' => 'post_meta',
						'post_id'=> $post->ID
							);
					// this gets the post_meta value and echos back the input
			$this->plugin_name_render_settings_field($args);
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
			$this->plugin_name_render_settings_field($args);
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
				  'required' => '',
				  'get_options_list' => 'get_target_list',
				  'value_type'=>'normal',
				  'wp_data' => 'post_meta',
					'post_id'=> $post->ID
	          );
					// this gets the post_meta value and echos back the input
			$this->plugin_name_render_settings_field($args);
			echo '</li>';
			echo '<li><label for="'.$this->plugin_name.'_clicks">';
			_e( 'Clicks', $this->plugin_name.'_clicks' );
			echo '</label>';
			$args = array (
									'type'      => 'input',
						'subtype'	  => 'number',
						'id'	  => $this->plugin_name.'_clicks',
						'name'	  => $this->plugin_name.'_clicks',
						'required' => '',
						'get_options_list' => '',
						'value_type'=>'normal',
						'wp_data' => 'post_meta',
						'post_id'=> $post->ID
							);
					// this gets the post_meta value and echos back the input
			$this->plugin_name_render_settings_field($args);
			echo '</li><li><label for="'.$this->plugin_name.'_impressions">';
			_e( 'Impressions', $this->plugin_name.'_impressions' );
			echo '</label>';
			unset($args);
				$args = array (
									'type'      => 'input',
						'subtype'	  => 'number',
						'id'	  => $this->plugin_name.'_impressions',
						'name'	  => $this->plugin_name.'_impressions',
						'required' => '',
						'get_options_list' => '',
						'value_type'=>'normal',
						'wp_data' => 'post_meta',
						'post_id'=> $post->ID
							);
			// this gets the post_meta value and echos back the input
			$this->plugin_name_render_settings_field($args);
		
			echo '</li></ul></div>';
		
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
		public function plugin_name_render_settings_field($args) {
			if($args['wp_data'] == 'option'){
				$wp_data_value = get_option($args['name']);
			} elseif($args['wp_data'] == 'post_meta'){
				$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
			}
			
			switch ($args['type']) {
				case 'select':
					// get the options list array from the get_options_list array value
					$wp_data_list = call_user_func(array('Wpmerchant_Admin', $args['get_options_list']), $args);
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
		function saveCustomPostTypeMetaBoxData( $post_id ) {
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
}