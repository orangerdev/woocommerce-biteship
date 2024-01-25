<div class="wrap">
    <h1>Biteship Plugin Options</h1>
    <form method="post" action="options.php">
        <?php settings_fields("ordv-biteship"); ?>
        <table class="form-table">
            <tr valign="top" class="wpex-custom-admin-screen-background-section">
                <th scope="row"><?php esc_html_e( 'Select Example', 'text-domain' ); ?></th>
                <td>
                    <select name="theme_options[select_example]">
                        <?php
                        $options = array(
                            '1' => esc_html__( 'Option 1', 'text-domain' ),
                            '2' => esc_html__( 'Option 2', 'text-domain' ),
                            '3' => esc_html__( 'Option 3', 'text-domain' ),
                        );
                        foreach ( $options as $id => $label ) { ?>
                            <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value, $id, true ); ?>>
                                <?php echo strip_tags( $label ); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
