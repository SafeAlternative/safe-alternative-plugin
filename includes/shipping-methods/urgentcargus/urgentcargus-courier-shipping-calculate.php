<?php

if (safealternative_is_woocommerce_active()) {
	$dir = plugin_dir_path(__FILE__);

	include_once($dir . 'urgent_cargus.class.php');

	function WC_Urgent_Cargus_Shipping_Method()
	{
		if (!class_exists('WC_Urgent_Cargus_Shipping_Method')) {

			class WC_Urgent_Cargus_Shipping_Method extends WC_Shipping_Method
			{
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct()
				{
					$this->id                 = 'urgentcargus_courier';
					$this->method_title       = __('Urgent Cargus Shipping', 'safealternative-plugin');
					$this->method_description = __('Urgent Cargus Shipping Method for courier', 'safealternative-plugin');

					// Availability & Countries
					$this->availability = 'including';
					$this->countries = array('RO');

					$this->init();

					$this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Urgent Cargus Shipping', 'safealternative-plugin');
				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init()
				{
					// Load the settings API
					$this->init_form_fields();
					$this->init_settings();

					// Save settings in admin if you have any defined
					add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
				}

				function init_form_fields()
				{
					$this->form_fields = array(
						'title' => array(
							'title' => __('Denumire metoda livrare *', 'safealternative-plugin'),
							'type' => 'text',
							'description' => __('Denumirea metodei de livrare in Cart si Checkout, vizibila de catre client.', 'safealternative-plugin'),
							'default' => __('Urgent Cargus', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'tarif_contract' => array(
							'title' 		=> __('Afisare tarif contract', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> 'no',
							'css' 			=> 'width:400px;',
							'description' => __('Pentru a activa aceasta optiune trebuie sa aveti si metoda DPD - AWB activata si configurata.', 'dpd'),
							'desc_tip'      => true,
							'options'		=> array(
								'no' 	    => __('Nu', 'safealternative-plugin'),
								'yes' 	    => __('Da', 'safealternative-plugin')
							),
						),
						'prag_gratis_Bucuresti' => array(
							'title' => __('Prag gratis Bucuresti', 'safealternative-plugin'),
							'type' => 'text',
							'default' => __('250', 'safealternative-plugin')
						),
						'suma_fixa_Bucuresti' => array(
							'title' => __('Suma fixa Bucuresti', 'safealternative-plugin'),
							'type' => 'text',
							'default' => __('15', 'safealternative-plugin')
						),
						'prag_gratis_provincie' => array(
							'title' => __('Prag gratis provincie', 'safealternative-plugin'),
							'type' => 'text',
							'default' => __('250', 'safealternative-plugin')
						),
						'suma_fixa_provincie' => array(
							'title' => __('Suma fixa provincie', 'safealternative-plugin'),
							'type' => 'text',
							'default' => __('18', 'safealternative-plugin')
						),
						'pret_km_suplimentar' => array(
							'title' 		=> __('Pret km suplimentar', 'safealternative-plugin'),
							'type' 			=> 'text',
							'default'		=> __('1', 'safealternative-plugin')
						),
						'tarif_implicit' => array(
							'title'         => __('Tarif implicit', 'safealternative-plugin'),
							'type'          => 'number',
							'default'       => __('0', 'safealternative-plugin'),
							'desc_tip'      => true,
							'custom_attributes' => array('step' => '0.01', 'min' => '0'),
							'description'   => __('Tariful implicit pentru metoda de livrare, pentru a arata o suma atunci cand nu este introdusa adresa de livrare. ', 'safealternative-plugin')
						),
						'tarif_maxim' => array(
							'title' 		=> __('Tarif maxim livrare', 'safealternative-plugin'),
							'type' 			=> 'text',
							'default'		=> __('40', 'safealternative-plugin'),
							'description' 		=> __('Plafonare tarif de livrare. Clientul nu plateste mai mult decat suma specificata', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'orasexp' => array(
							'title' 		=> __('Oras expeditor', 'safealternative-plugin'),
							'type' 			=> 'text',
							'default'		=> __('Bucuresti', 'safealternative-plugin')
						),
						'judetexp' => array(
							'title' 		=> __('Judet expeditor', 'safealternative-plugin'),
							'type' 			=> 'text',
							'default'		=> __('Bucuresti', 'safealternative-plugin')
						),
						'numar_colete' => array(
							'title' 		=> __('Numar colete', 'safealternative-plugin'),
							'type' 			=> 'number',
							'default'		=> __('1', 'safealternative-plugin'),
							'description' 		=> __('Nr. colete pe formularul de awb', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'numar_plicuri' => array(
							'title' 		=> __('Numar plicuri', 'safealternative-plugin'),
							'type' 			=> 'number',
							'default'		=> __('0', 'safealternative-plugin'),
							'description' 		=> __('Nr. plicuri pe formularul de awb', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'transport_asigurat' => array(
							'title' 		=> __('Val declarata (Asigurare)', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> 'Nu',
							'css' 			=> 'width:400px;',
							'options'		=> array(
								'Nu' 	    => __('Nu', 'safealternative-plugin'),
								'Da' 	    => __('Da', 'safealternative-plugin')
							),
							'description' 		=> __('Daca se doreste asigurare, implicit valoarea declarata este valoarea cosului', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'tip_ramburs' => array(
							'title' 		=> __('Tip ramburs', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> 'Cont',
							'css' 			=> 'width:400px;',
							'options'		=> array(
								'Cont' 	    => __('Cont colector', 'safealternative-plugin'),
								'Cash' 	=> __('Numerar', 'safealternative-plugin')
							),
							'description' 		=> __('La cash rambursul vine in plic, la cont vine in contul din contractul semnat', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'plata_transportului' => array(
							'title' 		=> __('Plata transportului', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> 'destinatar',
							'css' 			=> 'width:400px;',
							'options'		=> array(
								'1' 	    => __('Expeditor', 'safealternative-plugin'),
								'2' 	    => __('Destinatar', 'safealternative-plugin')
							),
							'description' 		=> __('Cine plateste livrarea. Se selecteaza expeditor sau destinatar', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'plata_rambursului' => array(
							'title' 		=> __('Plata rambursului', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> 'destinatar',
							'css' 			=> 'width:400px;',
							'options'		=> array(
								'expeditor' 	    => __('Expeditor', 'safealternative-plugin'),
								'destinatar' 	    => __('Destinatar', 'safealternative-plugin')
							),
							'description' 		=> __('Cine plateste rambursul (Atentie, nu cine primeste rambursul). Se selecteaza expeditor sau destinatar', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'id_serviciu' => array(
							'title' 		=> __('Tip serviciu', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> '1',
							'css' 			=> 'width:400px;',
							'options'		=> array(
								'1' 	    => __('Standard', 'safealternative-plugin'),
								'4' 	    => __('Business Partener', 'safealternative-plugin'),
								'34' 	    => __('Economic Standard', 'safealternative-plugin'),
								'35' 	    => __('Standard Plus', 'safealternative-plugin'),
								'36' 	    => __('Palet Standard', 'safealternative-plugin'),
							),
							'description' 		=> __('Tipul serviciului', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'id_tarif' => array(
							'title' 		=> __('ID tarif', 'safealternative-plugin'),
							'type' 			=> 'text',
							'default' 		=> '',
							'css' 			=> 'width:400px;',
							'description' 	=> __('Id-ul tarifului. Nu este obligatoriu daca aveti doar unul. Se cere de la Urgent Cargus.', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'OpenPackage' => array(
							'title' 		=> __('Deschidere colet', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> '0',
							'css' 			=> 'width:400px;',
							'options'		=> array(
								'0' 	    => __('Nu', 'safealternative-plugin'),
								'1' 	    => __('Da', 'safealternative-plugin')
							),
							'description' 		=> __('Deschidere la livrare', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'MorningDelivery' => array(
							'title' 		=> __('Livrare matinala', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> '0',
							'css' 			=> 'width:400px;',
							'options'		=> array(
								'0' 	    => __('Nu', 'safealternative-plugin'),
								'1' 	    => __('Da', 'safealternative-plugin')
							),
							'description' 		=> __('Livrare pana la ora 10', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'SaturdayDelivery' => array(
							'title' 		=> __('Livrare Sambata', 'safealternative-plugin'),
							'type' 			=> 'select',
							'default' 		=> '0',
							'css' 			=> 'width:400px;',
							'options'		=> array(
								'0' 	    => __('Nu', 'safealternative-plugin'),
								'1' 	    => __('Da', 'safealternative-plugin')
							),
							'description' 		=> __('Livrare sambata', 'safealternative-plugin'),
							'desc_tip'      => true
						),
						'url' => array(
							'title' => __('API url', 'safealternative-plugin'),
							'type' => 'text',
							'default' => __('https://urgentcargus.azure-api.net/api', 'safealternative-plugin'),
							'custom_attributes' => array('readonly' => 'readonly'),
						),
						'key' => array(
							'title' => __('Subscription-Key', 'safealternative-plugin'),
							'type' => 'text',
							'default' => 'c76c9992055e4e419ff7fa953c3e4569',
							'custom_attributes' => array('readonly' => 'readonly'),
						),
					);
				}

				public function admin_options()
				{
					echo '<h1>SafeAlternative - Metoda de livrare Urgent Cargus</h1><table class="form-table">';
					$this->generate_settings_html();
					echo '</table>';
				}

				private function fix_format($value)
				{
					$value = str_replace(',', '.', $value);
					return $value;
				}

				////////////////////////////////////////////////////
				/////////// calculate_shipping ////////////////////
				///////////////////////////////////////////////////
				function calculate_shipping($package = array())
				{
					global $wpdb;

					$orasexp = $this->get_option('orasexp');
					$judetexp = $this->get_option('judetexp');
					$orasdest = trim($package['destination']['city']);
					$judetdest = trim($package['destination']['state']);

					$valoare_declarata = 0;
					$ramburs = 0;
					$valoare_cos = 0;
					$weight_list = [];

					foreach ($package['contents'] as $product) {
						$valoare_declarata += ($product['line_total'] + $product['line_tax']);
						$ramburs += ($product['line_total'] + $product['line_tax']);
						$valoare_cos += ($product['line_total'] + $product['line_tax']);

						if (method_exists($product['data'], 'get_height')) {
							$greutate = ((float)$product['data']->get_weight() * $product['quantity']) ?: 1;
							$latime = wc_get_dimension($this->fix_format(null != $product['data']->get_width() ? $product['data']->get_width() : 10), 'cm') * $product['quantity'];
							$inaltime = wc_get_dimension($this->fix_format(null != $product['data']->get_height() ? $product['data']->get_height() : 10), 'cm');
							$lungime = wc_get_dimension($this->fix_format(null != $product['data']->get_length() ? $product['data']->get_length()  : 10), 'cm');
						} else {
							$greutate = ((float)$product['data']->weight * $product['quantity']) ?: 1;
							$latime = wc_get_dimension($this->fix_format(null != $product['data']->width ? $product['data']->width : 10), 'cm') * $product['quantity'];
							$inaltime = wc_get_dimension($this->fix_format(null != $product['data']->height ? $product['data']->height : 10), 'cm');
							$lungime = wc_get_dimension($this->fix_format(null != $product['data']->length ? $product['data']->length : 10), 'cm');
						}

						$weight_list[] = max($greutate, ($latime * $lungime * $inaltime / 6000));
					}

					$greutate = ceil(array_sum($weight_list));

					if ($greutate < 1) {
						$greutate = 1;
					} else {
						$greutate = round($greutate);
					}

					$asigurare = $this->get_option('transport_asigurat');
					if ($asigurare == 'Nu') {
						$valoare_declarata = 0;
					}

					$tipramburs = $this->get_option('tip_ramburs');
					if (!isset($_POST['payment_method'])) {
						$_POST['payment_method'] = 'cod';
					}

					if (addslashes($_POST['payment_method']) != 'cod') {
						$ramburs = 0;
					}

					if ($tipramburs == 'cash') {
						$ramburs_cash = $ramburs;
						$ramburs_cont = 0;
					} else {
						$ramburs_cash = 0;
						$ramburs_cont = $ramburs;
					}

					$url = $this->get_option('url');
					$key = $this->get_option('key');
					$tarif_contract = $this->get_option('tarif_contract');
					$prag_gratis_Bucuresti = $this->get_option('prag_gratis_Bucuresti');
					$suma_fixa_Bucuresti = $this->get_option('suma_fixa_Bucuresti');
					$prag_gratis_provincie = $this->get_option('prag_gratis_provincie');
					$suma_fixa_provincie = $this->get_option('suma_fixa_provincie');
					$pret_km_suplimentar = $this->get_option('pret_km_suplimentar');
					$orasexp = $this->get_option('orasexp');
					$judetexp = $this->get_option('judetexp');
					$tarif_maxim = $this->get_option('tarif_maxim');
					if (!$tarif_maxim) $tarif_maxim = 99999;
					$numar_plicuri = $this->get_option('numar_plicuri');
					$numar_colete = $this->get_option('numar_colete');
					$plata_transportului = $this->get_option('plata_transportului');
					$id_serviciu = $this->get_option('id_serviciu');
					$id_tarif = $this->get_option('id_tarif');
					$OpenPackage = $this->get_option('OpenPackage');
					$MorningDelivery = $this->get_option('MorningDelivery');
					$SaturdayDelivery = $this->get_option('SaturdayDelivery');

					//////////////////////////////////
					$localityList = $wpdb->get_results("SELECT * FROM courier_localities WHERE cargus_locality_id IS NOT NULL AND county_initials='" . $judetdest . "' AND  locality_name='" . $orasdest . "' LIMIT 1");

					////////// a fost selectat un oras din lista
					$label = $this->title;
					if ($prag_gratis_Bucuresti == $prag_gratis_provincie && $valoare_cos >= $prag_gratis_Bucuresti) {
						$transport = 0;
						$label = $this->title . ': Gratis';
					} else {
						if (!empty($localityList)) {
							/////////afisam tariful din contract////////////////////////////
							if ($tarif_contract == 'yes' && class_exists('CargusAWB')) {

								$UserName = get_option('uc_username');
								$Password = get_option('uc_password');

								$obj_urgent = new UrgentcargusCourier($url, $key, $UserName, $Password);

								if (!isset($_SESSION['token'])) {
									$_SESSION['token'] = $obj_urgent->getToken();
								}

								$shippingCalculationList = array(
									"FromCountyName" => $judetexp,
									"FromLocalityName" => $orasexp,
									"ToCountyName" => $judetdest,
									"ToLocalityName" => $localityList[0]->cargus_locality_name,
									"Parcels" => (int)$numar_colete,
									"Envelopes" => (int)$numar_plicuri,
									"TotalWeight" => (int)$greutate,
									"DeclaredValue" => (int)$valoare_declarata,
									"CashRepayment" => (int)$ramburs_cash,
									"BankRepayment" => (int)$ramburs_cont,
									"OtherRepayment" => '',
									"OpenPackage" => filter_var($OpenPackage, FILTER_VALIDATE_BOOLEAN),
									"MorningDelivery" => filter_var($MorningDelivery, FILTER_VALIDATE_BOOLEAN),
									"SaturdayDelivery" => filter_var($SaturdayDelivery, FILTER_VALIDATE_BOOLEAN),
									"PriceTableId" => (int)$id_tarif,
									"ShipmentPayer" => (int)$plata_transportului,
									"ServiceId" => (int)$id_serviciu
								);

								$jsonShippingCalculation = json_encode($shippingCalculationList);

								$resultShippingCalculation = $obj_urgent->callCourierMethod('ShippingCalculation',  'POST',  $jsonShippingCalculation,  $_SESSION['token']);

								if ($resultShippingCalculation['status'] == "200") {
									$jsonShippingCalculationList = $resultShippingCalculation['message'];
									$cost = json_decode($jsonShippingCalculationList, true);
									$transport = $cost['GrandTotal'];

									// praguri dupa valoarea cosului
									if ($judetdest == "B") {
										if ($valoare_cos >= $prag_gratis_Bucuresti) $transport = 0;
									} else {
										if ($valoare_cos >= $prag_gratis_provincie) $transport = 0;
									}

									if ($transport == 0)
										$label = $this->title . ': Gratis';
								} else {
									$transport = 0;
								}
							}
							///////// afisam praguri ///////////////////////////////////////////
							else {
								global $wpdb;

								$extra_km = !empty($localityList) ? $localityList[0]->cargus_extra_km : 0;

								// praguri dupa valoarea cosului
								if ($judetdest == "B") {
									if ($valoare_cos < $prag_gratis_Bucuresti) $transport = $suma_fixa_Bucuresti;
									if ($valoare_cos >= $prag_gratis_Bucuresti) $transport = 0;
								} else {
									if ($valoare_cos < $prag_gratis_provincie) $transport = $suma_fixa_provincie + $pret_km_suplimentar * $extra_km;
									if ($valoare_cos >= $prag_gratis_provincie) $transport = 0 + $pret_km_suplimentar * $extra_km;
								}

								if ($transport == 0) $label = $this->title . ': Gratis';
							}
						} // end a fost selectata o localitate
						// Nu s-a selectat o localitate
						else {
							$transport = (float) $this->get_option('tarif_implicit') ?: 0;
						}
					}

					$transport = min($transport, $tarif_maxim);

					$args = array(
						'id' 	=> $this->id,
						'label' => $label,
						'cost' 	=> $transport,
						'taxes' => true
					);

					if ($transport !== 0 || (strpos($label, 'Gratis') !== false)) {
						$args = apply_filters('safealternative_overwrite_cargus_shipping', $args, $judetdest, $extra_km ?? null, $orasdest);
						$this->add_rate($args);
					}
				}
			} // end class
		} // end if class exist
	} // end function method

	add_action('woocommerce_shipping_init', 'WC_Urgent_Cargus_Shipping_Method');

	function add_urgent_cargus_method($methods)
	{
		$methods[] = 'WC_Urgent_Cargus_Shipping_Method';
		return $methods;
	}

	add_filter('woocommerce_shipping_methods', 'add_urgent_cargus_method');

	////////////////////////////////////////////////////////////////////////////
	//////ACTIVATE CITY DEACTIVATE CODE ////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////

	// activate city
	add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');

	// deactivate zip
	// add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );

	// add_filter( 'woocommerce_checkout_fields' , 'wc_city_remove_postcode_checkout' );
	function wc_city_remove_postcode_checkout($fields)
	{
		unset($fields['billing']['billing_postcode']);
		unset($fields['shipping']['shipping_postcode']);
		return $fields;
	}

	////////////////////////////////////
	add_filter('woocommerce_default_address_fields', 'urgentcargus_move_checkout_fields_woo_3_uc');
	function urgentcargus_move_checkout_fields_woo_3_uc($fields)
	{

		$fields['state']['priority'] = 70;
		$fields['city']['priority'] = 80;
		return $fields;
	}

	add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache_uc');
	function clear_wc_shipping_rates_cache_uc()
	{
		$packages = WC()->cart->get_shipping_packages();

		foreach ($packages as $key => $value) {
			$shipping_session = "shipping_for_package_$key";
			unset(WC()->session->$shipping_session);
		}
	}
} // end if


add_filter('woocommerce_billing_fields', 'woo_filter_state_billing_uc', 10, 1);
add_filter('woocommerce_shipping_fields', 'woo_filter_state_shipping_uc', 10, 1);

function woo_filter_state_billing_uc($address_fields)
{
	$address_fields['billing_state']['required'] = true;
	return $address_fields;
}

function woo_filter_state_shipping_uc($address_fields)
{
	$address_fields['shipping_state']['required'] = true;
	return $address_fields;
}

add_action('admin_menu', 'register_cargus_shipping_subpage');
function register_cargus_shipping_subpage()
{
	add_submenu_page(
		'safealternative-menu-content',
		'Cargus - Livrare',
		'Cargus - Livrare',
		'manage_woocommerce',
		'urgent_redirect',
		function () {
			wp_safe_redirect(safealternative_redirect_url('admin.php?page=wc-settings&tab=shipping&section=urgentcargus_courier'));
			exit;
		}
	);
}
/////////////////////////////////////////////////////////
