<tr class="wc_shipping_fan_collectpoint">
	<th><?php esc_html_e( 'Alege punct CollectPoint', 'safealternative-plugin' ) ?> <abbr class="required" title="required">*</abbr></th>
	<td>
		<select name="safealternative_fan_collectpoint" id="safealternative_fan_collectpoint_select" style="width: 100%;">
			<?php foreach( $collectpoints as $collectpoint ) : ?>
				<option value="<?= esc_html( $collectpoint['Strada'] ) ?>"><?= ucwords(strtolower($collectpoint['Strada'])) ?> - <?= $collectpoint['Distanta'] ?> km</option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>
