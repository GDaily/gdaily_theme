<?php 


//輸入圖片 返回相似的tailwind class顏色名
use ColorThief\ColorThief;
 
class ColorMatcher {
	
	private $tailwindColors;
	
	public function __construct() {
        $jsonPath = __DIR__ . '../data/tailwind-colors.json';
        $jsonContent = file_get_contents($jsonPath);
        $this->tailwindColors = json_decode($jsonContent, true);
    }
    // 計算兩個顏色之間的距離
    private function colorDistance($color1, $color2) {
        $rDiff = $color1[0] - $color2[0];
        $gDiff = $color1[1] - $color2[1];
        $bDiff = $color1[2] - $color2[2];
        return sqrt($rDiff * $rDiff + $gDiff * $gDiff + $bDiff * $bDiff);
    }

    // 找到與輸入顏色最接近的 Tailwind 顏色名稱
    public function findClosestColor($filePath) {
        $inputColor = ColorThief::getColor($filePath); 
        $closestBaseColor = null;
        $minBaseDistance = PHP_INT_MAX;

        // 在 base 類別中找到最接近的顏色
        if (isset($this->tailwindColors['base'])) {
            foreach ($this->tailwindColors['base'] as $name => $color) {
                $distance = $this->colorDistance($inputColor, $color);
                if ($distance < $minBaseDistance) {
                    $minBaseDistance = $distance;
                    $closestBaseColor = $name;
                }
            }
        }

        // 提取顏色名稱的前綴（如 "red-500" 變為 "red"）
        $colorParts = explode('-', $closestBaseColor);
        
        // 取得 base 顏色的十六進制碼
        $tailwindBaseHex = null;
        if ($closestBaseColor) {
            $tailwindBaseRGB = $this->tailwindColors['base'][$closestBaseColor];
            $tailwindBaseHex = sprintf('#%02x%02x%02x', $tailwindBaseRGB[0], $tailwindBaseRGB[1], $tailwindBaseRGB[2]);
        }
        
        // 根據 base 顏色名稱取得對應的淺色（light）
        $tailwindLightHex = null;
        if ($closestBaseColor) {
            $lightColorName = $colorParts[0] . '-50'; // 將 xxx-500 改為 xxx-50
            
            if (isset($this->tailwindColors['light'][$lightColorName])) {
                $tailwindLightRGB = $this->tailwindColors['light'][$lightColorName];
                $tailwindLightHex = sprintf('#%02x%02x%02x', $tailwindLightRGB[0], $tailwindLightRGB[1], $tailwindLightRGB[2]);
            }
        }

        return [ 
            'tailwind_hex_base_color' => $tailwindBaseHex,
            'tailwind_hex_light_color' => $tailwindLightHex
        ];
    }
}


?>