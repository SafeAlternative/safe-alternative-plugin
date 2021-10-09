<tr class="wc_shipping_dpd_collect_points">
	<th>
		<?php esc_html_e('Alege punct de ridicare', 'safealternative-plugin') ?> <abbr class="required" title="required">*</abbr>
	</th>
	<td>
		<select name="safealternative_dpd_box" id="safealternative_dpd_box_select" style="width: 100%;">
			<?php 
				foreach ($collect_points as $collect_point) : ?>
				<option value="<?= esc_html($collect_point['id']) ?>"> <?= ucwords(strtolower($collect_point['address']['fullAddressString'])) ?> </option>
				<?php endforeach;
 			?>
		</select>
	</td>
</tr>