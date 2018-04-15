<?php if (!defined('ABSPATH')) { exit; }

/**
 * Template file of a table on the DWS Prices List.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     \Deep_Web_Solutions\Front\DWS_PricesList
 */

?>

<table class="shop_table" style="table-layout: fixed; width: 100%; white-space: normal;">
	<thead class="dws_text-center dws_bold">
	<tr>
		<?php foreach ($header as $column): ?>
			<td style="font-weight: 700;"><?php echo $column; ?></td>
		<?php endforeach; ?>
	</tr>
	</thead>
	<tbody class="dws_text-center">
	<?php foreach ($body as $row): ?>
		<tr>
			<?php foreach ($row as $i => $column): ?>
				<td <?php if ($i === 0) echo 'width="40%"' ?>><?php echo $column; ?></td>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>