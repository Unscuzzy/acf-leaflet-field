<?php
/*
Plugin Name: Advanced Custom Fields: Leaflet Map Field
Plugin URI: https://github.com/Unscuzzy/acf-leaflet-field
Description: This plugin adds a Leaflet map field to the Advanced Custom Fields plugin.
Version: 1.0.0
Author: Unscuzzy
Author URI: https://unscuzzy.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Copyright (C) 2018  Unscuzzy (email : contact@unscuzzy.com)

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


// check if class already exists
if ( !class_exists( 'Zz_acf_plugin_Leaflet' ) ) {

    class Zz_acf_plugin_Leaflet
    {
        // vars
        var $settings;

        /**
         *  __construct
         *
         * @since    1.0.0
         *
         * @param    void
         * @return    void
         */

        function __construct()
        {
            // settings
            // - these will be passed into the field class.
            $this->settings = array(
                'version' => '1.0.0',
                'url' => plugin_dir_url( __FILE__ ),
                'path' => plugin_dir_path( __FILE__ )
            );

            // include field
            add_action( 'acf/include_field_types', array($this, 'include_field') ); // v5
        }


        /**
         *  This function will include the field type class
         *
         * @since    1.0.0
         *
         * @param    $version (int) major ACF version. Defaults to 4
         * @return    void
         */

        function include_field($version = 5)
        {
            // load textdomain
            load_plugin_textdomain( 'zz', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );

            // include
            include_once('fields/class-Zz-acf-field-Leaflet-v' . $version . '.php');
        }

    }


    // initialize
    new Zz_acf_plugin_Leaflet();

}