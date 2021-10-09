<?php

$default_template = 'Buna ziua,
<br/>
Comanda dumneavoastra  cu numarul: [nr_comanda] din data de [data_comanda] a fost expediata prin serviciu de curierat bookurier si este in curs de livrare.
<br/>
Nota de transport (AWB-ul) are numarul [nr_awb] si poate fi urmarita aici: <a href="https://www.bookurier.ro/colete/AWB/track0.php" target="_blank">Status comanda</a>
<br/>
Va felicitam pentru alegerea facuta si va asteptam cu drag sa va onoram si alte comenzi.
<br/>
Detalii comanda:
<br/>[tabel_produse]<br/>';      

add_option('bookurier_email_template', $default_template);
register_setting('bookurier-plugin-settings', 'bookurier_email_template');

