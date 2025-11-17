<?php

/**
 * 將文章內容中的 H3 標籤替換為 H2 標籤
 * 使用批次處理方式
 */

// 註冊 REST API 路由
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/convert-h3-to-h2/', array(
        'methods'  => 'POST',
        'callback' => 'convert_h3_to_h2_batch',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        }
    ));
});

function convert_h3_to_h2_batch($request)
{
    $offset = intval($request->get_param('offset') ?? 0);
    $limit = 50; // 減少批次大小，避免超時

    // 獲取總文章數（每次都計算，確保正確）
    $total_posts_count = wp_count_posts('post')->publish;

    $posts = get_posts(array(
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => $limit,
        'offset'      => $offset,
        'orderby'     => 'ID',
        'order'       => 'ASC'
    ));

    $count_updated = 0;
    $processed_posts = [];

    foreach ($posts as $post) {
        // 處理單篇文章的 H3 到 H2 轉換
        $result = process_single_post_h3_to_h2($post->ID);

        if ($result && $result['converted']) {
            $count_updated++;
        }

        // 記錄所有處理的文章（無論是否轉換）
        $processed_posts[] = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'h3_count' => $result ? $result['h3_count'] : 0,
            'converted' => $result ? $result['converted'] : false
        ];
    }

    // 計算是否還有更多文章需要處理
    $has_more = count($posts) === $limit;
    $next_offset = $has_more ? $offset + $limit : null;

    return array(
        'success' => true,
        'updated' => $count_updated,
        'next_offset' => $next_offset,
        'current_batch_posts' => count($posts),
        'current_offset' => $offset,
        'total_posts_count' => $total_posts_count,
        'processed_so_far' => $offset + count($posts),
        'processed_posts' => $processed_posts,
        'has_more' => $has_more
    );
}

/**
 * 處理單篇文章的 H3 到 H2 轉換
 */
function process_single_post_h3_to_h2($post_id)
{
    $post = get_post($post_id);
    if (!$post) {
        return false;
    }

    $content = $post->post_content;

    // 檢查是否包含 H3 標籤
    if (strpos($content, '<h3') === false && strpos($content, '</h3>') === false) {
        return [
            'success' => false,
            'h3_count' => 0,
            'converted' => false,
            'message' => '沒有找到 H3 標籤'
        ];
    }

    // 計算 H3 標籤數量
    preg_match_all('/<h3[^>]*>/i', $content, $matches);
    $h3_count = count($matches[0]);

    // 替換開始標籤 <h3> 和帶屬性的 <h3 class="...">
    $new_content = preg_replace('/<h3([^>]*)>/i', '<h2$1>', $content);

    // 替換結束標籤 </h3>
    $new_content = preg_replace('/<\/h3>/i', '</h2>', $new_content);

    // 如果內容有變化，則更新文章
    if ($new_content !== $content) {
        $result = wp_update_post(array(
            'ID' => $post->ID,
            'post_content' => $new_content
        ));

        if ($result && !is_wp_error($result)) {
            // 記錄日誌
            error_log("轉換文章 ID: {$post->ID} - {$post->post_title} (轉換 {$h3_count} 個 H3 標籤)");

            return [
                'success' => true,
                'h3_count' => $h3_count,
                'converted' => true,
                'message' => "成功轉換 {$h3_count} 個 H3 標籤"
            ];
        } else {
            return [
                'success' => false,
                'h3_count' => $h3_count,
                'converted' => false,
                'message' => '更新文章失敗'
            ];
        }
    }

    return [
        'success' => false,
        'h3_count' => $h3_count,
        'converted' => false,
        'message' => '內容沒有變化'
    ];
}

// 添加到遷移工具選單
add_action('admin_menu', function () {
    // 確保父選單存在（如果 add-tailwind_color.php 沒有載入）
    if (!menu_page_url('migration-tools', false)) {
        add_menu_page(
            '遷移工具',
            '遷移工具',
            'manage_options',
            'migration-tools',
            function () {
                echo '<div class="wrap"><h1>遷移工具</h1><p>請從左側子選單選擇功能。</p></div>';
            },
            'dashicons-migrate',
            20
        );
    }

    // 子選單：H3 到 H2 轉換
    add_submenu_page(
        'migration-tools',
        'H3 到 H2 轉換工具',
        'H3 到 H2 轉換',
        'manage_options',
        'h3-to-h2-convert',
        'render_h3_to_h2_convert_page'
    );
});

function render_h3_to_h2_convert_page()
{
?>
    <div class="wrap">
        <h1>H3 到 H2 標籤轉換工具</h1>
        <div class="card" style="max-width: 800px;">
            <h2>功能說明</h2>
            <p>此工具會批次將所有文章內容中的 <code>&lt;h3&gt;</code> 標籤替換為 <code>&lt;h2&gt;</code> 標籤。</p>
            <ul>
                <li>✅ 批次處理所有已發布的文章</li>
                <li>✅ 保留原有的 class 和其他屬性</li>
                <li>✅ 即時顯示處理進度</li>
                <li>✅ 可以隨時中止處理</li>
                <li>✅ 詳細的處理記錄</li>
            </ul>

            <h3>轉換範例：</h3>
            <div style="background: #f9f9f9; padding: 10px; border-left: 4px solid #0073aa;">
                <p><strong>轉換前：</strong></p>
                <code>&lt;h3&gt;標題&lt;/h3&gt;</code><br>
                <code>&lt;h3 class="custom-class"&gt;帶樣式的標題&lt;/h3&gt;</code>

                <p style="margin-top: 15px;"><strong>轉換後：</strong></p>
                <code>&lt;h2&gt;標題&lt;/h2&gt;</code><br>
                <code>&lt;h2 class="custom-class"&gt;帶樣式的標題&lt;/h2&gt;</code>
            </div>
        </div>

        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>開始轉換</h2>
            <p><strong>注意：</strong>此操作會直接修改文章內容，建議先備份資料庫！</p>
            <button id="start-conversion" class="button button-primary">開始轉換</button>
            <button id="stop-conversion" class="button">中止轉換</button>
            <button id="test-connection" class="button" style="margin-left: 10px;">測試連線</button>

            <!-- 進度條區域 -->
            <div id="progress-container" style="margin-top: 1em; display: none;">
                <div style="background: #f1f1f1; border-radius: 13px; padding: 3px;">
                    <div id="progress-bar"
                        style="background: #4CAF50; width: 0%; height: 20px; border-radius: 10px; transition: width 0.3s;">
                    </div>
                </div>
                <div id="progress-text" style="margin-top: 5px; font-weight: bold;"></div>
            </div>

            <div id="conversion-log"
                style="margin-top: 1em; max-height: 400px; overflow-y: auto; background: #f9f9f9; padding: 10px; border: 1px solid #ddd; display: none;">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let offset = 0;
            let stopFlag = false;
            let totalPosts = 0;
            let processedSoFar = 0;
            let totalConverted = 0;
            const logEl = document.getElementById('conversion-log');
            const startBtn = document.getElementById('start-conversion');
            const stopBtn = document.getElementById('stop-conversion');
            const testBtn = document.getElementById('test-connection');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            startBtn.addEventListener('click', () => {
                if (!confirm('確定要開始轉換嗎？建議先備份資料庫。\n\n此操作會將所有文章中的 H3 標籤替換為 H2 標籤。')) {
                    return;
                }

                offset = 0;
                stopFlag = false;
                totalPosts = 0;
                processedSoFar = 0;
                totalConverted = 0;
                logEl.innerHTML = "<p><strong>🚀 開始批次轉換 H3 到 H2 標籤...</strong></p>";
                logEl.style.display = 'block';
                progressContainer.style.display = 'block';
                progressBar.style.width = '0%';
                progressText.textContent = '準備中...';
                startBtn.disabled = true;
                stopBtn.disabled = false;
                runBatch();
            });

            stopBtn.addEventListener('click', () => {
                stopFlag = true;
                logEl.innerHTML += "<p><strong>🛑 使用者中止轉換處理。</strong></p>";
                startBtn.disabled = false;
                stopBtn.disabled = true;
            });

            testBtn.addEventListener('click', async () => {
                testBtn.disabled = true;
                logEl.innerHTML = "<p><strong>🔍 測試 API 連線...</strong></p>";
                logEl.style.display = 'block';

                try {
                    const res = await fetch(
                        '<?php echo esc_url(rest_url('custom/v1/convert-h3-to-h2/')); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                            },
                            body: JSON.stringify({
                                offset: 0
                            })
                        });

                    if (!res.ok) {
                        throw new Error(`HTTP 錯誤! 狀態: ${res.status}`);
                    }

                    const data = await res.json();

                    if (data.success) {
                        logEl.innerHTML += `<p><strong style="color: green;">✅ API 連線正常！</strong></p>`;
                        logEl.innerHTML += `<p>總文章數: ${data.total_posts_count}</p>`;
                        logEl.innerHTML += `<p>第一批處理的文章數: ${data.current_batch_posts}</p>`;
                        logEl.innerHTML += `<p>找到可轉換的文章: ${data.updated}</p>`;
                    } else {
                        logEl.innerHTML += `<p><strong style="color: red;">❌ API 回應異常</strong></p>`;
                    }
                } catch (error) {
                    logEl.innerHTML +=
                        `<p><strong style="color: red;">❌ 連線測試失敗: ${error.message}</strong></p>`;
                }

                testBtn.disabled = false;
            });

            function updateProgress() {
                if (totalPosts > 0) {
                    const percentage = Math.round((processedSoFar / totalPosts) * 100);
                    progressBar.style.width = percentage + '%';
                    progressText.textContent =
                        `進度: ${processedSoFar} / ${totalPosts} (${percentage}%) | 已轉換: ${totalConverted} 篇`;
                }
            }

            async function runBatch() {
                if (stopFlag) return;

                try {
                    const res = await fetch('<?php echo esc_url(rest_url('custom/v1/convert-h3-to-h2/')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                        },
                        body: JSON.stringify({
                            offset
                        })
                    });

                    if (!res.ok) {
                        throw new Error(`HTTP 錯誤! 狀態: ${res.status}`);
                    }

                    const data = await res.json();
                    console.log('Response data:', data);

                    if (!data.success) {
                        throw new Error('伺服器回應錯誤');
                    }

                    // 更新總數（每次都更新，確保正確）
                    if (data.total_posts_count) {
                        totalPosts = data.total_posts_count;
                    }

                    // 更新已處理數量
                    processedSoFar = data.processed_so_far || (offset + data.current_batch_posts);

                    // 更新總轉換數量
                    totalConverted += data.updated;

                    // 更新進度條
                    updateProgress();

                    logEl.innerHTML +=
                        `<p><strong>📊 處理批次 ${Math.floor(offset / 50) + 1}：已轉換 ${data.updated} 篇 (當前批次 ${data.current_batch_posts} 篇文章)</strong></p>`;

                    // 顯示每篇處理的文章詳細資訊
                    if (data.processed_posts && data.processed_posts.length > 0) {
                        data.processed_posts.forEach(post => {
                            const status = post.converted ? '✅' : '⏭️';
                            const info = post.converted ? ` | 轉換了 ${post.h3_count} 個 H3 標籤` :
                                ' | 沒有 H3 標籤需要轉換';
                            logEl.innerHTML +=
                                `<p style="margin-left: 20px; font-size: 12px;">${status} ID: ${post.id} | ${post.title}${info}</p>`;
                        });
                    }

                    logEl.scrollTop = logEl.scrollHeight;

                    // 檢查是否還有更多文章需要處理
                    if (data.next_offset !== null && data.has_more && !stopFlag) {
                        offset = data.next_offset;
                        // 增加延遲避免伺服器負載過重
                        setTimeout(runBatch, 1000);
                    } else {
                        // 處理完成
                        progressBar.style.width = '100%';
                        progressText.textContent = `✅ 完成！總共處理 ${processedSoFar} 篇文章，轉換 ${totalConverted} 篇`;
                        logEl.innerHTML +=
                            `<p><strong>🎉 所有批次轉換完成！總共轉換了 ${totalConverted} 篇文章的 H3 標籤為 H2 標籤。</strong></p>`;
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
                    }
                } catch (error) {
                    console.error('轉換錯誤:', error);
                    logEl.innerHTML += `<p><strong style="color: red;">❌ 發生錯誤: ${error.message}</strong></p>`;
                    logEl.innerHTML += `<p><strong>請檢查：</strong></p>`;
                    logEl.innerHTML += `<p>1. 網路連線是否正常</p>`;
                    logEl.innerHTML += `<p>2. WordPress REST API 是否啟用</p>`;
                    logEl.innerHTML += `<p>3. 使用者是否有足夠權限</p>`;
                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                }
            }
        });
    </script>
<?php
}
?>