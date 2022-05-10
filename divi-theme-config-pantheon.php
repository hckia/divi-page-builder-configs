<?php
/*
	Plugin Name: Divi Theme Config for Pantheon
	Description: READ THIS BEFORE UTILIZING - This plugin is still in the testing phase. This Contains rules for turning on/off Divi Theme Features by default known 
	to cause issues within the Pantheon environment. Cyrus has created a separate mu plugin for the Divi Page Builder that
	can be found here. Divi theme and Divi page builder plugin attempt to purge et_cache of static resources before any 
	changes are implemented, exacerbating the very problem trying to be addressed. By installing this as an mu-plugin, 
	the settings are implemented and enforced early enough in the bootstrap process to not cause issues, but a manual 
	purge of et_cache will still be required. Future modifications may be done by Pantheon.
	Version: 0.6
	Author: Steve Ryan
	Author URI: https://engineering.asu.edu/
*/

# Ensuring that this is operating within Pantheon.
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) :

    # ---------------
    # Common variables
    # ---------------
    $theme = wp_get_theme(); // gets the current theme.
    $siteurl = get_site_url(); // returns the current URL for the site.

    # ---------------
    # Plugin and theme options specifically for ASU Divi Theme.
    # ---------------

    // Checks for the existence for ASU Divi. Also checks for Divi as either the active or parent theme.
    if ( 'Divi-child' == $theme->name || 'Divi' == $theme->name || 'Divi' == $theme->parent_theme ) {

        // Options for Classic Editor can be edited by a user, and are OK with the out-of-the-box defaults.

        // add any divi plugin dependencies here and uncomment. Below is an example...
/*        if ( is_plugin_inactive( 'accessible-divi/divi-accessibility.php' ) ) {
            activate_plugin( 'accessible-divi/divi-accessibility.php' );
        } */

        // Strict disablement of Divi option that, when activated, attempts to write a static CSS file in an unwritable place in the Pantheon filesystem.
        $etdivi = get_option( 'et_divi' );
        $etdivi['et_pb_static_css_file'] 			= 'off';
        $etdivi['et_pb_css_in_footer']   			= 'off';

        // Divi v4.10+ introduced new theme options which cache various parts of the builder to the filesystem to reduce load times.
        // Within Pantheon, those cached files are never deleted, which eventually causes the builder to timeout while loading admin pages.
        // Disabling these options prevents these errors.
        $etdivi['divi_dynamic_module_framework'] 	= 'false';
        $etdivi['divi_dynamic_css'] 				= 'false';
        $etdivi['divi_critical_css'] 				= 'false';
        $etdivi['divi_dynamic_js_libraries'] 		= 'false';
        $etdivi['divi_enable_jquery_body'] 			= 'false';
        $etdivi['divi_google_fonts_inline'] 		= 'false';

        update_option( 'et_divi', $etdivi );
    }

    endif;
