<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', function () {
    \Carbon_Fields\Carbon_Fields::boot();
});

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', __('Tailwind Color', '自動偵測的被景色'))
        ->where('post_type', '=', 'post')
        ->add_fields(array(
            //新增 tailwind_hex_base_color
            Field::make('text', 'tailwind_hex_base_color', __('圖片深色調基礎十六進制碼'))
                ->set_default_value('')
                ->set_attribute('readOnly', true)
                ->set_width(25),

            //新增 tailwind_hex_base_color_custom 自訂深色調
            Field::make('color', 'tailwind_hex_base_color_custom', __('自訂圖片深色調基礎十六進制碼'))
                ->set_default_value('')
                ->set_palette(array(
                    '#dc2626', // red-600
                    '#ca8a04', // yellow-600
                    '#16a34a', // green-600
                    '#2563eb', // blue-600
                    '#4f46e5', // indigo-600
                    '#9333ea', // purple-600
                    '#db2777', // pink-600
                    '#0d9488', // teal-600
                    '#0891b2', // cyan-600
                    '#0284c7', // sky-600
                    '#a21caf', // fuchsia-600
                    '#e11d48', // rose-600
                ))
                ->set_width(25),

            //新增 tailwind_hex_light_color
            Field::make('text', 'tailwind_hex_light_color', __('圖片淺色調基礎十六進制碼'))
                ->set_default_value('')
                ->set_attribute('readOnly', true)
                ->set_width(25),

            //新增 tailwind_hex_light_color_custom 自訂淺色調
            Field::make('color', 'tailwind_hex_light_color_custom', __('自訂圖片淺色調基礎十六進制碼'))
                ->set_default_value('')
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
                ->set_width(25),
        ));

    Container::make('post_meta', __('APP簡稱', 'crb'))
        ->where('post_type', '=', 'post')
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

    /*   custom META 標題描述欄位 */
    Container::make('post_meta', 'Meta Title & Description')
        ->where('post_type', '=', 'post')
        ->add_fields(array(
            Field::make('text', 'meta_title', 'Meta Title')
                ->set_default_value('')
                ->set_width(50),
            Field::make('textarea', 'meta_description', 'Meta Description')
                ->set_default_value('')
                ->set_width(100),
        ));
});

// 添加驗證，防止設置為 #ffffff
add_action('carbon_fields_post_meta_container_saved', function ($post_id, $container) {
    // 檢查基礎色
    $base_color_custom = carbon_get_post_meta($post_id, 'tailwind_hex_base_color_custom');
    if ($base_color_custom === '#ffffff' || $base_color_custom === '#FFFFFF') {
        carbon_set_post_meta($post_id, 'tailwind_hex_base_color_custom', '');
    }

    // 檢查淺色調
    $light_color_custom = carbon_get_post_meta($post_id, 'tailwind_hex_light_color_custom');
    if ($light_color_custom === '#ffffff' || $light_color_custom === '#FFFFFF') {
        carbon_set_post_meta($post_id, 'tailwind_hex_light_color_custom', '');
    }
}, 10, 2);

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


    return isset($colors[$colorCode]) ? $colors[$colorCode] : '';
}

function enqueue_custom_admin_script()
{
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 輔助函數：根據背景色計算對比文字顏色
            function getContrastColor(hexColor) {
                if (!hexColor) return '#000000';
                const hex = hexColor.replace('#', '');

                if (hex.length !== 6) return '#000000';

                const r = parseInt(hex.substr(0, 2), 16);
                const g = parseInt(hex.substr(2, 2), 16);
                const b = parseInt(hex.substr(4, 2), 16);

                const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                return brightness > 128 ? '#000000' : '#FFFFFF';
            }

            // 初始化顏色欄位的函數
            function initializeColorFields() {
                const fieldConfigs = [{
                        selector: 'input[name="carbon_fields_compact_input[_tailwind_hex_base_color]"]',
                        defaultColor: '#FFFFFF',
                        hasContrast: false
                    },
                    {
                        selector: 'input[name="carbon_fields_compact_input[_tailwind_hex_light_color]"]',
                        defaultColor: '#FFFFFF',
                        hasContrast: false
                    }
                ];

                fieldConfigs.forEach(config => {
                    const element = document.querySelector(config.selector);
                    if (element && !element.dataset.colorInitialized) {
                        element.dataset.colorInitialized = 'true';

                        function updateBackgroundColor() {
                            const selectedColor = element.value;
                            if (selectedColor) {
                                element.style.backgroundColor = selectedColor;
                                if (config.hasContrast) {
                                    element.style.color = getContrastColor(selectedColor);
                                }
                            } else {
                                element.style.backgroundColor = config.defaultColor;
                                if (config.hasContrast) {
                                    element.style.color = getContrastColor(config.defaultColor);
                                }
                            }
                        }

                        // 初始設定
                        updateBackgroundColor();

                        // 事件監聽
                        element.addEventListener('input', updateBackgroundColor);
                        element.addEventListener('change', updateBackgroundColor);
                        element.addEventListener('keyup', updateBackgroundColor);

                        console.log('Color field initialized:', config.selector);
                    }
                });
            }

            // 立即執行一次
            initializeColorFields();

            // 延遲執行，確保 Carbon Fields 完全載入
            setTimeout(initializeColorFields, 500);
            setTimeout(initializeColorFields, 1000);
            setTimeout(initializeColorFields, 2000);

            // 使用 MutationObserver 監聽 DOM 變化
            if (window.MutationObserver) {
                const observer = new MutationObserver(function(mutations) {
                    let shouldInit = false;
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            // 檢查是否有新的 Carbon Fields 元素加入
                            for (let node of mutation.addedNodes) {
                                if (node.nodeType === 1 && (
                                        node.querySelector && node.querySelector(
                                            '[name*="carbon_fields_compact_input"]') ||
                                        node.matches && node.matches(
                                            '[name*="carbon_fields_compact_input"]')
                                    )) {
                                    shouldInit = true;
                                    break;
                                }
                            }
                        }
                    });

                    if (shouldInit) {
                        setTimeout(initializeColorFields, 100);
                    }
                });

                // 開始觀察
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }

            // 監聽 Carbon Fields 相關事件（如果存在）
            document.addEventListener('carbon-fields-loaded', initializeColorFields);
            document.addEventListener('carbon-fields-container-rendered', initializeColorFields);
        });
    </script>
<?php
}
add_action('admin_footer', 'enqueue_custom_admin_script');

?>