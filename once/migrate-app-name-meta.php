<?php
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/migrate-app-name/', array(
        'methods' => 'POST',
        'callback' => function () {
            $posts = get_posts(array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'numberposts' => -1,
            ));
            $count = 0;
            foreach ($posts as $post) {
                $old_value = get_post_meta($post->ID, 'APP_NAME', true);
                if ($old_value) {
                    $new_value = carbon_get_post_meta($post->ID, 'app_name');
                    if (empty($new_value)) {
                        carbon_set_post_meta($post->ID, 'app_name', $old_value);
                        $count++;
                    }
                    delete_post_meta($post->ID, 'APP_NAME');
                }
            }
            return ['migrated' => $count];
        },
        'permission_callback' => function () {
            return current_user_can('manage_options');
        }
    ));
});

add_action('admin_menu', function () {
    // 建立父選單，但 callback 是一個空函式，只放置 JS 讓頁面自動跳轉
    add_menu_page(
        '遷移工具',
        '遷移工具',
        'manage_options',
        'migration-tools',
        function () {
            ?>
<script>
// 頁面載入時直接跳轉到子選單頁面
window.location.href = '<?php echo admin_url('admin.php?page=migrate-app-name'); ?>';
</script>
<p>正在跳轉...</p>
<?php
        },
        'dashicons-migrate',
        20
    );

    // 只有一個子選單
    add_submenu_page(
        'migration-tools',
        '遷移 APP_NAME',
        '遷移 APP_NAME',
        'manage_options',
        'migrate-app-name',
        function () {
            ?>
<div class="wrap">
    <h1>遷移 APP_NAME ➜ app_name</h1>
    <button id="migrate-app-name" class="button button-primary">開始遷移</button>
    <div id="migration-log" style="margin-top: 1em;"></div>
</div>

<script>
document.getElementById('migrate-app-name').addEventListener('click', async () => {
    const log = document.getElementById('migration-log');
    log.textContent = '處理中...';
    const res = await fetch('<?php echo esc_url(rest_url('custom/v1/migrate-app-name/')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
        },
    });
    const data = await res.json();
    log.textContent = `遷移完成，成功處理 ${data.migrated} 筆`;
});
</script>
<?php
        }
    );
});