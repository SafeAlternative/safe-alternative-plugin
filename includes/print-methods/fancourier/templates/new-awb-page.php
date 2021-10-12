<?php 
function format_field_labels($k)
{
    switch ($k) {
        case 'height': return 'Inaltime';
        case 'length': return 'Lungime';
        case 'width': return 'Latime';
        default: return ucfirst(str_replace('_', ' ', $k));
    }
} 
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />

<style>
    .admin_page_generate-awb-fan table.form-table, p.submit { width: 60%; max-width: 775px; }
    .admin_page_generate-awb-fan input, .admin_page_generate-awb-fan select, .admin_page_generate-awb-fan textarea { width: 100%; }
    .admin_page_generate-awb-fan .form-table td { padding: 0; }
    .admin_page_generate-awb-fan .form-table th { padding: 15px 10px 15px 0; }
    .admin_page_generate-awb-fan .select2-container { width: 100% !important; }
</style>

<div class="wrap">
    <h1>Genereaza AWB pentru Fan Courier</h1>
    <br/>
    <form method="post" action="<?= plugin_dir_url( __DIR__ ) ?>generate.php?order_id=<?= $_GET['order_id'] ?>">
        <input type="hidden" name="awb[epod_opod]" value="<?= $awb_details['epod_opod'] ?>">
        <?php foreach($_GET as $k => $v) { ?>
        <input type="hidden" name="<?= $k ?>" value="<?= $v ?>">
        <?php } ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Punct de ridicare</th>
                <td>
                    <select name="awb[clientId]">
                        <?php foreach($clientIds as $list_clientId) { ?>
                            <option value="<?= $list_clientId->client_id ?? null; ?>"
                                <?php if(get_option('fan_clientID') == $list_clientId->client_id) { ?> selected <?php } ?>>
                                <?= $list_clientId->nume; ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>    
            
          


        <?php
            unset($_POST['awb']['epod_opod']);
            foreach($_POST['awb'] as $k => $v) {
                if(is_array($v)) {
                    $label = $k;
        ?>
            <tr valign="top">
                <th scope="row"><?= $label ?></th>
                <td></td>
            </tr>
            <?php foreach($v as $k => $v) { ?>
                <?php if($k=='LocationId') { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td>
                        <select name="awb[<?= $label ?>][<?= $k ?>]">
                            <?php foreach($jsonPickupLocations as $c) { ?>
                            <option value="<?= $c['LocationId'] ?>"<?php if($v==$c['LocationId']) { ?> selected<?php } ?>><?= $c['Name'] ?> <?= $c['ContactPerson'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } elseif($k=='judet') { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td>
                        <select name="awb[<?= $label ?>][<?= $k ?>]">
                            <?php foreach($countiesListFan as $c) { ?>
                            <option value="<?= $c['CountyId'] ?>"<?php if($v==$c['CountyId']) { ?> selected<?php } ?>><?= $c['Name'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } elseif($k=='localitate') { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td>
                        <select name="awb[<?= $label ?>][<?= $k ?>]">
                            <?php foreach($localitiesListFan as $c) { ?>
                            <option value="<?= $c['Name'] ?>"<?php if($v==$c['LocalityId']||strtolower($v)==strtolower($c['Name'])) { ?> selected<?php } ?>><?= $c['Name'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } else { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td><input type="text" name="awb[<?= $label ?>][<?= $k ?>]" value="<?= $v; ?>" /></td>
                </tr>
                <?php } ?>
            <?php }
            } else { 
            if($k=='localitatee') { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td>
                        <select name="awb[<?= $k ?>]">
                            <?php foreach($localitiesListFan as $kk => $c) { ?>
                            <option value="<?= $c ?>"<?php if($v==$kk||strtolower($v)==strtolower($c)) { ?> selected<?php } ?>><?= $c ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } elseif($k=='judet') { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td>
                        <select name="awb[<?= $k ?>]">
                            <?php foreach($countiesList as $kk => $c) { ?>
                            <option value="<?= $c ?>"<?php if($v==$kk||strtolower($v)==strtolower($c)) { ?> selected<?php } ?>><?= $c ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } elseif($k=='tip_serviciu') { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td>
                        <select name="awb[<?= $k ?>]">
                            <?php foreach($servicesListFan as $kk => $c) { ?>
                            <option value="<?= $c ?>"<?php if($v==$kk||strtolower($v)==strtolower($c)) { ?> selected<?php } ?>><?= $c ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } elseif($k=='deschidere_la_livrare') { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td>
                        <select name="awb[<?= $k ?>]">
                            <?php foreach($deschidere_la_livrare as $kk => $c) { ?>
                            <option value="<?= $c ?>"> <?= $c ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } elseif($k=='livrare_sambata') { ?>
                <tr valign="top">
                    <th scope="row"><?= format_field_labels($k) ?></th>
                    <td>
                        <select name="awb[<?= $k ?>]">
                            <?php foreach($livrare_sambata as $kk => $c) { ?>
                            <option value="<?= $c ?>"> <?= $c ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } else { ?>
                    <tr valign="top">
                        <th scope="row"><?= format_field_labels($k) ?></th>
                        <td><input type="text" name="awb[<?= $k ?>]" value="<?= $v; ?>" size="40"; /></td>
                    </tr>
                <?php } ?>
                <?php } ?>
            <?php } ?>
        </table>
        <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Genereaza AWB"></p>
    </form>
</div>
<p class="submit" style="text-align: center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri FanCourier creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></p>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script> jQuery(function($){ $('select').select2(); }) </script>