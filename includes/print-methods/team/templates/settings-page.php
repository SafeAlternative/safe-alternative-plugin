<?php ?>
<style>
    .safealternative_page_team-plugin-setting .helper {
        padding: 2px 5px;
        background: rgba(0, 188, 212, 0.32);
        cursor: default;
        float: right;
        color: grey;
        font-weight: 300;
    }
    .safealternative_page_team-plugin-setting p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .safealternative_page_team-plugin-setting th {
        width: 20%;
        min-width: 155px;
    }
    .safealternative_page_team-plugin-setting table {
        width: 70vw;
        max-width: 775px;
    }
    .safealternative_page_team-plugin-setting button[name="reset_email_template"] {
        border-color: #F44336 !important; 
        color: #F44336 !important;
    }
    .safealternative_page_team-plugin-setting button[name="reset_email_template"]:focus {
        box-shadow: 0 0 0 1px #F44336 !important;
    }
    .safealternative_page_team-plugin-setting input:not(.ed_button), 
    .safealternative_page_team-plugin-setting select, 
    .safealternative_page_team-plugin-setting textarea, 
    .safealternative_page_team-plugin-setting button.button {
        width: 100%;
        max-width: 100%;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative Team</h1>
    <br/>
    <br/>
    <form action="options.php" method="post" id="team_settings_form">
    <?php
        settings_fields('team-plugin-settings');
        do_settings_sections('team-plugin-settings');
    ?>
    <table>
        <tr>
            <th align="left">TeamCourier API Key:</th>
            <td><input type="text"  name="team_key" value="<?= esc_attr(get_option('team_key')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left" class="responseHereTeam"></th>
            <td align="right">
                <button type="button" name="validate_team" class="button">Valideaza credentialele Team</button>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>            

        <tr>
            <th  align="left">Tip colet:</th>
            <td>
                <select name="team_package_type">
                    <option value="envelope" <?= esc_attr(get_option('team_package_type')) == 'envelope' ? 'selected="selected"' : ''; ?>>Plic</option>
                    <option value="package" <?= esc_attr(get_option('team_package_type')) == 'package' ? 'selected="selected"' : ''; ?>>Colet</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th  align="left">Serviciul implicit:</th>
            <td>
                <select name="team_service">
                <?php 
                    $services = (new SafealternativeTeamClass)->get_services();
                    $current_service = esc_attr(get_option('team_service'));
                    if (!empty($services) && empty($services['error'])) {
                        foreach($services as $service) {
                            $selected = ($service['name'] == $current_service) ? 'selected="selected"' : '';
                            echo "<option value='{$service['name']}' {$selected}>{$service['name']}</option>";
                        }
                    } else {
                        ?> 
                            <option value="Eco" <?= esc_attr(get_option('team_service')) == 'Eco' ? 'selected="selected"' : ''; ?>>Eco</option>
                        <?php
                    }
                ?>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Retur:</th>
            <td>
                <select name="team_retur">
                    <option value="true" <?= esc_attr(get_option('team_retur')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                    <option value="false" <?= esc_attr(get_option('team_retur')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                </select>
            </td>
        </tr>

        <tr id='tipRetur' <?= esc_attr(get_option('team_retur')) == 'false' ? 'style="display: none;"' : '' ?>>
            <th  align="left">Tip retur:</th>
            <td>
                <select name="team_retur_type">
                    <option value="document" <?= esc_attr(get_option('team_retur_type')) == 'document' ? 'selected="selected"' : ''; ?>>Document</option>
                    <option value="colet" <?= esc_attr(get_option('team_retur_type')) == 'colet' ? 'selected="selected"' : ''; ?>>Colet</option>
                </select>
            </td>
        </tr>

        <tr>
            <th align="left">Numar colete:</th>
            <td><input type="number" name="team_parcel_count" value="<?= esc_attr(get_option('team_parcel_count')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Continut:</th>
            <td><input type="text" name="team_content" value="<?= esc_attr(get_option('team_content')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Valoare asigurare:</th>
            <td><input type="text" name="team_insurance" value="<?= esc_attr(get_option('team_insurance')); ?>" size="50"/></td>
        </tr>
        
        <tr>
            <th  align="left">Platitor expeditie:</th>
            <td>
                <select name="team_payer">
                    <option value="expeditor" <?= esc_attr(get_option('team_payer')) == 'expeditor' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                    <option value="destinatar" <?= esc_attr(get_option('team_payer')) == 'destinatar' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                </select>
            </td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>

        <tr>
            <th align="left">Nume expeditor:</th>
            <td><input type="text" name="team_name" value="<?= esc_attr(get_option('team_name')); ?>" size="50" required /></td>
        </tr>
        
        <tr>
            <th align="left">Persoana de contact:</th>
            <td><input type="text" name="team_contact_person" value="<?= esc_attr(get_option('team_contact_person')); ?>" size="50" required /></td>
        </tr>
        
        <tr>
            <th align="left">Telefon expeditor:</th>
            <td><input type="tel" name="team_phone" value="<?= esc_attr(get_option('team_phone')); ?>" size="50" required pattern="\+?\d{6,14}" /></td>
        </tr>      
        
        <tr>
            <th align="left">Email expeditor:</th>
            <td><input type="email" name="team_email" value="<?= esc_attr(get_option('team_email')); ?>" size="50" required /></td>
        </tr> 

        <tr>
            <th align="left">Judet expeditor:</th>
            <td><input type="text" name="team_county" value="<?= esc_attr(get_option('team_county')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Oras expeditor:</th>
            <td><input type="text" name="team_city" value="<?= esc_attr(get_option('team_city')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Adresa expeditor:</th>
            <td><input type="text" name="team_address" value="<?= esc_attr(get_option('team_address')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th align="left">Cod Postal expeditor:</th>
            <td><input type="text" name="team_postcode" value="<?= esc_attr(get_option('team_postcode')); ?>" size="50" required /></td>
        </tr>

        <tr>
            <th><hr style="margin: 15px 0;"></th>                
            <td><hr style="margin: 15px 0;"></td>
        </tr>
        
        <tr>
            <th  align="left">Deschidere colet:</th>
            <td>
                <select name="team_open_package">
                    <option value="false" <?= esc_attr(get_option('team_open_package')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_open_package')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Livrare Sambata:</th>
            <td>
                <select name="team_sat_delivery">
                    <option value="false" <?= esc_attr(get_option('team_sat_delivery')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_sat_delivery')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Taxa urgent express:</th>
            <td>
                <select name="team_tax_urgent_express">
                    <option value="false" <?= esc_attr(get_option('team_tax_urgent_express')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_tax_urgent_express')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Schimbare adresa de livrare:</th>
            <td>
                <select name="team_change_delivery_address">
                    <option value="false" <?= esc_attr(get_option('team_change_delivery_address')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_change_delivery_address')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Ora speciala de livrare:</th>
            <td>
                <select name="team_special_delivery_hour">
                    <option value="false" <?= esc_attr(get_option('team_special_delivery_hour')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_special_delivery_hour')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Swap (colet la schimb):</th>
            <td>
                <select name="team_swap_package">
                    <option value="false" <?= esc_attr(get_option('team_swap_package')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_swap_package')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Retur confirmare de primire:</th>
            <td>
                <select name="team_retur_delivery_confirmation">
                    <option value="false" <?= esc_attr(get_option('team_retur_delivery_confirmation')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_retur_delivery_confirmation')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Retur documente:</th>
            <td>
                <select name="team_retur_documents">
                    <option value="false" <?= esc_attr(get_option('team_retur_documents')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_retur_documents')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">A 3-a livrare nationala:</th>
            <td>
                <select name="team_3rd_national_delivery">
                    <option value="false" <?= esc_attr(get_option('team_3rd_national_delivery')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_3rd_national_delivery')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Retur expediere/colet nelivrat:</th>
            <td>
                <select name="team_retur_expedition_undelivered_package">
                    <option value="false" <?= esc_attr(get_option('team_retur_expedition_undelivered_package')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_retur_expedition_undelivered_package')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>


        <tr>
            <th  align="left">Adaugare AWB agent TeamCourier:</th>
            <td>
                <select name="team_awb_by_delivery_agent">
                    <option value="false" <?= esc_attr(get_option('team_awb_by_delivery_agent')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_awb_by_delivery_agent')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Etichetare colet/plic la sediu TeamCourier:</th>
            <td>
                <select name="team_labeling_package_with_awb">
                    <option value="false" <?= esc_attr(get_option('team_labeling_package_with_awb')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_labeling_package_with_awb')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Colete aditionale:</th>
            <td>
                <select name="team_multiple_packages">
                    <option value="false" <?= esc_attr(get_option('team_multiple_packages')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_multiple_packages')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>

        <tr>
            <th  align="left">Colete fragile:</th>
            <td>
                <select name="team_is_fragile">
                    <option value="false" <?= esc_attr(get_option('team_is_fragile')) == 'false' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="true" <?= esc_attr(get_option('team_is_fragile')) == 'true' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr> 

        <tr>
            <th align="left">Tip printare AWB:</th>
            <td>
                <select name="team_page_type">
                    <option value="default" <?= esc_attr( get_option('team_page_type') ) == 'default' ? 'selected="selected"' : ''; ?>>A5</option>
                    <option value="A6" <?= esc_attr( get_option('team_page_type') ) == 'A6' ? 'selected="selected"' : ''; ?>>A6</option>
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
                <select name="team_trimite_mail">
                    <option value="nu" <?= esc_attr(get_option('team_trimite_mail')) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?= esc_attr(get_option('team_trimite_mail')) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th align="left">Subiect mail:</th>
            <td><input type="text"  name="team_subiect_mail" value="<?= esc_attr(get_option('team_subiect_mail')); ?>" size="50" /></td>
        </tr>

        <tr>
            <th align="left">Continut mail:</th>
            <td>
                <?php
                    $email_template = get_option('team_email_template');
                    wp_editor( $email_template, 'team_email_template', $settings = array('textarea_rows'=> '10', 'media_buttons' => false ) );
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
                <select name="team_auto_generate_awb">
                    <option value="nu" <?php echo esc_attr( get_option('team_auto_generate_awb') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('team_auto_generate_awb') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
                <select name="team_auto_mark_complete">
                    <option value="nu" <?php echo esc_attr( get_option('team_auto_mark_complete') ) == 'nu' ? 'selected="selected"' : ''; ?>>Nu</option>
                    <option value="da" <?php echo esc_attr( get_option('team_auto_mark_complete') ) == 'da' ? 'selected="selected"' : ''; ?>>Da</option>
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
            <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Team creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></td>
        </tr>
    </table>
    </form>
</div>

<script>
    jQuery( $ => {
        const url = "<?=SAFEALTERNATIVE_API_URL?>/api/validateTeamAuth";
        
        $('button[name="validate_team"]').on('click', function(){
            let responseDiv =  $('.responseHereTeam'),
                submitBtn = $('#team_settings_form #submit');
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    api_key: $('input[name="team_key"]').val(),
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

        $('select[name="team_retur"]').on('change', function(){
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
                    courier: 'team',
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
