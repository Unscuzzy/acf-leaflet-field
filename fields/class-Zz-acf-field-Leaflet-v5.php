<?php

// exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


// check if class already exists
if ( !class_exists( 'Zz_acf_field_Leaflet' ) && class_exists( 'acf_field' ) ) {

    class Zz_acf_field_Leaflet extends acf_field
    {

        /**
         * @var array $defaults Default field options
         * @var array $settings Default plugins options
         */
        var $defaults, $settings;

        /**
         *    Initialize function which sets the field’s data
         *   such as name, label, category and defaults
         */
        function __construct($settings)
        {
            $this->name = 'leaflet_map_field';
            $this->label = __( 'Leaflet Map', 'zz' );
            $this->category = 'jquery';
            $this->defaults = array(
                'lat' => '0',
                'lng' => '0',
                'zoom' => 14,
                'height' => 320
            );

            $this->settings = $settings;

            // do not delete!
            parent::__construct();
        }


        /**
         *  render_field_settings()
         *
         *  Create extra settings for your field. These are visible when editing a field
         *
         * @param    $field (array) the $field being edited
         */
        function render_field_settings($field)
        {
            /*
            *  acf_render_field_setting
            *
            *  This function will create a setting for your field.
             * Simply pass the $field parameter and an array of field settings.
            *  The array of settings does not require a `value` or `prefix`;
             * These settings are found from the $field array.
            *
            *  More than one setting can be added by copy/paste the above code.
            *  Please note that you must also have a matching $defaults value for the field name (font_size)
            */
            // lat
            acf_render_field_setting( $field, array(
                'label' => __( 'Center', 'zz' ),
                'instructions' => __( 'Center the initial map', 'zz' ),
                'type' => 'text',
                'name' => 'lat',
                'prepend' => 'lat',
                'placeholder' => $this->defaults['lat']
            ) );

            // lng
            acf_render_field_setting( $field, array(
                'label' => __( 'Center', 'zz' ),
                'instructions' => __( 'Center the initial map', 'zz' ),
                'type' => 'text',
                'name' => 'lng',
                'prepend' => 'lng',
                'placeholder' => $this->defaults['lng'],
                '_append' => 'lat'
            ) );

            // zoom
            acf_render_field_setting( $field, array(
                'label' => __( 'Zoom', 'zz' ),
                'instructions' => __( 'Set the initial zoom level', 'zz' ),
                'type' => 'text',
                'name' => 'zoom',
                'placeholder' => $this->defaults['zoom']
            ) );

            // Height
            acf_render_field_setting( $field, array(
                'label' => __( 'Height', 'zz' ),
                'instructions' => __( 'Customise the map height', 'zz' ),
                'type' => 'text',
                'name' => 'height',
                'append' => 'px',
                'placeholder' => $this->defaults['height']
            ) );

        }


        /**
         *  Create the HTML interface for your field
         *
         * @param    $field (array) the $field being edited
         */
        function render_field($field)
        {
            if ( empty( $field['value'] ) )
                $field['value'] = array();

            // value
            $field['value'] = wp_parse_args( $field['value'], array(
                'address' => '',
                'lat' => '',
                'lng' => '',
                'zoom' => ''
            ) );

            // default options
            foreach ($this->defaults as $k => $v) {
                if ( empty( $field[$k] ) )
                    $field[$k] = $v;
            }
            /*
            echo '<pre>';
            print_r( $field );
            echo '</pre>';
            */
            // vars
            $atts = array(
                'id' => $field['id'],
                'class' => "acf-leaflet-map {$field['class']}",
                'data-lat' => $field['value']['lat'] ? $field['value']['lat'] : $field['lat'],
                'data-lng' => $field['value']['lng'] ? $field['value']['lng'] : $field['lng'],
                'data-zoom' => $field['value']['zoom'] ? $field['value']['zoom'] : $field['zoom']
            );
            ?>

            <div <?php acf_esc_attr_e( $atts ); ?>>

                <div class="acf-hidden">
                    <?php foreach ($field['value'] as $k => $v):
                        acf_hidden_input( array('name' => $field['name'] . '[' . $k . ']', 'value' => $v, 'class' => 'input-' . $k) );
                    endforeach; ?>
                </div>

                <input type="text" id="<?php echo esc_attr( $field['id'] ); ?>-search" class="form-control"
                       value="<?php echo esc_attr( $field['value']['address'] ) ?>"
                       placeholder="<?php echo __( 'Search an address', 'zz' ); ?>">

                <div id="<?php echo esc_attr( $field['id'] ); ?>-map"
                     style="height:<?php echo esc_attr( $field['height'] ); ?>px; z-index: 1;"></div>
            </div>
            <?php
        }


        /**
         *  validate_value()
         *
         *  This filter is used to perform validation on the value prior to saving.
         *  All values are validated regardless of the field's required setting. This allows you to validate and return
         *  messages to the user if the value is not correct
         *
         * @param    $valid (boolean) validation status based on the value and the field's required setting
         * @param    $value (mixed) the $_POST value
         * @param    $field (array) the field array holding all the field options
         * @param    $input (string) the corresponding input name for $_POST value
         * @return    $valid
         */
        function validate_value($valid, $value, $field, $input)
        {
            // bail early if not required
            if ( !$field['required'] )
                return $valid;

            if ( empty( $value ) || empty( $value['lat'] ) || empty( $value['lng'] ) )
                return false;


            return $valid;

        }


        /**
         *  update_value()
         *
         *  This filter is applied to the $value before it is saved in the db
         *
         * @param    $value (mixed) the value found in the database
         * @param    $post_id (mixed) the $post_id from which the value was loaded
         * @param    $field (array) the field array holding all the field options
         * @return    $value
         */
        function update_value($value, $post_id, $field)
        {

            if ( empty( $value ) || empty( $value['lat'] ) || empty( $value['lng'] ) )
                return false;

            return $value;
        }


        /**
         *  input_admin_enqueue_scripts()
         *
         *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
         *  Use this action to add CSS + JavaScript to assist your render_field() action.
         */
        function input_admin_enqueue_scripts()
        {
            // vars
            $url = $this->settings['url'];

            // register & include JS
            wp_register_script( 'jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js', array(), null, true );
            wp_register_script( 'leaflet', 'https://cdn.jsdelivr.net/leaflet/1/leaflet.js', array(), null, true );
            wp_register_script( 'places', 'https://cdn.jsdelivr.net/npm/places.js@1.6.0', array(), null, true );
            wp_register_script( 'leaflet-providers', $url . "assets/js/leaflet-providers.min.js", array('leaflet'), null, true );
            wp_register_script( 'input', $url . "assets/js/input.js", array('jquery', 'leaflet', 'leaflet-providers', 'places'), null, true );


            if ( !wp_script_is( 'jquery', 'enqueued' ) )
                wp_enqueue_script( 'jquery' );

            wp_enqueue_script( 'leaflet' );
            wp_enqueue_script( 'input' );
            wp_enqueue_script( 'places' );
            wp_enqueue_script( 'leaflet-providers' );

            // register & include CSS
            wp_register_style( 'leaflet', "https://cdn.jsdelivr.net/leaflet/1/leaflet.css", array(), null );
            wp_enqueue_style( 'leaflet' );

        }

        /**
         *  This action is called in the admin_footer action on the edit screen where your field is created.
         *  Use this action to add CSS and JavaScript to assist your render_field() action.
         *
         */
        /*function input_admin_footer() {

        }*/

        /**
         *  This action is called in the admin_head action on the edit screen where your field is created.
         *  Use this action to add CSS and JavaScript to assist your render_field() action.
         */
        /*function input_admin_head() {

        }*/

        /**
         *  This function is called once on the 'input' page between the head and footer
         *  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
         *  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
         *  seen on comments / user edit forms on the front end. This function will always be called, and includes
         *  $args that related to the current screen such as $args['post_id']
         *
         * @param    $args (array)
         */
        /*function input_form_data( $args ) {

        }*/

        /**
         *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
         *  Use this action to add CSS + JavaScript to assist your render_field_options() action.
         */
        /*function field_group_admin_enqueue_scripts() {

        }*/

        /**
         *  This action is called in the admin_head action on the edit screen where your field is edited.
         *  Use this action to add CSS and JavaScript to assist your render_field_options() action.
         */
        /*function field_group_admin_head() {

        }*/

        /**
         *  This filter is applied to the $value after it is loaded from the db
         *
         * @param    $value (mixed) the value found in the database
         * @param    $post_id (mixed) the $post_id from which the value was loaded
         * @param    $field (array) the field array holding all the field options
         * @return    $value
         */
        /*function load_value( $value, $post_id, $field ) {

            return $value;

        }*/

        /**
         *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
         *
         * @param    $value (mixed) the value which was loaded from the database
         * @param    $post_id (mixed) the $post_id from which the value was loaded
         * @param    $field (array) the field array holding all the field options
         *
         * @return    $value (mixed) the modified value
         */
        /*function format_value( $value, $post_id, $field ) {

            // bail early if no value
            if( empty($value) ) {

                return $value;

            }

            // apply setting
            if( $field['font_size'] > 12 ) {

                // format the value
                // $value = 'something';

            }

            // return
            return $value;
        }*/


        /**
         *  This action is fired after a value has been deleted from the db.
         *  Please note that saving a blank value is treated as an update, not a delete
         *
         * @param    $post_id (mixed) the $post_id from which the value was deleted
         * @param    $key (string) the $meta_key which the value was deleted
         * @return    n/a
         */
        /*function delete_value( $post_id, $key ) {

        }*/

        /**
         *
         *  This filter is applied to the $field after it is loaded from the database
         *
         * @param    $field (array) the field array holding all the field options
         * @return    $field
         */
        /* function load_field( $field ) {

            return $field;

        }*/

        /**
         *  This filter is applied to the $field before it is saved to the database
         *
         * @param    $field (array) the field array holding all the field options
         * @return    $field
         */
        /*function update_field( $field ) {

            return $field;

        }*/

        /**
         *  This action is fired after a field is deleted from the database
         *
         * @param    $field (array) the field array holding all the field options
         */
        /*
        function delete_field( $field ) {

        }
        */

    }

    // initialize
    new Zz_acf_field_Leaflet( $this->settings );

}