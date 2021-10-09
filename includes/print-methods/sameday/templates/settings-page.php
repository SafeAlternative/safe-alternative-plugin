<?php 
    $sameday = new SafealternativeSamedayClass;
?>

<style>
    .safealternative_page_sameday-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_sameday-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_sameday-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_sameday-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_sameday-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_sameday-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_sameday-plugin-setting input:not(.ed_button), 
    .safealternative_page_sameday-plugin-setting select, 
    .safealternative_page_sameday-plugin-setting textarea, 
    .safealternative_page_sameday-plugin-setting button.button {
        width: 100%;
        max-width: 100%;
    }
    <?= (bool) esc_attr(get_option('sameday_valid_auth')) == false ? '.hideOnFail { display: none; }' : ''; ?>
</style>

<div class="wrap">
    <h1>SafeAlternative Sameday</h1>
    <br/>
    <br/>
    <form action="options.php" method="post" id="sameday_settings_form">
    <?php
        settings_fields('sameday-plugin-settings');
        do_settings_sections('sameday-plugin-settings');
    ?>
    <table>
        <input type="hidden" name="sameday_valid_auth" value="<?= esc_attr(get_option('sameday_valid_auth')); ?>">
        <tr>
            <th align="left">Utilizator Sameday</th>
            <td><input type="text"  name="sameday_username" value="<?= esc_attr(get_option('sameday_username')); ?>" size="50" placeholder="Numele utilizatorului Sameday"/></td>
        </tr>

        <tr>
            <th align="left">Parola Sameday</th>
            <td><input type="password"  name="sameday_password" value="<?= esc_attr(get_option('sameday_password')); ?>" size="50" placeholder="Parola utilizatorului Sameday"/></td>
        </tr>

        <tr>
            <th align="left" class="responseHereSameday"></th>
            <td align="right">
                <button type="button" name="validate_sameday" class="button">Valideaza credentialele Sameday</button>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>            
        
        <tr class="hideOnFail"> 
            <th align="left">Punct de ridicare:</th> 
            <td>
                <select name="sameday_pickup_point">
                    <?php 
                        if ((bool) esc_attr(get_option('sameday_valid_auth'))) {
                            $pickup_points = get_transient('sameday_pickup_points');
                            $current_pickup_point = esc_attr(get_option('sameday_pickup_point'));
                            if (empty($pickup_points)) {
                                $pickup_points = json_decode($sameday->CallMethod('pickup_points', [], 'GET')['message'], true);
                                set_transient('sameday_pickup_points', $pickup_points, DAY_IN_SECONDS);
                            }
                            if (!empty($pickup_points)) {
                                foreach($pickup_points as $pickup_point) {
                                    $selected = ($pickup_point['id'] == $current_pickup_point) ? 'selected="selected"' : '';
                                    echo "<option value='{$pickup_point['id']}' {$selected}>{$pickup_point['name']}</option>";
                                }
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th  align="left">Serviciul implicit:</th>
            <td>
                <select name="sameday_service_id">
                    <?php 
                        if ((bool) esc_attr(get_option('sameday_valid_auth'))) {
                            $services = get_transient('sameday_services');
                            $current_service_id = esc_attr(get_option('sameday_service_id'));
                            if (empty($services)) {
                                $services = json_decode($sameday->CallMethod('services', [], 'GET')['message'], true);
                                set_transient('sameday_services', $services, DAY_IN_SECONDS);
                            }
                            if (!empty($services)) {
                                foreach($services as $service) {
                                    $selected = ($service['id'] == $current_service_id) ? 'selected="selected"' : '';
                                    echo "<option value='{$service['id']}' {$selected}>{$service['name']}</option>";
                                }
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th  align="left">Tip colet:</th>
            <td>
                <select name="sameday_package_type">
                    <option value="0" <?= esc_attr(get_option('sameday_package_type')) == '0' ? 'selected="selected"' : ''; ?>>Colet</option>
                    <option value="1" <?= esc_attr(get_option('sameday_package_type')) == '1' ? 'selected="selected"' : ''; ?>>Plic</option>
                    <option value="2" <?= esc_attr(get_option('sameday_package_type')) == '2' ? 'selected="selected"' : ''; ?>>Palet</option>
                </select>
            </td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Valoare declarata:</th>
            <td><input type="number" name="sameday_declared_value" value="<?= esc_attr( get_option('sameday_declared_value') ); ?>" size="50" /></td>
        </tr>

        <tr class="hideOnFail">
            <th align="left">Tip printare AWB:</th>
            <td>
                <select name="sameday_page_type">
                    <option value="A4" <?= esc_attr( get_option('sameday_page_type') ) == 'A4' ? 'selected="selected"' : ''; ?>>A4</option>
                    <option value="A6" <?= esc_attr( get_option('sameday_page_type') ) == 'A6' ? 'selected="selected"' : ''; ?>>A6</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th align="left">Observatii:<br> (max 200 caractere):</th>
            <td>
                <textarea name="sameday_observation" lines="2" maxlength="200"><?= esc_attr( get_option('sameday_observation') ) ?></textarea>
                <sub style="float:right;"><span class="letterCount">0</span>/200</sub>
            </td>
        </tr>

        <tr>
                <th align="left">Descriere continut:</th>
                <td>
                    <select name="sameday_descriere_continut">
                        <option value="nu" <?= esc_attr( get_option('sameday_descriere_continut') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="name" <?= esc_attr( get_option('sameday_descriere_continut') ) == 'name' ? 'selected="selected"' : ''; ?>>Denumire produs</option>
                        <option value="sku" <?= esc_attr( get_option('sameday_descriere_continut') ) == 'sku' ? 'selected="selected"' : ''; ?>>SKU produs</option>
                        <option value="both" <?= esc_attr( get_option('sameday_descriere_continut') ) == 'both' ? 'selected="selected"' : ''; ?>>Denumire+SKU produs</option>
                    </select>
                </td>
            </tr>
        </tr>
        
        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Greutate implicita:</th>
            <td><input type="number" name="sameday_default_weight" value="<?= esc_attr( get_option('sameday_default_weight') ); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Inaltime implicita:</th>
            <td><input type="number" name="sameday_default_height" value="<?= esc_attr( get_option('sameday_default_height') ); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Lungime implicita:</th>
            <td><input type="number" name="sameday_default_length" value="<?= esc_attr( get_option('sameday_default_length') ); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Latime implicita:</th>
            <td><input type="number" name="sameday_default_width" value="<?= esc_attr( get_option('sameday_default_width') ); ?>" size="50" /></td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Trimite mail la generare:</th>
            <td>
                <select name="sameday_trimite_mail">
                    <option value="nu" <?= esc_attr(get_option('sameday_trimite_mail')) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?= esc_attr(get_option('sameday_trimite_mail')) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th align="left">Subiect mail:</th>
            <td><input type="text"  name="sameday_subiect_mail" value="<?= esc_attr(get_option('sameday_subiect_mail')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Continut mail:</th>
            <td>
                <?php
                    $email_template = get_option('sameday_email_template');
                    wp_editor( $email_template, 'sameday_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
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
                <select name="sameday_auto_generate_awb">
                    <option value="nu" <?php echo esc_attr( get_option('sameday_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('sameday_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                <select name="sameday_auto_mark_complete">
                    <option value="nu" <?php echo esc_attr( get_option('sameday_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('sameday_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
            <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Sameday creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
        </tr>
    </table>
    </form>
</div>

<script>
    jQuery( $ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/";
        
        $('button[name="validate_sameday"]').on('click', function(){
            let responseDiv = $('.responseHereSameday'),
                submitBtn = $('#sameday_settings_form #submit');
            $.ajax({
                type: 'POST',
                url: url+"validateSamedayAuth",
                data: {
                    username: $('input[name="sameday_username"]').val(),
                    password: $('input[name="sameday_password"]').val(),
                },
                dataType: "json",
                success: function(response) { 
                    if(response['success']){
                        responseDiv.text('Autentificare reusita.').css('color', '#34a934');
                        $('input[name="sameday_valid_auth"]').val('1');
                    } else {
                        responseDiv.text('Autentificare esuata.').css('color', '#f44336');
                        $('input[name="sameday_valid_auth"]').val('0');
                    }
                    submitBtn.click();
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
                    courier: 'sameday',
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

        $('.letterCount').text($('textarea[name="sameday_observation"]').val().length);
        $('textarea[name="sameday_observation"]').on('keyup change', function(){$('.letterCount').text($(this).val().length);})
    })
</script>
