<?php

    $order_id   = $post_id;
    $order      = wc_get_order( $order_id );
    $get_item   = $order->get_items();
    
    $detail_shipping = $order->get_items( 'shipping' );
    
    foreach ($order->get_items() as $item_id => $item ) {
        $product_name   = $item->get_name(); // Get the item name (product name)
        $item_quantity  = $item->get_quantity(); // Get the item quantity
        
        echo '<p>'.$product_name.' x '.$item_quantity.'</p>';
        
    }

    $kurir_name = '';
    foreach( $detail_shipping as $item_id => $item ){
        // Get the data in an unprotected array
        $item_data = $item->get_data();
        $shipping_data_name = $item_data['name'];
        $kurir_name         = $shipping_data_name;
    }

    echo '<p><strong>Kurir:</strong> '.$kurir_name.'</p>';

    
    $name = $order->get_billing_first_name().' '.$order->get_billing_last_name();
    $order_biteship_id = get_post_meta( $order_id, 'order_biteship_id', true );
    $no_resi = get_post_meta( $order_id, 'no_resi', true );    
    $tracking_status = get_post_meta( $order_id, 'status_tracking', true );    
    $code_status = get_post_meta( $order_id, 'status_code', true );

?>

<?php if( $order_biteship_id): ?>
    <p><strong>Tracking ID:</strong> <?php echo $order_biteship_id; ?></p>
<?php else: ?>
    <!-- do nothing -->
<?php endif; ?>

<?php if( $no_resi): ?>
    <p><strong>AWB:</strong> <?php echo $no_resi; ?></p>
<?php else: ?>
    <!-- do nothing -->
<?php endif; ?> 


<?php 

    if( $tracking_status ): 
    $tracking_status = str_replace('[receiver_name]', $name, $tracking_status);

?>
    <p class="biteship-status-<?php echo $order_id; ?>"><strong>Status:</strong> <?php echo $tracking_status; ?></p>
<?php else: ?>
    <!-- do nothing -->
<?php endif; ?>        


<?php 
    if( $order->has_shipping_method('ordv-biteship') ):
    
        if( $order->is_paid()||$order->has_status('processing')):
?>
            <?php 
                $order_biteship_id = get_post_meta( $order_id, 'order_biteship_id', true );
                if( ! $order_biteship_id ):
            ?>
                <p style="margin-top:8px;">
                    <a class="button button-secondary btn-create-order-biteship" data_order_id="<?php echo $order_id; ?>" href="#">Buat Order di Biteship</a>        
                </p>
            <?php endif; ?>

        <?php endif; ?>

    <?php 
        else:
            // do nothing
        endif; 
    ?>

<?php
    if ( $order->has_status('waiting-delivery')):
        
        $order_biteship_id = get_post_meta( $order_id, 'order_biteship_id', true );
        
        $get_order_data = ordv_biteship_fn_get_biteship_order_data( $order_biteship_id );
        $awb_number     = $get_order_data['awb_number'];
        $tracking_status = $get_order_data['tracking_status'];

        update_post_meta( $order_id, 'status_tracking',  $tracking_status );

        $no_resi = get_post_meta( $order_id, 'no_resi', true );

        if( ! $no_resi | '' == $no_resi){
            update_post_meta( $order_id, 'no_resi',  $awb_number );
        }else{
            // do nothing
        }

        $pickup_code = get_post_meta( $order_id, 'pickup_code', true );
        $is_activate = get_post_meta( $order_id, 'is_activate', true );
        
?>
    <?php 
        if( $order->has_shipping_method('ordv-biteship') ): 
            if( '1' !== $is_activate ):           
    ?>
                <p style="margin-top:8px;">                
                    <a href="#" class="button wc-action-button open-dialog" data_order_id="<?php echo $order_id; ?>">Aktifkan Pickup Order</a>
                </p>
            <?php else: ?>
                <!--  hide button -->
            <?php endif; ?>

    <?php 
        else:
            // do nothing
        endif; 
    ?>

<?php endif; ?>