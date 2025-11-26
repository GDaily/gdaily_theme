<?php



/* 
插入 adsense js 檔案
單為單篇文章時需要檢查，其他頁面不需要判斷固定直接引入  
*/


// 在 <head> 載入 Adsense JS 的邏輯
function gd_insert_adsense_head_js()
{
    global $post;
    // 取得值並轉換成布林
    $adsense_enable_raw = is_object($post) ? carbon_get_post_meta($post->ID, 'adsense_enable') : '';
    $adsense_enable = filter_var($adsense_enable_raw, FILTER_VALIDATE_BOOLEAN);

    if ((is_single() && $adsense_enable) || ! is_single()) {
        echo '<script defer  src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7349735987764759" crossorigin="anonymous"></script>' . "\n";
    }
}
add_action('wp_head', 'gd_insert_adsense_head_js', 5);


// 取得廣告模板字串
function get_ads_template()
{
    if (function_exists('get_template_part')) {
        ob_start();
        get_template_part('adsense/adsense_content');
        return ob_get_clean();
    }
    return '';
}

// 統一插入廣告（只在單篇文章且 adsense_enable 開啟）
function adsense_insert_content($content)
{
    if (is_singular('post') && function_exists('carbon_get_post_meta') && carbon_get_post_meta(get_the_ID(), 'adsense_enable') === 'true') {
        $ads = get_ads_template();

        // 在第一個段落後插入廣告（段落含超過20個中文字才插入）
        $paragraphs = explode('</p>', $content);
        $new_content = '';
        $inserted = false;

        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph)) {
                $paragraph .= '</p>';
                $new_content .= $paragraph;

                if (!$inserted) {
                    $chinese_count = preg_match_all("/[\x{4e00}-\x{9fff}]/u", $paragraph, $matches);
                    if ($chinese_count > 20) {
                        $new_content .= $ads;
                        $inserted = true;
                    }
                }
            }
        }
        $content = $new_content;

        // 在第二個和第四個 H2/H3 標題前插入廣告
        preg_match_all('/<h[23][^>]*>.*?<\/h[23]>/i', $content, $matches, PREG_OFFSET_CAPTURE);

        // 從後往前插入，避免位置偏移問題
        $insert_positions = [];
        if (isset($matches[0][3])) { // 第四個標題
            $insert_positions[] = $matches[0][3][1];
        }
        if (isset($matches[0][1])) { // 第二個標題
            $insert_positions[] = $matches[0][1][1];
        }

        // 從後往前插入廣告
        foreach ($insert_positions as $insert_pos) {
            $content = substr_replace($content, $ads, $insert_pos, 0);
        }

        // 在文章底部插入廣告
        $content = $content . $ads;
    }


    return $content;
}

// 註冊內容過濾器
add_filter('the_content', 'adsense_insert_content');
