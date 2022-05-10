<?php
/*
	Plugin Name: Divi Page Builder Config for Pantheon
	Description: READ THIS BEFORE UTILIZING - This Contains rules for turning on/off Divi Page Builder Features by default 
	known to cause issues within the Pantheon environment. The Divi theme and Divi Page Builder plugin attempt to purge 
	et_cache of static resources before any changes are implemented, exacerbating the very problem trying to be addressed. 
	By installing this as an mu-plugin, the settings are implemented and enforced early enough in the bootstrap process to 
	not cause issues, but a manual purge of et_cache will still be required.
	Version: 0.5
	Author: Cyrus Kia
	Author URI: https://pantheon.io
*/

# Ensuring that this is operating within Pantheon.
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) :

    # ---------------
    # Common variables
    # ---------------
    $theme = wp_get_theme(); // gets the current theme.
    $siteurl = get_site_url(); // returns the current URL for the site.
    $divi_builder_plugin = 'divi-builder/divi-builder.php'; // Divi builder plugin.
    $et_cache_dir = ABSPATH . '../tmp/et-cache';
    $et_cache_cleared = true; // will not work if set to true. if false, makes sure to only purge it once.

    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    // Checks if divi builder exists
    //
    if ( is_plugin_active( $divi_builder_plugin ) ) {

        // Strict disablement of divi options that, when activated, attempts to write a static CSS file in an unwritable place in the Pantheon filesystem.
        $etdivi = get_option( 'et_pb_builder_options' ); // for divi it et_divi, for divi builder plugin et_pb_builder_options
        $etdivi['performance_main_dynamic_module_framework'] = 'off';
        $etdivi['performance_main_dynamic_css']  = 'off';
        $etdivi['performance_main_dynamic_js_libraries']  = 'off';
        $etdivi['performance_main_dynamic_icons']  = 'on';
        $etdivi['advanced_main_et_pb_static_css_file']  = 'off';
        $etdivi['performance_main_critical_css']  = 'off';
        $etdivi['advanced_main_et_pb_product_tour_global']  = 'off';
        $etdivi['performance_main_inline_stylesheet']  = 'off';

        # for each post type these will include customer custom post types. ignore this section for now as it was just a test
        //$etdivi['post_type_integration_main_et_pb_post_type_integration']['post'] = 'off';
        //$etdivi['post_type_integration_main_et_pb_post_type_integration']['page'] = 'off';
        //$etdivi['post_type_integration_main_et_pb_post_type_integration']['project'] = 'off';
        // $etdivi['post_type_integration_main_et_pb_post_type_integration']['gated_content'] = 'off';
        // $etdivi['post_type_integration_main_et_pb_post_type_integration']['memberpressproduct'] = 'off';
        // $etdivi['post_type_integration_main_et_pb_post_type_integration']['memberpressgroup'] = 'off';
        // $etdivi['post_type_integration_main_et_pb_post_type_integration']['testimonial'] = 'off';
        // $etdivi['post_type_integration_main_et_pb_post_type_integration']['testimonials'] = '';
        // $etdivi['post_type_integration_main_et_pb_post_type_integration']['integrations_detail'] = 'off';



        update_option( 'et_pb_builder_options', $etdivi );

    }
    // is the dir there?
    if ( !is_dir($et_cache_dir ) {
        mkdir( "$et_cache_dir" ); 
    }
    /*
        The following works, but does it too often. Will adjust in future iterations. The second boolean set to true so it does not fire.
        need to do some basic date math to keep it from firing too often...
        date("Y-m-d");
        => string(10) "2022-03-25"
        date("m");
        => string(2) "03"
        date("d");
        => string(2) "25"
    */
    if ( is_dir($et_cache_dir) and !$et_cache_cleared ) {
        error_log('et_cache exists! purging...');
        $et_cache_files = glob( $et_cache_dir . '/*' );

        foreach( $et_cache_files as $cached_et_file ){

            if(is_file($cached_et_file)) {
                // Delete the given file
                unlink($cached_et_file);
            }
        }
        $et_cache_cleared = true;
    }

endif;
