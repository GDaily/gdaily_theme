<?php

/**
 * AdSense 短代碼
 * 使用 [adsense] 來顯示 AdSense 廣告內容
 */

function adsense_shortcode()
{
    // 引入 adsense_content.php 的內容
    $adsense_content = file_get_contents(get_template_directory() . '/adsense/adsense_content.php');
    return $adsense_content;
}

add_shortcode('adsense', 'adsense_shortcode');
