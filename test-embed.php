<?php
/**
 * 嵌入功能測試腳本
 * 將此文件放在主題根目錄並透過瀏覽器訪問來測試嵌入功能
 */

// 載入 WordPress
require_once('../../../wp-load.php');

echo "<h1>WordPress 嵌入功能測試</h1>";

// 檢查嵌入功能是否啟用
echo "<h2>嵌入功能狀態檢查：</h2>";
echo "<ul>";

// 檢查 WP_Embed 物件
global $wp_embed;
echo "<li>WP_Embed 物件: " . (isset($wp_embed) ? "✅ 已啟用" : "❌ 未啟用") . "</li>";

// 檢查嵌入端點
$rewrite_rules = get_option('rewrite_rules');
$embed_rule_exists = false;
if ($rewrite_rules) {
    foreach ($rewrite_rules as $pattern => $replacement) {
        if (strpos($pattern, 'embed') !== false) {
            $embed_rule_exists = true;
            break;
        }
    }
}
echo "<li>嵌入重寫規則: " . ($embed_rule_exists ? "✅ 存在" : "❌ 不存在") . "</li>";

// 檢查主題支援
echo "<li>主題嵌入支援: " . (current_theme_supports('responsive-embeds') ? "✅ 已啟用" : "❌ 未啟用") . "</li>";

// 檢查模板文件
$embed_template = get_template_directory() . '/embed.php';
echo "<li>嵌入模板文件: " . (file_exists($embed_template) ? "✅ 存在" : "❌ 不存在") . "</li>";

echo "</ul>";

// 測試嵌入 URL 生成
echo "<h2>嵌入 URL 測試：</h2>";
$test_post = get_posts(array('numberposts' => 1, 'post_status' => 'publish'));
if ($test_post) {
    $post_id = $test_post[0]->ID;
    $embed_url = get_post_embed_url($post_id);
    echo "<p>測試文章 ID: {$post_id}</p>";
    echo "<p>嵌入 URL: <a href='{$embed_url}' target='_blank'>{$embed_url}</a></p>";
    
    // 生成嵌入 iframe
    echo "<h3>嵌入預覽：</h3>";
    echo "<iframe src='{$embed_url}' width='400' height='300' frameborder='0'></iframe>";
} else {
    echo "<p>❌ 沒有找到可測試的文章</p>";
}

// 輸出當前過濾器
echo "<h2>已註冊的嵌入過濾器：</h2>";
global $wp_filter;
if (isset($wp_filter['embed_template'])) {
    echo "<ul>";
    foreach ($wp_filter['embed_template']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            $function_name = is_array($callback['function']) ? 
                get_class($callback['function'][0]) . '::' . $callback['function'][1] : 
                $callback['function'];
            echo "<li>優先級 {$priority}: {$function_name}</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>❌ 沒有註冊的 embed_template 過濾器</p>";
}

echo "<hr>";
echo "<p><strong>如果所有項目都顯示 ✅，但嵌入仍然不工作，請嘗試以下步驟：</strong></p>";
echo "<ol>";
echo "<li>到 WordPress 後台 → 設定 → 永久連結，點擊「儲存變更」來刷新重寫規則</li>";
echo "<li>清除任何快取</li>";
echo "<li>檢查 .htaccess 文件是否正確</li>";
echo "<li>確認 WordPress 版本支援嵌入功能（WordPress 4.4+）</li>";
echo "</ol>";
?>