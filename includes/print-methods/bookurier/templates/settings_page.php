<?php
?>
<style>
    .safealternative_page_bookurier-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_bookurier-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_bookurier-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_bookurier-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_bookurier-plugin-setting input:not(.ed_button), 
    .safealternative_page_bookurier-plugin-setting select, 
    .safealternative_page_bookurier-plugin-setting textarea, 
    .safealternative_page_bookurier-plugin-setting button.button {
        width: 100%;
        max-width: 100%;
    }
    .safealternative_page_bookurier-plugin-setting .other_sender_row{
        display: none;
    }
    .safealternative_page_bookurier-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_bookurier-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_bookurier-plugin-setting .remove_other_sender{
        width: 100px;
        float: left;
        margin-right: 30px;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative Bookurier</h1>
    <br/>
    <br/>
    <form action="options.php" method="post">

        <?php
        settings_fields('bookurier-plugin-settings');
        do_settings_sections('bookurier-plugin-settings');
        ?>

        <table>

            <tr>
                <th align="left">Utilizator Bookurier:</th>
                <td><input type="text" name="bookurier_user" value="<?= esc_attr(get_option('bookurier_user')); ?>" size="50" placeholder="Numele utilizatorului Bookurier"/></td>
            </tr>

            <tr>
                <th align="left">Parola Bookurier:</th>
                <td><input type="password" name="bookurier_password" value="<?= esc_attr(get_option('bookurier_password')); ?>" size="50" placeholder="Parola utilizatorului Bookurier"/></td>
            </tr>

            <tr>
                <th align="left">Cod Client Bookurier:</th>
                <td><input type="text" name="bookurier_senderid" value="<?= esc_attr(get_option('bookurier_senderid')); ?>" size="50" placeholder="Cod Client Bookurier"/></td>
            </tr>

            <tr>
                <th align="left" class="responseHereBookurier"></th>
                <td align="right">
                    <button type="button" name="validate_bookurier" class="button">Valideaza credentialele Bookurier</button>
                </td>
            </tr>           

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>

            <tr>
                <th align="left">Serviciul de livrare:</th>
                <td>
                    <select name="bookurier_services">
                        <option value="1" <?= esc_attr(get_option('bookurier_services')) == '1' ? 'selected="selected"' : ''; ?>>Bucuresti 24h</option>
                        <option value="2" <?= esc_attr(get_option('bookurier_services')) == '2' ? 'selected="selected"' : ''; ?>>Bucuresti Express</option>
                        <option value="3" <?= esc_attr(get_option('bookurier_services')) == '3' ? 'selected="selected"' : ''; ?>>Metropolitan</option>
                        <option value="9" <?= esc_attr(get_option('bookurier_services')) == '9' ? 'selected="selected"' : ''; ?>>National 24h</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Observatii:</th>
                <td><input type="text" name="bookurier_observatii" value="<?= esc_attr(get_option('bookurier_observatii')); ?>" size="50" placeholder="Ex: A se contacta telefonic"/></td>
            </tr>

            <tr>
                <th align="left">Numar colete:</th>
                <td>
                    <input type="number" min="0" step="1" name="bookurier_pcount" value="<?= esc_attr(get_option('bookurier_pcount')); ?>" placeholder="Numar colete"/>
                </td>
            </tr>

            <tr>
                <th align="left">Valoare asigurata:</th>
                <td>
                    <input type="number" min="0" step="1" name="bookurier_insurance_val" value="<?= esc_attr(get_option('bookurier_insurance_val')); ?>" placeholder="Valoare asigurata"/>
                </td>
            </tr>

            <tr>
                <th><hr style="margin: 15px 0;"></th>                
                <td><hr style="margin: 15px 0;"></td>
            </tr>
            
            <tr>
                <th align="left">Trimite mail la generare:</th>
                <td>
                    <select name="bookurier_trimite_mail">
                        <option value="da" <?= esc_attr(get_option('bookurier_trimite_mail')) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                        <option value="nu" <?= esc_attr(get_option('bookurier_trimite_mail')) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Subiect mail:</th>
                <td><input type="text" name="bookurier_subiect_mail" value="<?= esc_attr(get_option('bookurier_subiect_mail')); ?>" size="50" placeholder="Ex: AWB bookurier a fost generat pentru comanda dumneavoastra"/></td>
            </tr>

            <tr>
                <th align="left">Continut mail:</th>
                <td>
                    <?php
                        $email_template = get_option('bookurier_email_template');
                        wp_editor( $email_template, 'bookurier_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
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
                    <select name="bookurier_auto_generate_awb">
                        <option value="nu" <?php echo esc_attr( get_option('bookurier_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?php echo esc_attr( get_option('bookurier_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                    <select name="bookurier_auto_mark_complete">
                        <option value="nu" <?php echo esc_attr( get_option('bookurier_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="da" <?php echo esc_attr( get_option('bookurier_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri bookurier creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
            </tr>

        </table>
    </form>
</div>

<script>
    jQuery($ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/";

        $('button[name="validate_bookurier"]').on('click', function(){
            $.ajax({
                type: 'POST',
                url: url+"validateBookurierAuth",
                data: {
                    userid: $('input[name="bookurier_user"]').val(),
                    pwd: $('input[name="bookurier_password"]').val(),
                },
                dataType: "json",
                success: function(response) { 
                    if(response['success']){
                        $('.responseHereBookurier').text('Autentificare reusita.').css('color', '#34a934');
                    } else {
                        $('.responseHereBookurier').text('Autentificare esuata.').css('color', '#f44336');
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
                    courier: 'bookurier',
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
