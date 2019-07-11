<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    Link_Tracking
 * @subpackage Link_Tracking/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	 <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
		 <h3>Get Started Tracking your Links in 1 Minute</h3>
		 <ol>
		 <ul><a href="/wp-admin/edit.php?post_type=link_tracking_Links">Create a Link</a></ul>
		 <ul><a href="/wp-admin/edit.php?post_type=link_tracking_Links">See your links impressions and visits</a></ul>
		 </ol>
		
	 <p><a href="https://www.wplauncher.com/contact" target="_blank">Contact Us</a> with any questions.</p>
</div>