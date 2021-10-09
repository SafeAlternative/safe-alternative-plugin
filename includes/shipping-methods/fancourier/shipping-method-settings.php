<?php

return array(
    'title' => array(
        'title' => __('Denumire metoda livrare *', 'fan'),
        'type' => 'text',
        'description' => __('Denumirea metodei de livrare in Cart si Checkout, vizibila de catre client.', 'fan'),
        'default' => __('Fan Courier', 'fan'),
        'desc_tip'      => true,
        'custom_attributes' => array('required' => 'required')
    ),
    'tarif_contract' => array(
        'title'         => __('Afisare tarif contract', 'fan'),
        'type'          => 'select',
        'default'       => 'no',
        'description' => __('Pentru a activa aceasta optiune trebuie sa aveti si metoda FanCourier - AWB activata si configurata.', 'fan'),
        'css'           => 'width:400px;',
        'desc_tip'      => true,
        'options'       => array(
            'no'        => __('Nu', 'fan'),
            'yes'       => __('Da', 'fan')
        ),
    ),
    'tarif_contract_tva' => array(
        'title'         => __('Adauga cota TVA pentru tarif contract', 'fan'),
        'type'          => 'number',
        'default'       => __('0', 'fan'),
        'desc_tip'      => true,
        'custom_attributes' => array('step' => '0.1', 'min' => '0'),
        'description'   => __('Adauga % TVA atunci cand este folosit Tariful din contract - Acest pret vine fara TVA de la FanCourier', 'fan')
    ),
    'prag_gratis_Bucuresti' => array(
        'title'     => __('Prag gratis Bucuresti', 'fan'),
        'type'      => 'text',
        'default'   => __('250', 'fan')
    ),
    'suma_fixa_Bucuresti' => array(
        'title'     => __('Suma fixa Bucuresti', 'fan'),
        'type'      => 'text',
        'default'   => __('15', 'fan')
    ),
    'prag_gratis_provincie' => array(
        'title'     => __('Prag gratis provincie', 'fan'),
        'type'      => 'text',
        'default'   => __('250', 'fan')
    ),
    'suma_fixa_provincie' => array(
        'title'     => __('Suma fixa provincie', 'fan'),
        'type'      => 'text',
        'default'   => __('18', 'fan')
    ),
    'pret_km_suplimentar' => array(
        'title'         => __('Pret KM suplimentar', 'fan'),
        'type'          => 'text',
        'default'       => __('1', 'fan'),
        'desc_tip'      => true,
        'description'   => __('Pretul de KM suplimentar va fi adaugat la suma fixa, daca doriti sa folositi doar pretul fix, setati acest camp pe 0.', 'fan')
    ),
    'tarif_implicit' => array(
        'title'         => __('Tarif implicit', 'fan'),
        'type'          => 'number',
        'default'       => __('0', 'fan'),
        'desc_tip'      => true,
        'custom_attributes' => array('step' => '0.01', 'min' => '0'),
        'description'   => __('Tariful implicit pentru metoda de livrare, pentru a arata o suma atunci cand nu este introdusa adresa de livrare. ', 'fan')
    ),
    'tarif_maxim' => array(
        'title'         => __('Tarif maxim livrare', 'fan'),
        'type'          => 'text',
        'default'       => __('40', 'fan'),
        'desc_tip'      => true,
        'description'   => __('Tariful final nu poate depasi aceasta valoare', 'fan')
    ),
    'numar_colete' => array(
        'title'         => __('Numar colete', 'fan'),
        'type'          => 'number',
        'default'       => __('1', 'fan'),
        'desc_tip'      => true,
        'description'   => __('Nr. colete pentru calcul tarif', 'fan')
    ),
    'numar_plicuri' => array(
        'title'         => __('Numar plicuri', 'fan'),
        'type'          => 'number',
        'default'       => __('1', 'fan'),
        'desc_tip'      => true,
        'description'   => __('Nr. plicuri pentru calcul tarif', 'fan')
    ),
    'val_declarata' => array(
        'title'         => __('Val declarata (Asigurare)', 'fan'),
        'type'          => 'select',
        'default'       => 'Nu',
        'css'           => 'width:400px;',
        'options'       => array(
            'Nu'        => __('Nu', 'fan'),
            'Da'        => __('Da', 'fan')
        ),
        'desc_tip'      => true,
        'description'       => __('Daca se doreste asigurare, implicit valoarea declarata este valoarea cosului', 'fan')
    ),
    'plata_transportului' => array(
        'title'         => __('Plata transportului', 'fan'),
        'type'          => 'select',
        'default'       => 'destinatar',
        'css'           => 'width:400px;',
        'options'       => array(
            'expeditor'         => __('Expeditor', 'fan'),
            'destinatar'        => __('Destinatar', 'fan')
        ),
        'desc_tip'      => true,
        'description'       => __('Cine plateste livrarea. Se selecteaza expeditor sau destinatar', 'fan')
    ),
    'plata_rambursului' => array(
        'title'         => __('Plata rambursului', 'fan'),
        'type'          => 'select',
        'default'       => 'destinatar',
        'css'           => 'width:400px;',
        'options'       => array(
            'expeditor'         => __('Expeditor', 'fan'),
            'destinatar'        => __('Destinatar', 'fan')
        ),
        'desc_tip'      => true,
        'description'       => __('Cine plateste rambursul (Atentie, nu cine primeste rambursul). Se selecteaza expeditor sau destinatar', 'fan')
    ),
    'collectpoint_activ' => array(
        'title'         => __('Activeaza serviciul Collect Point <span style="color: red">(functie premium)</span>', 'fan'),
        'type'          => 'select',
        'default'       => 'Nu',
        'css'           => 'width:400px;',
        'options'       => array(
            'no'        => __('Nu', 'fan'),
            'yes'        => __('Da', 'fan')
        ),
        'desc_tip'      => true,
        'description'       => __('Pe pagina de checkout clientii vor avea posibilitatea de a alege livrarea catre un Collect Point Fancourier. Setarea va fi ignorata daca folositi un cont SafeAlternative FanCourier gratuit.', 'fan')
    ),
    'numar_collectpoints' => array(
        'title'         => __('Numar locatii Collect Point afisate <span style="color: red">(functie premium)</span>', 'fan'),
        'type'          => 'number',
        'default'       => __('0', 'fan'),
        'desc_tip'      => true,
        'description'   => __('Cate locatii Collect Point sunt afisate pe pagina de checkout ca optiune ', 'fan')
    ),
    'tarif_collectpoints' => array(
        'title'         => __('Suma fixa pentru Collect Point <span style="color: red">(functie premium)</span>', 'fan'),
        'type'          => 'number',
        'default'       => __('0', 'fan'),
        'desc_tip'      => true,
        'description'   => __('Daca doriti ca pretul sa fie completat automat cu tariful de contract, lasati acesti camp gol sau 0.', 'fan'),
        'custom_attributes' => array('step' => '0.01', 'min' => '0'),
    ),
    'prag_gratis_collectpoints' => array(
        'title'         => __('Prag gratis Collect Point <span style="color: red">(functie premium)</span>', 'fan'),
        'type'          => 'number',
        'default'       => __('0', 'fan'),
        'custom_attributes' => array('step' => '0.01', 'min' => '0'),
    )
);
