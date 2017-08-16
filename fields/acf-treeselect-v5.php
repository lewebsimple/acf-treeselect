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
				'choices'       => array(),
				'allow_parent'  => false,
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
			// Encode choices (convert from array)
			$field['choices'] = acf_treeselect_encode_choices( $field['choices'] );

			// Choices
			acf_render_field_setting( $field, array(
				'label'        => __( "Choices", 'acf-treeselect' ),
				'instructions' => __( 'Enter each choice on a new line.', 'acf-treeselect' ) . '<br /><br />' . __( "Specify the parent, value and label like this:", 'acf-treeselect' ) . '<br /><br />' . __( 'parent / child : Child', 'acf-treeselect' ),
				'type'         => 'textarea',
				'name'         => 'choices',
			) );
			// Allow parent selection
			acf_render_field_setting( $field, array(
				'label'        => __( "Allow parent selection", 'acf-treeselect' ),
				'instructions' => __( "Don't force selection of last level elements.", 'acf-treeselect' ),
				'type'         => 'true_false',
				'ui'           => 1,
				'name'         => 'allow_parent',
			) );
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
		 * This filter is applied to the $field before it is saved to the database
		 *
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return  $field
		 */
		function update_field( $field ) {
			// Decode choices (convert from string)
			$field['choices'] = acf_treeselect_decode_choices( $field['choices'] );

			return $field;
		}

		/**
		 * Create the HTML interface for your field
		 *
		 * @param $field (array) the $field being rendered
		 */
		function render_field( $field ) {
			?>
            <div class="acf-input-wrap acf-treeselect">
				<?php
				if ( $select_inputs = acf_treeselect_select_inputs( $field ) ) {
					foreach ( $select_inputs as $select_input ) {
						acf_select_input( $select_input );
					}
				}
				?>
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

			wp_register_style( 'acf-treeselect', "{$url}assets/css/acf-treeselect.css", array( 'acf-input' ), $version );
			wp_enqueue_style( 'acf-treeselect' );
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
			// Decode value string as an array
			if ( ! is_string( $value ) ) {
				return array();
			}
			$parts  = array_merge( array( 0 ), explode( '/', $value ) );
			$output = array();
			$value  = &$output;
			while ( count( $parts ) ) {
				$current_value = array_shift( $parts );
				if ( reset( $parts ) ) {
					$output[ $current_value ] = array( 'value' => reset( $parts ) );
					$output                   = &$output[ $current_value ];
				}
			}

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
			// Encode value array as a string
			if ( ! is_array( $value ) ) {
				return '';
			}
			$parts = array();
			$value = reset( $value );
			while ( ! empty( $value['value'] ) ) {
				$parts[] = $value['value'];
				$value   = $value[ $value['value'] ];
			}

			return implode( '/', $parts );
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

/**
 * Convert hierarchical choices array to string
 *
 * @param array $choices Choices array to convert
 * @param string $parent Initial parent value
 *
 * @return string
 */
function acf_treeselect_encode_choices( $choices = array(), $parent = '' ) {
	if ( ! is_array( $choices ) || empty( $choices ) ) {
		return '';
	}
	$string = '';

	foreach ( $choices as $value => $choice ) {
		$choice_parent = $parent;
		if ( count( $choice['children'] ) > 0 ) {
			$string        .= $choice_parent . $value . ' : ' . $choice['label'] . "\n";
			$choice_parent .= $value . ' / ';
			$string        .= acf_treeselect_encode_choices( $choice['children'], $choice_parent );
		} else {
			$string .= $choice_parent . $value . ' : ' . $choice['label'] . "\n";
		}
	}

	return $string;
}

/**
 * Convert choices string to hierarchical array
 *
 * @param string $string Choices string to convert
 *
 * @return array
 */
function acf_treeselect_decode_choices( $string = '' ) {
	if ( ! is_string( $string ) ) {
		return array();
	}
	$choices = array();

	$lines = explode( "\n", $string );

	foreach ( $lines as $line ) {
		$line    = explode( ' : ', $line );
		$parents = explode( ' / ', $line[0] );
		$value   = array_pop( $parents );
		$label   = trim( $line[1] );

		$children = &$choices;
		while ( count( $parents ) ) {
			$parent_value = array_shift( $parents );
			if ( ! isset( $children[ $parent_value ] ) ) {
				$children[ $parent_value ] = array(
					'label'    => $parent_value,
					'children' => array(),
				);
			}
			$children = &$children[ $parent_value ]['children'];
		}
		if ( ! isset( $children[ $value ] ) ) {
			$children[ $value ] = array(
				'label'    => $label,
				'children' => array(),
			);
		} else {
			$children[ $value ]['label'] = $label;
		}
	}

	return $choices;
}

/**
 * Build select inputs attributes recursively.
 *
 * @param $field
 * @param string $parent
 *
 * @return array|bool
 */
function acf_treeselect_select_inputs( $field, $parent = '0' ) {
	$select_inputs = array();

	$parents        = explode( '/', $parent );
	$choices        = $field['choices'];
	$subfield_name  = '';
	$show           = true;
	$value          = $field['value'];
	$current_parent = '';

	while ( count( $parents ) ) {
		$current_parent = array_shift( $parents );

		// Walk down choices array
		if ( isset( $choices[ $current_parent ] ) && isset( $choices[ $current_parent ]['children'] ) ) {
			$choices = $choices[ $current_parent ]['children'];
		} else if ( $current_parent != '0' ) {
			return false;
		}

		// Walk down value array
		if ( isset( $value[ $current_parent ] ) ) {
			$show  = true;
			$value = $value[ $current_parent ];
		} else {
			$show  = $value['value'] == $current_parent;
			$value = null;
		}

		// Append to data-parent attribute
		$subfield_name .= '[' . $current_parent . ']';
	}

	// Create select input
	$options = array( '' => '- ' . __( "Select", 'acf-treeselect' ) . ' -' );
	foreach ( $choices as $choice_key => $choice ) {
		$options[ $choice_key ] = $choice['label'];
	}
	$select_input = array(
		'name'    => $field['name'] . $subfield_name . '[value]',
		'choices' => $options,
	);

	// Set value and show / hide
	if ( ! empty( $value['value'] ) ) {
		$select_input['value'] = $value['value'];
	} else if ( $current_parent != '0' && ! $show ) {
		$select_input['style'] = 'display: none;';
	}
	$select_inputs[] = $select_input;

	// Create select inputs for children
	foreach ( $choices as $choice_key => $choice ) {
		if ( count( $choice['children'] ) ) {
			$select_inputs = array_merge( $select_inputs, acf_treeselect_select_inputs( $field, $parent . '/' . $choice_key ) );
		}
	}

	return $select_inputs;
}
