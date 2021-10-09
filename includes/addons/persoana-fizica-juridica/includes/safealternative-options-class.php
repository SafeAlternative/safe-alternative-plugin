<?php

class Safealternative_Options
{
	private $options = array();

	public static function get_instance()
	{
		return new self;
	}

	public function get_keys()
	{
		return apply_filters('safealternative_options_keys', array('_safealternative_pf_pj_option_cnp', '_safealternative_pf_pj_option_nr_reg_com', '_safealternative_pf_pj_option_cui', '_safealternative_pf_pj_option_nume_banca', '_safealternative_pf_pj_option_iban'));
	}

	public function get_cnp($order_id)
	{
		if (!isset($this->options[$order_id])) {
			$this->options[$order_id] = get_post_meta($order_id, 'safealternative_pf_pj_option', true);
		}

		return isset($this->options[$order_id]['cnp']) ? $this->options[$order_id]['cnp'] : '-';
	}

	public function get_nr_reg_com($order_id)
	{
		if (!isset($this->options[$order_id])) {
			$this->options[$order_id] = get_post_meta($order_id, 'safealternative_pf_pj_option', true);
		}

		return isset($this->options[$order_id]['nr_reg_com']) ? $this->options[$order_id]['nr_reg_com'] : '-';
	}

	public function get_cui($order_id)
	{
		if (!isset($this->options[$order_id])) {
			$this->options[$order_id] = get_post_meta($order_id, 'safealternative_pf_pj_option', true);
		}

		return isset($this->options[$order_id]['cui']) ? $this->options[$order_id]['cui'] : '-';
	}

	public function get_nume_banca($order_id)
	{
		if (!isset($this->options[$order_id])) {
			$this->options[$order_id] = get_post_meta($order_id, 'safealternative_pf_pj_option', true);
		}

		return isset($this->options[$order_id]['nume_banca']) ? $this->options[$order_id]['nume_banca'] : '-';
	}

	public function get_iban($order_id)
	{
		if (!isset($this->options[$order_id])) {
			$this->options[$order_id] = get_post_meta($order_id, 'safealternative_pf_pj_option', true);
		}

		return isset($this->options[$order_id]['iban']) ? $this->options[$order_id]['iban'] : '-';
	}

	public function get_tip($order_id)
	{
		if (!isset($this->options[$order_id])) {
			$this->options[$order_id] = get_post_meta($order_id, 'safealternative_pf_pj_option', true);
		}

		return isset($this->options[$order_id]['safealternative_pf_pj_type']) ? $this->options[$order_id]['safealternative_pf_pj_type'] : '-';
	}
}

new Safealternative_Options;
