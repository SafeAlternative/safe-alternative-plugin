<?php 
    $sameday = new SafealternativeSamedayClass;
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />

<style>    
    .admin_page_generate-awb-sameday table.form-table, p.submit { width: 100%; max-width: 775px; }
    .admin_page_generate-awb-sameday input, .admin_page_generate-awb-sameday select, .admin_page_generate-awb-sameday textarea { width: 100%; }
    .admin_page_generate-awb-sameday .form-table td { padding: 0; }
    .admin_page_generate-awb-sameday .form-table th { padding: 15px 10px 15px 0; }
    .admin_page_generate-awb-sameday .select2-container { width: 100% !important; }
    .admin_page_generate-awb-sameday .sameday_parcel_table th{ text-align: center; width: initial; }
    .admin_page_generate-awb-sameday .sameday_parcel_table { border-spacing: 6px; }
</style>

<div class="wrap">
<h2>Genereaza AWB Sameday</h2>
<br/>
<form method="post" action="<?= plugin_dir_url(__DIR__); ?>generate.php?order_id=<?= $_GET['order_id'] ?>">
    
    <input type="hidden" name="awb[domain]" value="<?=site_url()?>" />
    <input type="hidden" name="awb[lockerId]" value="<?= $_POST['awb']['lockerId'] ?>" />

    <table class="form-table">
        <tbody>

            <tr> 
                <th align="left">Punct de ridicare:</th> 
                <td>
                    <select name="awb[pickup_point]">
                        <?php 
                            $pickup_points = get_transient('sameday_pickup_points');
                            $current_pickup_point = $_POST['awb']['pickup_point'];
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
                        ?>
                    </select>
                </td>
            </tr>

            <tr>    
                <th  align="left">Serviciu:</th>
                <td>
                    <select name="awb[service_id]">
                        <?php 
                            $services = get_transient('sameday_services');
                            $lockers = get_post_meta($order->get_id(),'safealternative_sameday_lockers',true);
                            $current_service_id = $lockers ? '15' : $_POST['awb']['service_id'];
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
                        ?>
                    </select>
                    <?php if ($lockers) : ?>
                        <span style = "color:red"> A fost selectata optiunea de EasyBox. Recomandam selectarea unui serviciu compatibil. </span>
                    <?php endif; ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Tip colet:</th>
                <td>
                    <select name="awb[package_type]">
                        <option value="0" <?= $_POST['awb']['package_type'] == '0' ? 'selected="selected"' : ''; ?>>Colet</option>
                        <option value="1" <?= $_POST['awb']['package_type'] == '1' ? 'selected="selected"' : ''; ?>>Plic</option>
                        <option value="2" <?= $_POST['awb']['package_type'] == '2' ? 'selected="selected"' : ''; ?>>Palet</option>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><hr></th>                
                <td><hr></td>
            </tr>  
            
            <tr valign="top">
                <th scope="row">Numar colete:</th>
                <td><input type="number" min="1" name="parcel_number" value="1"></td>
            </tr>

            <tr>
                <th scope="row">Observatii (max 200 caractere):</th>
                <td>
                    <textarea name="awb[observation]" lines="2" maxlength="200"><?= $_POST['awb']['observation'];?></textarea>
                    <sub style="float:right;"><span class="letterCount">0</span>/200</sub>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Continut:</th>
                <td><input type="text" name="awb[priceObservation]" value="<?= $_POST['awb']['priceObservation'] ?>"></td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <table class="sameday_parcel_table">
                        <tbody>
                            <tr>
                                <th scope="col">Lungime (cm)</th>
                                <th scope="col">Latime (cm)</th>
                                <th scope="col">Inaltime (cm)</th>
                                <th scope="col">Greutate (Kg)</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" name="awb[parcel_dimensions][0][length]" value="<?= $_POST['awb']['length'] ?>">
                                </td>
                                <td>
                                    <input type="text" name="awb[parcel_dimensions][0][width]" value="<?= $_POST['awb']['width'] ?>">
                                </td>
                                <td>
                                    <input type="text" name="awb[parcel_dimensions][0][height]" value="<?= $_POST['awb']['height'] ?>">
                                </td>
                                <td>
                                    <input type="number" name="awb[parcel_dimensions][0][weight]" value="<?= $_POST['awb']['weight'] ?>" required>
                                </td>                        
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><hr></th>                
                <td><hr></td>
            </tr>  

            <tr valign="top">
                <th scope="row">Nume destinatar:</th>
                <td><input type="text" name="awb[name]" value="<?= $_POST['awb']['name'];?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Telefon destinatar:</th>
                <td><input type="text" name="awb[phone]" value="<?= $_POST['awb']['phone'];?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Email destinatar:</th>
                <td><input type="text" name="awb[email]" value="<?= $_POST['awb']['email'];?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Judet destinatar:</th>
                <td><input type="text" name="awb[state]" value="<?= $_POST['awb']['state'];?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Oras destinatar:</th>
                <td><input type="text" name="awb[city]" value="<?= $_POST['awb']['city'];?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Adresa destinatar:</th>
                <td><input type="text" name="awb[address]" value="<?= $_POST['awb']['address'];?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Valoare declarata:</th>
                <td><input type="number" step="0.01" name="awb[declared_value]" value="<?= $_POST['awb']['declared_value'];?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Valoare ramburs:</th>
                <td><input type="number" step="0.01" name="awb[cod_value]" value="<?= $_POST['awb']['cod_value'];?>"></td>
            </tr>
            
            <tr>
                <td colspan="2" style="padding: 20px 0;">
                    <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Generează AWB"></p>
                </td>
            </tr>
        </tbody>
    </table>
</form>
<p class="submit" style="text-align: center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Sameday creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script>
jQuery($ => {
    $('select').select2();

    function template_row_fields(row_index){
        return `
            <tr>
                <td>    
                    <input type="text" name="awb[parcel_dimensions][${row_index}][length]" value="">
                </td>
                <td>
                    <input type="text" name="awb[parcel_dimensions][${row_index}][width]" value="">
                </td>
                <td>
                    <input type="text" name="awb[parcel_dimensions][${row_index}][height]" value="">
                </td>
                <td>
                    <input type="number" name="awb[parcel_dimensions][${row_index}][weight]" value="" required>
                </td>                        
            </tr>    
        `;
    }

    $('input[name="parcel_number"]').change(function (){
        let parcels = $(this).val(),
            current_rows = $('.sameday_parcel_table tr').length - 1;
        
        if (current_rows > parcels) {
            $('.sameday_parcel_table tr').slice(parcels-current_rows).remove();
        }

        while (current_rows < parcels) {
            $('.sameday_parcel_table').append(template_row_fields(current_rows));
            current_rows++;
        }
    })

    $('.letterCount').text($('textarea[name="awb[observation]"]').val().length);
    $('textarea[name="awb[observation]"]').on('keyup change', function(){$('.letterCount').text($(this).val().length);})
});
</script>
