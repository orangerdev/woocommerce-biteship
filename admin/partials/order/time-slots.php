<?php 

						$list_time = ordv_biteship_fn_get_pickup_time();


						$list_radio = '<li>';
						$list_radio .= '</li>';

						$list_data = array();

						foreach ($list_time as $slot) {
							
							$start_time = $slot->start_time;
							$end_time   = $slot->end_time;

							$date_slot = substr( $start_time, 0, 10 );
							$label_start_time = substr( $start_time,-14,-9 ).' WIB';
							$label_end_time   = substr( $end_time,-14,-9 ).' WIB';
							$value_slot = $start_time.'|'.$end_time;

							$list_data[] = array(
								'date_slot'     => $date_slot,
								'label_slot'    => $label_start_time.' - '.$label_end_time,
								'value_slot'    => $value_slot
							);

						}

						$groupedItems = array();
						
						foreach($list_data as $item)
						{
							$groupedItems[$item['date_slot']][] = $item;
						}
						
						$groupedItems = array_values($groupedItems);

						
					?>
				
					<form method="POST" name="set-pickup-time" id="set-pickup-time" action="<?php echo admin_url('admin.php'); ?>">

						<?php 							

							foreach ($groupedItems as $key => $value) {
								
								$date = strtotime($value[$key]['date_slot']);
								$new_date_format = date('d F Y', $date);
								
								echo '<ul>';
								echo '<h4>'.$new_date_format.'</h4>';
								foreach ($value as $v) {
									?>
										<li>
											<input type="radio" id="pickup_time" name="pickup_time" value="<?php echo $v['value_slot']; ?>">
											<label for="pickup_time"><?php echo $v['label_slot']; ?></label>
										</li>
									<?php
								}
								echo '</ul>';
							}

						?>
						<input type="hidden" id="order_id" name="order_id" value="<?php echo $order_id; ?>" />

						<input class="button button-primary" type="submit" value="Pilih Jadwal Pickup" style="margin-top:16px;">
					</form>