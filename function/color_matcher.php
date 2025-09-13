<?php 


//輸入圖片 返回相似的tailwind class顏色名
use ColorThief\ColorThief;
 
class ColorMatcher {
	
	private $tailwindColors = [
		"red-500" => [239, 68, 68],
		"yellow-500" => [234, 179, 8],
		"green-500" => [34, 197, 94],
		"blue-500" => [59, 130, 246],
		"indigo-500" => [99, 102, 241],
		"purple-500" => [147, 51, 234],
		"pink-500" => [236, 72, 153],
/* 		"lime-500" => [133, 189, 36], */
		"teal-500" => [20, 184, 166],
		"cyan-500" => [6, 182, 212],
		"sky-500" => [14, 165, 233],
		"blue-500" => [59, 130, 246],
		"indigo-500" => [67, 56, 202],
		/* "violet-500" => [139, 92, 246], */
		"fuchsia-500" => [225, 29, 157],
		"pink-500" => [244, 63, 94],
		"rose-500" => [239, 68, 68],
	];
    // 計算兩個顏色之間的距離
    private function colorDistance($color1, $color2) {
        $rDiff = $color1[0] - $color2[0];
        $gDiff = $color1[1] - $color2[1];
        $bDiff = $color1[2] - $color2[2];
        return sqrt($rDiff * $rDiff + $gDiff * $gDiff + $bDiff * $bDiff);
    }

    // 找到與輸入顏色最接近的 Tailwind 顏色名稱
    public function findClosestColor($filePath) {

		$inputColor = 	ColorThief::getColor($filePath); 
        $closestColor = null;
        $minDistance = PHP_INT_MAX;

        foreach ($this->tailwindColors as $name => $color) {
            $distance = $this->colorDistance($inputColor, $color);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestColor = $name;
            }
        }

        // 提取顏色名稱的前綴（如 "red-200" 變為 "red"）
        $colorParts = explode('-', $closestColor);
        return $colorParts[0];
    }
}


?>