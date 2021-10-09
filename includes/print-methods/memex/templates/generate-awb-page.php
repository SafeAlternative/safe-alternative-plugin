<?php ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />

<style>    
    .admin_page_generate-awb-memex table.form-table, p.submit { width: 100%; max-width: 775px; }
    .admin_page_generate-awb-memex input, .admin_page_generate-awb-memex select, .admin_page_generate-awb-memex textarea { width: 100%; }
    .admin_page_generate-awb-memex .form-table td { padding: 6px 10px; }
    .admin_page_generate-awb-memex .form-table th { padding: 6px 15px 0px 0px; }
    .admin_page_generate-awb-memex .select2-container { width: 100% !important; }
</style>

<div class="wrap">
<h2>Genereaza AWB Memex</h2>
<br/>
<form method="post" action="<?= plugin_dir_url(__DIR__); ?>generate.php?order_id=<?= $_GET['order_id'] ?>">
    
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][PointId]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['PointId']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipTo][PointId]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['PointId']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][Name]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['Name']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][Address]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['Address']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][City]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['City']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][PostCode]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['PostCode']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][CountryCode]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['CountryCode']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][Contact]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['Contact']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][Email]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['Email']; ?>" />
    <input type="hidden" name="awb[shipmentRequest][ShipFrom][IsPrivatePerson]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['IsPrivatePerson'];?>" />
    <input type="hidden" name="awb[shipmentRequest][InsuranceAmount]" value="<?= $_POST['awb']['shipmentRequest']['COD']['Amount'];?>" />
    <input type="hidden" name="awb[shipmentRequest][MPK]" value="<?= $_POST['awb']['shipmentRequest']['MPK'];?>" />
    <input type="hidden" name="awb[shipmentRequest][RebateCoupon]" value="<?= $_POST['awb']['shipmentRequest']['RebateCoupon'];?>" />
    <input type="hidden" name="awb[shipmentRequest][LabelFormat]" value="<?= $_POST['awb']['shipmentRequest']['LabelFormat'];?>" />

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">Serviciu:</th>
                <td>
                    <select name="awb[shipmentRequest][ServiceId]">
                        <option value="38" <?= esc_attr(get_option('memex_service_id')) == '38' ? 'selected="selected"' : ''; ?>>38 - National Standard</option>
                        <option value="112" <?= esc_attr(get_option('memex_service_id')) == '112' ? 'selected="selected"' : ''; ?>>112 - Express 6 ore Bucuresti</option>
                        <option value="113" <?= esc_attr(get_option('memex_service_id')) == '113' ? 'selected="selected"' : ''; ?>>113 - Express 2 ore Bucuresti</option>
                        <option value="121" <?= esc_attr(get_option('memex_service_id')) == '121' ? 'selected="selected"' : ''; ?>>121 - Loco Standard</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><hr></th>                
                <td><hr></td>
            </tr>     

            <tr>
                <th scope="row">Nume expeditor:</th>
                <td><input type="text" name="awb[shipmentRequest][ShipFrom][Person]" value="<?= $_POST['awb']['shipmentRequest']['ShipFrom']['Name'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Nume destinatar:</th>
                <td><input type="text" name="awb[shipmentRequest][ShipTo][Name]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['Name'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Adresa:</th>
                <td><input type="text" name="awb[shipmentRequest][ShipTo][Address]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['Address'];?>"></td>
            </tr>

            <tr style ="display: none;" >
                <th scope="row">Codul tarii:</th>
                <td><input type="hidden" name="awb[shipmentRequest][ShipTo][CountryCode]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['CountryCode'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Oras:</th>
                <td><input type="text" name="awb[shipmentRequest][ShipTo][City]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['City'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Cod postal:</th>
                <td><input type="text" name="awb[shipmentRequest][ShipTo][PostCode]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['PostCode'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Persoana contact:</th>
                <td><input type="text" name="awb[shipmentRequest][ShipTo][Person]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['Person'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Telefon:</th>
                <td><input type="text" name="awb[shipmentRequest][ShipTo][Contact]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['Contact'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Email:</th>
                <td><input type="text" name="awb[shipmentRequest][ShipTo][Email]" value="<?= $_POST['awb']['shipmentRequest']['ShipTo']['Email'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Persoana privata:</th>
                <td>
                    <select name="awb[shipmentRequest][ShipTo][IsPrivatePerson]">
                        <option value="true" <?= $_POST['awb']['shipmentRequest']['ShipTo']['IsPrivatePerson'] == true ? 'selected' : '' ?> >Da</option>
                        <option value="false" <?= $_POST['awb']['shipmentRequest']['ShipTo']['IsPrivatePerson'] == false ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Continut:</th>
                <td><input type="text" name="awb[shipmentRequest][ContentDescription]" value="<?= $_POST['awb']['shipmentRequest']['ContentDescription']; ?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Colete</th>
                <td><input type="number" min="0" name="awb[Parcels]" value="<?= esc_attr(get_option('memex_package_count')); ?>"></td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <table cellspacing="0" class="memex_package_size_table">
                        <tbody>
                            <tr  <?php if(get_option('memex_package_count') < 1) echo 'style="display: none;"'; ?>>
                                <th scope="col">Lungime (cm)</th>
                                <th scope="col">Latime (cm)</th>
                                <th scope="col">Inaltime (cm)</th>
                                <th scope="col">Greutate (Kg)</th>
                            </tr>
                            <?php 
                            for($i = 0; $i < get_option('memex_package_count'); $i++){
                                $row_field_length = ($i == 0 ? $_POST['awb']['shipmentRequest']['Parcels'][0]['Parcel']['D'] : null);
                                $row_field_width = ($i == 0 ? $_POST['awb']['shipmentRequest']['Parcels'][0]['Parcel']['S'] : null);
                                $row_field_height = ($i == 0 ? $_POST['awb']['shipmentRequest']['Parcels'][0]['Parcel']['W'] : null);
                                $row_field_weight = ($i == 0 ? $_POST['awb']['shipmentRequest']['Parcels'][0]['Parcel']['Weight'] : null);
                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][IsNST]" value="true">
                                        <input type="hidden" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][Type]" value="Package">
                                        <input type="number" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][D]" value="<?= $row_field_length; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][S]" value="<?= $row_field_width; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][W]" value="<?= $row_field_height; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" max="35" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][Weight]" value="<?= $row_field_weight; ?>" required>
                                    </td>                        
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Plicuri</th>
                <td><input type="number" min="0" name="awb[Envelopes]" value="<?= esc_attr(get_option('memex_envelope_count')); ?>"></td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <table cellspacing="0" class="memex_envelope_size_table">
                        <tbody>
                            <tr  <?php if(get_option('memex_envelope_count') < 1) echo 'style="display: none;"'; ?>>
                                <th scope="col">Lungime (cm)</th>
                                <th scope="col">Latime (cm)</th>
                                <th scope="col">Inaltime (cm)</th>
                                <th scope="col">Greutate (Kg)</th>
                            </tr>
                            <?php 
                            for($i = get_option('memex_package_count'); $i < (int)get_option('memex_envelope_count')+(int)get_option('memex_package_count'); $i++){
                                $index = 1;
                                if(get_option('memex_package_count') == 0) {
                                    $index = 0;
                                }
                                $row_field_length = ($i == get_option('memex_package_count') ? $_POST['awb']['shipmentRequest']['Parcels'][$index]['Parcel']['D'] : null);
                                $row_field_width = ($i == get_option('memex_package_count') ? $_POST['awb']['shipmentRequest']['Parcels'][$index]['Parcel']['S'] : null);
                                $row_field_height = ($i == get_option('memex_package_count') ? $_POST['awb']['shipmentRequest']['Parcels'][$index]['Parcel']['W'] : null);
                                $row_field_weight = ($i == get_option('memex_package_count') ? $_POST['awb']['shipmentRequest']['Parcels'][$index]['Parcel']['Weight'] : null);
                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][IsNST]" value="true">
                                        <input type="hidden" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][Type]" value="Envelope">
                                        <input type="number" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][D]" value="<?= $row_field_length; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][S]" value="<?= $row_field_width; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][W]" value="<?= $row_field_height; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.1" max="0.5" name="awb[shipmentRequest][Parcels][<?= $i ?>][Parcel][Weight]" value="<?= $row_field_weight; ?>" required>
                                    </td>                        
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>

            <tr>
                <th scope="row">Serviciu aditional SMS:</th>
                <td>
                    <select name="awb[additional_sms]">
                        <option value="Da" <?= $_POST['awb']['additional_sms'] == 'Da' ? 'selected' : '' ?> >Da</option>
                        <option value="Nu" <?= $_POST['awb']['additional_sms'] == 'Nu' ? 'selected' : '' ?> >Nu</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th align="left">Valaore ramburs:</th>
                <td><input type="number" step="0.01" name="awb[shipmentRequest][COD][Amount]" value="<?= $_POST['awb']['shipmentRequest']['COD']['Amount'];?>"></td>
            </tr>

            <tr>
                <th align="left">Valoare asigurare:</th>
                <td><input type="number" step="0.01" name="awb[shipmentRequest][InsuranceAmount]" value="<?= $_POST['awb']['shipmentRequest']['InsuranceAmount'];?>"></td>
            </tr>
            
            <tr>
                <td colspan="2" style="padding: 20px 0;">
                    <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Generează AWB"></p>
                </td>
            </tr>
        </tbody>
    </table>
</form>
<p class="submit" style="text-align: center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Memex creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></p>
</div>
<script>
jQuery($ => {
    function template_row_fields(row_index,type){
        let td;
        if(type == 'Package') {
            td = `<input type="number" max="35" name="awb[shipmentRequest][Parcels][${ row_index }][Parcel][Weight]" value="" required>`;
        } else {
            td = `<input type="number" step="0.1" max="0.5" name="awb[shipmentRequest][Parcels][${ row_index }][Parcel][Weight]" value="" required>`;
        }
        return `
            <tr>
                <td>
                    <input type="hidden" name="awb[shipmentRequest][Parcels][${ row_index }][Parcel][IsNST]" value="true">
                    <input type="hidden" name="awb[shipmentRequest][Parcels][${ row_index }][Parcel][Type]" value="${ type }">
                    <input type="number" name="awb[shipmentRequest][Parcels][${ row_index }][Parcel][D]" value="" required>
                </td>
                <td>
                    <input type="number" name="awb[shipmentRequest][Parcels][${ row_index }][Parcel][S]" value="" required>
                </td>
                <td>
                    <input type="number" name="awb[shipmentRequest][Parcels][${ row_index }][Parcel][W]" value="" required>
                </td>
                <td>`
                +td+
                `</td>                        
            </tr>  
        `;
    }

    function create(count, current_rows, table_tr, table, type){
        if (count < 1) {
            table_tr.first().hide();
        } else {
            table_tr.first().show();
        }
        
        if (current_rows > count) {
            table_tr.slice(count-current_rows).remove();
        }

        let index = 0;
        while (current_rows < count) {
            index = $('.memex_package_size_table tr').length + $('.memex_envelope_size_table tr').length - 2;
            table.find('tbody').append(template_row_fields(index,type));
            current_rows++;
        }
    }

    $('input[name="awb[Parcels]"]').change(function() {
        create(
            $(this).val(),
            $('.memex_package_size_table tr').length - 1, 
            $('.memex_package_size_table tr'), 
            $('.memex_package_size_table'), 
            'Package'
        )
    });

    $('input[name="awb[Envelopes]"]').change(function() {
        create(
            parseInt($(this).val()),
            $('.memex_envelope_size_table tr').length - 1, 
            $('.memex_envelope_size_table tr'), 
            $('.memex_envelope_size_table'), 
            'Envelope'
        )
    });

});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script> jQuery(function($){ $('select').select2() }) </script>
