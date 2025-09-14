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

    // 獲取總文章數（僅在第一次請求時計算）
    $total_posts_count = 0;
    if ($offset === 0) {
        $total_posts_count = wp_count_posts('post')->publish;
    }

    $posts = get_posts(array(
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => $limit,
        'offset'      => $offset,
    ));

    $count_updated = 0;
    $processed_posts = [];
 
    foreach ($posts as $post) {
        // 使用共用的處理函數
        $result = process_post_tailwind_color($post->ID);
        
        if ($result && $result['success']) {
            $processed_posts[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'tailwind_class' => $result['tailwind_class'],
                'base_color' => $result['base_color'],
                'light_color' => $result['light_color']
            ];
            
            $count_updated++;
        }
    }

    return array(
        'updated' => $count_updated,
        'next_offset' => count($posts) < $limit ? null : $offset + $limit,
        'current_batch_posts' => count($posts),
        'current_offset' => $offset,
        'total_posts_count' => $total_posts_count,
        'processed_so_far' => $offset + count($posts),
        'processed_posts' => $processed_posts
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
    
    <!-- 進度條區域 -->
    <div id="progress-container" style="margin-top: 1em; display: none;">
        <div style="background: #f1f1f1; border-radius: 13px; padding: 3px;">
            <div id="progress-bar" style="background: #4CAF50; width: 0%; height: 20px; border-radius: 10px; transition: width 0.3s;"></div>
        </div>
        <div id="progress-text" style="margin-top: 5px; font-weight: bold;"></div>
    </div>
    
    <div id="migration-log" style="margin-top: 1em; max-height: 300px; overflow-y: auto;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let offset = 0;
    let stopFlag = false;
    let totalPosts = 0;
    let processedSoFar = 0;
    const logEl = document.getElementById('migration-log');
    const startBtn = document.getElementById('start-migration');
    const stopBtn = document.getElementById('stop-migration');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');

    startBtn.addEventListener('click', () => {
        offset = 0;
        stopFlag = false;
        totalPosts = 0;
        processedSoFar = 0;
        logEl.innerHTML = "<p>開始批次處理...</p>";
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';
        progressText.textContent = '準備中...';
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

    function updateProgress() {
        if (totalPosts > 0) {
            const percentage = Math.round((processedSoFar / totalPosts) * 100);
            progressBar.style.width = percentage + '%';
            progressText.textContent = `進度: ${processedSoFar} / ${totalPosts} (${percentage}%)`;
        }
    }

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
        console.log('Response data:', data); // 調試用
        
        // 更新總數（只在第一次）
        if (data.total_posts_count && totalPosts === 0) {
            totalPosts = data.total_posts_count;
        }
        
        // 更新已處理數量
        if (data.processed_so_far) {
            processedSoFar = data.processed_so_far;
        }
        
        // 更新進度條
        updateProgress();
        
        logEl.innerHTML += `<p><strong>處理 offset ${offset}：已更新 ${data.updated} 筆 (當前批次 ${data.current_batch_posts} 筆文章)</strong></p>`;
        
        // 顯示每篇處理的文章詳細資訊
        if (data.processed_posts && data.processed_posts.length > 0) {
            data.processed_posts.forEach(post => {
                const colorInfo = post.base_color ? ` | Base: ${post.base_color} | Light: ${post.light_color}` : '';
                logEl.innerHTML += `<p style="margin-left: 20px; font-size: 12px;">ID: ${post.id} | ${post.title} | 顏色: ${post.tailwind_class}${colorInfo}</p>`;
            });
        }
        
        logEl.scrollTop = logEl.scrollHeight;

        if (data.next_offset !== null && !stopFlag) {
            offset = data.next_offset;
            setTimeout(runBatch, 1000);
        } else if (!stopFlag) {
            progressBar.style.width = '100%';
            progressText.textContent = `完成！總共處理 ${processedSoFar} 篇文章`;
            logEl.innerHTML += "<p><strong>✅ 所有批次處理完成。</strong></p>";
            startBtn.disabled = false;
            stopBtn.disabled = true;
        }
    }
});
</script>
<?php
}