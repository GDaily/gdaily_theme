<?php

/**
 * 圖片處理工具類
 * 
 * 此檔案包含圖片處理相關的函數，主要用於分析和處理圖片的白邊區域
 * 
 * @author 您的名字
 * @version 1.0
 * @since 2025-09-20
 */

/**
 * 去除圖片白邊並計算內容區域最大尺寸
 * 
 * 此函數分析圖片中的非背景色像素，計算去除白邊後的內容區域，
 * 並返回內容區域的最大尺寸（寬度或高度中的較大值）
 * 
 * @param string $imagePath 圖片檔案的完整路徑
 * @param array $bgColor 背景色 RGB 值，預設為白色 [255, 255, 255]
 * @param int $tolerance 顏色容忍度，用於判斷像素是否接近背景色，預設為 10
 * 
 * @return int|array 成功時返回內容區域的最大尺寸（整數），
 *                   檔案不存在時返回 192，
 *                   沒有內容像素時返回原始尺寸陣列 [width, height]
 * 
 * @throws Exception 當圖片格式不支援或無法創建圖片資源時會中止執行
 * 
 * @example
 * // 基本使用
 * $maxSize = trimImageWhitespace('/path/to/image.jpg');
 * 
 * // 自訂背景色和容忍度
 * $maxSize = trimImageWhitespace('/path/to/image.png', [240, 240, 240], 15);
 * 
 * @note 支援的圖片格式：JPEG、PNG、GIF
 * @note 對於 PNG 和 GIF 格式，完全透明的像素會被視為白邊
 */
function trimImageWhitespace($imagePath, $bgColor = [255, 255, 255], $tolerance = 10)
{
    // 檢查圖片檔案是否存在
    if (!file_exists($imagePath)) {
        error_log("圖片檔案不存在: " . $imagePath);
        return 192; // 返回預設值
    }

    // 獲取圖片資訊並確定圖片類型
    $imageInfo = getimagesize($imagePath);
    if ($imageInfo === false) {
        error_log("無法獲取圖片資訊: " . $imagePath);
        return 192;
    }
    
    $imageType = $imageInfo[2];

    // 根據圖片格式創建對應的 GD 圖片資源
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($imagePath);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($imagePath);
            break;
        default:
            error_log("不支援的圖片格式: " . $imagePath . " (類型: " . $imageType . ")");
            return 192;
    }

    // 檢查圖片資源是否成功創建
    if (!$image) {
        error_log("無法創建圖片資源: " . $imagePath);
        return 192;
    }

    // 獲取圖片尺寸
    $width = imagesx($image);
    $height = imagesy($image);

    // 初始化邊界變數
    // minX, minY: 內容區域的左上角座標
    // maxX, maxY: 內容區域的右下角座標
    $minX = $width;   // 初始設為最大值，之後會找到更小的值
    $minY = $height;  // 初始設為最大值，之後會找到更小的值
    $maxX = 0;        // 初始設為最小值，之後會找到更大的值
    $maxY = 0;        // 初始設為最小值，之後會找到更大的值

    // 遍歷圖片的每個像素，尋找非背景色的邊界
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            // 獲取當前像素的顏色值
            $rgb = imagecolorat($image, $x, $y);
            $colors = imagecolorsforindex($image, $rgb);

            // 處理透明度（僅適用於 PNG 和 GIF 格式）
            if (($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF)) {
                // 檢查 alpha 通道，127 表示完全透明
                $alpha = ($rgb & 0x7F000000) >> 24;
                if ($alpha == 127) {
                    // 完全透明的像素視為背景，跳過
                    continue;
                }
            }

            // 計算當前像素與指定背景色的顏色差異
            $rDiff = abs($colors['red'] - $bgColor[0]);
            $gDiff = abs($colors['green'] - $bgColor[1]);
            $bDiff = abs($colors['blue'] - $bgColor[2]);

            // 檢查當前像素是否為非背景色（任一顏色通道超過容忍度）
            if ($rDiff > $tolerance || $gDiff > $tolerance || $bDiff > $tolerance) {
                // 更新內容區域的邊界座標
                if ($x < $minX) $minX = $x;  // 更新左邊界
                if ($x > $maxX) $maxX = $x;  // 更新右邊界
                if ($y < $minY) $minY = $y;  // 更新上邊界
                if ($y > $maxY) $maxY = $y;  // 更新下邊界
            }
        }
    }

    // 釋放圖片記憶體資源
    imagedestroy($image);

    // 檢查是否找到任何非背景色像素
    if ($minX > $maxX || $minY > $maxY) {
        // 沒有找到非背景色像素，返回原始尺寸
        error_log("圖片中沒有找到非背景色內容: " . $imagePath);
        return [$width, $height];
    }

    // 計算去除白邊後的內容區域尺寸
    $trimmedWidth = $maxX - $minX + 1;   // 內容寬度
    $trimmedHeight = $maxY - $minY + 1;  // 內容高度

    // 返回內容區域的最大尺寸（用於保持比例的正方形裁切等用途）
    $maxSize = max($trimmedWidth, $trimmedHeight);

    return $maxSize;
}