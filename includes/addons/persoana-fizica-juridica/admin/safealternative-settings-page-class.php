<?php

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('Safealternative_Settings_Page', false)) {
	return new Safealternative_Settings_Page();
}

class Safealternative_Settings_Page extends WC_Settings_Page
{
	public function __construct()
	{
		$this->id    = 'safealternative-pf-pj';
		$this->label = esc_html__('Persoana fizica/juridica (BETA)', 'safealternative_pf_pj');

		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections()
	{
		$sections = array(
			''         => esc_html__('General', 'safealternative_pf_pj'),
			'pers-fiz' => esc_html__('Persoana Fizica', 'safealternative_pf_pj'),
			'pers-jur' => esc_html__('Persoana Juridica', 'safealternative_pf_pj'),
		);

		return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
	}

	/**
	 * Output the settings.
	 */
	public function output()
	{
		global $current_section;

		$settings = $this->get_settings($current_section);
		WC_Admin_Settings::output_fields($settings);
	}

	/**
	 * Save settings.
	 */
	public function save()
	{
		global $current_section;

		$settings = $this->get_settings($current_section);
		WC_Admin_Settings::save_fields($settings);

		if ($current_section) {
			do_action('woocommerce_update_options_' . $this->id . '_' . $current_section);
		}
	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section Current section name.
	 * @return array
	 */
	public function get_settings($current_section = '')
	{
		if ('pers-fiz' === $current_section) {
			$settings = array(
				array(
					'title' => esc_html__('Setari Persoane Fizice', 'safealternative_pf_pj'),
					'type'  => 'title',
					'id'    => 'safealternative_pfiz_start',
				),
				array(
					'name'    => esc_html__('Label Persoana Fizica', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Persoana Fizica', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pfiz_label]'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative_pfiz_end',
				),

				array(
					'title' => esc_html__('Camp CNP', 'safealternative_pf_pj'),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'safealternative_pfiz_cnp_start',
				),
				array(
					'name'    => esc_html__('Label', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('CNP', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pfiz_cnp_label]'
				),
				array(
					'name'    => esc_html__('Placeholder', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Introduceti Codul numeric personal', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pfiz_cnp_placeholder]'
				),
				array(
					'title'   => esc_html__('Vizibilitate', 'safealternative_pf_pj'),
					'desc'    => esc_html__('Arata acest camp pe pagina de checkout', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pfiz_cnp_visibility]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__('Obligatoriu', 'safealternative_pf_pj'),
					'desc'    => __('Da, campul <strong>CNP</strong> este Obligatoriu', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pfiz_cnp_required]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'name'    => esc_html__('Mesaj Eroare', 'safealternative_pf_pj'),
					'type'    => 'textarea',
					'default' => esc_html__('Datorita legislatiei in vigoare trebuie sa completati campul CNP', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pfiz_cnp_error]'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative_pfiz_cnp_end',
				),
			);
		} elseif ('pers-jur' === $current_section) {
			$settings = array(
				array(
					'title' => esc_html__('Setari Persoane Juridice', 'safealternative_pf_pj'),
					'type'  => 'title',
					'id'    => 'safealternative_pjur_start',
				),
				array(
					'name'    => esc_html__('Label Persoana Juridica', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Persoana Juridica', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_label]'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative_pjur_end',
				),

				// Nume Firma
				array(
					'title' => esc_html__('Camp Nume Firma', 'safealternative_pf_pj'),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'safealternative_pjur_company_start',
				),
				array(
					'name'    => esc_html__('Label', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Nume Firma', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_company_label]'
				),
				array(
					'name'    => esc_html__('Placeholder', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Introduceti numele firmei dumneavoastra', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_company_placeholder]'
				),
				array(
					'title'   => esc_html__('Vizibilitate', 'safealternative_pf_pj'),
					'desc'    => esc_html__('Arata acest camp pe pagina de checkout', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_company_visibility]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__('Obligatoriu', 'safealternative_pf_pj'),
					'desc'    => __('Da, campul <strong>Nume Firma</strong> este Obligatoriu', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_company_required]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'name'    => esc_html__('Mesaj Eroare', 'safealternative_pf_pj'),
					'type'    => 'textarea',
					'default' => esc_html__('Pentru a putea plasa comanda, avem nevoie de numele firmei dumneavoastra', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_company_error]'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative_pjur_company_end',
				),

				// CUI
				array(
					'title' => esc_html__('Camp CUI', 'safealternative_pf_pj'),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'safealternative_pjur_cui_start',
				),
				array(
					'name'    => esc_html__('Label', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('CUI', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_cui_label]'
				),
				array(
					'name'    => __('Placeholder', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Introduceti Codul Unic de Inregistrare', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_cui_placeholder]'
				),
				array(
					'title'   => esc_html__('Vizibilitate', 'safealternative_pf_pj'),
					'desc'    => esc_html__('Arata acest camp pe pagina de checkout', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_cui_visibility]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__('Obligatoriu', 'safealternative_pf_pj'),
					'desc'    => __('Da, campul <strong>CUI</strong> este Obligatoriu', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_cui_required]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'name'    => esc_html__('Mesaj Eroare', 'safealternative_pf_pj'),
					'type'    => 'textarea',
					'default' => esc_html__('Pentru a putea plasa comanda, avem nevoie de CUI-ul firmei dumneavoastra', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_cui_error]'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative_pjur_cui_end',
				),

				// Nr. Reg. Com.
				array(
					'title' => esc_html__('Camp Nr. Reg. Com.', 'safealternative_pf_pj'),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'safealternative_pjur_nr_reg_com_start',
				),
				array(
					'name'    => esc_html__('Label', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Nr. Reg. Com', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nr_reg_com_label]'
				),
				array(
					'name'    => esc_html__('Placeholder', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => 'J20/20/20.02.2020',
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nr_reg_com_placeholder]'
				),
				array(
					'title'   => esc_html__('Vizibilitate', 'safealternative_pf_pj'),
					'desc'    => esc_html__('Arata acest camp pe pagina de checkout', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nr_reg_com_visibility]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__('Obligatoriu', 'safealternative_pf_pj'),
					'desc'    => __('Da, campul <strong>Nr. Reg. Com</strong> este Obligatoriu', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nr_reg_com_required]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'name'    => esc_html__('Mesaj Eroare', 'safealternative_pf_pj'),
					'type'    => 'textarea',
					'default' => esc_html__('Pentru a putea plasa comanda, avem nevoie de numarul de ordine in registrul comertului', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nr_reg_com_error]'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative_pjur_nr_reg_com_end',
				),

				// Nume Banca
				array(
					'title' => esc_html__('Camp Nume Banca', 'safealternative_pf_pj'),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'safealternative_pjur_nume_banca_start',
				),
				array(
					'name'    => esc_html__('Label', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Nume Banca', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nume_banca_label]'
				),
				array(
					'name'    => esc_html__('Placeholder', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Numele bancii cu care lucrati', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nume_banca_placeholder]'
				),
				array(
					'title'   => esc_html__('Vizibilitate', 'safealternative_pf_pj'),
					'desc'    => esc_html__('Arata acest camp pe pagina de checkout', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nume_banca_visibility]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__('Obligatoriu', 'safealternative_pf_pj'),
					'desc'    => __('Da, campul <strong>Nume Banca</strong> este Obligatoriu', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nume_banca_required]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'name'    => esc_html__('Mesaj Eroare', 'safealternative_pf_pj'),
					'type'    => 'textarea',
					'default' => esc_html__('Pentru a putea plasa comanda, avem nevoie de numele bancii cu care lucrati', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_nume_banca_error]',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative_pjur_nume_banca_end',
				),

				// IBAN
				array(
					'title' => esc_html__('Camp IBAN', 'safealternative_pf_pj'),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'safealternative_pjur_iban_start',
				),
				array(
					'name'    => esc_html__('Label', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('IBAN', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_iban_label]'
				),
				array(
					'name'    => esc_html__('Placeholder', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Numarul contului IBAN', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_iban_placeholder]'
				),
				array(
					'title'   => esc_html__('Vizibilitate', 'safealternative_pf_pj'),
					'desc'    => esc_html__('Arata acest camp pe pagina de checkout', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_iban_visibility]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__('Obligatoriu', 'safealternative_pf_pj'),
					'desc'    => __('Da, campul <strong>IBAN</strong> este Obligatoriu', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_iban_required]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'name'    => esc_html__('Mesaj Eroare', 'safealternative_pf_pj'),
					'type'    => 'textarea',
					'default' => esc_html__('Pentru a putea plasa comanda, avem nevoie de numarul contului', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pjur_iban_error]'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative_pjur_iban_end',
				),

			);
		} else {
			$settings = array(
				array(
					'title' => esc_html__('Setari Generale', 'safealternative_pf_pj'),
					'type'  => 'title',
					'id'    => 'safealternative-pf-pj_general_start',
				),
				array(
					'name' => esc_html__('Tip camp', 'safealternative_pf_pj'),
					'type' => 'select',
					'options' => array(
						'radio'  => esc_html__('Butoane radio', 'safealternative_pf_pj'),
						'select' => esc_html__('Select', 'safealternative_pf_pj'),
						'hidden' => esc_html__('Doar persoana fizica', 'safealternative_pf_pj'),
					),
					'default' => 'select',
					'desc' => __('<p>Cum va fi afisata optiunea de a alege intre persoana fizica sau juridica</p>', 'safealternative_pf_pj'),
					'id'   => 'safealternative_pf_pj_option[safealternative_pf_pj_output]'
				),
				array(
					'name' => esc_html__('Optiune implicita', 'safealternative_pf_pj'),
					'type' => 'select',
					'options' => array(
						'pers-fiz' => esc_html__('Persoana Fizica', 'safealternative_pf_pj'),
						'pers-jur' => esc_html__('Persoana Juridica', 'safealternative_pf_pj'),
					),
					'desc' => __('<p>Optiunea care va fi selectata implicit pe pagina de checkout</p>', 'safealternative_pf_pj'),
					'id'   => 'safealternative_pf_pj_option[safealternative_pf_pj_default]'
				),
				array(
					'name'    => esc_html__('Label', 'safealternative_pf_pj'),
					'type'    => 'text',
					'default' => esc_html__('Tip Client', 'safealternative_pf_pj'),
					'id'      => 'safealternative_pf_pj_option[safealternative_pf_pj_label]'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'safealternative-pf-pj_general_end',
				),
			);
		}

		return apply_filters('woocommerce_get_settings_' . $this->id, $settings, $current_section);
	}
}

return new Safealternative_Settings_Page();
