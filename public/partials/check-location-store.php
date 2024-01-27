<?php
$loc_name = carbon_get_theme_option( 'biteship_location_default' );
$loc_cookie  = isset($_COOKIE['wb_loc']) ? $_COOKIE['wb_loc'] : '';
if ( $loc_cookie ) :
    $loc_name = $loc_cookie;
endif;

if ( isset( $_GET['attribute_pa_lokasi'] ) && !empty( $_GET['attribute_pa_lokasi'] ) ) :
    $loc_name = $_GET['attribute_pa_lokasi'];
endif;

$term = get_term_by('slug', $loc_name, 'pa_lokasi');
if ($term) :
    $loc_name = $term->name;
endif;
?>
<?php
if ( $loc_name ) :
?>
    <div class="wb-loc-store">
        <p>Rank Sports Location: <b><?php echo $loc_name; ?></b></p>
    </div>
<?php
endif;
?>
<div class="wb-check-location-store-wrap">
    <input type="hidden" name="wb_product_id" id="wb_product_id" value="<?php echo get_the_ID(); ?>">
    <button type="button" class="wb-check-location-store-open-btn"><?php _e('Cek Lokasi Toko Lainnya','ordv-biteship'); ?></button>
</div>
<div class="wb-popup-wrap wb-check-location-store-popup"> 
    <div class="wb-popup">
        <div class="wb-popup-header">
            <h3><?php _e('Cari Toko','ordv-biteship'); ?></h3>
            <span class="wb-check-location-store-close-btn">
                <img src="<?php echo ORDV_BITESHIP_URI; ?>/public/img/x-icon.svg" alt="x">
            </span>
        </div>
        <div class="wb-popup-content">
            <div class="wb-popup-content-loading">
                <p><?php _e('Sedang mengambil data toko ...','ordv-biteship'); ?></p>
            </div>
            <div class="wb-location-stores">
            </div>
        </div>
    </div>
</div>