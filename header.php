<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-white text-gray-900 antialiased' ); ?>>
    <?php wp_body_open(); ?>
    <?php do_action( 'tailpress_site_before' ); ?>
    <?php do_action( 'tailpress_header' ); ?>

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
        @media (min-width: 768px) {
            .mobile-menu {
                display: flex !important;
            }
        }

        .menu-toggle {
            cursor: pointer;
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
        </style>

        <!-- 辅助功能跳过链接 -->
        <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'textdomain' ); ?></a>


        <nav role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'textdomain' ); ?>"
            class="relative bg-white primary-navigation">
            <div class="container px-6 py-3 mx-auto md:flex">
                <!-- 网站Logo/品牌 -->
                <div class="site-branding">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="custom-logo-link"
                        aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/resources/img/________logo.svg"
                            alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class=" w-24  ">

                    </a>
                </div>
                <div class="flex items-center justify-between">


                    <!-- 移动端菜单按钮 -->
                    <div class="flex lg:hidden">
                        <button
                            class="menu-toggle text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                            type="button" aria-expanded="false" aria-controls="primary-menu"
                            aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'textdomain' ); ?>">
                            <svg class="hamburger-icon w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
                            </svg>
                            <svg class="close-icon w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 主导航菜单 -->
                <div class="mobile-menu   absolute inset-x-0 z-20 w-full px-6 py-4 bg-white md:mt-0 md:p-0 md:top-0 md:relative md:opacity-100 md:translate-x-0 md:flex md:items-center md:justify-between"
                    id="primary-menu">

                    <!-- 主导航链接 -->
                    <div class=" flex justify-between" role="menubar"
                        aria-label="<?php esc_attr_e( 'Main Navigation', 'textdomain' ); ?>">
                        <ul
                            class="flex  justify-between flex-col px-2 -mx-4 md:flex-row md:mx-10 md:py-0 main-navigation-links">
                            <li role="menuitem">

                                <a href="<?php echo esc_url( get_category_link(551) ); ?>"
                                    class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white md:mx-2"
                                    <?php if ( is_category(551) ) echo 'aria-current="page"'; ?>>
                                    <?php echo esc_html( get_cat_name(551) ); ?>
                                </a>

                            </li>
                            <li role="menuitem">
                                <a href="<?php echo esc_url( get_category_link(1768) ); ?>"
                                    class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white md:mx-2"
                                    <?php if ( is_category(1768) ) echo 'aria-current="page"'; ?>>
                                    <?php echo esc_html( get_cat_name(1768) ); ?>
                                </a>
                            </li>
                            <li role="menuitem">
                                <a href="<?php echo esc_url( get_category_link(562) ); ?>"
                                    class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white md:mx-2"
                                    <?php if ( is_category(562) ) echo 'aria-current="page"'; ?>>
                                    <?php echo esc_html( get_cat_name(562) ); ?>
                                </a>
                            </li>
                            <li role="menuitem">
                                <a href="<?php echo esc_url( get_category_link(1098) ); ?>"
                                    class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white md:mx-2"
                                    <?php if ( is_category(1098) ) echo 'aria-current="page"'; ?>>
                                    <?php echo esc_html( get_cat_name(1098) ); ?>
                                </a>
                            </li>
                        </ul>



                    </div>

                    <!-- 社交媒体链接 -->
                    <div class="relative flex mt-4 md:mt-0 md:ml-auto social-links" role="complementary"
                        aria-label="<?php esc_attr_e( 'Social Media Links', 'textdomain' ); ?>">
                        <a href="https://www.facebook.com/GDaily.org/" target="_blank" rel="noopener noreferrer"
                            aria-label="<?php esc_attr_e( 'Visit our Facebook page', 'textdomain' ); ?>">
                            <span class="inset-y-0 left-0 flex items-center pl-3">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512"
                                    aria-hidden="true">
                                    <path fill="#74C0FC"
                                        d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                                </svg>
                            </span>
                        </a>

                        <a href="https://t.me/gdaily_org" target="_blank" rel="noopener noreferrer"
                            aria-label="<?php esc_attr_e( 'Visit our Telegram channel', 'textdomain' ); ?>">
                            <span class="inset-y-0 left-0 flex items-center pl-3">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="19.375" viewBox="0 0 496 512"
                                    aria-hidden="true">
                                    <path fill="#9ee2ff"
                                        d="M248 8C111 8 0 119 0 256S111 504 248 504 496 393 496 256 385 8 248 8zM363 176.7c-3.7 39.2-19.9 134.4-28.1 178.3-3.5 18.6-10.3 24.8-16.9 25.4-14.4 1.3-25.3-9.5-39.3-18.7-21.8-14.3-34.2-23.2-55.3-37.2-24.5-16.1-8.6-25 5.3-39.5 3.7-3.8 67.1-61.5 68.3-66.7 .2-.7 .3-3.1-1.2-4.4s-3.6-.8-5.1-.5q-3.3 .7-104.6 69.1-14.8 10.2-26.9 9.9c-8.9-.2-25.9-5-38.6-9.1-15.5-5-27.9-7.7-26.8-16.3q.8-6.7 18.5-13.7 108.4-47.2 144.6-62.3c68.9-28.6 83.2-33.6 92.5-33.8 2.1 0 6.6 .5 9.6 2.9a10.5 10.5 0 0 1 3.5 6.7A43.8 43.8 0 0 1 363 176.7z" />
                                </svg>
                            </span>
                        </a>
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
            }
        }

        // 确保DOM加载完成后绑定事件
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('.menu-toggle');
            if (menuButton) {
                menuButton.addEventListener('click', toggleMobileMenu);
            }
        });
        </script>
    </header>

    <?php if ( is_front_page() ) { ?>
    <?php } ?>

    <?php do_action( 'tailpress_content_start' ); ?>

    <main id="main" role="main" class="site-main">