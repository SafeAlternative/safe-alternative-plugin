<tr class="wc_shipping_sameday_lockers">
	<th>
		<?php esc_html_e('Alege punct EasyBox', 'safealternative-plugin') ?> <abbr class="required" title="required">*</abbr>
	</th>
	<td>
		<select name="safealternative_sameday_lockers" id="safealternative_sameday_lockers_select" style="width: 100%;">
			<?php 
			if ($gasit) :
				foreach ($lockers as $locker) : ?>
				<option value="<?= esc_html($locker->id) ?>"> <?= ucwords(strtolower($locker->city)) . ' - ' . ucwords(strtolower($locker->name)) . ' - ' . ucwords(strtolower($locker->address))  ?> </option>
				<?php endforeach;
			else :
				foreach ($lockers as $locker) : ?>
				<option value="<?= esc_html($locker->id) ?>"> <?= ucwords(strtolower($locker->name)) . ' - ' . ucwords(strtolower($locker->address))  ?> </option>
				<?php endforeach;
			endif; ?>
		</select>
	</td>
</tr>