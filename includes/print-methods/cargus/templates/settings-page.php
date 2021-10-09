<?php

$url = get_option('uc_url');
$key = get_option('uc_key');
$UserName = get_option('uc_username');
$Password = get_option('uc_password');
$obj_urgent = new UrgentCargusAPI($url, $key);
$fields = array(
    'UserName' => $UserName,
    'Password' => $Password,
);
$json_login = json_encode($fields);
$login = $obj_urgent->CallMethod('LoginUser', $json_login, 'POST');

$token = $login['status'] == "200" ? json_decode($login['message']) : null;

$resultLocations = $obj_urgent->CallMethod('PickupLocations/GetForClient', "", 'GET', $token);
$resultMessage = $resultLocations['message'];
$arrayResultLocations = json_decode($resultMessage, true);

$valid_auth = ($resultLocations['status'] == "200" && $resultMessage != "Failed to authenticate!");

?>
<style>
    .safealternative_page_urgent-cargus-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_urgent-cargus-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_urgent-cargus-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_urgent-cargus-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_urgent-cargus-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_urgent-cargus-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_urgent-cargus-setting input:not(.ed_button), 
    .safealternative_page_urgent-cargus-setting select, 
    .safealternative_page_urgent-cargus-setting textarea, 
    .safealternative_page_urgent-cargus-setting button.button {
        width: 100%;
        max-width: 100%;
    }
    .cargus_auth {
        border: solid 1px;
        padding: 20px;
    }
    <?= $valid_auth == false ? '.hideOnFail { display: none; }' : '.hideEmail { display: none; }'; ?>
</style>

<div class="wrap">
    <h1>SafeAlternative UrgentCargus</h1>
    <br/>
    <br/>
    <form action="options.php" method="post" id="urgentcargus_settings_form">
    <?php
        settings_fields('urgent-cargus-plugin-settings');
        do_settings_sections('urgent-cargus-plugin-settings');
    ?>
    <table>
        <input type="hidden" name="uc_url" value="https://urgentcargus.azure-api.net/api">
        <input type="hidden" name="uc_key" value="c76c9992055e4e419ff7fa953c3e4569">

        <tr>
            <th align="left">Utilizator UrgentCargus</th>
            <td><input type="text"  name="uc_username" value="<?= esc_attr(get_option('uc_username')); ?>" size="50" placeholder="Numele utilizatorului Cargus"/></td>
        </tr>

        <tr>
            <th align="left">Parola UrgentCargus</th>
            <td><input type="password"  name="uc_password" value="<?= esc_attr(get_option('uc_password')); ?>" size="50" placeholder="Parola utilizatorului Cargus"/></td>
        </tr>

        <tr>
            <th align="left" class="responseHereUrgent"></th>
            <td align="right">
                <button type="button" name="validate_urgent" class="button">Valideaza credentialele Cargus</button>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr> 

        <tr class="hideEmail">
            <th align="left"></th>
            <td>
            <span style="color: red;"><b>Buna ziua!<br>Pentru credențialele de Cargus care intampina probleme cu autentificarea in modul, trebuie sa trimiteti emailul de mai jos celor de la Cargus pe adresa de email: <a href = "mailto: ecom@urgentcargus.ro">ecom@urgentcargus.ro</a> si sa completati datele din email - pastrati insa api key neschimbat.</b></span>
            </td>
        </tr>

        <tr class="hideEmail">
        <th align="left">Continut mail:</th>
            <td>
                <div class="cargus_auth">Buna ziua,
                <br/>
                Va rugam sa ne ajutati cu sincronizarea contului firmei noastre cu cheia api de mai jos.
                <br/>
                Datele firmei sunt:
                <br/>
                Nume firme: [COMPLETATI AICI]
                <br/>
                Cod fiscal: [COMPLETATI AICI]
                <br/>
                Nr inregistrare recom:  [COMPLETATI AICI]
                <br/>
                Administrator: [COMPLETATI AICI]
                <br/>
                API Key: <strong>c76c9992055e4e419ff7fa953c3e4569</strong>
                </br>
                Username Webexpress:  [COMPLETATI AICI]</div>
            </td>
        </tr>        
        
        <tr class="hideOnFail"> 
            <th align="left">ID punct ridicare</th> 
            <td> 
                <select name="uc_punct_ridicare"> <?php
                    if($valid_auth)
                        foreach ($arrayResultLocations as $location) {  
                            ?><option value="<?= $location['LocationId']; ?>" <?= esc_attr(get_option('uc_punct_ridicare')) == $location['LocationId'] ? 'selected="selected"' : ''; ?>><?= $location['Name']; ?></option><?php
                        }
                    ?> 
                </select> 
            </td> 
        </tr>

        <tr class="hideOnFail">
            <th  align="left">ID tarif</th>
            <?php
                $resultPriceTables = $obj_urgent->CallMethod('PriceTables', "", 'GET', $token);
                $resultMessage = $resultPriceTables['message'];
                $arrayPriceTables = json_decode($resultMessage, true);
                if ($valid_auth) { 
                    ?> <td><select name="uc_price_table_id"> <?php
                    foreach ($arrayPriceTables as $price_table) {  
                        ?><option value="<?= $price_table['PriceTableId']; ?>" <?= esc_attr(get_option('uc_price_table_id')) == $price_table['PriceTableId'] ? 'selected="selected"' : ''; ?>><?= $price_table['Name']; ?></option><?php
                    }
                    ?> </select></td> <?php                        
                } else {       
                    ?> <td><input type="text" name="uc_price_table_id" value="<?= esc_attr(get_option('uc_price_table_id')); ?>" size="50" /></td> 
            <?php } ?>
        </tr> 

        <tr class="hideOnFail">
            <th align="left">Numar colete:</th>
            <td><input type="number" name="uc_nr_colete" value="<?= esc_attr(get_option('uc_nr_colete')); ?>" size="50" /></td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Numar plicuri:</th>
            <td><input type="number" name="uc_nr_plicuri" value="<?= esc_attr(get_option('uc_nr_plicuri')); ?>" size="50" /></td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Plata transport:</th>
            <td>
                <select name="uc_plata_transport">
                    <option value="1" <?= esc_attr(get_option('uc_plata_transport')) == '1' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                    <option value="2" <?= esc_attr(get_option('uc_plata_transport')) == '2' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                </select>
            </td>
        </tr>
        
        <tr class="hideOnFail">
            <th align="left">Plata ramburs:</th>
            <td>
                <select name="uc_plata_ramburs">
                    <option value="1" <?= esc_attr(get_option('uc_plata_ramburs')) == '1' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                    <option value="2" <?= esc_attr(get_option('uc_plata_ramburs')) == '2' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Asigurare:</th>
            <td>
                <select name="uc_asigurare">
                    <option value="0" <?= esc_attr(get_option('uc_asigurare')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="1" <?= esc_attr(get_option('uc_asigurare')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Deschidere la livrare:</th>
            <td>
                <select name="uc_deschidere">
                    <option value="0" <?= esc_attr(get_option('uc_deschidere')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="1" <?= esc_attr(get_option('uc_deschidere')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>
        
        <tr class="hideOnFail">
            <th align="left">Livrare matinala:</th>
            <td>
                <select name="uc_matinal">
                    <option value="0" <?= esc_attr(get_option('uc_matinal')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="1" <?= esc_attr(get_option('uc_matinal')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>
        
        <tr class="hideOnFail">
            <th  align="left">Livrare Sambata:</th>
            <td>
                <select name="uc_sambata">
                    <option value="0" <?= esc_attr(get_option('uc_sambata')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="1" <?= esc_attr(get_option('uc_sambata')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th  align="left">Tip serviciu:</th>
            <td>
                <?php $uc_tip_serviciu = esc_attr(get_option('uc_tip_serviciu')); ?>
                <select name="uc_tip_serviciu">
                    <option value="1" <?= $uc_tip_serviciu == '1' ? 'selected="selected"' : ''; ?>>Standard</option>
                    <option value="4" <?= $uc_tip_serviciu == '4' ? 'selected="selected"' : ''; ?>>Business Partener</option>
                    <option value="34" <?= $uc_tip_serviciu == '34' ? 'selected="selected"' : ''; ?>>Economic Standard</option>
                    <option value="35" <?= $uc_tip_serviciu == '35' ? 'selected="selected"' : ''; ?>>Standard Plus</option>
                    <option value="36" <?= $uc_tip_serviciu == '36' ? 'selected="selected"' : ''; ?>>Palet Standard</option>
                </select>
            </td>
        </tr>
    
        <tr class="hideOnFail">
            <th  align="left">Descrie continut in AWB:</th>
            <td>
                <select name="uc_descrie_continut">
                    <option value="0" <?= esc_attr(get_option('uc_descrie_continut')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="1" <?= esc_attr(get_option('uc_descrie_continut')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th  align="left">Format tiparire AWB:</th>
            <td>
                <select name="uc_print_format">
                    <option value="0" <?= esc_attr(get_option('uc_print_format')) == '0' ? 'selected="selected"' : ''; ?>>A4</option>
                    <option value="1" <?= esc_attr(get_option('uc_print_format')) == '1' ? 'selected="selected"' : ''; ?>>10x14</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th  align="left">Tiparire dubla AWB:</th>
            <td>
                <select name="uc_print_once">
                    <option value="0" <?= esc_attr(get_option('uc_print_once')) == '0' ? 'selected="selected"' : ''; ?>>Da</option>
                    <option value="1" <?= esc_attr(get_option('uc_print_once')) == '1' ? 'selected="selected"' : ''; ?>>Nu</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Observatii:</th>
            <td><input type="text" placeholder="A se contacta telefonic" name="uc_observatii" value="<?= esc_attr(get_option('uc_observatii')); ?>" size="50" /></td>
        </tr>    

        <tr class="hideOnFail">
            <th align="left">Referinta Serie Client:</th>
            <td><input type="text" name="uc_serie_client" value="<?= esc_attr(get_option('uc_serie_client')); ?>" size="50" /></td>
        </tr>   

        <tr class="hideOnFail">
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Lungime colet standard:</th>
            <td><input type="text" name="uc_force_length" value="<?= esc_attr( get_option('uc_force_length') ); ?>" size="50" /></td>
        </tr>   

        <tr class="hideOnFail">
            <th align="left">Latime colet standard:</th>
            <td><input type="text" name="uc_force_width" value="<?= esc_attr( get_option('uc_force_width') ); ?>" size="50" /></td>
        </tr>   

        <tr class="hideOnFail">
            <th align="left">Inaltime colet standard:</th>
            <td><input type="text" name="uc_force_height" value="<?= esc_attr( get_option('uc_force_height') ); ?>" size="50" /></td>
        </tr>        

        <tr class="hideOnFail">
            <th align="left">Greutate colet standard:</th>
            <td><input type="text" name="uc_force_weight" value="<?= esc_attr( get_option('uc_force_weight') ); ?>" size="50" /></td>
        </tr>   

        <tr class="hideOnFail">
            <th align="left"></th>
            <td>In cazul in care dimensiunile standard nu sunt completate, ele vor fi calculate automat in functie de parametrii configurati la nivel de produs</td>
        </tr>                                    

        <tr class="hideOnFail">
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Trimite mail la generare:</th>
            <td>
                <select name="uc_trimite_mail">
                    <option value="1" <?= esc_attr(get_option('uc_trimite_mail')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                    <option value="0" <?= esc_attr(get_option('uc_trimite_mail')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                </select>
            </td>
        </tr>
        
        <tr class="hideOnFail">
            <th align="left">Subiect mail:</th>
            <td><input type="text"  name="uc_subiect_mail" value="<?= esc_attr(get_option('uc_subiect_mail')); ?>" size="50" /></td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Continut mail:</th>
            <td>
                <?php
                    $email_template = get_option('uc_email_template');
                    wp_editor( $email_template, 'uc_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
                ?>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th align="left"></th>
            <td align="right">
                <button type="button" name="reset_email_template" class="button">Reseteaza subiect si continut mail implicit</button>
            </td>
        </tr>  

        <tr class="hideOnFail">
            <th align="left"></th>
            <td><b>In text-ul de mai sus urmatoarele expresii vor fi completate automat la generarea AWB-ului:</b><br>
                <b>[nr_comanda]</b>    - Reprezinta numarul comenzii.<br>
                <b>[data_comanda]</b>  - Reprezinta data in care a fost plasata comanda.<br>
                <b>[nr_awb]</b>        - Reprzinta numarul AWB-ului generat.<br>
                <b>[tabel_produse]</b> - Reprezinta un tabel cu capetele de coloana Nume produs / Cantitate / Pret.
            </td>
        </tr>

        <tr class="hideOnFail">
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>          

        <tr class="hideOnFail">
            <th align="left">Generare AWB automata:</th>
            <td>
                <select name="uc_auto_generate_awb">
                    <option value="nu" <?= esc_attr( get_option('uc_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?= esc_attr( get_option('uc_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th align="left"></th>
            <td>Generarea AWB-ului automata in momentul in care se plaseaza o comanda noua si primeste statusul Processing. </td>
        </tr>     

        <tr class="hideOnFail">
            <th align="left">Marcheaza comanda Complete automat:</th>
            <td>
                <select name="uc_auto_mark_complete">
                    <option value="nu" <?= esc_attr( get_option('uc_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?= esc_attr( get_option('uc_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th align="left"></th>
            <td>Marcheaza comanda cu statusul Complete automat atunci cand curierul ii marcheaza statusul ca si Confirmat sau Rambursat.</td>
        </tr>                      

        <tr class="hideOnFail">
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <td colspan="2"><?php submit_button('Salveaza modificarile'); ?></td>
        </tr>

        <tr>
            <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri UrgentCargus creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
        </tr>
    </table>
    </form>
</div>

<script>
    jQuery( $ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/";
        
        $('button[name="validate_urgent"]').on('click', function(){
            let responseDiv =  $('.responseHereUrgent'),
                submitBtn = $('#urgentcargus_settings_form #submit');
            $.ajax({
                type: 'POST',
                url: url+"validateUrgentAuth",
                data: {
                    urgent_user: $('input[name="uc_username"]').val(),
                    urgent_pass: $('input[name="uc_password"]').val(),
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
                    courier: 'urgent',
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
    })
</script>
