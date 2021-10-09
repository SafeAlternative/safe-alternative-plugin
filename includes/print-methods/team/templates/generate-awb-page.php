<?php ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />

<style>    
    .admin_page_generate-awb-team table.form-table, p.submit { width: 100%; max-width: 775px; }
    .admin_page_generate-awb-team input, .admin_page_generate-awb-team select, .admin_page_generate-awb-team textarea { width: 100%; }
    .admin_page_generate-awb-team .form-table td { padding: 0; }
    .admin_page_generate-awb-team .form-table th { padding: 15px 10px 15px 0; }
    .admin_page_generate-awb-team .select2-container { width: 100% !important; }
</style>

<div class="wrap">
<h2>Genereaza AWB Team</h2>
<br/>
<form method="post" action="<?= plugin_dir_url(__DIR__); ?>generate.php?order_id=<?= $_GET['order_id'] ?>">
    <input type="hidden" name="awb[payer]" value="<?=$_POST['awb']['payer']?>" />
    <input type="hidden" name="awb[ramburs_type]" value="<?=$_POST['awb']['ramburs_type']?>" />
    <input type="hidden" name="awb[insurance]" value="<?=$_POST['awb']['insurance']?>" />
    <input type="hidden" name="awb[content]" value="<?=$_POST['awb']['content']?>" />
    <input type="hidden" name="awb[from_name]" value="<?=$_POST['awb']['from_name']?>" />
    <input type="hidden" name="awb[from_contact]" value="<?=$_POST['awb']['from_contact']?>" />
    <input type="hidden" name="awb[from_email]" value="<?=$_POST['awb']['from_email']?>" />
    <input type="hidden" name="awb[from_phone]" value="<?=$_POST['awb']['from_phone']?>" />
    <input type="hidden" name="awb[from_county]" value="<?=$_POST['awb']['from_county']?>" />
    <input type="hidden" name="awb[from_city]" value="<?=$_POST['awb']['from_city']?>" />
    <input type="hidden" name="awb[from_address]" value="<?=$_POST['awb']['from_address']?>" />
    <input type="hidden" name="awb[from_zipcode]" value="<?=$_POST['awb']['from_zipcode']?>" />
    
    <table class="form-table">
        <tbody>
            <tr>
                <th  align="left">Tip colet:</th>
                <td>
                    <select name="awb[type]">
                        <option value="envelope" <?= $_POST['awb']['type'] == 'envelope' ? 'selected' : '' ?> >Plic</option>
                        <option value="package" <?= $_POST['awb']['type'] == 'package' ? 'selected' : '' ?>>Colet</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Serviciu:</th>
                <td>
                    <select name="awb[service_type]">
                    <?php 
                        $services = (new SafealternativeTeamClass)->get_services();
                        $current_service = $_POST['awb']['service_type'];
                        if (!empty($services) && empty($services['error'])) {
                            foreach($services as $service) {
                                $selected = ($service['name'] == $current_service_id) ? 'selected="selected"' : '';
                                echo "<option value='{$service['name']}' {$selected}>{$service['name']}</option>";
                            }
                        } else {
                            ?> 
                            <option value="Eco" <?= $current_service == 'Eco' ? 'selected="selected"' : ''; ?>>Eco</option>
                            <?php
                        }
                    ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><hr></th>                
                <td><hr></td>
            </tr>  
            
            <tr>
                <th scope="row">Retur:</th>
                <td>
                    <select name="awb[retur]">
                        <option value="true" <?= $_POST['awb']['retur'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['retur'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>   

            <tr id='tipRetur' <?= $_POST['awb']['retur'] == 'false' ? 'style="display: none;"' : '' ?>>
                <th scope="row">Tip retur:</th>
                <td>
                    <select name="awb[retur_type]">
                        <option value="document" <?= $_POST['awb']['retur_type'] == 'document' ? 'selected' : '' ?> >Document</option>
                        <option value="colet" <?= $_POST['awb']['retur_type'] == 'colet' ? 'selected' : '' ?> >Colet</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Platitor expeditie:</th>
                <td>
                    <select name="awb[payer]">
                        <option value="expeditor" <?=$_POST['awb']['payer'] == 'expeditor' ? 'selected="selected"' : '';  ?> >Expeditor</option>
                        <option value="destinatar" <?=$_POST['awb']['payer'] == 'destinatar' ? 'selected="selected"' : '';  ?> >Destinatar</option>
                    </select>
                </td>
            </tr> 

            <tr>
                <th scope="row">Nume destinatar:</th>
                <td><input type="text" name="awb[to_name]" value="<?= $_POST['awb']['to_name'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Persoana de contact:</th>
                <td><input type="text" name="awb[to_contact]" value="<?= $_POST['awb']['to_contact'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Telefon destinatar:</th>
                <td><input type="text" name="awb[to_phone]" value="<?= $_POST['awb']['to_phone'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Email destinatar:</th>
                <td><input type="text" name="awb[to_email]" value="<?= $_POST['awb']['to_email'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Judet destinatar:</th>
                <td><input type="text" name="awb[to_county]" value="<?= $_POST['awb']['to_county'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Oras destinatar:</th>
                <td><input type="text" name="awb[to_city]" value="<?= $_POST['awb']['to_city'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Cod postal destinatar:</th>
                <td><input type="text" name="awb[to_zipcode]" value="<?= $_POST['awb']['to_zipcode'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Adresa destinatar:</th>
                <td><input type="text" name="awb[to_address]" value="<?= $_POST['awb']['to_address'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Valoare asigurare:</th>
                <td><input type="text" name="awb[insurance]" value="<?=$_POST['awb']['insurance']?>"></td>
            </tr>

            <tr>
                <th scope="row">Deschidere colet:</th>
                <td>
                    <select name="awb[service_41]">
                        <option value="true" <?= $_POST['awb']['service_41'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_41'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Livrare sambata:</th>
                <td>
                    <select name="awb[service_42]">
                        <option value="true" <?= $_POST['awb']['service_42'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_42'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">Taxa urgent express:</th>
                <td>
                    <select name="awb[service_51]">
                        <option value="true" <?= $_POST['awb']['service_51'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_51'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Schimbare adresa de livrare:</th>
                <td>
                    <select name="awb[service_62]">
                        <option value="true" <?= $_POST['awb']['service_62'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_62'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Ora speciala de livrare:</th>
                <td>
                    <select name="awb[service_63]">
                        <option value="true" <?= $_POST['awb']['service_63'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_63'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Swap (colet la schimb):</th>
                <td>
                    <select name="awb[service_64]">
                        <option value="true" <?= $_POST['awb']['service_64'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_64'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Retur confirmare de primire:</th>
                <td>
                    <select name="awb[service_66]">
                        <option value="true" <?= $_POST['awb']['service_66'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_66'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Retur documente:</th>
                <td>
                    <select name="awb[service_67]">
                        <option value="true" <?= $_POST['awb']['service_67'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_67'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">A 3-a livrare nationala:</th>
                <td>
                    <select name="awb[service_73]">
                        <option value="true" <?= $_POST['awb']['service_73'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_73'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Retur expediere/colet nelivrat:</th>
                <td>
                    <select name="awb[service_84]">
                        <option value="true" <?= $_POST['awb']['service_84'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_84'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Adaugare AWB agent TeamCourier:</th>
                <td>
                    <select name="awb[service_104]">
                        <option value="true" <?= $_POST['awb']['service_104'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_104'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Etichetare colet\plic la sediu Team Courier:</th>
                <td>
                    <select name="awb[service_108]">
                        <option value="true" <?= $_POST['awb']['service_108'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_108'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Colete aditionale:</th>
                <td>
                    <select name="awb[service_292]">
                        <option value="true" <?= $_POST['awb']['service_292'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['service_292'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Pachet fragil:</th>
                <td>
                    <select name="awb[fragile]">
                        <option value="true" <?= $_POST['awb']['fragile'] == 'true' ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['fragile'] == 'false' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Valoare ramburs:</th>
                <td><input type="number" step="0.01" name="awb[ramburs]" value="<?= $_POST['awb']['ramburs'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Numar de pachete:</th>
                <td><input type="number" name="awb[cnt]" value="<?= $_POST['awb']['cnt'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Greutate totala (kg):</th>
                <td><input type="number" name="awb[weight]" value="<?= $_POST['awb']['weight'];?>"></td>
            </tr>
            
            <tr>
                <td colspan="2" style="padding: 20px 0;">
                    <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Generează AWB"></p>
                </td>
            </tr>
        </tbody>
    </table>
</form>
<p class="submit" style="text-align: center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Team creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script> 
jQuery(function($) { 
    $('select').select2(); 

    $('select[name="awb[retur]"]').on('change', function(){
        if($(this).val() == 'true'){
            $('#tipRetur').show();
        } else {
            $('#tipRetur').hide();
        }
    });
}) 
</script>
