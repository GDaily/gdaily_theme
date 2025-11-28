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
        foreach ($check_paths as $p) {
            if (file_exists($p)) {
                $deploy_info_path = $p;
                break;
            }
        }

        if ($deploy_info_path) {
            $json = @file_get_contents($deploy_info_path);
            $data = @json_decode($json, true);
        }

        if (!isset($data['deployed_at'])) {
            // 找不到 JSON 或 JSON 壞掉 → 今日 HK
            $dt = new DateTime('now', new DateTimeZone('Asia/Hong_Kong'));
            $time_str = $dt->format('Y-m-d H:i:s');
            $commit_short = '';
        } else {
            // JSON 正常
            $dt = new DateTime($data['deployed_at'], new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Asia/Hong_Kong'));
            $time_str = $dt->format('Y-m-d H:i:s');
            $commit_short = substr($data['commit'] ?? '', 0, 7);
        }

        // ★ Footer 最簡短顯示（含 HK）
        echo '<p class="text-[11px] text-gray-500 mt-2">
        Deploy：' . esc_html($time_str) . '（HK）' .
            ($commit_short ? ' · ' . esc_html($commit_short) : '') . '
      </p>';
        ?>


    </div>
</footer>



<?php wp_footer(); ?>

</body>

</html>