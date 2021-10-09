<?php ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />

<style>    
    .admin_page_generate-awb-optimus table.form-table, p.submit { width: 100%; max-width: 775px; }
    .admin_page_generate-awb-optimus input, .admin_page_generate-awb-optimus select, .admin_page_generate-awb-optimus textarea { width: 100%; }
    .admin_page_generate-awb-optimus .form-table td { padding: 0; }
    .admin_page_generate-awb-optimus .form-table th { padding: 15px 10px 15px 0; }
    .admin_page_generate-awb-optimus .select2-container { width: 100% !important; }
</style>

<div class="wrap">
<h2>Genereaza AWB OptimusCourier</h2>
<br/>
<form method="post" action="<?= plugin_dir_url(__DIR__); ?>generate.php?order_id=<?= $_GET['order_id'] ?>">
    
    <input type="hidden" name="awb[ref_factura]" value="<?= $_POST['awb']['ref_factura']; ?>" />
    
    <table class="form-table">
        <tbody>   
            <tr>
                <th scope="row">Nume destinatar:</th>
                <td><input type="text" name="awb[destinatar_nume]" value="<?= $_POST['awb']['destinatar_nume'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Adresa destinatar:</th>
                <td><input type="text" name="awb[destinatar_adresa]" value="<?= $_POST['awb']['destinatar_adresa'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Localitate destinatar:</th>
                <td><input type="text" name="awb[destinatar_localitate]" value="<?= $_POST['awb']['destinatar_localitate'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Judet destinatar:</th>
                <td><input type="text" name="awb[destinatar_judet]" value="<?= $_POST['awb']['destinatar_judet'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Cod postal</th>
                <td><input type="text" name="awb[destinatar_cod_postal]" value="<?= $_POST['awb']['destinatar_cod_postal'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Persoana de contact:</th>
                <td><input type="text" name="awb[destinatar_contact]" value="<?= $_POST['awb']['destinatar_contact'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Telefon:</th>
                <td><input type="text" name="awb[destinatar_telefon]" value="<?= $_POST['awb']['destinatar_telefon'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Numar colete:</th>
                <td><input type="number" name="awb[colet_buc]" value="<?= $_POST['awb']['colet_buc'];?>"></td>
            </tr>
            
            <tr>
                <th scope="row">Greutate:</th>
                <td><input type="number" step="0.01" name="awb[colet_greutate]" value="<?= $_POST['awb']['colet_greutate'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Continut:</th>
                <td><input type="text"  name="awb[colet_descriere]" value="<?= $_POST['awb']['colet_descriere'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Data colectare:</th>
                <td><input type="date" name="awb[data_colectare]" value="<?= $_POST['awb']['data_colectare'];?>"></td>
            </tr>

            <tr>
                <th scope="row">Valoare ramburs:</th>
                <td><input type="number" step="0.01" name="awb[ramburs_valoare]" value="<?= $_POST['awb']['ramburs_valoare'];?>"></td>
            </tr>
            
            <tr>
                <td colspan="2" style="padding: 20px 0;">
                    <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Generează AWB"></p>
                </td>
            </tr>
        </tbody>
    </table>
</form>
<p class="submit" style="text-align: center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri Optimus creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a></p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script> jQuery(function($){ $('select').select2() }) </script>
