<?php ?>
<style>
    .safealternative_page_memex-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_memex-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_memex-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_memex-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_memex-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_memex-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_memex-plugin-setting input:not(.ed_button), 
    .safealternative_page_memex-plugin-setting select, 
    .safealternative_page_memex-plugin-setting textarea, 
    .safealternative_page_memex-plugin-setting button.button {
        width: 100%;
        max-width: 100%;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative Memex</h1>
    <br/>
    <br/>
    <form action="options.php" method="post" id="memex_settings_form">
    <?php
        settings_fields('memex-plugin-settings');
        do_settings_sections('memex-plugin-settings');
    ?>
    <table>
        <tr>
            <th align="left">Utilizator Memex</th>
            <td><input type="text"  name="memex_username" value="<?= esc_attr(get_option('memex_username')); ?>" size="50" placeholder="Numele utilizatorului Memex"/></td>
        </tr>

        <tr>
            <th align="left">Parola Memex</th>
            <td><input type="password"  name="memex_password" value="<?= esc_attr(get_option('memex_password')); ?>" size="50" placeholder="Parola utilizatorului Memex"/></td>
        </tr>

        <tr>
            <th align="left" class="responseHereMemex"></th>
            <td align="right">
                <button type="button" name="validate_memex" class="button">Valideaza credentialele Memex</button>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>            

        <tr>
            <th  align="left">Serviciul implicit:</th>
            <td>
                <select name="memex_service_id">
                    <option value="38" <?= esc_attr(get_option('memex_service_id')) == '38' ? 'selected="selected"' : ''; ?>>38 - National Standard</option>
                    <option value="112" <?= esc_attr(get_option('memex_service_id')) == '112' ? 'selected="selected"' : ''; ?>>112 - Express 6 ore Bucuresti</option>
                    <option value="113" <?= esc_attr(get_option('memex_service_id')) == '113' ? 'selected="selected"' : ''; ?>>113 - Express 2 ore Bucuresti</option>
                    <option value="121" <?= esc_attr(get_option('memex_service_id')) == '121' ? 'selected="selected"' : ''; ?>>121 - Loco Standard</option>
                </select>
            </td>
        </tr>

        <tr>
            <th align="left">Nume expeditor:</th>
            <td><input type="text" name="memex_name" value="<?= esc_attr(get_option('memex_name')); ?>" size="50" required/></td>
        </tr>

        <tr>
            <th align="left">Adresa expeditor:</th>
            <td><input type="text" name="memex_address" value="<?= esc_attr(get_option('memex_address')); ?>" size="50" required/></td>
        </tr>

        <tr>
            <th align="left">Oras expeditor:</th>
            <td><input type="text" name="memex_city" value="<?= esc_attr(get_option('memex_city')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Cod postal expeditor:</th>
            <td><input type="text" name="memex_postcode" value="<?= esc_attr(get_option('memex_postcode')); ?>" size="50" required/></td>
        </tr>

        <tr style ="display: none;" >
            <th align="left">Codul tarii:</th>
            <td><input type="hidden" name="memex_countrycode" value="<?= esc_attr(get_option('memex_countrycode')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Persoana de contact:</th>
            <td><input type="text" name="memex_person" value="<?= esc_attr(get_option('memex_person')); ?>" size="50" required/></td>
        </tr>

        <tr>
            <th align="left">Telefon:</th>
            <td><input type="text" name="memex_contact" value="<?= esc_attr(get_option('memex_contact')); ?>" size="50" required/></td>
        </tr>

        <tr>
            <th align="left">Email:</th>
            <td><input type="text" name="memex_email" value="<?= esc_attr(get_option('memex_email')); ?>" size="50" required/></td>
        </tr>

        <tr>
            <th  align="left">Asigurare:</th>
            <td>
                <select name="memex_insurance">
                    <option value="Da" <?= esc_attr(get_option('memex_insurance')) == 'Da' ? 'selected="selected"' : ''; ?>>Da</option>
                    <option value="Nu" <?= esc_attr(get_option('memex_insurance')) == 'Nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Serviciu aditional SMS:</th>
            <td>
                <select name="memex_additional_sms">
                    <option value="Da" <?= esc_attr(get_option('memex_additional_sms')) == 'Da' ? 'selected="selected"' : ''; ?>>Da</option>
                    <option value="Nu" <?= esc_attr(get_option('memex_additional_sms')) == 'Nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                </select>
            </td>
        </tr>

        <tr>
                <th align="left">Descriere continut:</th>
                <td>
                    <select name="memex_parcel_content">
                        <option value="nu" <?= esc_attr( get_option('memex_parcel_content') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="name" <?= esc_attr( get_option('memex_parcel_content') ) == 'name' ? 'selected="selected"' : ''; ?>>Denumire produs</option>
                        <option value="sku" <?= esc_attr( get_option('memex_parcel_content') ) == 'sku' ? 'selected="selected"' : ''; ?>>SKU produs</option>
                        <option value="both" <?= esc_attr( get_option('memex_parcel_content') ) == 'both' ? 'selected="selected"' : ''; ?>>Denumire+SKU produs</option>
                    </select>
                </td>
            </tr>
        </tr>

        <tr>
            <th  align="left">Persoana Privata:</th>
            <td>
                <select name="memex_is_private_person">
                    <option value="false" <?= esc_attr(get_option('memex_is_private_person')) == false ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('memex_is_private_person')) == true ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Numar colete:</th>
            <td><input type="number" name="memex_package_count" value="<?= esc_attr(get_option('memex_package_count')); ?>" size="50" /></td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Numar plicuri:</th>
            <td><input type="number" name="memex_envelope_count" value="<?= esc_attr(get_option('memex_envelope_count')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Lungime pachet:</th>
            <td><input type="number" name="memex_parcel_length" value="<?= esc_attr(get_option('memex_parcel_length')); ?>" size="50" required ></td>
        </tr>

        <tr>
            <th align="left">Inaltime pachet:</th>
            <td><input type="number" name="memex_parcel_height" value="<?= esc_attr(get_option('memex_parcel_height')); ?>" size="50" required ></td>
        </tr>

        <tr>
            <th align="left">Latime pachet:</th>
            <td><input type="number" name="memex_parcel_width" value="<?= esc_attr(get_option('memex_parcel_width')); ?>" size="50" required ></td>
        </tr>

        <tr>
            <th align="left">Greutate pachet:</th>
            <td><input type="number" step="0.01" name="memex_parcel_weight" value="<?= esc_attr(get_option('memex_parcel_weight')); ?>" max="34" size="50" required ></td>
        </tr>

        <tr>
            <th align="left">Lungime plic:</th>
            <td><input type="number" name="memex_envelope_length" value="<?= esc_attr(get_option('memex_envelope_length')); ?>" size="50"  required ></td>
        </tr>

        <tr>
            <th align="left">Inaltime plic:</th>
            <td><input type="number" step="0.1" name="memex_envelope_height" value="<?= esc_attr(get_option('memex_envelope_height')); ?>" size="50" required ></td>
        </tr>

        <tr>
            <th align="left">Latime plic:</th>
            <td><input type="number" name="memex_envelope_width" value="<?= esc_attr(get_option('memex_envelope_width')); ?>" size="50" required ></td>
        </tr>

        <tr>
            <th align="left">Greutate plic:</th>
            <td><input type="number" step="0.01" name="memex_envelope_weight" value="<?= esc_attr(get_option('memex_envelope_weight')); ?>" max="0.5" size="50" required ></td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Label format:</th>
            <td>
                <select name="memex_label_format">
                    <option value="PDF" <?= esc_attr( get_option('memex_label_format') ) == 'PFD' ? 'selected="selected"' : ''; ?>>PDF</option>
                    <option value="PDFA4" <?= esc_attr( get_option('memex_label_format') ) == 'PDFA4' ? 'selected="selected"' : ''; ?>>PDFA4 (A4 size)</option>
                </select>
            </td>
        </tr>

        <tr>
            <th align="left">Nota colet implicita<br> (max 200 caractere):</th>
            <td>
                <textarea name="memex_parcel_note" lines="2" maxlength="200"><?= esc_attr( get_option('memex_parcel_note') ) ?></textarea>
                <sub style="float:right;"><span class="letterCount">0</span>/200</sub>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Solicita curier:</th>
            <td>
                <select name="memex_call_pickup">
                    <option value="1" <?= esc_attr(get_option('memex_call_pickup')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                    <option value="0" <?= esc_attr(get_option('memex_call_pickup')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                </select>
            </td>
        </tr>

        <tr class='memexCurier' <?= esc_attr(get_option('memex_call_pickup')) == '0' ? 'style="display: none;"' : '' ?>>
            <th  align="left">Ora minima de ridicare:</th>
            <td>
                <input class="memex_pickup" type="hidden" name="memex_pickup_date" value="<?= esc_attr(get_option('memex_pickup_date')); ?>"/>
                <input type="time" step="1" name="memex_pickup_time" value="<?= esc_attr(get_option('memex_pickup_time')); ?>" style="width: 35%" />
            </td>
        </tr>

        <tr class='memexCurier' <?= esc_attr(get_option('memex_call_pickup')) == '0' ? 'style="display: none;"' : '' ?>>
            <th  align="left">Ora maxima de ridicare:</th>
            <td>
                <input class="memex_pickup" type="hidden" name="memex_max_pickup_date" value="<?= esc_attr(get_option('memex_max_pickup_date')); ?>"/>
                <input type="time" step="1" name="memex_max_pickup_time" value="<?= esc_attr(get_option('memex_max_pickup_time')); ?>" style="width: 35%" />
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Trimite mail la generare:</th>
            <td>
                <select name="memex_trimite_mail">
                    <option value="nu" <?= esc_attr(get_option('memex_trimite_mail')) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?= esc_attr(get_option('memex_trimite_mail')) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th align="left">Subiect mail:</th>
            <td><input type="text"  name="memex_subiect_mail" value="<?= esc_attr(get_option('memex_subiect_mail')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Continut mail:</th>
            <td>
                <?php
                    $email_template = get_option('memex_email_template');
                    wp_editor( $email_template, 'memex_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
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
                <select name="memex_auto_generate_awb">
                    <option value="nu" <?php echo esc_attr( get_option('memex_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('memex_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                <select name="memex_auto_mark_complete">
                    <option value="nu" <?php echo esc_attr( get_option('memex_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('memex_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
            <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Memex creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
        </tr>
    </table>
    </form>
</div>

<script>
    jQuery( $ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/";
        
        $('button[name="validate_memex"]').on('click', function(){
            let responseDiv =  $('.responseHereMemex'),
                submitBtn = $('#memex_settings_form #submit');
            $.ajax({
                type: 'POST',
                url: url+"validateMemexAuth",
                data: {
                    token: {
                        UserName: $('input[name="memex_username"]').val(),
                        Password: $('input[name="memex_password"]').val()
                    },
                },
                dataType: "json",
                success: function(response) { 
                    if(response['success']){
                        responseDiv.text('Autentificare reusita.').css('color', '#34a934');
                    } else {
                        responseDiv.text('Autentificare esuata.').css('color', '#f44336');
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
                    courier: 'memex',
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
        
        $('.letterCount').text($('textarea[name="memex_parcel_note"]').val().length);
        $('textarea[name="memex_parcel_note"]').on('keyup change', function(){$('.letterCount').text($(this).val().length);})

        $('select[name="memex_call_pickup"]').on('change', function(){
            if($(this).val() == '1'){
                $('.memexCurier').show();
                $('input[name$=pickup_time]').prop('required', true);
            }else{
                $('.memexCurier').hide();
                $('input[name$=pickup_time]').prop('required', false);
            }
        });

        var date = new Date();
        $('.memex_pickup').val(date.toJSON().slice(0,10));
        $('select[name="memex_call_pickup"]').trigger('change');
    })
</script>
