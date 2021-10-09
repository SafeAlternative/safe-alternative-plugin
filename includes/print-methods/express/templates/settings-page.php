<?php ?>
<style>
    .safealternative_page_express-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_express-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_express-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_express-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_express-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_express-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_express-plugin-setting input:not(.ed_button), 
    .safealternative_page_express-plugin-setting select, 
    .safealternative_page_express-plugin-setting textarea, 
    .safealternative_page_express-plugin-setting button.button {
        width: 100%;
        max-width: 100%;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative Express</h1>
    <br/>
    <br/>
    <form action="options.php" method="post" id="express_settings_form">
    <?php
        settings_fields('express-plugin-settings');
        do_settings_sections('express-plugin-settings');
    ?>
    <table>
        <tr>
            <th align="left">Express Key</th>
            <td><input type="text"  name="express_key" value="<?= esc_attr(get_option('express_key')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left" class="responseHereExpress"></th>
            <td align="right">
                <button type="button" name="validate_express" class="button">Valideaza credentialele Express</button>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>            

        <tr>
            <th  align="left">Tip colet:</th>
            <td>
                <select name="express_package_type">
                    <option value="envelope" <?= esc_attr(get_option('express_package_type')) == 'envelope' ? 'selected="selected"' : ''; ?>>Plic</option>
                    <option value="package" <?= esc_attr(get_option('express_package_type')) == 'package' ? 'selected="selected"' : ''; ?>>Colet</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th  align="left">Serviciul implicit:</th>
            <td>
                <select name="express_service">
                <?php 
                    $services = (new SafealternativeExpressClass)->get_services();
                    $current_service = esc_attr(get_option('express_service'));
                    if (!empty($services) && empty($services['error'])) {
                        foreach($services as $service) {
                            $selected = ($service['name'] == $current_service) ? 'selected="selected"' : '';
                            echo "<option value='{$service['name']}' {$selected}>{$service['name']}</option>";
                        }
                    } else {
                        ?> 
                            <option value="Standard" <?= esc_attr(get_option('express_service')) == 'Standard' ? 'selected="selected"' : ''; ?>>Standard</option>
                        <?php
                    }
                ?>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Retur:</th>
            <td>
                <select name="express_retur">
                    <option value="true" <?= esc_attr(get_option('express_retur')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                    <option value="false" <?= esc_attr(get_option('express_retur')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                </select>
            </td>
        </tr>

        <tr id='tipRetur' <?= esc_attr(get_option('express_retur')) == 'false' ? 'style="display: none;"' : '' ?>>
            <th  align="left">Tip retur:</th>
            <td>
                <select name="express_retur_type">
                    <option value="document" <?= esc_attr(get_option('express_retur_type')) == 'document' ? 'selected="selected"' : ''; ?>>Document</option>
                    <option value="colet" <?= esc_attr(get_option('express_retur_type')) == 'colet' ? 'selected="selected"' : ''; ?>>Colet</option>
                </select>
            </td>
        </tr>

        <tr>
            <th align="left">Numar colete:</th>
            <td><input type="number" name="express_parcel_count" value="<?= esc_attr(get_option('express_parcel_count')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Continut:</th>
            <td><input type="text" name="express_content" value="<?= esc_attr(get_option('express_content')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Valoare asigurare:</th>
            <td><input type="text" name="express_insurance" value="<?= esc_attr(get_option('express_insurance')); ?>" size="50"/></td>
        </tr>
        
        <tr>
            <th  align="left">Platitor:</th>
            <td>
                <select name="express_payer">
                    <option value="expeditor" <?= esc_attr(get_option('express_payer')) == 'expeditor' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                    <option value="destinatar" <?= esc_attr(get_option('express_payer')) == 'destinatar' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                </select>
            </td>
        </tr>

        <tr>
            <th align="left">Nume:</th>
            <td><input type="text" name="express_name" value="<?= esc_attr(get_option('express_name')); ?>" size="50" required /></td>
        </tr>
        
        <tr>
            <th align="left">Persoana de contact:</th>
            <td><input type="text" name="express_contact_person" value="<?= esc_attr(get_option('express_contact_person')); ?>" size="50" required /></td>
        </tr>
        
        <tr>
            <th align="left">Telefon:</th>
            <td><input type="tel" name="express_phone" value="<?= esc_attr(get_option('express_phone')); ?>" size="50" required pattern="\+?\d{6,14}" /></td>
        </tr>      
        
        <tr>
            <th align="left">Email:</th>
            <td><input type="email" name="express_email" value="<?= esc_attr(get_option('express_email')); ?>" size="50" required /></td>
        </tr> 

        <tr>
            <th align="left">Judet:</th>
            <td><input type="text" name="express_county" value="<?= esc_attr(get_option('express_county')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Oras:</th>
            <td><input type="text" name="express_city" value="<?= esc_attr(get_option('express_city')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Adresa:</th>
            <td><input type="text" name="express_address" value="<?= esc_attr(get_option('express_address')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Cod Postal:</th>
            <td><input type="text" name="express_postcode" value="<?= esc_attr(get_option('express_postcode')); ?>" size="50" required /></td>
        </tr>
        
        <tr>
            <th  align="left">Livrare Sambata:</th>
            <td>
                <select name="express_is_sat_delivery">
                    <option value="false" <?= esc_attr(get_option('express_is_sat_delivery')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('express_is_sat_delivery')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Retur document semnat:</th>
            <td>
                <select name="express_retur_signed_doc_delivery">
                    <option value="false" <?= esc_attr(get_option('express_retur_signed_doc_delivery')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('express_retur_signed_doc_delivery')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Livrare 18:00 - 20:00:</th>
            <td>
                <select name="express_18hr_20hr_package">
                    <option value="false" <?= esc_attr(get_option('express_18hr_20hr_package')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('express_18hr_20hr_package')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Curierul sa vina cu AWB printat:</th>
            <td>
                <select name="express_printed_awb">
                    <option value="false" <?= esc_attr(get_option('express_printed_awb')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('express_printed_awb')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Pachete Fragile:</th>
            <td>
                <select name="express_is_fragile">
                    <option value="false" <?= esc_attr(get_option('express_is_fragile')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('express_is_fragile')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr> 

        <tr>
            <th align="left">Tip printare AWB:</th>
            <td>
                <select name="express_page_type">
                    <option value="default" <?= esc_attr( get_option('express_page_type') ) == 'default' ? 'selected="selected"' : ''; ?>>A5</option>
                    <option value="A6" <?= esc_attr( get_option('express_page_type') ) == 'A6' ? 'selected="selected"' : ''; ?>>A6</option>
                   
                </select>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Trimite mail la generare:</th>
            <td>
                <select name="express_trimite_mail">
                    <option value="nu" <?= esc_attr(get_option('express_trimite_mail')) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?= esc_attr(get_option('express_trimite_mail')) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th align="left">Subiect mail:</th>
            <td><input type="text"  name="express_subiect_mail" value="<?= esc_attr(get_option('express_subiect_mail')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Continut mail:</th>
            <td>
                <?php
                    $email_template = get_option('express_email_template');
                    wp_editor( $email_template, 'express_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
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
                <select name="express_auto_generate_awb">
                    <option value="nu" <?php echo esc_attr( get_option('express_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('express_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                <select name="express_auto_mark_complete">
                    <option value="nu" <?php echo esc_attr( get_option('express_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('express_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
            <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Express creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
        </tr>
    </table>
    </form>
</div>

<script>
    jQuery( $ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/validateExpressAuth";
        
        $('button[name="validate_express"]').on('click', function(){
            let responseDiv =  $('.responseHereExpress'),
                submitBtn = $('#express_settings_form #submit');
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    api_key: $('input[name="express_key"]').val(),
                },
                dataType: "json",
                success: function(response) { 
                    if(response['success']==1){
                        responseDiv.text('Autentificare reusita.').css('color', '#34a934');
                        submitBtn.click();
                    } else {
                        responseDiv.text('Autentificare esuata.').css('color', '#f44336');
                        submitBtn.click();
                    }
                }
            });
        });

        $('select[name="express_retur"]').on('change', function(){
            if($(this).val() == 'true'){
                $('#tipRetur').show();
            }else{
                $('#tipRetur').hide();
            }
        });

        $('button[name="reset_email_template"]').on('click', function(){
            let confirmation = confirm("Sunteți sigur(ă) că doriți să resetați câmpurile de mail la valorile implicite?");
            if (!confirmation) return;
            
            $.ajax({
                dataType: "json",
                type: "POST",
                url: ajaxurl,
                data: {
                    courier: 'express',
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
