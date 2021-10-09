<?php ?>
<style>
    .safealternative_page_dpd-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_dpd-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_dpd-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_dpd-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_dpd-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_dpd-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_dpd-plugin-setting input:not(.ed_button), 
    .safealternative_page_dpd-plugin-setting select, 
    .safealternative_page_dpd-plugin-setting textarea, 
    .safealternative_page_dpd-plugin-setting button.button {
        width: 100%;
        max-width: 100%;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative DPD</h1>
    <br/>
    <br/>
    <form action="options.php" method="post" id="dpd_settings_form">
    <?php
        settings_fields('dpd-plugin-settings');
        do_settings_sections('dpd-plugin-settings');
    ?>
    <table>
        <tr>
            <th align="left">Utilizator DPD</th>
            <td><input type="text"  name="dpd_username" value="<?= esc_attr(get_option('dpd_username')); ?>" size="50" placeholder="Numele utilizatorului DPD"/></td>
        </tr>

        <tr>
            <th align="left">Parola DPD</th>
            <td><input type="password"  name="dpd_password" value="<?= esc_attr(get_option('dpd_password')); ?>" size="50" placeholder="Parola utilizatorului DPD"/></td>
        </tr>

        <tr>
            <th align="left" class="responseHereDPD"></th>
            <td align="right">
                <button type="button" name="validate_dpd" class="button">Valideaza credentialele DPD</button>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>            
        
        <tr> 
            <th align="left">Punct expeditor:</th> 
            <td>
                <select name="dpd_sender_id">
                <?php 
                    $senders = (new SafealternativeDPDClass)->get_senders();
                    $current_sender_id = esc_attr(get_option('dpd_sender_id'));
                    if (!empty($senders)) {
                        foreach($senders as $sender) {
                            $selected = ($sender['clientId'] == $current_sender_id) ? 'selected="selected"' : '';
                            echo "<option value='{$sender['clientId']}' {$selected}>{$sender['address']['fullAddressString']}</option>";
                        }
                    } else {
                        ?> 
                        <option value="" <?= esc_attr(get_option('dpd_sender_id')) == '' ? 'selected="selected"' : ''; ?>>Utilizator implicit</option>
                        <?php
                    }
                ?>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Serviciul implicit:</th>
            <td>
                <select name="dpd_service_id">
                <?php 
                    $services = (new SafealternativeDPDClass)->get_services();
                    $current_service_id = esc_attr(get_option('dpd_service_id'));
                    if (!empty($services)) {
                        foreach($services as $service) {
                            $selected = ($service['id'] == $current_service_id) ? 'selected="selected"' : '';
                            echo "<option value='{$service['id']}' {$selected}>{$service['id']} - {$service['name']}</option>";
                        }
                    } else {
                        ?> 
                        <option value="2505" <?= esc_attr(get_option('dpd_service_id')) == '2505' ? 'selected="selected"' : ''; ?>>2505 - DPD STANDARD</option>
                        <option value="2002" <?= esc_attr(get_option('dpd_service_id')) == '2002' ? 'selected="selected"' : ''; ?>>2002 - CLASIC NATIONAL</option>
                        <option value="2003" <?= esc_attr(get_option('dpd_service_id')) == '2003' ? 'selected="selected"' : ''; ?>>2003 - CLASIC NATIONAL (COLET)</option>
                        <option value="2005" <?= esc_attr(get_option('dpd_service_id')) == '2005' ? 'selected="selected"' : ''; ?>>2005 - CARGO NATIONAL</option>
                        <option value="2412" <?= esc_attr(get_option('dpd_service_id')) == '2412' ? 'selected="selected"' : ''; ?>>2412 - PALLET ONE RO</option>
                        <?php
                    }
                ?>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Plata transport:</th>
            <td>
                <select name="dpd_courier_service_payer">
                    <option value="SENDER" <?= esc_attr(get_option('dpd_courier_service_payer')) == 'SENDER' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                    <option value="RECIPIENT" <?= esc_attr(get_option('dpd_courier_service_payer')) == 'RECIPIENT' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                    <option value="THIRD_PARTY" <?= esc_attr(get_option('dpd_courier_service_payer')) == 'THIRD_PARTY' ? 'selected="selected"' : ''; ?>>Contract/tert</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Platitor ambalaj:</th>
            <td>
                <select name="dpd_courier_package_payer">
                    <option value="SENDER" <?= esc_attr(get_option('dpd_courier_package_payer')) == 'SENDER' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                    <option value="RECIPIENT" <?= esc_attr(get_option('dpd_courier_package_payer')) == 'RECIPIENT' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                    <option value="THIRD_PARTY" <?= esc_attr(get_option('dpd_courier_package_payer')) == 'THIRD_PARTY' ? 'selected="selected"' : ''; ?>>Contract/tert</option>
                </select>
            </td>
        </tr>

        <tr>
            <th align="left">Numar colete:</th>
            <td><input type="number" name="dpd_parcel_count" value="<?= esc_attr(get_option('dpd_parcel_count')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Tip continut colete:</th>
            <td><input type="text" name="dpd_content_type" value="<?= esc_attr(get_option('dpd_content_type')); ?>" size="50" required /></td>
        </tr>
        
        <tr>
            <th  align="left">Livrare Sambata:</th>
            <td>
                <select name="dpd_is_sat_delivery">
                    <option value="n" <?= esc_attr(get_option('dpd_is_sat_delivery')) == 'n' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="y" <?= esc_attr(get_option('dpd_is_sat_delivery')) == 'y' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Pachete Fragile:</th>
            <td>
                <select name="dpd_is_fragile">
                    <option value="n" <?= esc_attr(get_option('dpd_is_fragile')) == 'n' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="y" <?= esc_attr(get_option('dpd_is_fragile')) == 'y' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr> 

        <tr>
            <th align="left">Tip printare AWB:</th>
            <td>
                <select name="dpd_page_type">
                    <option value="A4" <?= esc_attr( get_option('dpd_page_type') ) == 'A4' ? 'selected="selected"' : ''; ?>>A4</option>
                    <option value="A6" <?= esc_attr( get_option('dpd_page_type') ) == 'A6' ? 'selected="selected"' : ''; ?>>A6</option>
                    <option value="A4_4xA6" <?= esc_attr( get_option('dpd_page_type') ) == 'A4_4xA6' ? 'selected="selected"' : ''; ?>>A4_4xA6</option>
                </select>
            </td>
        </tr>

        <tr>
            <th align="left">Nota colet implicita<br> (max 200 caractere):</th>
            <td>
                <textarea name="dpd_parcel_note" lines="2" maxlength="200"><?= esc_attr( get_option('dpd_parcel_note') ) ?></textarea>
                <sub style="float:right;"><span class="letterCount">0</span>/200</sub>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Greutate colet standard:</th>
            <td>
                <input type="text" name="dpd_force_weight" value="<?= esc_attr( get_option('dpd_force_weight') ); ?>" size="50" />
            </td>
        </tr>

        <tr>
            <th align="left"></th>
            <td>In cazul in care greutatea standard nu este completata, ea va fi calculata automat in functie de parametrii configurati la nivel de produs.</td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Trimite mail la generare:</th>
            <td>
                <select name="dpd_trimite_mail">
                    <option value="nu" <?= esc_attr(get_option('dpd_trimite_mail')) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?= esc_attr(get_option('dpd_trimite_mail')) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th align="left">Subiect mail:</th>
            <td><input type="text"  name="dpd_subiect_mail" value="<?= esc_attr(get_option('dpd_subiect_mail')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Continut mail:</th>
            <td>
                <?php
                    $email_template = get_option('dpd_email_template');
                    wp_editor( $email_template, 'dpd_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
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
                <select name="dpd_auto_generate_awb">
                    <option value="nu" <?php echo esc_attr( get_option('dpd_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('dpd_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                <select name="dpd_auto_mark_complete">
                    <option value="nu" <?php echo esc_attr( get_option('dpd_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('dpd_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th align="left"></th>
            <td>Marcheaza comanda cu statusul Complete automat atunci cand curierul ii marcheaza statusul ca si Confirmata.</td>
        </tr>                      

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <td colspan="2"><?php submit_button('Salveaza modificarile'); ?></td>
        </tr>

        <tr>
            <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri DPD creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
        </tr>
    </table>
    </form>
</div>

<script>
    jQuery( $ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/";
        
        $('button[name="validate_dpd"]').on('click', function(){
            let responseDiv =  $('.responseHereDPD'),
                submitBtn = $('#dpd_settings_form #submit');
            $.ajax({
                type: 'POST',
                url: url+"validateDPDAuth",
                data: {
                    username: $('input[name="dpd_username"]').val(),
                    password: $('input[name="dpd_password"]').val(),
                },
                dataType: "json",
                success: function(response) { 
                    if(response['success']){
                        responseDiv.text('Autentificare reusita.').css('color', '#34a934');
                        submitBtn.click();
                    } else {
                        responseDiv.text('Autentificare esuata.').css('color', '#f44336');
                        submitBtn.click();
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
                    courier: 'dpd',
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
        
        $('.letterCount').text($('textarea[name="dpd_parcel_note"]').val().length);
        $('textarea[name="dpd_parcel_note"]').on('keyup change', function(){$('.letterCount').text($(this).val().length);})
    })
</script>
