<?php
// 註冊 REST API 路由（保持不動）
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/migrate/', array(
        'methods'  => 'POST',
        'callback' => 'migrate_app_name_meta_batch',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        }
    ));
});

function migrate_app_name_meta_batch($request) {
    $offset = intval($request->get_param('offset') ?? 0);
    $limit = 200;

    $posts = get_posts(array(
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => $limit,
        'offset'      => $offset,
    ));

    $count_updated = 0;

    foreach ($posts as $post) {
        $current_value = carbon_get_post_meta($post->ID, 'tailwind_color');

        if (empty($current_value)) {
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            if ($thumbnail_id) {
                $thumbnail_serverPath = get_attached_file($thumbnail_id);
                $matcher = new ColorMatcher();
                $tailwind_main_class = $matcher->findClosestColor($thumbnail_serverPath);
                carbon_set_post_meta($post->ID, 'tailwind_color', $tailwind_main_class);
                $count_updated++;
            }
        }
    }

    return array(
        'updated' => $count_updated,
        'next_offset' => count($posts) < $limit ? null : $offset + $limit
    );
}

// 建立後台選單
add_action('admin_menu', function () {
    // 父選單：遷移工具 (不會有內容，只是父選單)
    add_menu_page(
        '遷移工具',
        '遷移工具',
        'manage_options',
        'migration-tools',
        function () {
            // 父選單點擊時顯示空白頁或簡單文字
            echo '<div class="wrap"><h1>遷移工具</h1><p>請從左側子選單選擇功能。</p></div>';
        },
        'dashicons-migrate',
        20
    );

    // 子選單：遷移 Tailwind Color
    add_submenu_page(
        'migration-tools',
        'Tailwind Color 遷移工具',
        '遷移 Tailwind',
        'manage_options',
        'tailwind-migrate',
        'render_tailwind_migrate_page'
    );
});

function render_tailwind_migrate_page() {
    ?>
<div class="wrap">
    <h1>Tailwind Color 遷移工具</h1>
    <p>此工具會自動將尚未設定 tailwind_color 的文章依據縮圖進行分析與設定。</p>
    <button id="start-migration" class="button button-primary">開始處理</button>
    <button id="stop-migration" class="button">中止處理</button>
    <div id="migration-log" style="margin-top: 1em; max-height: 300px; overflow-y: auto;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let offset = 0;
    let stopFlag = false;
    const logEl = document.getElementById('migration-log');
    const startBtn = document.getElementById('start-migration');
    const stopBtn = document.getElementById('stop-migration');

    startBtn.addEventListener('click', () => {
        offset = 0;
        stopFlag = false;
        logEl.innerHTML = "<p>開始批次處理...</p>";
        startBtn.disabled = true;
        stopBtn.disabled = false;
        runBatch();
    });

    stopBtn.addEventListener('click', () => {
        stopFlag = true;
        logEl.innerHTML += "<p>🛑 使用者中止處理。</p>";
        startBtn.disabled = false;
        stopBtn.disabled = true;
    });

    async function runBatch() {
        if (stopFlag) return;

        const res = await fetch('<?php echo esc_url(rest_url('custom/v1/migrate/')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            },
            body: JSON.stringify({
                offset
            })
        });

        const data = await res.json();
        logEl.innerHTML += `<p>處理 offset ${offset}：已更新 ${data.updated} 筆</p>`;
        logEl.scrollTop = logEl.scrollHeight;

        if (data.next_offset !== null && !stopFlag) {
            offset = data.next_offset;
            setTimeout(runBatch, 1000);
        } else if (!stopFlag) {
            logEl.innerHTML += "<p><strong>✅ 所有批次處理完成。</strong></p>";
            startBtn.disabled = false;
            stopBtn.disabled = true;
        }
    }
});
</script>
<?php
}