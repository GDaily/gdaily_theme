<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-white text-gray-900 antialiased'); ?>>
    <?php wp_body_open(); ?>
    <?php do_action('tailpress_site_before'); ?>
    <?php do_action('tailpress_header'); ?>

    <header role="banner" class="site-header">
        <style>
        .scroll-hidden::-webkit-scrollbar {
            height: 0px;
            background: transparent;
        }

        /* 移除列表默认样式 */
        .main-navigation-links {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .main-navigation-links li {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        /* 移动端菜单样式 */
        .mobile-menu {
            display: none;
        }

        .mobile-menu.active {
            display: block;
        }

        /* 桌面端显示菜单 */
        @media (min-width: 1024px) {
            .mobile-menu {
                display: flex !important;
            }
        }

        .menu-toggle {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .hamburger-icon {
            display: block;
        }

        .close-icon {
            display: none;
        }

        .menu-toggle.active .hamburger-icon {
            display: none;
        }

        .menu-toggle.active .close-icon {
            display: block;
        }

        /* 確保選單按鈕在手機端右側對齊 */
        @media (max-width: 1023px) {
            .menu-toggle {
                margin-left: auto;
            }
        }

        /* 防止選單打開時頁面寬度變化 */
        body.menu-open {
            padding-right: var(--scrollbar-width, 0px);
        }

        /* 跳过链接 - 辅助功能 */
        .skip-link {
            position: absolute;
            left: -9999px;
            z-index: 999999999;
            padding: 8px 16px;
            background: #000;
            color: #fff;
            text-decoration: none;
        }

        .skip-link:focus {
            left: 6px;
            top: 7px;
        }

        /* 搜尋表單樣式 */
        .search-form-container {
            transform: translateY(-10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            display: block;
        }

        .search-form-container.active {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .search-toggle {
            transition: all 0.2s ease;
        }

        .search-toggle:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .search-toggle.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        /* 響應式搜尋 */
        @media (max-width: 640px) {
            .search-form-container {
                width: calc(100vw - 48px);
                right: -12px;
            }
        }
        </style>

        <!-- 辅助功能跳过链接 -->
        <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'textdomain'); ?></a>


        <nav role="navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'textdomain'); ?>"
            class="relative bg-white primary-navigation">
            <div class="container px-6 py-3 mx-auto lg:flex lg:items-center">
                <!-- 网站Logo/品牌 -->
                <div class="flex items-center justify-between lg:justify-start">
                    <!-- 移动端空白占位符，让logo居中 -->
                    <div class="w-6 h-6 lg:hidden"></div>

                    <div class="site-branding">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="custom-logo-link"
                            aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/resources/img/________logo.svg"
                                alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="w-24 ">
                        </a>
                    </div>

                    <!-- 移动端菜单按钮 -->
                    <div class="flex lg:hidden">
                        <button
                            class="text-gray-500 menu-toggle hover:text-gray-600 focus:outline-none focus:text-gray-600"
                            type="button" aria-expanded="false" aria-controls="primary-menu"
                            aria-label="<?php esc_attr_e('Toggle navigation menu', 'textdomain'); ?>">
                            <svg class="w-6 h-6 hamburger-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
                            </svg>
                            <svg class="w-6 h-6 close-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 主导航菜单 -->
                <div class="absolute inset-x-0 z-20 w-full px-6 py-4 bg-white shadow-lg mobile-menu lg:mt-0 lg:p-0 lg:top-0 lg:relative lg:shadow-none lg:opacity-100 lg:translate-x-0 lg:flex lg:items-center lg:w-auto lg:ml-6 lg:flex-1"
                    id="primary-menu">

                    <!-- 移動端：垂直布局 -->
                    <div class="flex flex-col lg:hidden space-y-4">
                        <!-- 主導航鏈接 -->
                        <div>
                            <ul class="flex flex-col space-y-2 main-navigation-links">
                                <li role="menuitem">
                                    <a href="<?php echo esc_url(get_category_link(551)); ?>"
                                        class="block px-3 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:bg-gray-100"
                                        <?php if (is_category(551)) echo 'aria-current="page"'; ?>>
                                        <?php echo esc_html(get_cat_name(551)); ?>
                                    </a>
                                </li>
                                <li role="menuitem">
                                    <a href="<?php echo esc_url(get_category_link(1768)); ?>"
                                        class="block px-3 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:bg-gray-100"
                                        <?php if (is_category(1768)) echo 'aria-current="page"'; ?>>
                                        <?php echo esc_html(get_cat_name(1768)); ?>
                                    </a>
                                </li>
                                <li role="menuitem">
                                    <a href="<?php echo esc_url(get_category_link(562)); ?>"
                                        class="block px-3 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:bg-gray-100"
                                        <?php if (is_category(562)) echo 'aria-current="page"'; ?>>
                                        <?php echo esc_html(get_cat_name(562)); ?>
                                    </a>
                                </li>
                                <li role="menuitem">
                                    <a href="<?php echo esc_url(get_category_link(1098)); ?>"
                                        class="block px-3 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:bg-gray-100"
                                        <?php if (is_category(1098)) echo 'aria-current="page"'; ?>>
                                        <?php echo esc_html(get_cat_name(1098)); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- 搜尋區塊 - 移動端直接顯示輸入框 -->
                        <div class="w-full">
                            <div class="text-sm text-gray-600 mb-2 font-medium">搜尋</div>
                            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
                                class="mobile-search-form">
                                <div class="relative">
                                    <input type="search"
                                        class="w-full px-4 py-3 pr-12 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                                        placeholder="搜尋文章和應用程式..." name="gs" autocomplete="off" />
                                    <button type="submit"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-blue-600 transition-colors"
                                        aria-label="搜尋">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- 社交媒體鏈接 - 移動端重新設計 -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="text-sm text-gray-600 mb-3 font-medium">關注我們</div>
                            <div class="flex space-x-4">
                                <a href="https://www.facebook.com/GDaily.org/" target="_blank" rel="noopener noreferrer"
                                    class="flex items-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                    aria-label="Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="18" width="18" viewBox="0 0 512 512"
                                        class="mr-2">
                                        <path fill="currentColor"
                                            d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                                    </svg>
                                    <span class="text-sm">Facebook</span>
                                </a>
                                <a href="https://t.me/gdaily_org" target="_blank" rel="noopener noreferrer"
                                    class="flex items-center px-3 py-2 bg-sky-50 text-sky-600 rounded-lg hover:bg-sky-100 transition-colors"
                                    aria-label="Telegram">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="18" width="17" viewBox="0 0 496 512"
                                        class="mr-2">
                                        <path fill="currentColor"
                                            d="M248 8C111 8 0 119 0 256S111 504 248 504 496 393 496 256 385 8 248 8zM363 176.7c-3.7 39.2-19.9 134.4-28.1 178.3-3.5 18.6-10.3 24.8-16.9 25.4-14.4 1.3-25.3-9.5-39.3-18.7-21.8-14.3-34.2-23.2-55.3-37.2-24.5-16.1-8.6-25 5.3-39.5 3.7-3.8 67.1-61.5 68.3-66.7 .2-.7 .3-3.1-1.2-4.4s-3.6-.8-5.1-.5q-3.3 .7-104.6 69.1-14.8 10.2-26.9 9.9c-8.9-.2-25.9-5-38.6-9.1-15.5-5-27.9-7.7-26.8-16.3q.8-6.7 18.5-13.7 108.4-47.2 144.6-62.3c68.9-28.6 83.2-33.6 92.5-33.8 2.1 0 6.6 .5 9.6 2.9a10.5 10.5 0 0 1 3.5 6.7A43.8 43.8 0 0 1 363 176.7z" />
                                    </svg>
                                    <span class="text-sm">Telegram</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 桌面端：原有的水平布局 -->
                    <div class="hidden lg:flex lg:items-center lg:justify-between lg:w-full">
                        <!-- 主导航链接 -->
                        <div class="flex lg:flex-1" role="menubar"
                            aria-label="<?php esc_attr_e('Main Navigation', 'textdomain'); ?>">
                            <ul class="flex space-x-6 main-navigation-links">
                                <li role="menuitem">
                                    <a href="<?php echo esc_url(get_category_link(551)); ?>"
                                        class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white"
                                        <?php if (is_category(551)) echo 'aria-current="page"'; ?>>
                                        <?php echo esc_html(get_cat_name(551)); ?>
                                    </a>
                                </li>
                                <li role="menuitem">
                                    <a href="<?php echo esc_url(get_category_link(1768)); ?>"
                                        class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white"
                                        <?php if (is_category(1768)) echo 'aria-current="page"'; ?>>
                                        <?php echo esc_html(get_cat_name(1768)); ?>
                                    </a>
                                </li>
                                <li role="menuitem">
                                    <a href="<?php echo esc_url(get_category_link(562)); ?>"
                                        class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white"
                                        <?php if (is_category(562)) echo 'aria-current="page"'; ?>>
                                        <?php echo esc_html(get_cat_name(562)); ?>
                                    </a>
                                </li>
                                <li role="menuitem">
                                    <a href="<?php echo esc_url(get_category_link(1098)); ?>"
                                        class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white"
                                        <?php if (is_category(1098)) echo 'aria-current="page"'; ?>>
                                        <?php echo esc_html(get_cat_name(1098)); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- 右側：社交媒體 + 搜尋 -->
                        <div class="flex items-center space-x-4">
                            <!-- 社交媒体链接 -->
                            <div class="flex social-links" role="complementary"
                                aria-label="<?php esc_attr_e('Social Media Links', 'textdomain'); ?>">
                                <a href="https://www.facebook.com/GDaily.org/" target="_blank" rel="noopener noreferrer"
                                    aria-label="<?php esc_attr_e('Visit our Facebook page', 'textdomain'); ?>">
                                    <span class="flex items-center pl-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20"
                                            viewBox="0 0 512 512">
                                            <path fill="#74C0FC"
                                                d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                                        </svg>
                                    </span>
                                </a>
                                <a href="https://t.me/gdaily_org" target="_blank" rel="noopener noreferrer"
                                    aria-label="<?php esc_attr_e('Visit our Telegram channel', 'textdomain'); ?>">
                                    <span class="flex items-center pl-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="19.375"
                                            viewBox="0 0 496 512">
                                            <path fill="#9ee2ff"
                                                d="M248 8C111 8 0 119 0 256S111 504 248 504 496 393 496 256 385 8 248 8zM363 176.7c-3.7 39.2-19.9 134.4-28.1 178.3-3.5 18.6-10.3 24.8-16.9 25.4-14.4 1.3-25.3-9.5-39.3-18.7-21.8-14.3-34.2-23.2-55.3-37.2-24.5-16.1-8.6-25 5.3-39.5 3.7-3.8 67.1-61.5 68.3-66.7 .2-.7 .3-3.1-1.2-4.4s-3.6-.8-5.1-.5q-3.3 .7-104.6 69.1-14.8 10.2-26.9 9.9c-8.9-.2-25.9-5-38.6-9.1-15.5-5-27.9-7.7-26.8-16.3q.8-6.7 18.5-13.7 108.4-47.2 144.6-62.3c68.9-28.6 83.2-33.6 92.5-33.8 2.1 0 6.6 .5 9.6 2.9a10.5 10.5 0 0 1 3.5 6.7A43.8 43.8 0 0 1 363 176.7z" />
                                        </svg>
                                    </span>
                                </a>
                            </div>

                            <!-- 桌面端搜尋按鈕 -->
                            <div class="relative flex items-center" role="search"
                                aria-label="<?php esc_attr_e('Search', 'textdomain'); ?>">
                                <!-- 搜尋圖示按鈕 -->
                                <button type="button"
                                    class="search-toggle p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 rounded-lg transition-colors duration-200"
                                    aria-label="<?php esc_attr_e('Toggle search', 'textdomain'); ?>"
                                    aria-expanded="false" aria-controls="search-form">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>

                                <!-- 桌面端搜尋表單 -->
                                <div class="search-form-container absolute top-full right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-50"
                                    id="search-form">
                                    <form role="search" method="get" class="search-form"
                                        action="<?php echo esc_url(home_url('/')); ?>">
                                        <label for="search-input"
                                            class="sr-only"><?php esc_html_e('Search for:', 'textdomain'); ?></label>
                                        <div class="relative">
                                            <input type="search" id="search-input"
                                                class="w-full px-4 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="<?php echo esc_attr_x('搜尋文章...', 'placeholder', 'textdomain'); ?>"
                                                value="<?php echo isset($_GET['gs']) ? esc_attr($_GET['gs']) : ''; ?>"
                                                name="gs" autocomplete="off" />
                                            <button type="submit"
                                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600"
                                                aria-label="<?php esc_attr_e('Submit search', 'textdomain'); ?>">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </form>

                                    <!-- 搜索提示 -->
                                    <div class="mt-3 text-xs text-gray-500">
                                        💡 輸入關鍵字搜尋相關文章和應用程式
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </nav>

        <script>
        function toggleMobileMenu() {
            const mobileMenu = document.querySelector('.mobile-menu');
            const menuToggle = document.querySelector('.menu-toggle');

            if (mobileMenu && menuToggle) {
                const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

                mobileMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
                menuToggle.setAttribute('aria-expanded', !isExpanded);

                // 防止背景滾動但保持頁面寬度
                if (mobileMenu.classList.contains('active')) {
                    // 計算滾動條寬度
                    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                    document.documentElement.style.setProperty('--scrollbar-width', scrollbarWidth + 'px');
                    document.body.classList.add('menu-open');
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.classList.remove('menu-open');
                    document.body.style.overflow = '';
                    document.documentElement.style.removeProperty('--scrollbar-width');
                }
            }
        }

        // 确保DOM加载完成后绑定事件
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('.menu-toggle');
            if (menuButton) {
                menuButton.addEventListener('click', toggleMobileMenu);
            }

            // 搜尋功能
            function toggleSearchForm() {
                const searchToggle = document.querySelector('.search-toggle');
                const searchForm = document.querySelector('.search-form-container');
                const searchInput = document.querySelector('#search-input');

                if (searchToggle && searchForm) {
                    const isActive = searchForm.classList.contains('active');

                    if (!isActive) {
                        searchForm.classList.add('active');
                        searchToggle.classList.add('active');
                        searchToggle.setAttribute('aria-expanded', 'true');
                        // 聚焦到搜尋輸入框
                        setTimeout(() => {
                            if (searchInput) {
                                searchInput.focus();
                            }
                        }, 300);
                    } else {
                        searchForm.classList.remove('active');
                        searchToggle.classList.remove('active');
                        searchToggle.setAttribute('aria-expanded', 'false');
                    }
                }
            }

            const searchButton = document.querySelector('.search-toggle');
            if (searchButton) {
                searchButton.addEventListener('click', toggleSearchForm);
            }

            // 點擊外部關閉搜尋表單
            document.addEventListener('click', function(event) {
                const searchForm = document.querySelector('.search-form-container');
                const searchToggle = document.querySelector('.search-toggle');
                const searchContainer = searchToggle?.parentElement;

                if (searchForm && searchToggle &&
                    searchForm.classList.contains('active') &&
                    !searchContainer?.contains(event.target)) {
                    toggleSearchForm();
                }
            });

            // ESC鍵關閉搜尋表單
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const searchForm = document.querySelector('.search-form-container');
                    if (searchForm && searchForm.classList.contains('active')) {
                        toggleSearchForm();
                    }
                }
            });

            // 點擊外部關閉選單
            document.addEventListener('click', function(event) {
                const mobileMenu = document.querySelector('.mobile-menu');
                const menuToggle = document.querySelector('.menu-toggle');
                const nav = document.querySelector('.primary-navigation');

                if (mobileMenu && menuToggle &&
                    mobileMenu.classList.contains('active') &&
                    !nav.contains(event.target)) {
                    toggleMobileMenu();
                }
            });

            // 桌面端自動關閉移動選單
            window.addEventListener('resize', function() {
                const mobileMenu = document.querySelector('.mobile-menu');
                const menuToggle = document.querySelector('.menu-toggle');

                if (window.innerWidth >= 1024 && mobileMenu && menuToggle) {
                    mobileMenu.classList.remove('active');
                    menuToggle.classList.remove('active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    document.body.classList.remove('menu-open');
                    document.body.style.overflow = '';
                    document.documentElement.style.removeProperty('--scrollbar-width');
                }
            });

            // 搜索分析追蹤 (Google Analytics)
            function trackSearchEvent(query) {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'search', {
                        search_term: query,
                        event_category: 'engagement',
                        event_label: 'site_search'
                    });
                }
            }

            // 提交搜索時追蹤
            document.querySelector('.search-form')?.addEventListener('submit', function(e) {
                const searchInput = document.querySelector('#search-input');
                const query = searchInput?.value.trim();
                if (query) {
                    trackSearchEvent(query);
                }
            });

            // 移動端搜索表單追蹤
            document.querySelector('.mobile-search-form')?.addEventListener('submit', function(e) {
                const mobileSearchInput = this.querySelector('input[name="gs"]');
                const query = mobileSearchInput?.value.trim();
                if (query) {
                    trackSearchEvent(query);
                }
            });
        });
        </script>
    </header>

    <?php if (is_front_page()) { ?>
    <?php } ?>

    <?php do_action('tailpress_content_start'); ?>

    <main id="main" role="main" class="site-main">