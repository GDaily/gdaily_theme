<?php

use ColorThief\ColorThief;

class ColorMatcher {

    private $tailwindColors;

    public function __construct() {
        $jsonPath = __DIR__ . '/../data/tailwind-colors.json';
        if (!file_exists($jsonPath)) {
            error_log('ColorMatcher: tailwind-colors.json file not found at: ' . $jsonPath);
            throw new Exception('Tailwind colors file not found');
        }
        $jsonContent = file_get_contents($jsonPath);
        $this->tailwindColors = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('ColorMatcher: JSON decode error: ' . json_last_error_msg());
            throw new Exception('Failed to decode tailwind colors JSON');
        }
    }

    // RGB 轉 XYZ (D65 illuminant)
    private function rgbToXyz($r, $g, $b) {
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;

        $r = $r > 0.04045 ? pow(($r + 0.055) / 1.055, 2.4) : $r / 12.92;
        $g = $g > 0.04045 ? pow(($g + 0.055) / 1.055, 2.4) : $g / 12.92;
        $b = $b > 0.04045 ? pow(($b + 0.055) / 1.055, 2.4) : $b / 12.92;

        $r *= 100;
        $g *= 100;
        $b *= 100;

        // Observer = 2°, Illuminant = D65
        $x = $r * 0.4124 + $g * 0.3576 + $b * 0.1805;
        $y = $r * 0.2126 + $g * 0.7152 + $b * 0.0722;
        $z = $r * 0.0193 + $g * 0.1192 + $b * 0.9505;

        return [$x, $y, $z];
    }

    // XYZ 轉 Lab
    private function xyzToLab($x, $y, $z) {
        $x /= 95.047;  // ref_X = 95.047   Observer= 2°, Illuminant= D65
        $y /= 100.000; // ref_Y = 100.000
        $z /= 108.883; // ref_Z = 108.883

        $x = $x > 0.008856 ? pow($x, 1/3) : (7.787 * $x) + (16 / 116);
        $y = $y > 0.008856 ? pow($y, 1/3) : (7.787 * $y) + (16 / 116);
        $z = $z > 0.008856 ? pow($z, 1/3) : (7.787 * $z) + (16 / 116);

        $l = (116 * $y) - 16;
        $a = 500 * ($x - $y);
        $b = 200 * ($y - $z);

        return [round($l, 3), round($a, 3), round($b, 3)];
    }

    // RGB 轉 Lab
    private function rgbToLab($rgb) {
        [$r, $g, $b] = $rgb;
        [$x, $y, $z] = $this->rgbToXyz($r, $g, $b);
        return $this->xyzToLab($x, $y, $z);
    }

    // 計算兩個 Lab 顏色的 ΔE*ab (CIE76)
    private function deltaE($lab1, $lab2) {
        [$l1, $a1, $b1] = $lab1;
        [$l2, $a2, $b2] = $lab2;
        return sqrt(
            pow($l1 - $l2, 2) +
            pow($a1 - $a2, 2) +
            pow($b1 - $b2, 2)
        );
    }

    // 從調色盤中選擇「最適合 Tailwind」的顏色（可加權：避免過暗/過亮）
    private function selectBestPaletteColor($palette) {
        $scores = [];

        foreach ($palette as $index => $rgb) {
            [$r, $g, $b] = $rgb;

            // 轉成 Lab
            $lab = $this->rgbToLab($rgb);

            // 簡單評分：避免極端亮度（L < 20 或 L > 95 可能不適合做主色）
            $l = $lab[0];
            $penalty = 0;
            if ($l < 20) $penalty += 10; // 太暗
            if ($l > 95) $penalty += 10; // 太亮

            // 飽和度評分（a^2 + b^2）越高越鮮明，可能更適合做主色
            $saturation = sqrt($lab[1] * $lab[1] + $lab[2] * $lab[2]);

            // 最終評分：飽和度加分，極端亮度扣分
            $score = $saturation - $penalty;

            $scores[] = [
                'rgb' => $rgb,
                'lab' => $lab,
                'score' => $score,
                'index' => $index
            ];
        }

        // 依分數排序，取最高分
        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);
        return $scores[0]['rgb'];
    }

    public function findClosestColor($filePath) {
        // 取得調色盤前 5 個主色
        $palette = ColorThief::getPalette($filePath, 5);

        if (empty($palette)) {
            throw new Exception('No colors extracted from image');
        }

        // 從調色盤選出最適合的代表色
        $representativeRgb = $this->selectBestPaletteColor($palette);
        $representativeLab = $this->rgbToLab($representativeRgb);

        $closestBaseColor = null;
        $minDistance = PHP_FLOAT_MAX;

        // 比較所有 base 顏色，找出最接近的
        if (isset($this->tailwindColors['base'])) {
            foreach ($this->tailwindColors['base'] as $name => $rgb) {
                $lab = $this->rgbToLab($rgb);
                $distance = $this->deltaE($representativeLab, $lab);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $closestBaseColor = $name;
                }
            }
        }

        if (!$closestBaseColor) {
            throw new Exception('No matching base color found');
        }

        // 提取前綴 (e.g., "red-500" → "red")
        $colorParts = explode('-', $closestBaseColor);
        $prefix = $colorParts[0];

        // 取得 base 顏色的十六進制碼
        $tailwindBaseRGB = $this->tailwindColors['base'][$closestBaseColor];
        $tailwindBaseHex = sprintf('#%02x%02x%02x', $tailwindBaseRGB[0], $tailwindBaseRGB[1], $tailwindBaseRGB[2]);

        // 取得對應的 light 顏色 (xxx-50)
        $lightColorName = $prefix . '-50';
        $tailwindLightHex = null;

        if (isset($this->tailwindColors['light'][$lightColorName])) {
            $tailwindLightRGB = $this->tailwindColors['light'][$lightColorName];
            $tailwindLightHex = sprintf('#%02x%02x%02x', $tailwindLightRGB[0], $tailwindLightRGB[1], $tailwindLightRGB[2]);
        }

        return [
            'tailwind_hex_base_color' => $tailwindBaseHex,
            'tailwind_hex_light_color' => $tailwindLightHex,
            'representative_rgb' => $representativeRgb, // 可選：回傳代表色供調試
            'closest_tailwind_color_name' => $closestBaseColor // 可選：回傳匹配的 Tailwind 類名
        ];
    }
}



/**
 * 處理單篇文章的縮圖顏色分析和設定
 * 
 * @param int $post_id 文章 ID
 * @return array|false 返回處理結果或 false（如果失敗）
 */
function process_post_tailwind_color($post_id) {
    // 檢查是否有縮圖
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if (!$thumbnail_id) {
        return false;
    }
    
    // 檢查縮圖文件是否存在
    $thumbnail_serverPath = get_attached_file($thumbnail_id);
    if (!file_exists($thumbnail_serverPath)) {
        return false;
    }
    
    try {
        // 使用 ColorMatcher 分析顏色
        $matcher = new ColorMatcher();
        $colorResult = $matcher->findClosestColor($thumbnail_serverPath);
        
        // 設定 meta 值
        carbon_set_post_meta($post_id, 'tailwind_hex_base_color', $colorResult['tailwind_hex_base_color']);
        carbon_set_post_meta($post_id, 'tailwind_hex_light_color', $colorResult['tailwind_hex_light_color']);
        
        return [
            'success' => true,
            'post_id' => $post_id, 
            'base_color' => $colorResult['tailwind_hex_base_color'],
            'light_color' => $colorResult['tailwind_hex_light_color']
        ];
        
    } catch (Exception $e) {
        return false;
    }
}



/**
 * 在文章儲存時自動執行顏色分析
 * 
 * @param int $post_id 文章 ID
 */
function auto_process_tailwind_color_on_save($post_id) {
    // 檢查是否為自動儲存或修訂版本
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }
    
    // 只處理 'post' 類型的文章
    if (get_post_type($post_id) !== 'post') {
        return;
    }
    
    // 執行顏色分析（不限制發布狀態）
    process_post_tailwind_color($post_id);
}

// 註冊多個 hook：在文章儲存時自動執行
add_action('save_post', 'auto_process_tailwind_color_on_save', 10, 1);
add_action('wp_insert_post', 'auto_process_tailwind_color_on_save', 10, 1);
add_action('edit_post', 'auto_process_tailwind_color_on_save', 10, 1);