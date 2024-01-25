<?php

$data_tracking      = $detail_data->courier->history;
$n_data             = count($data_tracking);
$latest_data_n      = ($n_data - 1);

$latest_status = $data_tracking[$latest_data_n]->logistic_status->note;

$hasil_cek_resi = array(
    'no_resi'       => $detail_data->courier->tracking_id,
    'order_id'      => $detail_data->id,
    'pengirim'      => $detail_data->origin->contact_name,
    'o_suburb'      => $detail_data->origin->note,
    'o_city'        => $detail_data->origin->address,
    'penerima'      => $detail_data->destination->contact_name,
    'd_suburb'      => $detail_data->destination->note,
    'd_city'        => $detail_data->destination->address,
    'kurir'         => $detail_data->courier->name,
    'tgl_kirim'     => $detail_data->delivery->datetime,
    'latest_status' => $latest_status
);

?>

    <div class="hasil-cek-resi">
        
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <h4><strong><?php echo $hasil_cek_resi['latest_status']?></strong></h4>
                        </td>
                        <td class="text-center">
                            <h4><strong><?php echo $hasil_cek_resi['kurir']; ?></strong></h4>
                        </td>
                    </tr>
                </tbody>
            </table>

    </div>

    <div class="status-paket">

        <table class="table">
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


