</main>

<?php do_action('tailpress_content_end'); ?>

</div>

<?php do_action('tailpress_content_after'); ?>

<footer id="colophon" class="site-footer bg-white py-12" role="contentinfo">
    <?php do_action('tailpress_footer'); ?>

    <div class="container mx-auto text-center text-gray-500">
        <?php echo "<p>GDaily @ 2012-" . date("Y") . " (" . (date("Y") - 2012) . " years)</p>"; ?>
        <?php
        $deploy_info_path = get_stylesheet_directory() . '/deploy_info.json';
        if ( file_exists( $deploy_info_path ) ) {
            $json = file_get_contents( $deploy_info_path );
            $data = json_decode( $json, true );
            if ( is_array( $data ) && ! empty( $data['deployed_at'] ) ) {
                $deployed_at = $data['deployed_at'];
                $commit = isset( $data['commit'] ) ? $data['commit'] : '';
                $time = date_i18n( 'Y-m-d H:i:s', strtotime( $deployed_at ) );
                echo '<p class="text-sm">部署時間：' . esc_html( $time ) . ( $commit ? ' — ' . esc_html( substr( $commit, 0, 7 ) ) : '' ) . '</p>';
            }
        }
        ?>

    </div>
</footer>



<?php wp_footer(); ?>

</body>

</html>
