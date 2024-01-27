<?php    
    
    $order_biteship_id   = get_post_meta( $post_id, 'order_biteship_id', true );    

    if( $order_biteship_id ):

        $detail_data = ordv_biteship_fn_detail_data_tracking( $order_biteship_id );
        $data_tracking  = $detail_data->trackings; 
        $n_data             = count($data_tracking);
        $latest_data_n      = ($n_data - 1);

        $latest_status = $data_tracking[$latest_data_n]->logistic_status->description;

        $hasil_cek_resi = array(
            'no_resi'       => $detail_data->awb_number,
            'order_id'      => $detail_data->order_id,
            'pengirim'      => $detail_data->consigner->name,
            'o_suburb'      => $detail_data->origin->suburb_name,
            'o_city'        => $detail_data->origin->city_name,
            'penerima'      => $detail_data->consignee->name,
            'd_suburb'      => $detail_data->destination->suburb_name,
            'd_city'        => $detail_data->destination->city_name,
            'kurir'         => $detail_data->courier->name,
            'tgl_kirim'     => $detail_data->creation_date,
            'latest_status' => $latest_status

        );

        $data_tracking = array_reverse($data_tracking);


?>  

    <div class="status-paket">

        <table class="table" style="width:100%;">
            <tbody>
                <tr>
                    <td>
                        No Resi/AWB<br />
                        <strong><?php echo $hasil_cek_resi['no_resi']; ?></strong>
                    </td>
                    <td>
                        Tanggal Pengiriman<br />
                        <strong>
                            <?php 
                                $str_time = $hasil_cek_resi['tgl_kirim'];                                 
                                $str_time = substr( $str_time, 0, 19 );
                                $str_time = str_replace('T', ' ', $str_time);

                                $datetime  = strtotime($str_time);
                                $new_date  = date('d-m-Y H:i:s', $datetime);

                                $new_date_7 = date('d-m-Y H:i:s', strtotime('+7 hours', strtotime($new_date)));
                                echo $new_date_7;
                            ?>
                        </strong>
                    </td>
                    <td>
                        Pengirim
                        <br />
                        <strong><?php echo $hasil_cek_resi['pengirim'];?></strong><br />
                        <strong><?php echo $hasil_cek_resi['o_suburb'].', '.$hasil_cek_resi['o_city']; ?></strong>
                    
                    </td>
                    <td>
                        Penerima
                        <br />
                        <strong><?php echo $hasil_cek_resi['penerima'];?></strong><br />
                        <strong><?php echo $hasil_cek_resi['d_suburb'].', '.$hasil_cek_resi['d_city']; ?></strong>                        
                    </td>
                    
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-bottom:30px;"></div>

    <div class="data-paket">        
        <table class="table is-striped">
            <thead>
                <tr>
                    <th>Tanggal & Waktu</th>
                    <th>Biteship Status</th>
                    <th>Logistic Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_tracking as $key => $data): ?>
                    <tr>                    
                        <td class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker <?php echo ($key === 0) ? 'is-danger': ''; ?>"></div>
                                <div class="timeline-content">
                                    <?php 
                                        $str_time = $data->created_date; 
                                        $str_time = substr( $str_time, 0, 19 );
                                        $str_time = str_replace('T', ' ', $str_time);

                                        $datetime  = strtotime($str_time);
                                        $new_date  = date('d-m-Y H:i:s', $datetime);

                                        $new_date_7 = date('d-m-Y H:i:s', strtotime('+7 hours', strtotime($new_date)));
                                        echo $new_date_7;
                                    ?>
                                </div>
                            </div>                                                
                        </td>
                        <td>
                            <p style="font-size:13px;">
                                <?php
                                    echo $data->biteship_status->description; 
                                ?>
                            </p>
                        </td>
                        <td>
                            <p style="font-size:13px;">
                                <?php echo $data->logistic_status->description; ?>
                            </p>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>



<?php
    else:
        // do nothing
    endif;
?>