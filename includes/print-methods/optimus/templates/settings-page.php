<?php ?>
<style>
    .safealternative_page_optimus-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_optimus-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_optimus-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_optimus-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_optimus-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_optimus-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_optimus-plugin-setting input:not(.ed_button), 
    .safealternative_page_optimus-plugin-setting select, 
    .safealternative_page_optimus-plugin-setting textarea, 
    .safealternative_page_optimus-plugin-setting button.button {
        width: 100%;
        max-width: 100%;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative OptimusCourier</h1>
    <br/>
    <br/>
    <form action="options.php" method="post" id="optimus_settings_form">
    <?php
        settings_fields('optimus-plugin-settings');
        do_settings_sections('optimus-plugin-settings');
    ?>
    <table>
        <tr>
            <th align="left">Utilizator Optimus</th>
            <td><input type="text"  name="optimus_username" value="<?= esc_attr(get_option('optimus_username')); ?>" size="50" placeholder="Numele utilizatorului Optimus" /></td>
        </tr>

        <tr>
            <th align="left">Cheie Optimus</th>
            <td><input type="text"  name="optimus_key" value="<?= esc_attr(get_option('optimus_key')); ?>" size="50" placeholder="Cheia utilizatorului Optimus" /></td>
        </tr>

        <tr>
            <th align="left" class="responseHereOptimus"></th>
            <td align="right">
                <button type="button" name="validate_optimus" class="button">Valideaza credentialele Optimus</button>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>       

        <tr>
            <th align="left">Numar colete:</th>
            <td><input type="number" name="optimus_count" value="<?= esc_attr(get_option('optimus_count')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Continut:</th>
            <td><input type="text" name="optimus_parcel_content" value="<?= esc_attr(get_option('optimus_parcel_content')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Greutate pachet:</th>
            <td><input type="number" step="0.01" name="optimus_parcel_weight" value="<?= esc_attr(get_option('optimus_parcel_weight')); ?>" size="50" required ></td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Trimite mail la generare:</th>
            <td>
                <select name="optimus_trimite_mail">
                    <option value="nu" <?= esc_attr(get_option('optimus_trimite_mail')) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?= esc_attr(get_option('optimus_trimite_mail')) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th align="left">Subiect mail:</th>
            <td><input type="text"  name="optimus_subiect_mail" value="<?= esc_attr(get_option('optimus_subiect_mail')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Continut mail:</th>
            <td>
                <?php
                    $email_template = get_option('optimus_email_template');
                    wp_editor( $email_template, 'optimus_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
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
                <select name="optimus_auto_generate_awb">
                    <option value="nu" <?php echo esc_attr( get_option('optimus_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('optimus_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                <select name="optimus_auto_mark_complete">
                    <option value="nu" <?php echo esc_attr( get_option('optimus_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('optimus_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
            <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Optimus creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
        </tr>
    </table>
    </form>
</div>

<script>
    jQuery( $ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/";
        
        $('button[name="validate_optimus"]').on('click', function(){
            let responseDiv =  $('.responseHereOptimus'),
                submitBtn = $('#optimus_settings_form #submit'),
                username = $('input[name="optimus_username"]').val(),
                api_key = $('input[name="optimus_key"]').val();
           
            if ( username == '' || api_key == '') {
                alert('Completati toate campurile pentru validare!');
            } else {
                $.ajax({
                type: 'POST',
                url: url+"validateOptimusAuth",
                data: {
                        username: username,
                        api_key: api_key
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
                    courier: 'optimus',
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
