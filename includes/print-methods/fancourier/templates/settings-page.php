<?php ?>

<style>
    .safealternative_page_fan-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_fan-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_fan-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_fan-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_fan-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_fan-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_fan-plugin-setting input:not(.ed_button), 
    .safealternative_page_fan-plugin-setting select, 
    .safealternative_page_fan-plugin-setting textarea, 
    .safealternative_page_fan-plugin-setting button.button,
    .safealternative_page_fan-plugin-setting .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative FanCourier SelfAWB</h1>
    <br/>
    <br/>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'fan-plugin-settings' );
        do_settings_sections( 'fan-plugin-settings' );
        ?>
        <table>

        <input type="hidden" name="token" value="<?= esc_attr( get_option('token') ); ?>" />
            <tr>
                <th align="left">Utilizator SelfAWB:</th>
                <td><input type="text" name="fan_user" value="<?= esc_attr( get_option('fan_user') ); ?>" size="50" placeholder="Numele utilizatorului FanCourier SelfAWB"/></td>
            </tr>

            <tr>
                <th align="left">Parola SelfAWB:</th>
                <td><input type="password" name="fan_password" value="<?= esc_attr( get_option('fan_password') ); ?>" size="50" placeholder="Parola utilizatorului FanCourier SelfAWB"/></td>
            </tr>

            <tr>
                <th align="left">Client ID SelfAWB:</th>
                <td><input type="text" name="fan_clientID" value="<?= esc_attr( get_option('fan_clientID') ); ?>" size="50" placeholder="Client ID-ul utilizatorului FanCourier SelfAWB"/></td>
            </tr>

            <tr>
                <th align="left" class="responseHereFan"></th>
                <td align="right">
                    <button type="button" name="validate_fan" class="button">Valideaza credentialele FanCourier</button>
                </td>
            </tr>

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>

            <tr>
                <th align="left">Numar colete:</th>
                <td><input type="number" name="fan_nr_colete" value="<?= esc_attr( get_option('fan_nr_colete') ); ?>" size="50" /></td>
            </tr>

            <tr>
                <th align="left">Numar plicuri:</th>
                <td><input type="number" name="fan_nr_plicuri" value="<?= esc_attr( get_option('fan_nr_plicuri') ); ?>" size="50" /></td>
            </tr>

            <tr align="left">
                <th>Plata transport: </th>
                <td>
                    <select name="fan_plata_transport">
                        <option value="expeditor" <?= esc_attr( get_option('fan_plata_transport') ) == 'expeditor' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                        <option value="destinatar" <?= esc_attr( get_option('fan_plata_transport') ) == 'destinatar' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th align="left">Plata ramburs:</th>
                <td>
                    <select name="fan_plata_ramburs">
                        <option value="expeditor" <?= esc_attr( get_option('fan_plata_ramburs') ) == 'expeditor' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                        <option value="destinatar" <?= esc_attr( get_option('fan_plata_ramburs') ) == 'destinatar' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Asigurare expeditie:</th>
                <td>
                    <select name="fan_asigurare">
                        <option value="nu" <?= esc_attr( get_option('fan_asigurare') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?= esc_attr( get_option('fan_asigurare') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Deschidere la livrare:</th>
                <td>
                    <select name="fan_deschidere">
                        <option value="" <?= esc_attr( get_option('fan_deschidere') ) == '' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?= esc_attr( get_option('fan_deschidere') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Livrare sambata:</th>
                <td>
                    <select name="fan_sambata">
                        <option value="" <?= esc_attr( get_option('fan_sambata') ) == '' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?= esc_attr( get_option('fan_sambata') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Optiune ePOD / oPOD:</th>
                <td>
                    <select name="fan_epod_opod">
                        <option value="nu" <?= esc_attr( get_option('fan_epod_opod') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="epod" <?= esc_attr( get_option('fan_epod_opod') ) == 'epod' ? 'selected="selected"' : ''; ?>>ePOD</option>
                        <option value="opod" <?= esc_attr( get_option('fan_epod_opod') ) == 'opod' ? 'selected="selected"' : ''; ?>>oPOD</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Tip printare AWB:</th>
                <td>
                    <select name="fan_page_type">
                        <option value="A4" <?= esc_attr( get_option('fan_page_type') ) == 'A4' ? 'selected="selected"' : ''; ?>>A4</option>
                        <option value="A5" <?= esc_attr( get_option('fan_page_type') ) == 'A5' ? 'selected="selected"' : ''; ?>>A5</option>
                        <option value="A6" <?= esc_attr( get_option('fan_page_type') ) == 'A6' ? 'selected="selected"' : ''; ?>>A6 (valabil doar pentru ePOD)</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th align="left">Date personale:</th>
                <td><input type="text" name="fan_personal_data" value="<?= esc_attr( get_option('fan_personal_data') ); ?>" size="50" /></td>
            </tr>           
            
            <tr>
                <th align="left">Contact expeditor:</th>
                <td><input type="text" name="fan_contact_exp" value="<?= esc_attr( get_option('fan_contact_exp') ); ?>" size="50" /></td>
            </tr>
            
            <tr>
                <th align="left">Observatii:</th>
                <td><input type="text" name="fan_observatii" value="<?= esc_attr( get_option('fan_observatii') ); ?>" size="50" /></td>
            </tr>      

            <tr>
                <th align="left">Descriere continut:</th>
                <td>
                    <select name="fan_descriere_continut">
                        <option value="nu" <?= esc_attr( get_option('fan_descriere_continut') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="name" <?= esc_attr( get_option('fan_descriere_continut') ) == 'name' ? 'selected="selected"' : ''; ?>>Denumire produs</option>
                        <option value="sku" <?= esc_attr( get_option('fan_descriere_continut') ) == 'sku' ? 'selected="selected"' : ''; ?>>SKU produs</option>
                        <option value="both" <?= esc_attr( get_option('fan_descriere_continut') ) == 'both' ? 'selected="selected"' : ''; ?>>Denumire+SKU produs</option>
                    </select>
                </td>
            </tr>              

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>

            <tr>
                <th align="left">Lungime colet standard:</th>
                <td><input type="text" name="fan_force_length" value="<?= esc_attr( get_option('fan_force_length') ); ?>" size="50" /></td>
            </tr>   

            <tr>
                <th align="left">Latime colet standard:</th>
                <td><input type="text" name="fan_force_width" value="<?= esc_attr( get_option('fan_force_width') ); ?>" size="50" /></td>
            </tr>   

            <tr>
                <th align="left">Inaltime colet standard:</th>
                <td><input type="text" name="fan_force_height" value="<?= esc_attr( get_option('fan_force_height') ); ?>" size="50" /></td>
            </tr>        

            <tr>
                <th align="left">Greutate colet standard:</th>
                <td><input type="text" name="fan_force_weight" value="<?= esc_attr( get_option('fan_force_weight') ); ?>" size="50" /></td>
            </tr>   

            <tr>
                <th align="left"></th>
                <td>In cazul in care dimensiunile standard nu sunt completate, ele vor fi calculate automat in functie de parametrii configurati la nivel de produs</td>
            </tr>                                 

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>            

            <tr>
                <th align="left">Trimite mail la generare:</th>
                <td>
                    <select name="fan_trimite_mail">
                        <option value="da" <?= esc_attr( get_option('fan_trimite_mail') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                        <option value="nu" <?= esc_attr( get_option('fan_trimite_mail') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th align="left">Subiect mail:</th>
                <td><input type="text" name="fan_subiect_mail" value="<?= esc_attr( get_option('fan_subiect_mail') ); ?>" size="50" /></td>
            </tr>

            <tr>
                <th align="left">Continut mail:</th>
                <td>
                    <?php
                        $email_template = get_option('fan_email_template');
                        wp_editor( $email_template, 'fan_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
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
                    <select name="fan_auto_generate_awb">
                        <option value="nu" <?= esc_attr( get_option('fan_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?= esc_attr( get_option('fan_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                    <select name="fan_auto_mark_complete">
                        <option value="nu" <?= esc_attr( get_option('fan_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?= esc_attr( get_option('fan_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri FanCourier creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
            </tr>
        </table>
    </form>
</div>

<script>
    jQuery(($) => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>fan/";
        
        $('button[name="validate_fan"]').on('click', function(e){
            $.ajax({
                type: 'POST',
                url: url+"validateAuth",
                headers: {
                    Authorization: 'Bearer '+$('input[name="token"]').val(),
                },
                data: {
                    username: $('input[name="fan_user"]').val(),
                    user_pass: $('input[name="fan_password"]').val(),
                    client_id: $('input[name="fan_clientID"]').val(),
                    
                },
                dataType: "json",

                statusCode: {
                    401: function (response) {
                        $('.responseHereFan').text('Autentificare esuata.').css('color', '#f44336');
                    },
                    400: function (response) {
                        $('.responseHereFan').text('Autentificare esuata.').css('color', '#f44336');   
                    },

                    404: function (response) {
                        $('.responseHereFan').text('Autentificare esuata.').css('color', '#f44336');                  }
                },

                success: function(response) { 
                    if(response['success']){
                        $('.responseHereFan').text('Autentificare reusita.').css('color', '#34a934');
                    } else {
                        $('.responseHereFan').text('Autentificare esuata.').css('color', '#f44336');
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
                    courier: 'fan',
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

<?php 