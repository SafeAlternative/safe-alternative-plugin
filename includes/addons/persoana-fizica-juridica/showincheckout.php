<?php

class Checkout_Form
{
	private $defaults;

	public function __construct()
	{
		$this->defaults = array(
			'safealternative_pfiz_label'                  => esc_html__('Persoana Fizica', 'safealternative_pf_pj'),
			'safealternative_pfiz_cnp_label'              => esc_html__('CNP', 'safealternative_pf_pj'),
			'safealternative_pfiz_cnp_placeholder'        => esc_html__('Introduceti Codul numeric personal', 'safealternative_pf_pj'),
			'safealternative_pfiz_cnp_visibility'         => 'no',
			'safealternative_pfiz_cnp_required'           => 'no',
			'safealternative_pfiz_cnp_error'              => esc_html__('Datorita legislatiei in vigoare trebuie sa completati campul CNP', 'safealternative_pf_pj'),
			'safealternative_pjur_label'                  => esc_html__('Persoana Juridica', 'safealternative_pf_pj'),
			'safealternative_pjur_visibility'			  => 'yes',
			'safealternative_pjur_company_label'          => esc_html__('Nume Firma', 'safealternative_pf_pj'),
			'safealternative_pjur_company_placeholder'    => esc_html__('Introduceti numele firmei dumneavoastra', 'safealternative_pf_pj'),
			'safealternative_pjur_company_visibility'     => 'yes',
			'safealternative_pjur_company_required'       => 'yes',
			'safealternative_pjur_company_error'          => esc_html__( 'Pentru a putea plasa comanda, avem nevoie de numele firmei dumneavoastra', 'safealternative_pf_pj' ),
			'safealternative_pjur_cui_label'              => esc_html__( 'CUI', 'safealternative_pf_pj' ),
			'safealternative_pjur_cui_placeholder'        => esc_html__( 'Introduceti Codul Unic de Inregistrare', 'safealternative_pf_pj' ),
			'safealternative_pjur_cui_visibility'         => 'yes',
			'safealternative_pjur_cui_required'           => 'yes',
			'safealternative_pjur_cui_error'              => esc_html__( 'Pentru a putea plasa comanda, avem nevoie de CUI-ul firmei dumneavoastra', 'safealternative_pf_pj' ),
			'safealternative_pjur_nr_reg_com_label'       => esc_html__( 'Nr. Reg. Com', 'safealternative_pf_pj' ),
			'safealternative_pjur_nr_reg_com_placeholder' => 'J20/20/20.02.2020',
			'safealternative_pjur_nr_reg_com_visibility'  => 'yes',
			'safealternative_pjur_nr_reg_com_required'    => 'yes',
			'safealternative_pjur_nr_reg_com_error'       => esc_html__( 'Pentru a putea plasa comanda, avem nevoie de numarul de ordine in registrul comertului', 'safealternative_pf_pj' ),
			'safealternative_pjur_nume_banca_label'       => esc_html__( 'Nume Banca', 'safealternative_pf_pj' ),
			'safealternative_pjur_nume_banca_placeholder' => esc_html__( 'Numele bancii cu care lucrati', 'safealternative_pf_pj' ),
			'safealternative_pjur_nume_banca_visibility'  => 'no',
			'safealternative_pjur_nume_banca_required'    => 'no',
			'safealternative_pjur_nume_banca_error'       => esc_html__( 'Pentru a putea plasa comanda, avem nevoie de numele bancii cu care lucrati', 'safealternative_pf_pj' ),
			'safealternative_pjur_iban_label'             => esc_html__( 'IBAN', 'safealternative_pf_pj' ),
			'safealternative_pjur_iban_placeholder'       => esc_html__( 'Numarul contului IBAN', 'safealternative_pf_pj' ),
			'safealternative_pjur_iban_visibility'        => 'no',
			'safealternative_pjur_iban_required'          => 'no',
			'safealternative_pjur_iban_error'             => esc_html__( 'Pentru a putea plasa comanda, avem nevoie de numarul contului', 'safealternative_pf_pj' ),
			'safealternative_pf_pj_output'               => 'select',
			'safealternative_pf_pj_default'              => 'pers-fiz',
			'safealternative_pf_pj_label'                => esc_html__('Tip Client', 'safealternative_pf_pj'),
		);
	}

	public function override_checkout_fields($fields)
	{
		$options = get_option('safealternative_pf_pj_option', array());
		$options = wp_parse_args($options, $this->defaults);

		// Adaug field persoana fizica/juridica in checkout
		$ordered_fields['safealternative_pf_pj_type'] = array(
			'type'     => $options['safealternative_pf_pj_output'],
			'label'    => $options['safealternative_pf_pj_label'],
			'required' => true,
			'class'    => array('form-row-wide'),
			'options'  => array(
				'pers-fiz' => $options['safealternative_pfiz_label'],
				'pers-jur' => $options['safealternative_pjur_label'],
			),
			'default'  => $options['safealternative_pf_pj_default'],
			'priority' => 0,
			'clear'    => true,
		);

		if ('radio' == $options['safealternative_pf_pj_output']) {
			$ordered_fields['safealternative_pf_pj_type']['class'][] = 'safealternative_pf_pj_type_radio';
		}

		//daca am selectat optiunea "Doar persoana fizica" sa imi ascunda label-ul
		if ($options['safealternative_pf_pj_output'] == 'hidden') {
			unset($ordered_fields['safealternative_pf_pj_type']['label']);
		}

		// Extra Fields
		$company = $fields['billing_company'];
		unset($fields['billing_company']); //distruge 
		$extra_fields = array();

		// CNP Field
		if ('yes' == $options['safealternative_pfiz_cnp_visibility']) {
			$extra_fields['cnp'] = array( //in array ul cnp din array ul extra_fields imi baga:
				'type'        => 'text',
				'label'       => $options['safealternative_pfiz_cnp_label'],
				'placeholder' => $options['safealternative_pfiz_cnp_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_fiz'), //Field will be displayed in both columns (so at 100% of width)
				'needed_req'  => $options['safealternative_pfiz_cnp_required'],
			);
		}

		// Company Field
		if ('yes' == $options['safealternative_pjur_company_visibility']) {
			$extra_fields['billing_company'] = $company;

			$extra_fields['billing_company']['label']       = $options['safealternative_pjur_company_label'];
			$extra_fields['billing_company']['placeholder'] = $options['safealternative_pjur_company_placeholder'];
			$extra_fields['billing_company']['needed_req']  = $options['safealternative_pjur_company_required'];
			$extra_fields['billing_company']['class'][]     = 'show_if_pers_jur';
			$extra_fields['billing_company']['required']    = false;
		}

		// CUI Field
		if ('yes' == $options['safealternative_pjur_cui_visibility']) {
			$extra_fields['cui'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pjur_cui_label'],
				'placeholder' => $options['safealternative_pjur_cui_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_jur'),
				'needed_req'  => $options['safealternative_pjur_cui_required'],
			);
		}

		// Nr. Reg. Com Field
		if ('yes' == $options['safealternative_pjur_nr_reg_com_visibility']) {
			$extra_fields['nr_reg_com'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pjur_nr_reg_com_label'],
				'placeholder' => $options['safealternative_pjur_nr_reg_com_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_jur'),
				'needed_req'  => $options['safealternative_pjur_nr_reg_com_required'],
			);
		}

		// Nume Banca Field
		if ('yes' == $options['safealternative_pjur_nume_banca_visibility']) {
			$extra_fields['nume_banca'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pjur_nume_banca_label'],
				'placeholder' => $options['safealternative_pjur_nume_banca_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_jur'),
				'needed_req'  => $options['safealternative_pjur_nume_banca_required'],
			);
		}

		// IBAN Field
		if ('yes' == $options['safealternative_pjur_iban_visibility']) {
			$extra_fields['iban'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pjur_iban_label'],
				'placeholder' => $options['safealternative_pjur_iban_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_jur'),
				'needed_req'  => $options['safealternative_pjur_iban_required'],
			);
		}

		foreach ($fields as $key => $field) {
			$ordered_fields[$key] = $field;

			if ('billing_last_name' == $key) {
				$ordered_fields = array_merge($ordered_fields, $extra_fields);
			}
		}

		$fields = $ordered_fields;

		return $fields;
	}

	public function form_field_checkout_args($args, $key, $value)
	{
		$our_fields = array('cnp', 'iban', 'nume_banca', 'nr_reg_com', 'cui', 'billing_company');
		$options_keys = array(
			'cnp' 				=> 'safealternative_pfiz_cnp_required',
			'iban' 				=> 'safealternative_pjur_iban_required',
			'nume_banca' 		=> 'safealternative_pjur_nume_banca_required',
			'nr_reg_com' 		=> 'safealternative_pjur_nr_reg_com_required',
			'cui' 				=> 'safealternative_pjur_cui_required',
			'billing_company'	=> 'safealternative_pjur_company_required'
		);

		if (in_array($key, $our_fields)) {

			$options = get_option('safealternative_pf_pj_option', array());
			$options = wp_parse_args($options, $this->defaults);
			$user_id = get_current_user_id();
			$user_type = get_user_meta($user_id, 'safealternative_pf_pj_type', true);
			$tip_persoana = $user_type ? $user_type : $options['safealternative_pf_pj_default'];

			if ('cnp' == $key && 'pers-fiz' != $tip_persoana) {
				$args['class'][] = 'av-hide';
			} elseif ('cnp' != $key && 'pers-jur' != $tip_persoana) {
				$args['class'][] = 'av-hide';
			}

			$args['needed_req'] =  $options[$options_keys[$key]];
		}

		return $args;
	}

	public function make_fields_optional($fields)
	{
		$our_fields = array('cnp', 'iban', 'nume_banca', 'nr_reg_com', 'cui', 'billing_company');
		foreach ($our_fields as $our_field) {
			if (isset($fields['billing'][$our_field])) {
				$fields['billing'][$our_field]['required'] = false;
			}
		}
		return $fields;
	}

	public function override_field_html($field, $key, $args)
	{
		$our_fields = array('cnp', 'iban', 'nume_banca', 'nr_reg_com', 'cui', 'billing_company');

		if (in_array($key, $our_fields)) {

			$optional_label = '<span class="optional">(' . esc_html__('optional', 'woocommerce') . ')</span>';
			$required_label = '<abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
			if ('yes' == $args['needed_req']) {
				$field = str_replace($optional_label, $required_label, $field);
			}
		}

		return $field;
	}

	public function hide_fields()
	{ //arata doar campurile destinate fiecarui tip de tip_persoana(fiz/jur)
		if ((function_exists('is_checkout') && is_checkout()) || (function_exists('is_account_page') && is_account_page())) {
			echo '<style>.woocommerce .woocommerce-billing-fields .av-hide,.woocommerce .woocommerce-address-fields .av-hide,.wcf-embed-checkout-form .woocommerce form .form-row.av-hide{display:none}.woocommerce .safealternative_pf_pj_type_radio span.woocommerce-input-wrapper {display: flex;align-items: center;}.woocommerce .safealternative_pf_pj_type_radio span.woocommerce-input-wrapper label + input[type="radio"] {margin-left: 10px;}.woocommerce .safealternative_pf_pj_type_radio span.woocommerce-input-wrapper label{line-height:1;}</style>';
		}
	}

	public function add_js_to_footer()
	{ //schimba val din pers fiz in jur 
		if ((function_exists('is_checkout') && is_checkout()) || (function_exists('is_account_page') && is_account_page())) {
			echo '<script>!function(i){"use strict";i(document).ready(function(){i("#safealternative_pf_pj_type, #safealternative_pf_pj_type_field .input-radio").change(function(){"pers-jur"==i(this).val()?(i(".show_if_pers_jur").show(),i(".show_if_pers_fiz").hide()):(i(".show_if_pers_jur").hide(),i(".show_if_pers_fiz").show())})})}(jQuery);</script>';
			echo '<script>(function($){$(document).ready(function(){if($().selectWoo){if($("select#safealternative_pf_pj_type").length>0){$("select#safealternative_pf_pj_type").selectWoo({minimumResultsForSearch:-1,width: "100%"});}}});})(jQuery);</script>';
		}
	}

	public function validate_checkout()
	{
		$options = get_option('safealternative_pf_pj_option', array());
		$options = wp_parse_args($options, $this->defaults);

		if ('pers-fiz' == $_POST['safealternative_pf_pj_type']) {
			// validate CNP
			if ('yes' == $options['safealternative_pfiz_cnp_required']) {
				if (!cnp_validate($_POST['cnp'])) {
					wc_add_notice($options['safealternative_pfiz_cnp_error'], 'error');
				}
			}
		}

		if ('pers-jur' == $_POST['safealternative_pf_pj_type']) {
			// validate Nume Firma
			if ('yes' == $options['safealternative_pjur_company_required'] && '' == $_POST['billing_company'] && '' != $options['safealternative_pjur_company_error']) {
				wc_add_notice($options['safealternative_pjur_company_error'], 'error');
			}

			// validate CUI
			if ('yes' == $options['safealternative_pjur_cui_required']) {
				if (!cif_validate($_POST['cui'])) {
					wc_add_notice($options['safealternative_pjur_cui_error'], 'error');
				}
			}

			// validate Nr. Reg. Com.
			if ('yes' == $options['safealternative_pjur_nr_reg_com_required'] && '' == $_POST['nr_reg_com'] && '' != $options['safealternative_pjur_nr_reg_com_error']) {
				wc_add_notice($options['safealternative_pjur_nr_reg_com_error'], 'error');
			}

			// validate Nume Banca
			if ('yes' == $options['safealternative_pjur_nume_banca_required'] && '' == $_POST['nume_banca'] && '' != $options['safealternative_pjur_nume_banca_error']) {
				wc_add_notice($options['safealternative_pjur_nume_banca_error'], 'error');
			}

			// validate Nume Banca
			if ('yes' == $options['safealternative_pjur_iban_required']) {
				if (!iban_validate($_POST['iban'])) {
					wc_add_notice($options['safealternative_pjur_iban_error'], 'error');
				}
			}
		}
	}

	// Adaug fields-uri in user profile
	public function user_profile_fields($fields, $load_address)
	{
		if ('billing' != $load_address) {
			return $fields;
		}

		$options = get_option('safealternative_pf_pj_option', array());
		$options = wp_parse_args($options, $this->defaults);
		$user_id = get_current_user_id();

		// Adaug field pers fiz/jur in user profile
		$ordered_fields['safealternative_pf_pj_type'] = array(
			'type'     => $options['safealternative_pf_pj_output'],
			'label'    => $options['safealternative_pf_pj_label'],
			'required' => true,
			'class'    => array('form-row-wide'),
			'options'  => array(
				'pers-fiz' => $options['safealternative_pfiz_label'],
				'pers-jur' => $options['safealternative_pjur_label'],
			),
			'default'  => $options['safealternative_pf_pj_default'],
			'priority' => 0,
			'clear'    => true,
			'value'    => get_user_meta($user_id, 'safealternative_pf_pj_type', true),
		);

		if ('radio' == $options['safealternative_pf_pj_output']) {
			$ordered_fields['safealternative_pf_pj_type']['class'][] = 'safealternative_pf_pj_type_radio';
		}

		// Extra Fields
		$company = $fields['billing_company'];
		unset($fields['billing_company']);
		$extra_fields = array();

		// CNP Field
		if ('yes' == $options['safealternative_pfiz_cnp_visibility']) {
			$extra_fields['cnp'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pfiz_cnp_label'],
				'placeholder' => $options['safealternative_pfiz_cnp_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_fiz'),
				'value'       => get_user_meta($user_id, 'cnp', true),
			);
		}

		// Company Field
		if ('yes' == $options['safealternative_pjur_company_visibility']) {
			$extra_fields['billing_company'] = $company;

			$extra_fields['billing_company']['label']       = $options['safealternative_pjur_company_label'];
			$extra_fields['billing_company']['placeholder'] = $options['safealternative_pjur_company_placeholder'];
			$extra_fields['billing_company']['needed_req']  = $options['safealternative_pjur_company_required'];
			$extra_fields['billing_company']['class'][]     = 'show_if_pers_jur';
			$extra_fields['billing_company']['required']    = false;
		}

		// CUI Field
		if ('yes' == $options['safealternative_pjur_cui_visibility']) {
			$extra_fields['cui'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pjur_cui_label'],
				'placeholder' => $options['safealternative_pjur_cui_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_jur'),
				'value'       => get_user_meta($user_id, 'cui', true),
			);
		}

		// Nr. Reg. Com Field
		if ('yes' == $options['safealternative_pjur_nr_reg_com_visibility']) {
			$extra_fields['nr_reg_com'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pjur_nr_reg_com_label'],
				'placeholder' => $options['safealternative_pjur_nr_reg_com_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_jur'),
				'value'       => get_user_meta($user_id, 'nr_reg_com', true),
			);
		}

		// Nume Banca Field
		if ('yes' == $options['safealternative_pjur_nume_banca_visibility']) {
			$extra_fields['nume_banca'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pjur_nume_banca_label'],
				'placeholder' => $options['safealternative_pjur_nume_banca_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_jur'),
				'value'       => get_user_meta($user_id, 'nume_banca', true),
			);
		}

		// IBAN Field
		if ('yes' == $options['safealternative_pjur_iban_visibility']) {
			$extra_fields['iban'] = array(
				'type'        => 'text',
				'label'       => $options['safealternative_pjur_iban_label'],
				'placeholder' => $options['safealternative_pjur_iban_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array('form-row-wide', 'show_if_pers_jur'),
				'value'       => get_user_meta($user_id, 'iban', true),
			);
		}

		foreach ($fields as $key => $field) {
			$ordered_fields[$key] = $field;

			if ('billing_last_name' == $key) {
				$ordered_fields = array_merge($ordered_fields, $extra_fields);
			}
		}

		return $ordered_fields;
	}

	public function save_user_profile_fields($user_id, $load_address)
	{
		if (isset($_POST['safealternative_pf_pj_type'])) {
			update_user_meta($user_id, 'safealternative_pf_pj_type', sanitize_text_field($_POST['safealternative_pf_pj_type']));
		}

		if (isset($_POST['cnp'])) {
			update_user_meta($user_id, 'cnp', sanitize_text_field($_POST['cnp']));
		}

		if (isset($_POST['cui'])) {
			update_user_meta($user_id, 'cui', sanitize_text_field($_POST['cui']));
		}

		if (isset($_POST['nume_banca'])) {
			update_user_meta($user_id, 'nume_banca', sanitize_text_field($_POST['nume_banca']));
		}

		if (isset($_POST['iban'])) {
			update_user_meta($user_id, 'iban', sanitize_text_field($_POST['iban']));
		}
	}
}
