<?php

$default_template = 'Buna ziua,
<br/>
Comanda dumneavoastra  cu numarul: [nr_comanda] din data de [data_comanda] a fost expediata prin serviciu de curierat GLS si este in curs de livrare.
<br/>
Nota de transport (AWB-ul) are numarul [nr_awb] si poate fi urmarita aici: <a href="https://gls-group.eu/RO/ro/urmarire-colet?match=[nr_awb]" target="_blank">Status comanda</a>
<br/>
Va felicitam pentru alegerea facuta si va asteptam cu drag sa va onoram si alte comenzi.
<br/>
Detalii comanda:
<br/>[tabel_produse]<br/>';      

add_option('GLS_email_template', $default_template);
register_setting('GLS-plugin-settings', 'GLS_email_template');

