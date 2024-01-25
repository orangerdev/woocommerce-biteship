<?php
    $data_tracking  = $detail_data->courier->history;
    $data_tracking = array_reverse($data_tracking);
?>

<div class="data-paket">
    <h4><strong>Detail Status Paket</strong></h4>
    <table class="table is-striped">
        <thead>
            <tr>
                <th>Tanggal & Waktu</th>
                <th>Status</th>
                <th>Description</th>
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
                                    $str_time = $data->updated_at; 
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
                                echo $data->status; 
                            ?>
                        </p>
                    </td>
                    <td>
                        <p style="font-size:13px;">
                            <?php echo $data->note; ?>
                        </p>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>