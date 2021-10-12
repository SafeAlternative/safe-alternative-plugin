<?php 
    $fan_print = esc_attr( get_option('enable_fan_print') );
    $fan_shipping = esc_attr( get_option('enable_fan_shipping') );
    $memex_print = esc_attr( get_option('enable_memex_print') );
    $memex_shipping = esc_attr( get_option('enable_memex_shipping') );
    $nemo_print = esc_attr( get_option('enable_nemo_print') );
    $nemo_shipping = esc_attr( get_option('enable_nemo_shipping') );
    $cargus_print = esc_attr( get_option('enable_cargus_print') );
    $cargus_shipping = esc_attr( get_option('enable_cargus_shipping') );
    $gls_print = esc_attr( get_option('enable_gls_print') );
    $gls_shipping = esc_attr( get_option('enable_gls_shipping') );
    $dpd_print = esc_attr( get_option('enable_dpd_print') );
    $dpd_shipping = esc_attr( get_option('enable_dpd_shipping') );
    $sameday_print = esc_attr( get_option('enable_sameday_print') );
    $sameday_shipping = esc_attr( get_option('enable_sameday_shipping') );
    $bookurier_print = esc_attr( get_option('enable_bookurier_print') );
    $bookurier_shipping = esc_attr( get_option('enable_bookurier_shipping') );
    $optimus_print = esc_attr( get_option('enable_optimus_print') );
    $optimus_shipping = esc_attr( get_option('enable_optimus_shipping') );
    $express_print = esc_attr( get_option('enable_express_print') );
    $express_shipping = esc_attr( get_option('enable_express_shipping') );
    $team_print = esc_attr( get_option('enable_team_print') );
    $team_shipping = esc_attr( get_option('enable_team_shipping') );
    $enable_checkout_city_select = esc_attr( get_option('enable_checkout_city_select') );
    $courier_email_from = esc_attr( get_option('courier_email_from') );
    $safealternative_is_multisite = esc_attr( get_option('safealternative_is_multisite') );
    $enable_pers_fiz_jurid = esc_attr(get_option('enable_pers_fiz_jurid'));
?>

<style>
    .toplevel_page_safealternative-menu-content p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .toplevel_page_safealternative-menu-content table {
        width: 70%;
        max-width: 775px;
    }
    .toplevel_page_safealternative-menu-content input, 
    .toplevel_page_safealternative-menu-content select, 
    .toplevel_page_safealternative-menu-content textarea, 
    .toplevel_page_safealternative-menu-content button.button {
        width: 100% !important;
        max-width: 100% !important;
    }
    .toplevel_page_safealternative-menu-content th {
        width: 40%;
        max-width: 215px;
    }
    h2 {
        margin: 0.2em 0;
    }
    hr {
        margin: 10px 0;
    }
    <?= get_option('auth_validity') == false ? '.hideOnFail { display: none; }' : ''; ?>
</style>

<div class="wrap">
    <h1>SafeAlternative - Setari generale</h1>
    <br>
    <form action="options.php" method="post">
        <?php
            settings_fields( 'safealternative_settings' );
            do_settings_sections( 'safealternative_settings' );
        ?>
        <table>

            <tr>
                <th align="left">Utilizator SafeAlternative* :</th>
                <td><input type="text" name="user_safealternative" value="<?= esc_attr(get_option('user_safealternative')); ?>" size="50" placeholder="Numele utilizatorului SafeAlternative"/></td>
            </tr>
            
            <tr>
                <th align="left">Parola SafeAlternative* :</th>
                <td><input type="password" name="password_safealternative" value="<?= esc_attr(get_option('password_safealternative')); ?>" size="50" placeholder="Parola utilizatorului SafeAlternative"/></td>
            </tr>

            <tr>
                <th align="left" class="responseHereApi"></th>
                <td align="right">
                    <button type="button" name="validate_api" class="button">Valideaza credentialele Safe Alternative</button>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>FanCourier</h2></th>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_fan_print">
                        <option value="0" <?= $fan_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $fan_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_fan_shipping">
                        <option value="0" <?= $fan_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $fan_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>UrgentCargus</h2></th>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_cargus_print">
                        <option value="0" <?= $cargus_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $cargus_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_cargus_shipping">
                        <option value="0" <?= $cargus_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $cargus_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>GLS</h2></th>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_gls_print">
                        <option value="0" <?= $gls_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $gls_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_gls_shipping">
                        <option value="0" <?= $gls_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $gls_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>DPD</h2></th>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_dpd_print">
                        <option value="0" <?= $dpd_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $dpd_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_dpd_shipping">
                        <option value="0" <?= $dpd_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $dpd_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>Sameday</h2></th>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_sameday_print">
                        <option value="0" <?= $sameday_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $sameday_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_sameday_shipping">
                        <option value="0" <?= $sameday_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $sameday_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>Bookurier</h2></th>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_bookurier_print">
                        <option value="0" <?= $bookurier_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $bookurier_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_bookurier_shipping">
                        <option value="0" <?= $bookurier_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $bookurier_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

	        <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

	        <tr class="hideOnFail">
                <th align="left"><h2>NemoExpress</h2></th>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_nemo_print">
                        <option value="0" <?= $nemo_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $nemo_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>
            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_nemo_shipping">
                        <option value="0" <?= $nemo_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $nemo_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>Memex</h2></th>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_memex_print">
                        <option value="0" <?= $memex_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $memex_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_memex_shipping">
                        <option value="0" <?= $memex_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $memex_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>OptimusCourier</h2></th>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_optimus_print">
                        <option value="0" <?= $optimus_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $optimus_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_optimus_shipping">
                        <option value="0" <?= $optimus_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $optimus_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>ExpressCourier</h2></th>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_express_print">
                        <option value="0" <?= $express_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $express_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>
            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_express_shipping">
                        <option value="0" <?= $express_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $express_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>TeamCourier</h2></th>
            </tr>
            
            <tr class="hideOnFail">
                <th align="left">Generare AWB:</th>
                <td>
                    <select name="enable_team_print">
                        <option value="0" <?= $team_print == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $team_print == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>
            <tr class="hideOnFail">
                <th align="left">Metoda de livrare:</th>
                <td>
                    <select name="enable_team_shipping">
                        <option value="0" <?= $team_shipping == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $team_shipping == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <tr class="hideOnFail">
                <th align="left"><h2>Extra</h2></th>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Persoana fizica/juridica in checkout (BETA):</th>
                <td>
                    <select name="enable_pers_fiz_jurid">
                        <option value="0" <?= $enable_pers_fiz_jurid == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $enable_pers_fiz_jurid == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Adresa de email expeditor AWB [1]:</th>
                <td>
                    <input type="hidden" name="courier_email_from" value="<?= $courier_email_from ?>">
                    <input type="text" name="courier_email_from" value="<?= $courier_email_from ?>" size="50" placeholder="Adresa de email"/>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Listă de orașe în checkout [2]:</th>
                <td>
                    <input type="hidden" name="enable_checkout_city_select" value="<?= $enable_checkout_city_select ?>">
                    <select name="enable_checkout_city_select">
                        <option value="0" <?= $enable_checkout_city_select == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $enable_checkout_city_select == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Suport pentru WP Multisite [3]:</th>
                <td>
                    <input type="hidden" name="safealternative_is_multisite" value="<?= $safealternative_is_multisite ?>">
                    <select name="safealternative_is_multisite">
                        <option value="0" <?= $safealternative_is_multisite == '0' ? 'selected="selected"' : ''; ?>>Inactiva</option>
                        <option value="1" <?= $safealternative_is_multisite == '1' ? 'selected="selected"' : ''; ?>>Activa</option>
                    </select>
                </td>
            </tr>

            <tr class="hideOnFail">
                <td colspan=2 style="font-weight: 500; color: red; font-size: 12px; padding-top: 10px; padding-bottom: 10px;">
                [1] Adresa de email de la care vor fi trimise email-urile cu AWB-ul generat aferent unei comenzi atunci când se optează pentru opțiunea "Trimite mail la generare". În cazul în care acest câmp este gol, se va folosi adresa de email a administratorului site-ului.<br/>
                [2] Această funcționalitate este activată automat, iar setarea este IGNORATĂ, atunci când orice metodă de livrare este activă.<br/>
                [3] Activati aceasta optiune daca folositi WP Multisite si intampinati erori 404 la generari sau cand accesati pagini folosite de plugin-ul nostru.</td>
            </tr>

            <tr class="hideOnFail">
                <td colspan="2">
                    <textarea id="textReport" cols="30" rows="8" readonly="true" style="resize: none;"><?=CR_generate_report()?></textarea>
                </td>
            </tr>

            <tr class="hideOnFail">
                <th align="left">Trimite raport pentru asistenta:</th>
                <td>
                    <input id="sendReport" type="button" class="button button-secondary" value="Trimite raport">
                </td>
            </tr>

            <tr>
                <th><hr></th>                
                <td><hr></td>
            </tr>

            <input type="hidden" name="auth_validity" value="<?= esc_attr( get_option('auth_validity') ); ?>">
            <input type="hidden" name="token" value="<?= esc_attr( get_option('token') ); ?>">

            <tr>
                <td colspan="2"><?php submit_button('Salveaza modificarile'); ?></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a>.</td>
            </tr>
        </table>
    </form>
</div>

<script>
    jQuery(($) => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>";

        $('form').on('submit', (e) => {
            $('button[name="validate_api"]').click();
        });

        $('#sendReport').on('click', function(){
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action': 'CR_send_report',
                },
                success: function(response) { 
                    response = JSON.parse(response);
                    if(response['success']) 
                        $('#sendReport').prop('disabled', true).val('✓ Raportul a fost trimis cu succes.').css('cssText', 'color: green !important; opacity: 0.75;');
                }
            });
        });

        $('button[name="validate_api"]').on('click', function(){
            let user_field = $('input[name="user_safealternative"]'),
                pass_field = $('input[name="password_safealternative"]'),
                validity_field = $('input[name="auth_validity"]'),
                token_field = $('input[name="token"]'),
                responseHereApi = $('.responseHereApi');

            $.ajax({
                type: 'POST',
                url: url+"login",
                async: false,
                data: {
                    username: user_field.val(),
                    password: pass_field.val(),
                },
                dataType: "json",
                success: function(data) { 
                    let response = data;
                    responseHereApi.text(response['message']);

                    if(response['success']){
                        responseHereApi.css('color', '#34a934');
                        user_field.attr('style', '');
                        pass_field.attr('style', '');
                        validity_field.val(1);
                        token_field.val(response['token']);

                        $('#submit').click();
                    } else {
                        responseHereApi.css('color', '#f44336');                      
                        pass_field.attr('style', '');
                        user_field.css('box-shadow', '0 0 2px 2px rgba(228, 7, 7, 0.45)');
                        validity_field.val(0);
                        token_field.val('NOTOKEN');
                        $('#submit').click();
                    }
                }
            });
        });
    });
</script>

<?php 