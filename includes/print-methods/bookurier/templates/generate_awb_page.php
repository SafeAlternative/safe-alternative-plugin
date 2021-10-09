<?php
?>
<style>
    input[type=color], input[type=date], input[type=datetime-local], input[type=datetime], input[type=email], input[type=month], input[type=number], input[type=password], input[type=search], input[type=tel], input[type=text], input[type=time], input[type=url], input[type=week], select, textarea {
        width: 555px !important;
        max-width: 555px !important;
    }   
    input[type=submit] {
        width: 782px !important;
    }
    .form-table th{
        padding: 6px 15px 0px 0px;
        vertical-align: middle;
    }
    .form-table td{
        padding: 6px 10px;
    }
</style>


<div class="wrap">
<h2>Genereaza AWB pentru Bookurier</h2>
<br>
    <form method="post" action="<?php echo plugin_dir_url( __DIR__ ) ?>generate.php?order_id=<?php echo $_GET['order_id'] ?>">

        <input type="hidden" name="awb[domain]" value="<?=$awb_details['domain']?>" />
        <input type="hidden" name="awb[client]" value="<?=$awb_details['client']?>" />
        <input type="hidden" name="awb[exchange_pack]" value="<?=$awb_details['exchange_pack']?>" />
        <input type="hidden" name="awb[confirmation]" value="<?=$awb_details['confirmation']?>" />

        <table class="form-table">

            <tr valign="top">
                <th scope="row">Nume destinatar:</th>
                <td><input type="text" name="awb[recv]" value="<?=$awb_details['recv']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Adresa destinatar:</th>
                <td><input type="text" name="awb[street]" value="<?=$awb_details['street']?>" size="40" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Judet destinatar:</th>
                <td><input type="text" name="awb[district]" value="<?=$awb_details['district']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Localitate destinatar:</th>
                <td><input type="text" name="awb[city]" value="<?=$awb_details['city']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Cod postal destinatar:</th>
                <td><input type="text" name="awb[zip]" value="<?=$awb_details['zip']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Tara destinatar:</th>
                <td><input type="text" name="awb[country]" value="<?=$awb_details['country']?>" size="40" /></td>
            </tr>           
            
            <tr valign="top">
                <th scope="row">Telefon destinatar:</th>
                <td><input type="text" name="awb[phone]" value="<?=$awb_details['phone']?>" size="40" /></td>
            </tr>       
            
            <tr valign="top">
                <th scope="row">Email destinatar:</th>
                <td><input type="text" name="awb[email]" value="<?=$awb_details['email']?>" size="40" /></td>
            </tr>  
                
            <tr valign="top">
                <th scope="row">Informatii aditionale:</th>
                <td><input type="text" name="awb[notes]" value="<?=$awb_details['notes']?>" size="40" /></td>
            </tr>                   
                   
            <tr valign="top">
                <th scope="row">Referinta comanda:</th>
                <td><input type="text" name="awb[unq]" value="<?=$awb_details['unq']?>" size="40" /></td>
            </tr>   

            <tr valign="top">
                <th scope="row">Valoare Ramburs:</th>
                <td><input type="text" name="awb[rbs_val]" value="<?=$awb_details['rbs_val']?>" size="40" /></td>
            </tr>     
            
            <tr valign="top">
                <th scope="row">Numar colete:</th>
                <td><input type="number" step="1" min="0" name="awb[packs]" value="<?=$awb_details['packs']?>" size="40" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Greutate colete:</th>
                <td><input type="number" step="1" min="0" name="awb[weight]" value="<?=$awb_details['weight']?>" size="40" /></td>
            </tr>      

            <tr valign="top">
                <th scope="row">Valoare asigurata:</th>
                <td><input type="number" min="0" name="awb[insurance_val]" value="<?=$awb_details['insurance_val']?>" size="40" /></td>
            </tr>  

            <tr valign="top">
                <th scope="row">Servicii:</th>
                <td>
                    <select name="awb[service]">
                        <option value="1" <?= esc_attr(get_option('bookurier_services')) == '1' ? 'selected="selected"' : ''; ?>>Bucuresti 24h</option>
                        <option value="2" <?= esc_attr(get_option('bookurier_services')) == '2' ? 'selected="selected"' : ''; ?>>Bucuresti Express</option>
                        <option value="3" <?= esc_attr(get_option('bookurier_services')) == '3' ? 'selected="selected"' : ''; ?>>Metropolitan</option>
                        <option value="9" <?= esc_attr(get_option('bookurier_services')) == '9' ? 'selected="selected"' : ''; ?>>National 24h</option>
                    </select>
                </td>
            </tr> 
        </table>

        <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Genereaza AWB"></p>

    </form>
</div>