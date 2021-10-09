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
<h2>Genereaza AWB pentru GLS</h2>
<br>
    <form method="post" action="<?php echo plugin_dir_url( __DIR__ ) ?>generate.php?order_id=<?php echo $_GET['order_id'] ?>">

        <input type="hidden" name="awb[domain]" value="<?=site_url()?>" />

        <table class="form-table">
        
            <tr valign="top">
                <th scope="row">ID Expeditor:</th>
                <td><input type="text" name="awb[senderid]" value="<?=$awb_details['senderid']?>" size="40" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Precompleteaza alt expeditor:</th>
                <td>
                    <?php
                        $other_senders = maybe_unserialize(get_option('GLS_other_senders'));
                        // dd($other_senders);
                        if(empty($other_senders)) 
                            $other_senders = array();
                            ?>  <select class="other_sender" disabled></select> <?php
                        if(count($other_senders)){ ?>
                            <select class="other_sender" name="other_sender">
                                    <option value="other_sender" 
                                        data-name="<?php echo $awb_details['sender_name'] ?? null; ?>"
                                        data-address="<?php echo $awb_details['sender_address'] ?? null; ?>"
                                        data-city="<?php echo $awb_details['sender_city'] ?? null; ?>"
                                        data-zipcode="<?php echo $awb_details['sender_zipcode'] ?? null; ?>"
                                        data-phone="<?php echo $awb_details['sender_phone'] ?? null; ?>"
                                        data-email="<?php echo $awb_details['sender_email'] ?? null; ?>"
                                        data-contact="<?php echo $awb_details['contact'] ?? null; ?>">                                    
                                    Expeditor implicit
                                    </option>
                                <?php foreach($other_senders as $other_sender){
                                    ?><option value="other_sender"
                                                data-name="<?php echo $other_sender['name'] ?? null; ?>"
                                                data-address="<?php echo $other_sender['address'] ?? null; ?>"
                                                data-city="<?php echo $other_sender['city'] ?? null; ?>"
                                                data-zipcode="<?php echo $other_sender['zipcode'] ?? null; ?>"
                                                data-phone="<?php echo $other_sender['phone'] ?? null; ?>"
                                                data-email="<?php echo $other_sender['email'] ?? null; ?>"
                                                data-contact="<?php echo $other_sender['contact'] ?? null; ?>">
                                        <?php echo $other_sender['name']; ?>, 
                                        <?php echo $other_sender['city']; ?>, 
                                        <?php echo $other_sender['address']; ?>
                                    </option><?php
                                } ?>
                            </select> <?php
                        }
                    ?>                    
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Expeditor:</th>
                <td>
                    <input type="text" name="awb[sender_name]" value="<?=$awb_details['sender_name']?>" size="40" />                
                </td>
            </tr>            

            <tr valign="top">
                <th scope="row">Adresa expeditor:</th>
                <td><input type="text" name="awb[sender_address]" value="<?=$awb_details['sender_address']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Localitate expeditor:</th>
                <td><input type="text" name="awb[sender_city]" value="<?=$awb_details['sender_city']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Cod postal expeditor:</th>
                <td><input type="text" name="awb[sender_zipcode]" value="<?=$awb_details['sender_zipcode']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Tara expeditor:</th>
                <td><input type="text" name="awb[sender_country]" value="<?=$awb_details['sender_country']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Contact expeditor:</th>
                <td><input type="text" name="awb[sender_contact]" value="<?=$awb_details['sender_contact']?>" size="40" /></td>
            </tr>                    
            
            <tr valign="top">
                <th scope="row">Telefon expeditor:</th>
                <td><input type="text" name="awb[sender_phone]" value="<?=$awb_details['sender_phone']?>" size="40" /></td>
            </tr>       
            
            <tr valign="top">
                <th scope="row">Email expeditor:</th>
                <td><input type="text" name="awb[sender_email]" value="<?=$awb_details['sender_email']?>" size="40" /></td>
            </tr>  

            <tr valign="top">
                <th scope="row">Nume destinatar:</th>
                <td><input type="text" name="awb[consig_name]" value="<?=$awb_details['consig_name']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Adresa destinatar:</th>
                <td><input type="text" name="awb[consig_address]" value="<?=$awb_details['consig_address']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Localitate destinatar:</th>
                <td><input type="text" name="awb[consig_city]" value="<?=$awb_details['consig_city']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Cod postal destinatar:</th>
                <td><input type="text" name="awb[consig_zipcode]" value="<?=$awb_details['consig_zipcode']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Tara destinatar:</th>
                <td><input type="text" name="awb[consig_country]" value="<?=$awb_details['consig_country']?>" size="40" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Contact destinatar:</th>
                <td><input type="text" name="awb[consig_contact]" value="<?=$awb_details['consig_contact']?>" size="40" /></td>
            </tr>                    
            
            <tr valign="top">
                <th scope="row">Telefon destinatar:</th>
                <td><input type="text" name="awb[consig_phone]" value="<?=$awb_details['consig_phone']?>" size="40" /></td>
            </tr>       
            
            <tr valign="top">
                <th scope="row">Email destinatar:</th>
                <td><input type="text" name="awb[consig_email]" value="<?=$awb_details['consig_email']?>" size="40" /></td>
            </tr>  
                
            <tr valign="top">
                <th scope="row">Informatii:</th>
                <td><input type="text" name="awb[content]" value="<?=$awb_details['content']?>" size="40" /></td>
            </tr>                   
            
            <tr valign="top">
                <th scope="row">Referinta client:</th>
                <td><input type="text" name="awb[clientref]" value="<?=$awb_details['clientref']?>" size="40" /></td>
            </tr>   
            
            <tr valign="top">
                <th scope="row">Referinta cod:</th>
                <td><input type="text" name="awb[codref]" value="<?=$awb_details['codref']?>" size="40" /></td>
            </tr>   

            <tr valign="top">
                <th scope="row">Valoare Ramburs:</th>
                <td><input type="text" name="awb[codamount]" value="<?=$awb_details['codamount']?>" size="40" /></td>
            </tr>     
            
            <tr valign="top">
                <th scope="row">Numar colete:</th>
                <td><input type="number" step="1" min="0" name="awb[pcount]" value="<?=$awb_details['pcount']?>" size="40" /></td>
            </tr>     

            <tr valign="top">
                <th scope="row">Data ridicare:</th>
                <td><input type="text" name="awb[pickupdate]" value="<?=$awb_details['pickupdate']?>" size="40" /></td>
            </tr> 

            <tr valign="top">
                <th scope="row">Servicii:</th>
                <td>
                    <select name="awb[services]">
                        <option value="" <?php echo esc_attr(get_option('GLS_services')) == '' ? 'selected="selected"' : ''; ?>>Niciunul</option>
                        <option value="FDS" <?php echo esc_attr(get_option('GLS_services')) == 'FDS' ? 'selected="selected"' : ''; ?>>FDS</option>
                        <option value="FDS+FSS" <?php echo esc_attr(get_option('GLS_services')) == 'FDS+FSS' ? 'selected="selected"' : ''; ?>>FDS + FSS</option>
                        <option value="SM2" <?php echo esc_attr(get_option('GLS_services')) == 'SM2' ? 'selected="selected"' : ''; ?>>SM2</option>
                    </select>
                </td>
            </tr> 

            <tr valign="top">
                <th scope="row">Format print:</th>
                <td>
                    <select name="awb[printertemplate]">
                        <option value="A6" <?php echo esc_attr(get_option('GLS_printertemplate')) == 'A6' ? 'selected="selected"' : ''; ?>>A6 format, blank label</option>
                        <option value="A6_PP" <?php echo esc_attr(get_option('GLS_printertemplate')) == 'A6_PP' ? 'selected="selected"' : ''; ?>>A6 format, preprinted label</option>
                        <option value="A6_ONA4" <?php echo esc_attr(get_option('GLS_printertemplate')) == 'A6_ONA4' ? 'selected="selected"' : ''; ?>>A6 format, printed on A4</option>
                        <option value="A4_2x2" <?php echo esc_attr(get_option('GLS_printertemplate')) == 'A4_2x2' ? 'selected="selected"' : ''; ?>>A4 format, 4 labels on layout 2x2</option>
                        <option value="A4_4x1" <?php echo esc_attr(get_option('GLS_printertemplate')) == 'A4_4x1' ? 'selected="selected"' : ''; ?>>A4 format, 4 labels on layout 4x1</option>
                        <option value="T_85x85" <?php echo esc_attr(get_option('GLS_printertemplate')) == 'T_85x85' ? 'selected="selected"' : ''; ?>>85x85 mm format for thermal labels </option>
                    </select>
                </td>
            </tr>   
        </table>

        <p class="submit"><input type="submit" name="generate_awb" id="submit" class="button button-primary" value="Genereaza AWB"></p>

    </form>
</div>

<script>
    $ = jQuery;
    $(".other_sender").change(function() {
        $( "input[name*='sender_name']" ).val($(this).find(':selected').data('name'));
        $( "input[name*='sender_address']" ).val($(this).find(':selected').data('address'));
        $( "input[name*='sender_city']" ).val($(this).find(':selected').data('city'));
        $( "input[name*='sender_zipcode']" ).val($(this).find(':selected').data('zipcode'));
        $( "input[name*='sender_phone']" ).val($(this).find(':selected').data('phone'));
        $( "input[name*='sender_email']" ).val($(this).find(':selected').data('email'));
        $( "input[name*='sender_contact']" ).val($(this).find(':selected').data('contact'));
    });
</script>