<?php


use Carbon_Fields\Container;
use Carbon_Fields\Field;



add_action('after_setup_theme', function () {
    \Carbon_Fields\Carbon_Fields::boot();
});




add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', __('Tailwind Color', '自動偵測的被景色'))
        ->where('post_type', '=', 'post')  // 适用于文章类型的字段
        ->add_fields(array(
            Field::make('text', 'tailwind_color', __('自動偵測的被景色'))
                ->set_default_value('')  // 设置默认值
                ->set_attribute('readOnly', true)  // 禁用编辑
                ->set_width(50),
                 
            //新增 tailwind_hex_base_color
            Field::make('text', 'tailwind_hex_base_color', __('圖片深色調基礎十六進制碼'))
                ->set_default_value('#ffffff')  // 设置默认值為白色 
                ->set_attribute('readOnly', true)  // 禁用编辑
                ->set_width(50),

            //新稱加 tailwind_hex_light_color
            Field::make('text', 'tailwind_hex_light_color', __('圖片淺色調基礎十六進制碼'))
                ->set_default_value('#ffffff')  // 设置默认值為白色 
                ->set_attribute('readOnly', true)  // 禁用编辑
                ->set_width(50),

            Field::make('color', 'tailwind_background_custom', '自訂背景色')
                ->set_palette(array(
                    '#FECCCC', // red-50
                    '#FDF8C8', // yellow-50
                    '#DCECE7', // green-50
                    '#DBEAF6', // blue-50
                    '#EEF2FF', // indigo-50
                    '#F5E8FF', // purple-50
                    '#FBCFE8', // pink-50
                    '#D9FBE8', // teal-50
                    '#C6F6FF', // cyan-50
                    '#CAF9FF', // sky-50
                    '#FCF3F9', // fuchsia-50
                    '#FFE4E1', // rose-50
                ))
                ->set_width(50),





        ));


    Container::make('post_meta', __('APP簡稱', 'crb'))
        ->where('post_type', '=', 'post') // 僅依文章類型判斷
        ->add_fields(array(
            Field::make('text', 'app_name', __('Custom Text', 'crb'))
                ->set_default_value('')
                ->set_width(50),
        ));


    Container::make('post_meta', 'Adsense顯示')
        ->where('post_type', '=', 'post')
        ->add_fields(array(
            Field::make('radio', 'adsense_enable', 'Adsense')
                ->add_options(array(
                    'true' => '啟用',
                    'false' => '禁用',
                ))
                ->set_default_value('true'),
        ));
});

$post_term_condition = [
    'field' => 'id',
    'value' => 1768, // 指定具体分类 ID
    'taxonomy' => 'category',
];

// 打印调试信息
error_log(print_r($post_term_condition, true)); // 将信息写入错误日志

function getColorName($colorCode)
{
    $colors = array(
        '#FECCCC' => 'red',
        '#FDF8C8' => 'yellow',
        '#DCECE7' => 'green',
        '#DBEAF6' => 'blue',
        '#EEF2FF' => 'indigo',
        '#F5E8FF' => 'purple',
        '#FBCFE8' => 'pink',
        '#D9FBE8' => 'teal',
        '#C6F6FF' => 'cyan',
        '#CAF9FF' => 'sky',
        '#FCF3F9' => 'fuchsia',
        '#FFE4E1' => 'rose',
        '#E3FCEC' => 'lime',      // 新增 lime
        '#FFEDD5' => 'orange',    // 新增 orange
        '#FEF3C7' => 'amber',     // 新增 amber
        '#D1FAE5' => 'emerald',   // 新增 emerald
        '#F0FDFA' => 'mint',      // 新增 mint
        '#FFFBEB' => 'warmGray',  // 新增 warm gray
        '#F5F5F4' => 'coolGray',  // 新增 cool gray
        '#FAFAFA' => 'neutral',   // 新增 neutral
        '#F8FAFC' => 'slate',     // 新增 slate
        '#FDF2F8' => 'violet',    // 新增 violet
    );


    return isset($colors[$colorCode]) ? $colors[$colorCode] : ''; // 如果找不到，返回 'white'
}

function enqueue_custom_admin_script()
{
?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const colorMapping = {
        'red': '#FECCCC',
        'yellow': '#FDF8C8',
        'green': '#DCECE7',
        'blue': '#DBEAF6',
        'indigo': '#EEF2FF',
        'purple': '#F5E8FF',
        'pink': '#FBCFE8',
        'teal': '#D9FBE8',
        'cyan': '#C6F6FF',
        'sky': '#CAF9FF',
        'fuchsia': '#FCF3F9',
        'rose': '#FFE4E1',
        'lime': '#E3FCEC', // 新增 lime
        'orange': '#FFEDD5', // 新增 orange
        'amber': '#FEF3C7', // 新增 amber
        'emerald': '#D1FAE5', // 新增 emerald
        'mint': '#F0FDFA', // 新增 mint
        'warmGray': '#FFFBEB', // 新增 warm gray
        'coolGray': '#F5F5F4', // 新增 cool gray
        'neutral': '#FAFAFA', // 新增 neutral
        'slate': '#F8FAFC', // 新增 slate
        'violet': '#FDF2F8', // 新增 violet
    };

    // 取得 tailwind_color input 元素
    const inputElement = document.querySelector('input[name="carbon_fields_compact_input[_tailwind_color]"]');
    if (inputElement) {
        // 更新背景顏色的函數
        function updateBackgroundColor() {
            const selectedColor = inputElement.value;
            if (colorMapping[selectedColor]) {
                inputElement.style.backgroundColor = colorMapping[selectedColor];
            } else {
                inputElement.style.backgroundColor = '#FFFFFF'; // 預設白色背景
            }
        }

        // 初次加載時調用一次
        updateBackgroundColor();

        // 監聽值變化
        inputElement.addEventListener('input', updateBackgroundColor);
    }

    // 取得 tailwind_hex input 元素
    const hexInputElement = document.querySelector('input[name="carbon_fields_compact_input[_tailwind_hex]"]');
    if (hexInputElement) {
        // 更新背景顏色的函數
        function updateBackgroundColor() {
            const selectedColor = hexInputElement.value;
            hexInputElement.style.backgroundColor = selectedColor || '#FFFFFF'; // 預設白色背景
        }

        // 初次加載時調用一次
        updateBackgroundColor();

        // 監聽值變化
        hexInputElement.addEventListener('input', updateBackgroundColor);
    }

    // 取得 tailwind_hex_base_color input 元素
    const hexBaseColorInputElement = document.querySelector(
        'input[name="carbon_fields_compact_input[_tailwind_hex_base_color]"]');
    if (hexBaseColorInputElement) {
        // 更新背景顏色的函數
        function updateBaseColorBackground() {
            const selectedColor = hexBaseColorInputElement.value;
            hexBaseColorInputElement.style.backgroundColor = selectedColor || '#FFFFFF'; // 預設白色背景
        }

        // 初次加載時調用一次
        updateBaseColorBackground();

        // 監聽值變化
        hexBaseColorInputElement.addEventListener('input', updateBaseColorBackground);
    }

    // 取得 tailwind_hex_light_color input 元素
    const hexLightColorInputElement = document.querySelector(
        'input[name="carbon_fields_compact_input[_tailwind_hex_light_color]"]');
    if (hexLightColorInputElement) {
        // 更新背景顏色的函數
        function updateLightColorBackground() {
            const selectedColor = hexLightColorInputElement.value;
            hexLightColorInputElement.style.backgroundColor = selectedColor || '#FFFFFF'; // 預設白色背景
        }

        // 初次加載時調用一次
        updateLightColorBackground();

        // 監聽值變化
        hexLightColorInputElement.addEventListener('input', updateLightColorBackground);
    }

});
</script>
<?php
}
add_action('admin_footer', 'enqueue_custom_admin_script');



?>