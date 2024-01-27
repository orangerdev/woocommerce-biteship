<tr valign="top">
    <td class="forminp" colspan="2">
        <h3><?php _e( 'Logistics', 'ordv-biteship' ); ?></h3>
        <table class="logistic-options" style="width:100%">
            <thead>
                <tr>
                    <th style="width:5%"></th>
                    <th style="width:10%"><?php esc_attr_e( 'Active', 'ordv-biteship' ); ?></th>
                    <th style="width:20%"><?php esc_attr_e( 'Name', 'ordv-biteship' ); ?></th>
                    <th style="width:15%"><?php esc_attr_e( 'Description', 'ordv-biteship' ); ?></th>
                    <th style="width:20%"><?php esc_attr_e( 'Type', 'ordv-biteship' ); ?></th>
                    <th style="width:15%"><?php esc_attr_e( 'Tier', 'ordv-biteship' ); ?></th>
                    <th style="width:15%"><?php esc_attr_e( 'Duration', 'ordv-biteship' ); ?></th>
                </tr>
            </thead>
            <tbody class="logistics-sortable">
                <?php
                if ( $logistics ) :
                    foreach ( $logistics as $key => $logistic ) :
                    ?>
                        <tr>
                            <td><span class="logistic-sort dashicons dashicons-menu"></span></td>
                            <td>
                                <input type="hidden" name="logistics_order[]" value="<?php echo $key; ?>">
                                <input type="checkbox" name="logistics_enabled[]" value="<?php echo $key; ?>" <?php echo in_array($key,$logistics_enabled) ? 'checked' : ''; ?>>
                            </td>
                            <td><?php echo $logistic->courier_name; ?> <?php echo $logistic->courier_service_name; ?></td>
                            <td><?php echo $logistic->description; ?></td>
                            <td><?php echo $logistic->shipping_type; ?></td>
                            <td><?php echo $logistic->tier; ?></td>
                            <td><?php echo $logistic->shipment_duration_range; ?> (<?php echo $logistic->shipment_duration_unit; ?>)</td>
                        </tr>
                    <?php
                    endforeach;
                else:
                ?>
                    <tr>
                        <td colspan="7"><p style="text-align:center">Empty Data</p></td>
                    </tr>
                <?php
                endif;
                ?>
            </tbody>
        </table>
    </td>
</tr>
<script>
jQuery(document).ready(function($) {
    $( ".logistics-sortable" ).sortable();
} );
</script>