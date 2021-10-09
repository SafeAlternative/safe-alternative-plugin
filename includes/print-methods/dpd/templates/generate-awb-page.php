<?php ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />

<style>    
    .admin_page_generate-awb-dpd table.form-table, p.submit { width: 100%; max-width: 775px; }
    .admin_page_generate-awb-dpd input, .admin_page_generate-awb-dpd select, .admin_page_generate-awb-dpd textarea { width: 100%; }
    .admin_page_generate-awb-dpd .form-table td { padding: 0; }
    .admin_page_generate-awb-dpd .form-table th { padding: 15px 10px 15px 0; }
    .admin_page_generate-awb-dpd .select2-container { width: 100% !important; }
</style>

<div class="wrap">
<h2>Genereaza AWB DPD</h2>
<br/>
<form method="post" action="<?= plugin_dir_url(__DIR__); ?>generate.php?order_id=<?= $_GET['order_id'] ?>">
    
    <input type="hidden" name="awb[domain]" value="<?=site_url()?>" />
    <input type="hidden" name="awb[language]" value="<?=$_POST['awb']['language']?>" />
    <input type="hidden" name="awb[courier_service_payer]" value="<?=$_POST['awb']['courier_service_payer']?>" />
    <input type="hidden" name="awb[package_payer]" value="<?=$_POST['awb']['package_payer']?>" />
    <input type="hidden" name="awb[third_party_client_id]" value="<?=$_POST['awb']['third_party_client_id']?>" />
    <input type="hidden" name="awb[package]" value="<?=$_POST['awb']['package']?>" />
    <input type="hidden" name="awb[contents]" value="<?=$_POST['awb']['contents']?>" />
    <input type="hidden" name="awb[ref1]" value="<?=$_POST['awb']['ref1']?>" />
    <input type="hidden" name="awb[autoadjust_pickup_date]" value="<?=$_POST['awb']['autoadjust_pickup_date']?>" />

    <table class="form-table">
        <tbody>

            <tr>
                <th scope="row">Serviciu:</th>
                <td>
                    <select name="awb[service_id]">
                    <?php 
                        $services = (new SafealternativeDPDClass)->get_services();
                        $current_service_id = $_POST['awb']['service_id'];
                        if (!empty($services)) {
                            foreach($services as $service) {
                                $selected = ($service['id'] == $current_service_id) ? 'selected="selected"' : '';
                                echo "<option value='{$service['id']}' {$selected}>{$service['id']} - {$service['name']}</option>";
                            }
                        } else {
                            ?> 
                            <option value="2505" <?= $current_service_id == '2505' ? 'selected="selected"' : ''; ?>>2505 - DPD STANDARD</option>
                            <option value="2002" <?= $current_service_id == '2002' ? 'selected="selected"' : ''; ?>>2002 - CLASIC NATIONAL</option>
                            <option value="2003" <?= $current_service_id == '2003' ? 'selected="selected"' : ''; ?>>2003 - CLASIC NATIONAL (COLET)</option>
                            <option value="2005" <?= $current_service_id == '2005' ? 'selected="selected"' : ''; ?>>2005 - CARGO NATIONAL</option>
                            <option value="2412" <?= $current_service_id == '2412' ? 'selected="selected"' : ''; ?>>2412 - PALLET ONE RO</option>
                            <?php
                        }
                    ?>
                    </select>
                </td>
            </tr>

            <tr> 
                <th scope="row">Punct expeditor:</th> 
                <td>
                    <select name="awb[sender_id]">
                    <?php 
                        $senders = (new SafealternativeDPDClass)->get_senders();
                        $current_sender_id = $_POST['awb']['sender_id'];
                        if (!empty($senders)) {
                            foreach($senders as $sender) {
                                $selected = ($sender['clientId'] == $current_sender_id) ? 'selected="selected"' : '';
                                echo "<option value='{$sender['clientId']}' {$selected}>{$sender['address']['fullAddressString']}</option>";
                            }
                        } else {
                            ?> 
                            <option <?= $current_sender_id == '' ? 'selected="selected"' : ''; ?>>Utilizator implicit</option>
                            <?php
                        }
                    ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th  align="left">Plata transport:</th>
                <td>
                    <select name="awb[dpd_courier_service_payer]">
                        <option value="SENDER" <?= $_POST['awb']['courier_service_payer'] == 'SENDER' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                        <option value="RECIPIENT" <?= $_POST['awb']['courier_service_payer'] == 'RECIPIENT' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                        <option value="THIRD_PARTY" <?= $_POST['awb']['courier_service_payer'] == 'THIRD_PARTY' ? 'selected="selected"' : ''; ?>>Contract/tert</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th  align="left">Platitor ambalaj:</th>
                <td>
                    <select name="awb[dpd_courier_package_payer]">
                        <option value="SENDER" <?= $_POST['awb']['package_payer'] == 'SENDER' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                        <option value="RECIPIENT" <?= $_POST['awb']['package_payer'] == 'RECIPIENT' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                        <option value="THIRD_PARTY" <?= $_POST['awb']['package_payer'] == 'THIRD_PARTY' ? 'selected="selected"' : ''; ?>>Contract/tert</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><hr></th>                
                <td><hr></td>
            </tr>     

            <tr>
                <th scope="row">Nume destinatar:</th>
                <td><input type="text" name="awb[recipient_name]" value="<?= $_POST['awb']['recipient_name'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Persoana de contact:</th>
                <td><input type="text" name="awb[recipient_contact]" value="<?= $_POST['awb']['recipient_contact'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Persoana privata:</th>
                <td>
                    <select name="awb[recipient_private_person]">
                        <option value="y" <?= $_POST['awb']['recipient_private_person'] == 'y' ? 'selected' : '' ?> >Da</option>
                        <option value="n" <?= $_POST['awb']['recipient_private_person'] == 'n' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Telefon destinatar:</th>
                <td><input type="text" name="awb[recipient_phone]" value="<?= $_POST['awb']['recipient_phone'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Email destinatar:</th>
                <td><input type="text" name="awb[recipient_email]" value="<?= $_POST['awb']['recipient_email'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Judet destinatar:</th>
                <td><input type="text" name="awb[recipient_address_state_id]" value="<?= $_POST['awb']['recipient_address_state_id'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Oras destinatar:</th>
                <td><input type="text" name="awb[recipient_address_site_name]" value="<?= $_POST['awb']['recipient_address_site_name'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Cod postal destinatar:</th>
                <td><input type="text" name="awb[recipient_address_postcode]" value="<?= $_POST['awb']['recipient_address_postcode'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Adresa destinatar linia 1:</th>
                <td><input type="text" name="awb[recipient_address_line1]" value="<?= $_POST['awb']['recipient_address_line1'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Adresa destinatar linia 2:</th>
                <td><input type="text" name="awb[recipient_address_line2]" value="<?= $_POST['awb']['recipient_address_line2'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Notite adresa destinatar:</th>
                <td><input type="text" name="awb[recipient_address_note]" value="<?= $_POST['awb']['recipient_address_note'];?>"></td>
            </tr>

            <tr>
                <th scope="row">ID collect point:</th>
                <td><input type="text" name="awb[recipient_pickup_office_id]" value="<?= $_POST['awb']['recipient_pickup_office_id'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Valoare declarata:</th>
                <td><input type="text" name="awb[declared_value_amount]" value="<?= $_POST['awb']['declared_value_amount'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Livrare sambata:</th>
                <td>
                    <select name="awb[saturday_delivery]">
                        <option value="y" <?= $_POST['awb']['saturday_delivery'] == 'y' ? 'selected' : '' ?> >Da</option>
                        <option value="n" <?= $_POST['awb']['saturday_delivery'] == 'n' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Pachet fragil:</th>
                <td>
                    <select name="awb[declared_value_fragile]">
                        <option value="y" <?= $_POST['awb']['declared_value_fragile'] == 'y' ? 'selected' : '' ?> >Da</option>
                        <option value="n" <?= $_POST['awb']['declared_value_fragile'] == 'n' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Valoare ramburs:</th>
                <td><input type="number" step="0.01" name="awb[cod_amount]" value="<?= $_POST['awb']['cod_amount'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Moneda:</th>
                <td><input type="text" name="awb[cod_currency]" value="<?= $_POST['awb']['cod_currency'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Numar de pachete:</th>
                <td><input type="number" name="awb[parcels_count]" value="<?= $_POST['awb']['parcels_count'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Greutate totala (kg):</th>
                <td><input type="number" name="awb[total_weight]" value="<?= $_POST['awb']['total_weight'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Nota colet (max 200 caractere):</th>
                <td>
                    <textarea name="awb[shipmentNote]" lines="2" maxlength="200"><?= $_POST['awb']['shipmentNote'];?></textarea>
                    <sub style="float:right;"><span class="letterCount">0</span>/200</sub>
                </td>
            </tr>
            
            <tr>
                <td colspan="2" style="padding: 20px 0;">
                    <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Generează AWB"></p>
                </td>
            </tr>
        </tbody>
    </table>
</form>
<p class="submit" style="text-align: center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri DPD creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script> jQuery(function($){ $('select').select2(); $('.letterCount').text($('textarea[name="awb[shipmentNote]"]').val().length); $('textarea[name="awb[shipmentNote]"]').on('keyup change', function(){$('.letterCount').text($(this).val().length);})}) </script>