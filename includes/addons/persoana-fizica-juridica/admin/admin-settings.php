<?php

class Admin_Settings
{
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
			'safealternative_pjur_company_error'          => esc_html__('Pentru a putea plasa comanda, avem nevoie de numele firmei dumneavoastra', 'safealternative_pf_pj'),
			'safealternative_pjur_cui_label'              => esc_html__('CUI', 'safealternative_pf_pj'),
			'safealternative_pjur_cui_placeholder'        => esc_html__('Introduceti Codul Unic de Inregistrare', 'safealternative_pf_pj'),
			'safealternative_pjur_cui_visibility'         => 'yes',
			'safealternative_pjur_cui_required'           => 'yes',
			'safealternative_pjur_cui_error'              => esc_html__('Pentru a putea plasa comanda, avem nevoie de CUI-ul firmei dumneavoastra', 'safealternative_pf_pj'),
			'safealternative_pjur_nr_reg_com_label'       => esc_html__('Nr. Reg. Com', 'safealternative_pf_pj'),
			'safealternative_pjur_nr_reg_com_placeholder' => 'J20/20/20.02.2020',
			'safealternative_pjur_nr_reg_com_visibility'  => 'yes',
			'safealternative_pjur_nr_reg_com_required'    => 'yes',
			'safealternative_pjur_nr_reg_com_error'       => esc_html__('Pentru a putea plasa comanda, avem nevoie de numarul de ordine in registrul comertului', 'safealternative_pf_pj'),
			'safealternative_pjur_nume_banca_label'       => esc_html__('Nume Banca', 'safealternative_pf_pj'),
			'safealternative_pjur_nume_banca_placeholder' => esc_html__('Numele bancii cu care lucrati', 'safealternative_pf_pj'),
			'safealternative_pjur_nume_banca_visibility'  => 'no',
			'safealternative_pjur_nume_banca_required'    => 'no',
			'safealternative_pjur_nume_banca_error'       => esc_html__('Pentru a putea plasa comanda, avem nevoie de numele bancii cu care lucrati', 'safealternative_pf_pj'),
			'safealternative_pjur_iban_label'             => esc_html__('IBAN', 'safealternative_pf_pj'),
			'safealternative_pjur_iban_placeholder'       => esc_html__('Numarul contului IBAN', 'safealternative_pf_pj'),
			'safealternative_pjur_iban_visibility'        => 'no',
			'safealternative_pjur_iban_required'          => 'no',
			'safealternative_pjur_iban_error'             => esc_html__('Pentru a putea plasa comanda, avem nevoie de numarul contului', 'safealternative_pf_pj'),
			'safealternative_pf_pj_output'               => 'select',
			'safealternative_pf_pj_default'              => 'pers-fiz',
			'safealternative_pf_pj_label'                => esc_html__('Tip Client', 'safealternative_pf_pj'),
		);
	}

	public function safealternative_settings_page_class($settings)
	{
		$settings[] = include 'safealternative-settings-page-class.php';
		return $settings;
	}

	public function register_safealternative_admin_tabs($tabs_with_sections)
	{
		$tabs_with_sections['safealternative-pf-pj'] = array('', 'pers-fiz', 'pers-jur');
		return $tabs_with_sections;
	}

	public function wc_admin_connect_page()
	{
		if (!function_exists('wc_admin_connect_page')) {
			return;
		}

		wc_admin_connect_page(
			array(
				'id'        => 'woocommerce-settings-safealternative-pf-pj',
				'parent'    => 'woocommerce-settings',
				'screen_id' => 'woocommerce_page_wc-settings-safealternative-pf-pj',
				'title'     => 'General', 'safealternative_pf_pj',
				'path'      => add_query_arg(
					array(
						'page' => 'wc-settings',
						'tab'  => 'safealternative-pf-pj',
					),
					'admin.php'
				),
			)
		);

		wc_admin_connect_page(
			array(
				'id'        => 'woocommerce-settings-safealternative-pf-pj-pers-fiz',
				'parent'    => 'woocommerce-settings-safealternative-pf-pj',
				'screen_id' => 'woocommerce_page_wc-settings-safealternative-pf-pj-pers-fiz',
				'title'     => __('Persoana Fizica', 'safealternative_pf_pj'),
			)
		);

		wc_admin_connect_page(
			array(
				'id'        => 'woocommerce-settings-safealternative-pf-pj-pers-jur',
				'parent'    => 'woocommerce-settings-safealternative-pf-pj',
				'screen_id' => 'woocommerce_page_wc-settings-safealternative-pf-pj-pers-jur',
				'title'     => __('Persoana Juridica', 'safealternative_pf_pj'),
			)
		);
	}

	/**
	 * Update the order meta with extra fields
	 */
	public function update_order_meta($order_id)
	{
		$av_settings = array();

		if (!isset($_POST['safealternative_pf_pj_type'])) {
			return;
		}

		$av_settings['safealternative_pf_pj_type'] = sanitize_text_field($_POST['safealternative_pf_pj_type']);

		if ('pers-fiz' == $_POST['safealternative_pf_pj_type']) {

			if (isset($_POST['cnp']) && '' != $_POST['cnp']) {
				$av_settings['cnp'] = sanitize_text_field($_POST['cnp']);
			}
		} elseif ('pers-jur' == $_POST['safealternative_pf_pj_type']) {
			if (isset($_POST['cui']) && '' != $_POST['cui']) {
				$av_settings['cui'] = sanitize_text_field($_POST['cui']);
			}

			if (isset($_POST['nr_reg_com']) && '' != $_POST['nr_reg_com']) {
				$av_settings['nr_reg_com'] = sanitize_text_field($_POST['nr_reg_com']);
			}

			if (isset($_POST['nume_banca']) && '' != $_POST['nume_banca']) {
				$av_settings['nume_banca'] = sanitize_text_field($_POST['nume_banca']);
			}

			if (isset($_POST['iban']) && '' != $_POST['iban']) {
				$av_settings['iban'] = sanitize_text_field($_POST['iban']);
			}
		}

		if (!empty($av_settings)) {
			update_post_meta($order_id, 'safealternative_pf_pj_option', $av_settings);
		}
	}

	/**
	 * Update the customer meta with extra fields
	 */
	public function update_customer_data($customer_id, $data)
	{
		$av_settings = array();

		if (!isset($data['safealternative_pf_pj_type'])) {
			return;
		}

		$av_settings['safealternative_pf_pj_type'] = $data['safealternative_pf_pj_type'];

		if ('pers-fiz' == $data['safealternative_pf_pj_type']) {

			if (isset($data['cnp']) && '' != $data['cnp']) {
				$av_settings['cnp'] = sanitize_text_field($data['cnp']);
			}
		} elseif ('pers-jur' == $data['safealternative_pf_pj_type']) {

			if (isset($data['cui']) && '' != $data['cui']) {
				$av_settings['cui'] = sanitize_text_field($data['cui']);
			}

			if (isset($data['nr_reg_com']) && '' != $data['nr_reg_com']) {
				$av_settings['nr_reg_com'] = sanitize_text_field($data['nr_reg_com']);
			}

			if (isset($data['nume_banca']) && '' != $data['nume_banca']) {
				$av_settings['nume_banca'] = sanitize_text_field($data['nume_banca']);
			}

			if (isset($data['iban']) && '' != $data['iban']) {
				$av_settings['iban'] = sanitize_text_field($data['iban']);
			}
		}

		if (!empty($av_settings)) {
			foreach ($av_settings as $key => $value) {
				update_user_meta($customer_id, $key, sanitize_text_field($value));
			}
		}
	}

	/**
	 * Filter billing fields.
	 */
	public function filter_billing_fields($fields, $order)
	{
		$defaults = array(
			'cnp'        => '',
			'cui'        => '',
			'nr_reg_com' => '',
			'nume_banca' => '',
			'iban'       => '',
		);

		$data = $order->get_meta('safealternative_pf_pj_option');
		$tip = isset($data['safealternative_pf_pj_type']) ? $data['safealternative_pf_pj_type'] : '';
		if (isset($data['safealternative_pf_pj_type'])) {
			unset($data['safealternative_pf_pj_type']);
		}

		if ('pers-fiz' == $tip && isset($fields['company'])) {
			$fields['company'] = '';
		}

		$extra_fields = wp_parse_args($data, $defaults);

		return array_merge($fields, $extra_fields);
	}

	public function myacc_filter_billing_fields($fields, $customer_id, $address_type)
	{

		$user_type = get_user_meta($customer_id, 'safealternative_pf_pj_type', true);
		$options = get_option('safealternative_pf_pj_option', array());
		$tip_persoana = $user_type ? $user_type : $options['safealternative_pf_pj_default'];

		if ('pers-fiz' == $user_type) {
			$fields['cnp']        = get_user_meta($customer_id, 'cnp', true);
			unset($fields['company']); //daca aleg persoana fizica, sa mi arate doar cnp ul
		} else {
			$fields['cui']        = get_user_meta($customer_id, 'cui', true);
			$fields['nr_reg_com'] = get_user_meta($customer_id, 'nr_reg_com', true);
			$fields['nume_banca'] = get_user_meta($customer_id, 'nume_banca', true);
			$fields['iban']       = get_user_meta($customer_id, 'iban', true);
		}

		return $fields;
	}

	/**
	 * Add replacements for our extra fields.
	 */
	public function extra_fields_replacements($replacements, $args)
	{
		$replacements['{cnp}']        = isset($args['cnp']) ? $args['cnp'] : '';
		$replacements['{cui}']        = isset($args['cui']) ? $args['cui'] : '';
		$replacements['{nr_reg_com}'] = isset($args['nr_reg_com']) ? $args['nr_reg_com'] : '';
		$replacements['{nume_banca}'] = isset($args['nume_banca']) ? $args['nume_banca'] : '';
		$replacements['{iban}']       = isset($args['iban']) ? $args['iban'] : '';

		return $replacements;
	}

	public function localisation_address_formats($formats)
	{
		$formats['default'] = "{name}\n{cnp}\n{company}\n{cui}\n{nr_reg_com}\n{nume_banca}\n{iban}\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}";

		return $formats;
	}

	public function action_links($links, $file)
	{
		if ('safealternative_pf_pj/index.php' == $file) {
			$links[] = '<a href="' . admin_url('admin.php?page=wc-settings&tab=safealternative-pf-pj&section') . '">' . esc_html__('Setari', 'safealternative_pf_pj') . '</a>';
		}

		return $links;
	}

	public function settings_links($links)
	{
		if (is_array($links)) {
			$links['safealternative-pf-pj-settings'] = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wc-settings&tab=safealternative-pf-pj'), __('Settings', 'safealternative_pf_pj'));
		}

		return $links;
	}

	public function admin_billing_fields($fields)
	{
		$options = get_option('safealternative_pf_pj_option', array());
		$options = wp_parse_args($options, $this->defaults);

		$new_fields = array(
			'safealternative_pf_pj_type' => array(
				'label'   		=> __('Tip client', 'woocommerce'),
				'show'    		=> false,
				'type'    		=> 'select',
				'wrapper_class' => 'form-field-wide',
				'options'       => array(
					'pers-fiz' => esc_html__('Persoana Fizica', 'safealternative_pf_pj'),
					'pers-jur' => esc_html__('Persoana Juridica', 'safealternative_pf_pj')
				),
			)
		);

		foreach ($fields as $key => $field) {
			$new_fields[$key] = $field;
			if ('company' == $key) {

				if (isset($options['safealternative_pfiz_cnp_visibility']) && 'yes' == $options['safealternative_pfiz_cnp_visibility']) {
					$new_fields['cnp'] = array(
						'label'			=> $options['safealternative_pfiz_cnp_label'],
						'wrapper_class' => 'form-field-wide safealternative_pf_pj_option_field show_if_pers-fiz',
						'show'			=> false,
					);
				}

				if (isset($options['safealternative_pjur_cui_visibility']) && 'yes' == $options['safealternative_pjur_cui_visibility']) {
					$new_fields['cui'] = array(
						'label'	=> $options['safealternative_pjur_cui_label'],
						'wrapper_class' => 'safealternative_pf_pj_option_field show_if_pers-jur',
						'show'	=> false,
					);
				}

				if (isset($options['safealternative_pjur_nr_reg_com_visibility']) && 'yes' == $options['safealternative_pjur_nr_reg_com_visibility']) {
					$new_fields['nr_reg_com'] = array(
						'label'			=> $options['safealternative_pjur_nr_reg_com_label'],
						'wrapper_class' => 'last safealternative_pf_pj_option_field show_if_pers-jur',
						'show'			=> false,
					);
				}

				if (isset($options['safealternative_pjur_nume_banca_visibility']) && 'yes' == $options['safealternative_pjur_nume_banca_visibility']) {
					$new_fields['nume_banca'] = array(
						'label'	=> $options['safealternative_pjur_nume_banca_label'],
						'wrapper_class' => 'safealternative_pf_pj_option_field show_if_pers-jur',
						'show'	=> false,
					);
				}

				if (isset($options['safealternative_pjur_iban_visibility']) && 'yes' == $options['safealternative_pjur_iban_visibility']) {
					$new_fields['iban'] = array(
						'label'			=> $options['safealternative_pjur_iban_label'],
						'wrapper_class' => 'last safealternative_pf_pj_option_field show_if_pers-jur',
						'show'			=> false,
					);
				}
			}
		}

		return $new_fields;
	}

	public function admin_billing_get_safealternative_pf_pj_type($value, $object)
	{
		$options_helper = Safealternative_Options::get_instance();
		$value = $options_helper->get_tip($object->get_id());
		return $value;
	}

	public function admin_billing_get_cnp($value, $object)
	{
		$options_helper = Safealternative_Options::get_instance();
		$value = $options_helper->get_cnp($object->get_id());
		return $value;
	}

	public function admin_billing_get_cui($value, $object)
	{
		$options_helper = Safealternative_Options::get_instance();
		$value = $options_helper->get_cui($object->get_id());
		return $value;
	}

	public function admin_billing_get_nume_banca($value, $object)
	{
		$options_helper = Safealternative_Options::get_instance();
		$value = $options_helper->get_nume_banca($object->get_id());
		return $value;
	}

	public function admin_billing_get_nr_reg_com($value, $object)
	{
		$options_helper = Safealternative_Options::get_instance();
		$value = $options_helper->get_nr_reg_com($object->get_id());
		return $value;
	}

	public function admin_billing_get_iban($value, $object)
	{
		$options_helper = Safealternative_Options::get_instance();
		$value = $options_helper->get_iban($object->get_id());
		return $value;
	}

	public function save_admin_billing_fields($order_id)
	{
		$av_settings = array();

		if (!isset($_POST['_billing_safealternative_pf_pj_type'])) {
			return;
		}

		$av_settings['safealternative_pf_pj_type'] = sanitize_text_field($_POST['_billing_safealternative_pf_pj_type']);
		unset($_POST['_billing_safealternative_pf_pj_type']);

		if ('pers-fiz' == $av_settings['safealternative_pf_pj_type']) {
			if (isset($_POST['_billing_cnp']) && '' != $_POST['_billing_cnp']) {
				$av_settings['cnp'] = sanitize_text_field($_POST['_billing_cnp']);
				unset($_POST['_billing_cnp']);
			}
		} elseif ('pers-jur' == $av_settings['safealternative_pf_pj_type']) {

			$fields = array('_billing_cui', '_billing_nr_reg_com', '_billing_iban', '_billing_nume_banca');
			foreach ($fields as $field_key) {
				$av_key = str_replace('_billing_', '', $field_key);
				if (isset($_POST[$field_key])) {
					$av_settings[$av_key] = sanitize_text_field($_POST[$field_key]);
					unset($_POST[$field_key]);
				}
			}
		}

		update_post_meta($order_id, 'safealternative_pf_pj_option', $av_settings);
	}

	public function admin_enqueue_scripts($hook)
	{
		$screen = get_current_screen();
		if ('post.php' != $hook) {
			return;
		}

		if ('shop_order' != $screen->post_type) {
			return;
		}

		wp_enqueue_script('safealternative_pf_pj', SAFEALTERNATIVE_PF_PJ_ASSETS . 'js/admin.js', array('jquery'), SAFEALTERNATIVE_PF_PJ_VERSION);
	}
}
