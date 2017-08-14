<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_field_treeselect' ) ) :

	class acf_field_treeselect extends acf_field {

		public $settings;

		/**
		 * acf_field_treeselect constructor.
		 *
		 * This function will setup the field type data
		 *
		 * @param $settings (array) The plugin settings
		 */
		function __construct( $settings ) {
			$this->name     = 'treeselect';
			$this->label    = __( 'Tree Select', 'acf-treeselect' );
			$this->category = 'basic';
			$this->defaults = array(
				'return_format' => 'array',
			);
			$this->settings = $settings;
			parent::__construct();
		}

		/**
		 * Create extra settings for your field. These are visible when editing a field
		 *
		 * @param $field (array) the $field being edited
		 */
		function render_field_settings( $field ) {
			// Return Format
			acf_render_field_setting( $field, array(
				'label'        => __( 'Return Format', 'acf-treeselect' ),
				'instructions' => __( 'Specify the value returned in the template.', 'acf-treeselect' ),
				'type'         => 'select',
				'choices'      => array(
					'array' => __( "Values (array)", 'acf-treeselect' ),
				),
				'name'         => 'return_format',
			) );
		}

		/**
		 * Create the HTML interface for your field
		 *
		 * @param $field (array) the $field being rendered
		 */
		function render_field( $field ) {
			?>
            <div class="acf-input-wrap acf-treeselect">
                <input type="text" name="<?= $field['name'] ?>" value="<?= $field['value'] ?>"/>
            </div>
			<?php
		}

		/**
		 *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
		 *  Use this action to add CSS + JavaScript to assist your render_field() action.
		 */
		function input_admin_enqueue_scripts() {
			$url     = $this->settings['url'];
			$version = $this->settings['version'];
			wp_register_script( 'acf-input-treeselect', "{$url}assets/js/input.js", array( 'acf-input' ), $version );
			wp_enqueue_script( 'acf-input-treeselect' );
		}

		/**
		 * This filter is applied to the $value after it is loaded from the db
		 *
		 * @param  $value (mixed) the value found in the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value
		 */
		function load_value( $value, $post_id, $field ) {
			return $value;
		}

		/**
		 * This filter is applied to the $value before it is saved in the db
		 *
		 * @param  $value (mixed) the value found in the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value
		 */
		function update_value( $value, $post_id, $field ) {
			return $value;
		}

		/**
		 * This filter is appied to the $value after it is loaded from the db and before it is returned to the template
		 *
		 * @param  $value (mixed) the value which was loaded from the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value (mixed) the formatted value
		 */
		function format_value( $value, $post_id, $field ) {
			if ( empty( $value ) ) {
				return $value;
			}

			return $value;
		}

		/**
		 *  This filter is used to perform validation on the value prior to saving.
		 *  All values are validated regardless of the field's required setting. This allows you to validate and return
		 *  messages to the user if the value is not correct
		 *
		 * @param  $valid (boolean) validation status based on the value and the field's required setting
		 * @param  $value (mixed) the $_POST value
		 * @param  $field (array) the field array holding all the field options
		 * @param  $input (string) the corresponding input name for $_POST value
		 *
		 * @return $valid
		 */
		function validate_value( $valid, $value, $field, $input ) {
			return $valid;
		}

	}

	new acf_field_treeselect( $this->settings );

endif;
