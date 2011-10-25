<?php
/*
Plugin Name: WP Sanitize
Plugin URI: http://www.vibethemes.com/wp-sanitize-wordpress-security-plugin/
Description: A super-simple plugin to optimize your blog
Version: 1.0
Author: VibeThemes
Author URI: http://www.vibethemes.com
License: GPL2
*/

// Set default values
$defaults = array(
    'rds_link'          => 1,
    'wlwmanifest_link'  => 1,
    'wp_generator'      => 1,
    'rds_link'          => 1,
    'wptexturize'       => 1,
    'wp_filter_kses'    => 1
);
add_option('wpsanitize', $defaults, '', 'yes');

function wps_optimization_cron_on(){
    wp_schedule_event(time(), 'daily', 'wps_optimize_database'); // rdd wps_optimize_database to wp cron events
}
function wps_optimization_cron_off(){
    wp_clear_scheduled_hook('wps_optimize_database'); // remove wps_optimize_database from wp cron events
}
register_activation_hook(__FILE__,'wps_optimization_cron_on'); // run wps_optimization_cron_on at plugin activation
register_deactivation_hook(__FILE__,'wps_optimization_cron_off'); // run wps_optimization_cron_off at plugin deactivation

// Init plugin options to white list our options
add_action( 'admin_init', 'wpsanitize_options_init' );
function wpsanitize_options_init() {
    register_setting( 'wps_options', 'wpsanitize');
}

// Load up the menu page
add_action( 'admin_menu', 'wp_sanitize_options' );
function wp_sanitize_options () {
	add_options_page( __( 'WP Sanitize' ), __( 'WP Sanitize' ), 'manage_options', 'wp_sanitize', 'wp_sanitize_options_page' );
}

// Reset to defaults
if (isset($_POST['reset-wpsanitize'])) {
    update_option('wpsanitize', $defaults);
    $msg =  '<div class="updated" id="message"><p><strong>Settings Reset to Default</strong></p></div>';
}

// Manually Optimize Database
if (isset($_POST['wps-optimizedb'])) {
    wps_optimize_database ();
    $msg =  '<div class="updated" id="message"><p><strong>Database Optimized</strong></p></div>';
}

function wp_sanitize_options_page() { global $msg; $opt = get_option('wpsanitize'); ?>

<div class="wrap"><div id="icon-tools" class="icon32"><br /></div>
<h2><?php _e("WP Sanitize Options Page"); ?></h2> <?php echo $msg; ?>

<div class="tool-box">
	<h3 class="title"><?php _e('Clean up wp_head and Content'); ?></h3>

    <form method="post" action="options.php"><?php settings_fields( 'wps_options' ); ?>

	<table class="form-table">

	<tr valign="top"><th scope="row"><?php _e( 'RSD Link' ); ?></th>
	    <td>
	        <input id="wpsanitize[rds_link]" name="wpsanitize[rds_link]" type="checkbox" value="1" <?php checked( '1', $opt['rds_link'] ); ?> />
	        <label class="description" for="wpsanitize[rds_link]"><?php _e( 'Remove Really simple discovery link' ); ?></label>
	    </td>
	</tr>

	<tr valign="top"><th scope="row"><?php _e( 'Windows Live Link' ); ?></th>
	    <td>
	        <input id="wpsanitize[wlwmanifest_link]" name="wpsanitize[wlwmanifest_link]" type="checkbox" value="1" <?php checked( '1', $opt['wlwmanifest_link'] ); ?> />
	        <label class="description" for="wpsanitize[wlwmanifest_link]"><?php _e( 'Remove Windows Live Writer link ' ); ?></label>
	    </td>
	</tr>

	<tr valign="top"><th scope="row"><?php _e( 'WP Version Number' ); ?></th>
	    <td>
	        <input id="wpsanitize[wp_generator]" name="wpsanitize[wp_generator]" type="checkbox" value="1" <?php checked( '1', $opt['wp_generator'] ); ?> />
	        <label class="description" for="wpsanitize[wp_generator]"><?php _e( 'Remove the version number (recommended for security reasons)' ); ?></label>
	    </td>
	</tr>

	<tr valign="top"><th scope="row"><?php _e( 'Content Curly Quotes' ); ?></th>
	    <td>
	        <input id="wpsanitize[wptexturize]" name="wpsanitize[wptexturize]" type="checkbox" value="1" <?php checked( '1', $opt['wptexturize'] ); ?> />
	        <label class="description" for="wpsanitize[wptexturize]"><?php _e( 'Remove curly quotes' ); ?></label>
	    </td>
	</tr>

	<tr valign="top"><th scope="row"><?php _e( 'User Profile HTML' ); ?></th>
	    <td>
	        <input id="wpsanitize[wp_filter_kses]" name="wpsanitize[wp_filter_kses]" type="checkbox" value="1" <?php checked( '1', $opt['wp_filter_kses'] ); ?> />
	        <label class="description" for="wpsanitize[wp_filter_kses]"><?php _e( 'Allow HTML in user profiles' ); ?></label>
	    </td>
	</tr>

	</table>

	<p class="submit">
	    <input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
	</p>
	</form>

</div>

<div class="tool-box">
	<h3 class="title"><?php _e('Optimize WordPress Database'); ?></h3>

	<p><?php _e('By default this plugin is set to optimize WordPress database tables daily by removing overhead (useless/excess data in a SQL table created by manipulating the database). This is an automated process but can be done manually as well by clicking on the button below.'); ?></p>
    <p>This plugin is brought to you by <a href="http://www.vibethemes.com" target="_blank">VibeThemes.com</a></p>

    <form method="post">
    <input type="hidden" name="wps-optimizedb" value="1">
    <p><input type="submit" class="button" value="<?php _e('Optimize Database Now') ?>" /></p>
    </form>

</div>


<form method="post"><br />
<input type="hidden" name="reset-wpsanitize" value="1">
<p><input type="submit" class="button-active" onclick="return confirm('Are you sure you want to reset to default settings?')" value="<?php _e('Reset to Defaults') ?>" /></p>
</form>

<script type="text/javascript">
    var $jq = jQuery.noConflict();
    $jq(document).ready(function() { $jq(".updated").fadeIn(1000).fadeTo(1000, 1).fadeOut(1000); });
</script>
</div>

<?php }

$opt = get_option('wpsanitize');

// Clean up wp_head
// Remove Really simple discovery link
if ($opt['rds_link']==1) {
    remove_action('wp_head', 'rsd_link');
}

// Remove Windows Live Writer link
if ($opt['wlwmanifest_link']==1) {
    remove_action('wp_head', 'wlwmanifest_link');
}

// Remove the version number
if ($opt['wp_generator']==1) {
    remove_action('wp_head', 'wp_generator');
}

// Remove curly quotes
if ($opt['wptexturize']==1) {
    remove_filter('the_content', 'wptexturize');
    remove_filter('comment_text', 'wptexturize');
}

// Allow HTML in user profiles
if ($opt['wp_filter_kses']==1) {
    remove_filter('pre_user_description', 'wp_filter_kses');
}

//Optimize Database
function wps_optimize_database(){
    global $wpdb; // get access to $wpdb object
    $all_tables = $wpdb->get_results('SHOW TABLES',ARRAY_A); // get all table names
    foreach ($all_tables as $tables){ // loop through every table name
        $table = array_values($tables); // get table name out of array
        $wpdb->query("OPTIMIZE TABLE ".$table[0]); // run the optimize SQL command on the table
    }
}

?>