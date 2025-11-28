</main>

<?php do_action('tailpress_content_end'); ?>

</div>

<?php do_action('tailpress_content_after'); ?>

<footer id="colophon" class="site-footer bg-white py-12" role="contentinfo">
    <?php do_action('tailpress_footer'); ?>

    <div class="container mx-auto text-center text-gray-500">
        <?php echo "<p>GDaily @ 2012-" . date("Y") . " (" . (date("Y") - 2012) . " years)</p>"; ?>
        <?php
        // 支援 child / parent theme 路徑，自動尋找 deploy_info.json
        $check_paths = array(
            get_stylesheet_directory() . '/deploy_info.json',
            get_template_directory() . '/deploy_info.json',
        );

        $deploy_info_path = null;
        foreach ( $check_paths as $p ) {
            if ( file_exists( $p ) ) {
                $deploy_info_path = $p;
                break;
            }
        }

        if ( ! $deploy_info_path ) {
            // 除錯用 comment，不會顯示給使用者
            echo '<!-- deploy_info.json not found. Checked: ' . esc_html( implode( ', ', $check_paths ) ) . ' -->';
        } else {
            $json = @file_get_contents( $deploy_info_path );
            $data = @json_decode( $json, true );
            if ( json_last_error() === JSON_ERROR_NONE && is_array( $data ) && ! empty( $data['deployed_at'] ) ) {
                $deployed_at = $data['deployed_at'];
                $commit = isset( $data['commit'] ) ? $data['commit'] : '';

                $ts = strtotime( $deployed_at );
                if ( $ts !== false && $ts > 0 ) {
                    $time = date_i18n( 'Y-m-d H:i:s', $ts );
                    echo '<p class="text-sm">部署時間：' . esc_html( $time ) . ( $commit ? ' — ' . esc_html( substr( $commit, 0, 7 ) ) : '' ) . '</p>';
                } else {
                    echo '<!-- deploy_info.json has invalid deployed_at: ' . esc_html( $deployed_at ) . ' -->';
                }
            } else {
                echo '<!-- deploy_info.json invalid JSON or missing deployed_at -->';
            }
        }
        ?>

    </div>
</footer>



<?php wp_footer(); ?>

</body>

</html>