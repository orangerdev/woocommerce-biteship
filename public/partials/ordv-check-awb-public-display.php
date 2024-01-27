<?php

?>

<div id="notices-area"></div>

<div id="cek-resi-area">
    <label>Masukan Nomor Resi / AWB anda</label>
    <form method="POST" id="check-resi" class="check-resi" name="check-resi">
        <input type="text" name="no_resi" id="no-resi" class="input" placeholder="Contoh: JD1236457654"  style="width:78%;" />        
        <?php wp_nonce_field( 'cek_no_resi', 'cek_no_resi_nonce' ); ?>
        <input type="submit" class="button is-dark" style="width:20%;" value="Cek Resi">
    </form>
</div>


<div id="hasil-data">
    
</div>

<div id="detail-paket">
    
</div>


