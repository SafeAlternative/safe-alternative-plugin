<?php
$fields = array (
    'UserName' => urldecode($UserName),
    'Password' => urldecode($Password) 
);        
$json = json_encode($fields);
$login = $this->urgent->CallMethod('LoginUser', $json, 'POST');
$token = json_decode($login['message']);
?>

<style>
    table {
        max-width: 775px !important;
    }
    .wp-admin select,
    .wp-core-ui .button,
    input {
        width: 100%;
        max-width: 100%;
    }
    p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    .form-table th{
        padding: 6px 15px 0px 0px;
        vertical-align: middle;
    }
    .form-table td{
        padding: 6px 10px;
    }
    .urgent_parcel_size_table th{
        text-align: center;
        width: initial;
    }
</style>

<div class="wrap">
<h2>Genereaza AWB Urgent Cargus</h2>
<?php if (isset($token->Error)) wp_die("<b>Eroare</b>: $token->Error"); ?>

<form method="post" action="<?= plugin_dir_url(__DIR__); ?>generate.php?order_id=<?= $_GET['order_id'] ?>">
    
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="hidden" name="awb[domain]" value="<?=site_url()?>" />
        
    <?php foreach($_GET as $k => $v) { ?>
        <input type="hidden" name="<?= $k ?>" value="<?= $v ?>">
    <?php } ?>

    <table class="form-table" style="max-width: 600px;">
        <tbody>
            <tr valign="top">
                <th scope="row" style="font-size: 21px;">Expeditor</th>
                <td></td>
            </tr>
            <tr valign="top">
                <th scope="row">Punct de lucru</th>
                <?php
                    $resultLocations = $this->urgent->CallMethod('PickupLocations/GetForClient', $json="", 'GET', $token);
                    $resultMessage = $resultLocations['message'];
                    $arrayResultLocations = json_decode($resultMessage, true);
                ?><td>
                <select name="awb[Sender][LocationId]"> <?php
                    foreach ($arrayResultLocations as $location) {  
                        ?><option value="<?= $location['LocationId']; ?>" <?= esc_attr(get_option('uc_punct_ridicare')) == $location['LocationId'] ? 'selected="selected"' : ''; ?>><?= $location['Name']; ?></option><?php
                    }
                    ?> 
                </select>
                </td> 
            </tr>
            <tr>
                <th><hr style="margin: 15px 0;"></th>           
                <td><hr style="margin: 15px 0;"></td>
            </tr>            
            <tr valign="top">
                <th scope="row" style="font-size: 21px;">Destinatar</th>
                <td></td>
            </tr>
            <tr valign="top">
                <th scope="row">Nume</th>
                <td><input type="text" name="awb[Recipient][Name]" value="<?= $_POST['awb']['Recipient']['Name'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Judet</th>
                <td><input type="text" name="awb[Recipient][CountyName]" value="<?= $_POST['awb']['Recipient']['CountyName'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Oras</th>
                <td><input type="text" name="awb[Recipient][LocalityName]" value="<?= $_POST['awb']['Recipient']['LocalityName'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Strada</th>
                <td><input type="text" name="awb[Recipient][StreetName]"  value="<?= $_POST['awb']['Recipient']['StreetName'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Numar</th>
                <td><input type="text" name="awb[Recipient][BuildingNumber]"  value="<?= $_POST['awb']['Recipient']['BuildingNumber'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Adresa</th>
                <td><input type="text" name="awb[Recipient][AddressText]"  value="<?= $_POST['awb']['Recipient']['AddressText'];?>""></td>
            </tr>
            <tr valign="top">
                <th scope="row">Cod Postal</th>
                <td><input type="text" name="awb[Recipient][CodPostal]"  value="<?= $_POST['awb']['Recipient']['CodPostal'];?>""></td>
            </tr>
            <tr valign="top">
                <th scope="row">Persoana contact</th>
                <td><input type="text" name="awb[Recipient][ContactPerson]"  value="<?= $_POST['awb']['Recipient']['ContactPerson'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Telefon</th>
                <td><input type="text" name="awb[Recipient][PhoneNumber]"  value="<?= $_POST['awb']['Recipient']['PhoneNumber'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Email</th>
                <td><input type="text" name="awb[Recipient][Email]"  value="<?= $_POST['awb']['Recipient']['Email'];?>"></td>
            </tr>

            <tr valign="top">
                <th scope="row">Plicuri</th>
                <td><input type="number" name="awb[Envelopes]" value="<?= esc_attr(get_option('uc_nr_plicuri')); ?>" min="0" max="9"></td>
            </tr>          

            <tr valign="top">
                <th scope="row">Colete</th>
                <td><input type="number" min="0" name="awb[Parcels]" value="<?= esc_attr(get_option('uc_nr_colete')); ?>"></td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <table cellspacing="0" class="urgent_parcel_size_table">
                        <tbody>
                            <tr  <?php if(get_option('uc_nr_colete') < 1) echo 'style="display: none;"'; ?>>
                                <th scope="col">Lungime (cm)</th>
                                <th scope="col">Latime (cm)</th>
                                <th scope="col">Inaltime (cm)</th>
                                <th scope="col">Greutate (Kg)</th>
                            </tr>
                            <?php 
                            for($i = 0; $i < get_option('uc_nr_colete'); $i++){
                                $row_field_length = ($i == 0 ? $_POST['awb']['ParcelCodes'][0]['Length'] : null);
                                $row_field_width = ($i == 0 ? $_POST['awb']['ParcelCodes'][0]['Width'] : null);
                                $row_field_height = ($i == 0 ? $_POST['awb']['ParcelCodes'][0]['Height'] : null);
                                $row_field_weight = ($i == 0 ? $_POST['awb']['ParcelCodes'][0]['Weight'] : null);
                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="awb[ParcelCodes][<?= $i ?>][Code]" value="<?= $i ?>">
                                        <input type="hidden" name="awb[ParcelCodes][<?= $i ?>][Type]" value="1">
                                        <input type="text" name="awb[ParcelCodes][<?= $i ?>][Length]" value="<?= $row_field_length; ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="awb[ParcelCodes][<?= $i ?>][Width]" value="<?= $row_field_width; ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="awb[ParcelCodes][<?= $i ?>][Height]" value="<?= $row_field_height; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="awb[ParcelCodes][<?= $i ?>][Weight]" value="<?= $row_field_weight; ?>" required>
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
                <th scope="row">Valoare declarata</th>
                <td><input type="text" name="awb[DeclaredValue]" value="<?= $_POST['awb']['DeclaredValue'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Ramburs cash</th>
                <td><input type="text" name="awb[CashRepayment]" value="<?= $_POST['awb']['CashRepayment'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Ramburs cont colector</th>
                <td><input type="text" name="awb[BankRepayment]" value="<?= $_POST['awb']['BankRepayment'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Retur, confirmare de primire, colet la schimb</th>
                <td><input type="text" name="awb[OtherRepayment]" value=""></td>
            </tr>
            <tr valign="top">
                <th scope="row">Id Tarif</th>
                <?php
                    $resultPriceTables = $this->urgent->CallMethod('PriceTables', $json="", 'GET', $token);
                    $resultMessage = $resultPriceTables['message'];
                    $arrayPriceTables = json_decode($resultMessage, true);
                    if ($resultPriceTables['status'] == "200" && $resultMessage != "Failed to authenticate!") { 
                        ?> <td><select name="awb[PriceTableId]"> <?php
                        foreach ($arrayPriceTables as $price_table) {  
                            ?><option value="<?= $price_table['PriceTableId']; ?>" <?= esc_attr(get_option('uc_price_table_id')) == $price_table['PriceTableId'] ? 'selected="selected"' : ''; ?>><?= $price_table['Name']; ?></option><?php
                        }
                        ?> </select></td> <?php                        
                    } else {       
                        ?> <td><input type="text" name="awb[PriceTableId]" value="<?= esc_attr(get_option('uc_price_table_id')); ?>" size="50" /></td> 
                <?php } ?>
            </tr>
            <tr valign="top">
                <th scope="row">Tip serviciu</th>
                <td>
                    <select name="awb[ServiceId]">
                        <option value="1" <?= esc_attr(get_option('uc_tip_serviciu')) == '1' ? 'selected="selected"' : ''; ?>>Standard</option>
                        <option value="4" <?= esc_attr(get_option('uc_tip_serviciu')) == '4' ? 'selected="selected"' : ''; ?>>Business Partener</option>
                        <option value="34" <?= esc_attr(get_option('uc_tip_serviciu')) == '34' ? 'selected="selected"' : ''; ?>>Economic Standard</option>
                        <option value="35" <?= esc_attr(get_option('uc_tip_serviciu')) == '35' ? 'selected="selected"' : ''; ?>>Standard Plus</option>
                        <option value="36" <?= esc_attr(get_option('uc_tip_serviciu')) == '36' ? 'selected="selected"' : ''; ?>>Palet Standard</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Platitor transport</th>
                <td>
                    <select name="awb[ShipmentPayer]">
                        <option value="1" <?= esc_attr(get_option('uc_plata_transport')) == '1' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                        <option value="2" <?= esc_attr(get_option('uc_plata_transport')) == '2' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Plata comision ramburs</th>
                <td>
                    <select name="awb[ShippingRepayment]">
                        <option value="1" <?= esc_attr(get_option('uc_plata_ramburs')) == '1' ? 'selected="selected"' : ''; ?>>Expeditor</option>
                        <option value="2" <?= esc_attr(get_option('uc_plata_ramburs')) == '2' ? 'selected="selected"' : ''; ?>>Destinatar</option>
                    </select> 
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Deschidere la livrare</th>
                <td>
                    <select name="awb[OpenPackage]">
                        <option value="0" <?= esc_attr(get_option('uc_deschidere')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="1" <?= esc_attr(get_option('uc_deschidere')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>                
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Livrare sambata</th>
                <td>
                    <select name="awb[SaturdayDelivery]">
                        <option value="0" <?= esc_attr(get_option('uc_sambata')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="1" <?= esc_attr(get_option('uc_sambata')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>                
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Livrare dimineata</th>
                <td>
                    <select name="awb[MorningDelivery]">
                        <option value="0" <?= esc_attr(get_option('uc_matinal')) == '0' ? 'selected="selected"' : ''; ?>>Nu</option>
                        <option value="1" <?= esc_attr(get_option('uc_matinal')) == '1' ? 'selected="selected"' : ''; ?>>Da</option>
                    </select>                
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Observatii</th>
                <td><input type="text" name="awb[Observations]" value="<?= $_POST['awb']['Observations'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Continut</th>
                <td><input type="text" name="awb[PackageContent]" value="<?= $_POST['awb']['PackageContent'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Referinta Serie Client</th>
                <td><input type="text" name="awb[CustomString]" value="<?= $_POST['awb']['CustomString'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Referinta expeditor 1</th>
                <td><input type="text" name="awb[SenderReference1]" value="<?= $_POST['awb']['SenderReference1'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Referinta destinatar 1</th>
                <td><input type="text" name="awb[RecipientReference1]" value="<?= $_POST['awb']['RecipientReference1'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Referinta destinatar 2</th>
                <td><input type="text" name="awb[RecipientReference2]" value="<?= $_POST['awb']['RecipientReference2'];?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Referinta facturare</th>
                <td><input type="text" name="awb[InvoiceReference]" value="<?= $_POST['awb']['InvoiceReference'];?>"></td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 20px 0;">
                    <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Generează AWB"></p>
                </td>
            </tr>
        </tbody>
    </table>
</form>
</div>


<script>
jQuery($ => {
    function template_row_fields(row_index){
        return `
            <tr>
                <td>
                    <input type="hidden" name="awb[ParcelCodes][${row_index}][Code]" value="${row_index}">
                    <input type="hidden" name="awb[ParcelCodes][${row_index}][Type]" value="1">       
                    <input type="text" name="awb[ParcelCodes][${row_index}][Length]" value="" required>
                </td>
                <td>
                    <input type="text" name="awb[ParcelCodes][${row_index}][Width]" value="" required>
                </td>
                <td>
                    <input type="text" name="awb[ParcelCodes][${row_index}][Height]" value="" required>
                </td>
                <td>
                    <input type="number" name="awb[ParcelCodes][${row_index}][Weight]" value="" required>
                </td>                        
            </tr>    
        `;
    }

    $('input[name="awb[Parcels]"]').change(function (){
        let parcels = $(this).val(),
            current_rows = $('.urgent_parcel_size_table tr').length - 1;

        if (parcels < 1) {
            $('.urgent_parcel_size_table tr').first().hide();
        } else {
            $('.urgent_parcel_size_table tr').first().show();
        }
        
        if (current_rows > parcels) {
            $('.urgent_parcel_size_table tr').slice(parcels-current_rows).remove();
        }

        while (current_rows < parcels) {
            $('.urgent_parcel_size_table').append(template_row_fields(current_rows));
            current_rows++;
        }
    })
});
</script>