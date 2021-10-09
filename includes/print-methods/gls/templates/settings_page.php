<?php
?>
<style>
    .safealternative_page_GLS-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_GLS-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_GLS-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_GLS-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_GLS-plugin-setting input:not(.ed_button), 
    .safealternative_page_GLS-plugin-setting select, 
    .safealternative_page_GLS-plugin-setting textarea, 
    .safealternative_page_GLS-plugin-setting button.button {
        width: 100%;
        max-width: 100%;
    }
    .safealternative_page_GLS-plugin-setting .other_sender_row{
        display: none;
    }
    .safealternative_page_GLS-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_GLS-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_GLS-plugin-setting .remove_other_sender{
        width: 100px;
        float: left;
        margin-right: 30px;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative GLS</h1>
    <br/>
    <br/>
    <form action="options.php" method="POST">
        <?php
        settings_fields('GLS-plugin-settings');
        do_settings_sections('GLS-plugin-settings');
        ?>
        <table>

            <tr>
                <th align="left">Utilizator GLS:</th>
                <td><input type="text" name="GLS_user" value="<?= esc_attr(get_option('GLS_user')); ?>" size="50" placeholder="Numele utilizatorului GLS"/></td>
            </tr>

            <tr>
                <th align="left">Parola GLS:</th>
                <td><input type="password" name="GLS_password" value="<?= esc_attr(get_option('GLS_password')); ?>" size="50" placeholder="Parola utilizatorului GLS"/></td>
            </tr>

            <tr>
                <th align="left">ID expeditor:</th>
                <td><input type="text" name="GLS_senderid" value="<?= esc_attr(get_option('GLS_senderid')); ?>" size="50" placeholder="ID expeditor GLS (deseori acelasi ca numele utilizatorului GLS)"/></td>
            </tr>

            <tr>
                <th align="left" class="responseHereGls"></th>
                <td align="right">
                    <button type="button" name="validate_gls" class="button">Valideaza credentialele GLS</button>
                </td>
            </tr>

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>

            <tr>
                <th align="left" style="padding-bottom: 10px; text-decoration: underline;">Expeditor implicit</th>
                <td></td>
            </tr>

            <tr>
                <th align="left">Nume expeditor:</th>
                <td><input type="text" name="GLS_sender_name" value="<?= esc_attr(get_option('GLS_sender_name')); ?>" size="50" placeholder="Ex: SC. FirmaTest SRL."/></td>
            </tr>

            <tr>
                <th align="left">Adresa expeditor:</th>
                <td><input type="text" name="GLS_sender_address" value="<?= esc_attr(get_option('GLS_sender_address')); ?>" size="50" placeholder="Ex: Str. Aurel Vlaicu Nr. 3-A"/></td>
            </tr>

            <tr>
                <th align="left">Localitate expeditor:</th>
                <td><input type="text" name="GLS_sender_city" value="<?= esc_attr(get_option('GLS_sender_city')); ?>" size="50" placeholder="Ex: Timisoara"/></td>
            </tr>

            <tr>
                <th align="left">Cod postal expeditor:</th>
                <td><input type="text" name="GLS_sender_zipcode" value="<?= esc_attr(get_option('GLS_sender_zipcode')); ?>" size="50" placeholder="Ex: 300702"/></td>
            </tr>

            <tr>
                <th align="left">Telefon expeditor:</th>
                <td><input type="text" name="GLS_sender_phone" value="<?= esc_attr(get_option('GLS_sender_phone')); ?>" size="50" placeholder="Ex: 0799123456"/></td>
            </tr>

            <tr>
                <th align="left">Email expeditor:</th>
                <td><input type="text" name="GLS_sender_email" value="<?= esc_attr(get_option('GLS_sender_email')); ?>" size="50" placeholder="Ex: test@email.com"/></td>
            </tr>

            <tr>
                <th align="left">Nume persoana contact:</th>
                <td><input type="text" name="GLS_sender_contact" value="<?= esc_attr(get_option('GLS_sender_contact')); ?>" size="50" placeholder="Ex: Ionut Popescu"/></td>
            </tr>

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>

            <tr>
                <th align="left" style="padding: 10px 0">
                    <span style="text-decoration: underline;"> Alti expeditori</span> (optional)
                </th>
                <td>
                    <button type="button" name="add_other_sender" class="button">Adauga alt expeditor</button>                    
                </td>
            </tr>

            <tr class="other_sender_row">
                <th align="left">Nume expeditor:</th>
                <td><input disabled type="text" name="GLS_other_sender[name]" value="" size="50" placeholder="Ex: SC. FirmaTest SRL."/></td>
            </tr>

            <tr class="other_sender_row">
                <th align="left">Adresa expeditor:</th>
                <td><input disabled type="text" name="GLS_other_sender[address]" value="" size="50" placeholder="Ex: Str. Aurel Vlaicu Nr. 3-A"/></td>
            </tr>

            <tr class="other_sender_row">
                <th align="left">Localitate expeditor:</th>
                <td><input disabled type="text" name="GLS_other_sender[city]" value="" size="50" placeholder="Ex: Timisoara"/></td>
            </tr>

            <tr class="other_sender_row">
                <th align="left">Cod postal expeditor:</th>
                <td><input disabled type="text" name="GLS_other_sender[zipcode]" value="" size="50" placeholder="Ex: 300702"/></td>
            </tr>

            <tr class="other_sender_row">
                <th align="left">Telefon expeditor:</th>
                <td><input disabled type="text" name="GLS_other_sender[phone]" value="" size="50" placeholder="Ex: 0799123456"/></td>
            </tr>

            <tr class="other_sender_row">
                <th align="left">Email expeditor:</th>
                <td><input disabled type="text" name="GLS_other_sender[email]" value="" size="50" placeholder="Ex: test@email.com"/></td>
            </tr>

            <tr class="other_sender_row">
                <th align="left">Nume persoana contact:</th>
                <td><input disabled type="text" name="GLS_other_sender[contact]" value="" size="50" placeholder="Ex: Ionut Popescu"/></td>
            </tr>
            <?php
            
            $other_senders = maybe_unserialize(get_option('GLS_other_senders'));
            if(empty($other_senders)) $other_senders = array();
            foreach($other_senders as $key => $sender){
            ?>
                <tr>
                    <th align="left">
                        <button type="button" class="remove_other_sender" name="remove_other_sender" value="<?= $key; ?>" class="button">Sterge</button>                    
                    </th>
                    <td><ul>
                        <li><?= $sender['name']; ?></li>
                        <li><?= $sender['address']; ?></li>
                        <li><?= $sender['city']; ?></li>
                        <li><?= $sender['zipcode']; ?></li>
                        <li><?= $sender['phone']; ?></li>
                        <li><?= $sender['email']; ?></li>
                        <li><?= $sender['contact']; ?></li>
                    </ul></td>
                </tr>
            <?php
            }
            ?>              

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>

            <tr>
                <th align="left">Servicii:</th>
                <td>
                    <select name="GLS_services">
                        <option value="" <?= esc_attr(get_option('GLS_services')) == '' ? 'selected="selected"' : ''; ?>>Niciunul</option>
                        <option value="FDS" <?= esc_attr(get_option('GLS_services')) == 'FDS' ? 'selected="selected"' : ''; ?>>FDS</option>
                        <option value="FDS+FSS" <?= esc_attr(get_option('GLS_services')) == 'FDS+FSS' ? 'selected="selected"' : ''; ?>>FDS + FSS</option>
                        <option value="SM2" <?= esc_attr(get_option('GLS_services')) == 'SM2' ? 'selected="selected"' : ''; ?>>SM2</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left"></th>
                <td style="padding-left: 5px;">* Pentru <b>FDS</b> asigurati-va ca adresa de email a clientului este completata in comanda si este valida. <br>
                    * Pentru <b>FDS + FSS</b> trebuie sa va asigurati ca criteriul mentionat mai sus este indeplinit, si numarul de telefon al clientului respecta standardul international (+40 in cazul in care este din Romania).<br>
                    * Pentru <b>SM2</b> trebuie sa va asigurati ca numarul de telefon al clientului respecta standardul international (+40 in cazul in care este din Romania).
                </td>
            </tr>

            <tr>
                <th align="left">Format print:</th>
                <td>
                    <select name="GLS_printertemplate">
                        <option value="A6" <?= esc_attr(get_option('GLS_printertemplate')) == 'A6' ? 'selected="selected"' : ''; ?>>A6 format, blank label</option>
                        <option value="A6_PP" <?= esc_attr(get_option('GLS_printertemplate')) == 'A6_PP' ? 'selected="selected"' : ''; ?>>A6 format, preprinted label</option>
                        <option value="A6_ONA4" <?= esc_attr(get_option('GLS_printertemplate')) == 'A6_ONA4' ? 'selected="selected"' : ''; ?>>A6 format, printed on A4</option>
                        <option value="A4_2x2" <?= esc_attr(get_option('GLS_printertemplate')) == 'A4_2x2' ? 'selected="selected"' : ''; ?>>A4 format, 4 labels on layout 2x2</option>
                        <option value="A4_4x1" <?= esc_attr(get_option('GLS_printertemplate')) == 'A4_4x1' ? 'selected="selected"' : ''; ?>>A4 format, 4 labels on layout 4x1</option>
                        <option value="T_85x85" <?= esc_attr(get_option('GLS_printertemplate')) == 'T_85x85' ? 'selected="selected"' : ''; ?>>85x85 mm format for thermal labels </option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Observatii:</th>
                <td><input type="text" name="GLS_observatii" value="<?= esc_attr(get_option('GLS_observatii')); ?>" size="50" placeholder="Ex: A se contacta telefonic"/></td>
            </tr>

            <tr>
                <th  align="left">Arata nota client dupa observatii:</th>
                <td>
                    <select name="GLS_show_client_note">
                        <option value="0" <?= esc_attr(get_option('GLS_show_client_note')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="1" <?= esc_attr(get_option('GLS_show_client_note')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th  align="left">Arata ID comanda dupa observatii:</th>
                <td>
                    <select name="GLS_show_order_id">
                        <option value="0" <?= esc_attr(get_option('GLS_show_order_id')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="1" <?= esc_attr(get_option('GLS_show_order_id')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>
                </td>
            </tr>            

            <tr>
                <th  align="left">Descrie continut dupa observatii:</th>
                <td>
                    <select name="GLS_show_content">
                        <option value="nu" <?= esc_attr( get_option('GLS_show_content') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="name" <?= esc_attr( get_option('GLS_show_content') ) == 'name' ? 'selected="selected"' : ''; ?>>Denumire produs</option>
                        <option value="sku" <?= esc_attr( get_option('GLS_show_content') ) == 'sku' ? 'selected="selected"' : ''; ?>>SKU produs</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Numar colete:</th>
                <td>
                    <input type="number" min="0" step="1" name="GLS_pcount" value="<?= esc_attr(get_option('GLS_pcount')); ?>" placeholder="Numar colete"/>
                </td>
            </tr>

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>
            
            <tr>
                <th align="left">Trimite mail la generare:</th>
                <td>
                    <select name="GLS_trimite_mail">
                        <option value="da" <?= esc_attr(get_option('GLS_trimite_mail')) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                        <option value="nu" <?= esc_attr(get_option('GLS_trimite_mail')) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Subiect mail:</th>
                <td><input type="text" name="GLS_subiect_mail" value="<?= esc_attr(get_option('GLS_subiect_mail')); ?>" size="50" placeholder="Ex: AWB GLS a fost generat pentru comanda dumneavoastra"/></td>
            </tr>

            <tr>
                <th align="left">Continut mail:</th>
                <td>
                    <?php
                        $email_template = get_option('GLS_email_template');
                        wp_editor( $email_template, 'GLS_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
                    ?>
                </td>
            </tr>

            <tr>
                <th align="left"></th>
                <td align="right">
                    <button type="button" name="reset_email_template" class="button">Reseteaza subiect si continut mail implicit</button>
                </td>
            </tr>  

            <tr>
                <th align="left"></th>
                <td><b>In text-ul de mai sus urmatoarele expresii vor fi completate automat la generarea AWB-ului:</b><br>
                    <b>[nr_comanda]</b>    - Reprezinta numarul comenzii.<br>
                    <b>[data_comanda]</b>  - Reprezinta data in care a fost plasata comanda.<br>
                    <b>[nr_awb]</b>        - Reprzinta numarul AWB-ului generat.<br>
                    <b>[tabel_produse]</b> - Reprezinta un tabel cu capetele de coloana Nume produs / Cantitate / Pret.
                </td>
            </tr>  

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>

            <tr>
                <th align="left">Generare AWB automata:</th>
                <td>
                    <select name="GLS_auto_generate_awb">
                        <option value="nu" <?php echo esc_attr( get_option('GLS_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?php echo esc_attr( get_option('GLS_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left"></th>
                <td>Generarea AWB-ului automata in momentul in care se plaseaza o comanda noua si primeste statusul Processing. </td>
            </tr>     

            <tr>
                <th align="left">Marcheaza comanda Complete automat:</th>
                <td>
                    <select name="GLS_auto_mark_complete">
                        <option value="nu" <?php echo esc_attr( get_option('GLS_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?php echo esc_attr( get_option('GLS_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left"></th>
                <td>Marcheaza comanda cu statusul Complete automat atunci cand curierul ii marcheaza statusul ca si Livrata. </td>
            </tr>              
                        
            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>          

            <tr>
                <td colspan="2"><?php submit_button('Salveaza modificarile'); ?></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri GLS creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
            </tr>

        </table>
    </form>
</div>

<script>
    jQuery($ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/";
        
        $('button[name="add_other_sender"]').on('click', function(){
            $('.other_sender_row').toggle();
            $('.other_sender_row input').prop('disabled', (i, v) => !v );
        });

        $('.remove_other_sender').on('click', function(){
            let remove_key = $(this).val();
            $.ajax({
                url: 'options.php',
                method: 'POST',
                data: {
                    'remove_GLS_other_sender': remove_key
                }
            }).done(function() {
                location.reload();
            });
        });

        $('button[name="validate_gls"]').on('click', function(){
            $.ajax({
                type: 'POST',
                url: url+"validateGLSAuth",
                data: {
                    gls_user: $('input[name="GLS_user"]').val(),
                    gls_pass: $('input[name="GLS_password"]').val(),
                },
                dataType: "json",
                success: function(response) { 
                    if(response['success']){
                        $('.responseHereGls').text('Autentificare reusita.').css('color', '#34a934');
                    } else {
                        $('.responseHereGls').text('Autentificare esuata.').css('color', '#f44336');
                    }
                }
            });
        });

        $('button[name="reset_email_template"]').on('click', function(){
            let confirmation = confirm("Sunteți sigur(ă) că doriți să resetați câmpurile de mail la valorile implicite?");
            if (!confirmation) return;
            
            $.ajax({
                dataType: "json",
                type: "POST",
                url: ajaxurl,
                data: {
                    courier: 'gls',
                    action: 'safealternative_reset_mail'
                },
                success: function(data) {
                    location.reload();
                },
                error: function() {
                    alert('Eroare, vă rugăm să ne contactați pentru a remedia problema.');
                }
            });
        });
    });
</script>
