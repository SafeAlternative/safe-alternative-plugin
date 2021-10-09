<?php 
    $avail_shipping_methods = WC()->shipping->get_shipping_methods();
    unset($avail_shipping_methods['flat_rate'], $avail_shipping_methods['free_shipping'], $avail_shipping_methods['local_pickup']);

    $current_order = get_option('safealternative_shipping_methods_order', '');
    if (!empty($current_order)) {
        $sorted_shipping_methods = array();
        foreach(explode(',',$current_order) as $method) {
            if(isset($avail_shipping_methods[$method])) {
                $sorted_shipping_methods[$method] = $avail_shipping_methods[$method];
                unset($avail_shipping_methods[$method]);
            }
        }
        $sorted_shipping_methods = array_merge($sorted_shipping_methods, $avail_shipping_methods);
    } else {
        $sorted_shipping_methods = $avail_shipping_methods;
    }
?>

<style>
    .wrap p.submit {
        margin: 20px 0 -10px 0;
        padding: 0;
    }
    .wrap table {
        width: 70%;
        max-width: 775px;
    }
    .wrap input, 
    .wrap button.button {
        width: 100% !important;
        max-width: 100% !important;
    }
    .list-group-item {
        padding: 6px 10px;
        background: white;
        margin: 5px 0;
        border: 1px solid lightgray;
        cursor: pointer;
    }
    .safealternative-background-class {
        background: #50d0e388;
        opacity: 0.5;
        color: white;
        border-color: white;
    }
    .wrap th {
        width: 40%;
        max-width: 215px;
    }
    h2 {
        margin: 0.2em 0;
    }
</style>

<div class="wrap">
    <h1>SafeAlternative - Ordoneaza metodele de livrare</h1>
    <p>Pentru a reordona lista de curieri din checkout tineti apasat si trageti de element in sus/jos.</p>
    <br>
    <form action="options.php" method="post">
        <?php
            settings_fields( 'safealternative_shipping_methods_order' );
            do_settings_sections( 'safealternative_shipping_methods_order' );
        ?>
        <table>
            <input type="hidden" name="safealternative_shipping_methods_order" value="<?= $current_order ?>">
            <tr>
                <th align="left">Ordinea metodelor de livrare:</th>
                <td colspan="2">
                    <div id="list">
                        <?php foreach($sorted_shipping_methods as $shipping_method): ?>
                        <div class="list-group-item" data-id="<?= $shipping_method->id ?>"><?= trim($shipping_method->get_title()) ?: trim($shipping_method->get_method_title()) ?></div>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2"><?php submit_button('Salveaza modificarile'); ?></td>
            </tr>

            <tr>
                <td colspan="2"><p><input type="button" class="button button-secondary resetOrder" value="Reseteaza ordinea"></p><br></td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:center;">© Copyright <script>document.write(new Date().getFullYear());</script> | Un sistem prietenos de generare AWB-uri creat de <a href="https://safe-alternative.ro/" target="_blank">SafeAlternative</a>.</td>
            </tr>
        </table>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.13.0/Sortable.min.js"></script>

<script>
    let sortable = new Sortable(document.getElementById('list'), {
        animation: 200,
        ghostClass: 'safealternative-background-class',
        onChange: function(e) {
            let order = sortable.toArray().toString();
            jQuery('input[name=safealternative_shipping_methods_order]').val(order);
        }
    });

    jQuery('body').on('click', '.resetOrder', function(){
        jQuery('input[name=safealternative_shipping_methods_order]').val('');
        jQuery('#submit').click();
    });

    jQuery($ => {
        jQuery('input[name=safealternative_shipping_methods_order]').val(sortable.toArray().toString());
    });
</script>

<?php 
