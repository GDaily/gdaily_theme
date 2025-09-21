<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    
    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://pagead2.googlesyndication.com">
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


    <!-- 插入 adsense js 檔案 <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7349735987764759"
     crossorigin="anonymous"></script> 
     單為單篇文章時需要檢查 carbon_get_post_meta($post->ID, 'adsense_enable') 
     其他頁面不需要判斷固定直接引入 -->

    <?php
global $post;

// 取得值並轉換成布林
$adsense_enable_raw = carbon_get_post_meta( $post->ID, 'adsense_enable' );
$adsense_enable = filter_var( $adsense_enable_raw, FILTER_VALIDATE_BOOLEAN );

if ( ( is_single() && $adsense_enable ) || ! is_single() ) {
    ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7349735987764759"
        crossorigin="anonymous"></script>
    <?php
}
?>


    <?php wp_head(); ?>
</head>


<body <?php body_class( 'bg-white text-gray-900 antialiased' ); ?>>

    <?php do_action( 'tailpress_site_before' ); ?>


    <?php do_action( 'tailpress_header' ); ?>

    <header>

        <!-- Require css -->
        <style>
        .scroll-hidden::-webkit-scrollbar {
            height: 0px;
            background: transparent;
            /* make scrollbar transparent */
        }
        </style>

        <nav x-data="{ isOpen: false }" class="relative bg-white ">
            <div class="container px-6 py-3 mx-auto md:flex">
                <div class="flex items-center justify-between">
                    <a href="<?php echo home_url() ;?>">

                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" width="128px" height="48px" xml:space="preserve">
                            <g id="PathID_17" transform="matrix(0.243439, 0.0864258, -0.0854797, 0.24379, 11.9, -5.55)"
                                opacity="0.976563">
                                <path style="fill: #62eef7;fill-opacity:0.568627;"
                                    d="M169.45 48.75L160.45 66.15L178.1 75.1L178.35 75.35Q178.65 77.9 175.4 82.1Q170.4 89.35 161.4 94.75Q151.05 101.25 139.3 102.95Q148.8 113.7 152.2 132.6Q154.75 147.25 153 153.45L152.75 154L152.5 154L133.35 151L130.1 170.4L130.1 170.65L129.85 170.65Q127.4 172.1 118.65 168.9Q110.45 165.65 104 160.2Q93.3 151.5 89.1 139.55Q83.35 150.5 67.9 161.2Q53.75 171.15 48.5 171.15L48.05 171.15L48.05 170.65L45.1 151.5L25.9 154.45L25.65 154.45L25.45 154.2Q23.9 153 23.9 148.05Q23.9 139.05 28.4 128.1Q33.65 115.4 42.85 107.2Q28.4 104.95 14.25 92.5Q8.75 87.8 4.5 82.85Q0.8 78.35 0.3 76.1L0 75.65L17.7 66.7L8.75 49.05L9 48.75Q9.75 47.05 14.45 45.8Q24.15 42.8 36.9 43.8Q50.75 45.05 59.95 50.5Q56 36.05 63.5 18.65Q65.95 12.25 69.7 6.75Q72.7 1.8 74.65 0.25L75.15 0L75.4 0.55L89.35 14.2L103 0.55L103 0.25L103.5 0.25Q105.95 0.55 109.95 7Q115.45 15.2 118.15 25.85Q121.2 38.85 118.4 49.3Q129.85 41.55 148.5 42.8Q155.2 43.55 161.7 45.25Q167.2 46.8 169.15 48.05L169.65 48.5L169.45 48.75M117.4 50.3L117.4 50.5L117.7 50.5L117.4 50.3">
                                </path>
                            </g>
                            <g id="PathID_18" transform="matrix(0.243439, 0.0864258, -0.0854797, 0.24379, 11.9, -5.55)"
                                opacity="0.976563">
                            </g>
                            <g id="PathID_19" transform="matrix(0.531647, 0, 0, 0.531647, 16, 33.25)"
                                opacity="0.972656">
                                <path style="fill:#FFFFFF;fill-opacity:1"
                                    d="M35.4 -5.7Q34.3 -4.45 32.75 -3.3Q31.2 -2.2 29.25 -1.35Q27.35 -0.5 25.05 0Q22.8 0.5 20.2 0.5Q15.8 0.5 12.55 -0.9Q9.3 -2.3 7.15 -4.8Q5 -7.3 3.95 -10.75Q2.9 -14.2 2.9 -18.35Q2.9 -22.6 3.95 -26Q5 -29.45 7.1 -31.85Q9.25 -34.25 12.45 -35.55Q15.65 -36.85 19.9 -36.85Q23 -36.85 25.35 -36.15Q27.75 -35.5 29.5 -34.3Q31.25 -33.15 32.5 -31.55Q33.75 -29.95 34.6 -28.15L29.35 -26.25Q28.8 -27.45 28 -28.55Q27.2 -29.65 26.05 -30.45Q24.9 -31.3 23.35 -31.75Q21.85 -32.25 19.9 -32.25Q17 -32.25 14.9 -31.25Q12.8 -30.25 11.4 -28.45Q10.05 -26.65 9.4 -24.05Q8.75 -21.5 8.75 -18.35Q8.75 -15.2 9.45 -12.55Q10.2 -9.95 11.6 -8.05Q13.05 -6.15 15.2 -5.1Q17.35 -4.05 20.2 -4.05Q22 -4.05 23.5 -4.4Q25.05 -4.75 26.25 -5.3Q27.5 -5.85 28.45 -6.5Q29.45 -7.2 30.1 -7.85L30.1 -14.15L20.3 -14.15L20.3 -18.5L35.4 -18.5L35.4 -5.7">
                                </path>
                            </g>
                            <g id="PathID_20" transform="matrix(0.531647, 0, 0, 0.531647, 16, 33.25)"
                                opacity="0.972656">
                            </g>
                            <g id="PathID_21" transform="matrix(0.466949, 0, 0, 0.466949, 50.8, 32.6)"
                                opacity="0.996094">
                                <path style="fill:#B2B2B2;fill-opacity:0.568627"
                                    d="M31.5 -2.45Q26.1 0.6 19.5 0.6Q11.8 0.6 7.05 -4.35Q2.35 -9.3 2.35 -17.45Q2.35 -25.75 7.6 -31.1Q12.85 -36.45 20.95 -36.45Q26.8 -36.45 30.75 -34.55L30.75 -29.9Q26.4 -32.65 20.45 -32.65Q14.45 -32.65 10.6 -28.5Q6.75 -24.35 6.75 -17.75Q6.75 -10.95 10.3 -7.05Q13.9 -3.15 20 -3.15Q24.2 -3.15 27.3 -4.85L27.3 -14.9L19.45 -14.9L19.45 -18.7L31.5 -18.7L31.5 -2.45">
                                </path>
                            </g>
                            <g id="PathID_22" transform="matrix(0.466949, 0, 0, 0.466949, 50.8, 32.6)"
                                opacity="0.996094">
                            </g>
                            <g id="PathID_23" transform="matrix(0.466949, 0, 0, 0.466949, 67.2, 32.6)"
                                opacity="0.996094">
                                <path style="fill:#B2B2B2;fill-opacity:0.568627"
                                    d="M4.7 0L4.7 -35.85L14.6 -35.85Q33.55 -35.85 33.55 -18.35Q33.55 -10.05 28.25 -5Q23 0 14.2 0L4.7 0M8.9 -32.05L8.9 -3.8L14.25 -3.8Q21.3 -3.8 25.2 -7.55Q29.15 -11.35 29.15 -18.25Q29.15 -32.05 14.5 -32.05L8.9 -32.05">
                                </path>
                            </g>
                            <g id="PathID_24" transform="matrix(0.466949, 0, 0, 0.466949, 67.2, 32.6)"
                                opacity="0.996094">
                            </g>
                            <g id="PathID_25" transform="matrix(0.466949, 0, 0, 0.466949, 83.95, 32.6)"
                                opacity="0.996094">
                                <path style="fill:#B2B2B2;fill-opacity:0.568627"
                                    d="M22.45 0L18.35 0L18.35 -4L18.25 -4Q15.6 0.6 10.4 0.6Q6.55 0.6 4.4 -1.4Q2.25 -3.45 2.25 -6.8Q2.25 -13.95 10.7 -15.15L18.35 -16.2Q18.35 -22.75 13.1 -22.75Q8.45 -22.75 4.75 -19.6L4.75 -23.8Q8.5 -26.2 13.45 -26.2Q22.45 -26.2 22.45 -16.65L22.45 0M18.35 -12.95L12.2 -12.1Q9.35 -11.7 7.9 -10.65Q6.45 -9.65 6.45 -7.1Q6.45 -5.2 7.75 -4Q9.1 -2.85 11.35 -2.85Q14.4 -2.85 16.35 -4.95Q18.35 -7.1 18.35 -10.4L18.35 -12.95">
                                </path>
                            </g>
                            <g id="PathID_26" transform="matrix(0.466949, 0, 0, 0.466949, 83.95, 32.6)"
                                opacity="0.996094">
                            </g>
                            <g id="PathID_27" transform="matrix(0.466949, 0, 0, 0.466949, 96.1, 32.6)"
                                opacity="0.996094">
                                <path style="fill:#B2B2B2;fill-opacity:0.568627"
                                    d="M5.95 -32.1Q4.85 -32.1 4.05 -32.85Q3.3 -33.6 3.3 -34.75Q3.3 -35.9 4.05 -36.65Q4.85 -37.4 5.95 -37.4Q7.1 -37.4 7.85 -36.65Q8.65 -35.9 8.65 -34.75Q8.65 -33.65 7.85 -32.85Q7.1 -32.1 5.95 -32.1M7.95 -7.15Q7.95 -3.6 10.7 -3.6Q11.8 -3.6 12.65 -3.9L12.65 -0.2Q12.2 -0.05 11.4 0.05Q10.6 0.15 9.75 0.15Q8.5 0.15 7.4 -0.25Q6.35 -0.65 5.55 -1.45Q4.8 -2.3 4.3 -3.5Q3.85 -4.75 3.85 -6.4L3.85 -25.6L7.95 -25.6L7.95 -7.15">
                                </path>
                            </g>
                            <g id="PathID_28" transform="matrix(0.466949, 0, 0, 0.466949, 96.1, 32.6)"
                                opacity="0.996094">
                            </g>
                            <g id="PathID_29" transform="matrix(0.466949, 0, 0, 0.466949, 102.1, 32.6)"
                                opacity="0.996094">
                                <path style="fill:#B2B2B2;fill-opacity:0.568627"
                                    d="M7.95 -7.15Q7.95 -3.6 10.7 -3.6Q11.8 -3.6 12.65 -3.9L12.65 -0.2Q12.2 -0.05 11.4 0.05Q10.6 0.15 9.75 0.15Q8.5 0.15 7.4 -0.25Q6.35 -0.65 5.55 -1.45Q4.8 -2.3 4.3 -3.5Q3.85 -4.75 3.85 -6.4L3.85 -37.9L7.95 -37.9L7.95 -7.15">
                                </path>
                            </g>
                            <g id="PathID_30" transform="matrix(0.466949, 0, 0, 0.466949, 102.1, 32.6)"
                                opacity="0.996094">
                            </g>
                            <g id="PathID_31" transform="matrix(0.466949, 0, 0, 0.466949, 108.15, 32.6)"
                                opacity="0.996094">
                                <path style="fill:#B2B2B2;fill-opacity:0.568627"
                                    d="M24.5 -25.6L12.75 4.1Q9.6 12.05 3.9 12.05Q2.3 12.05 1.2 11.75L1.2 8.05Q2.55 8.5 3.65 8.5Q6.75 8.5 8.3 4.8L10.35 -0.05L0.35 -25.6L4.9 -25.6L11.8 -5.9L12.35 -3.95L12.5 -3.95Q12.6 -4.55 13 -5.85L20.25 -25.6L24.5 -25.6">
                                </path>
                            </g>
                            <g id="PathID_32" transform="matrix(0.466949, 0, 0, 0.466949, 108.15, 32.6)"
                                opacity="0.996094">
                            </g>
                        </svg>


                    </a>

                    <!-- Mobile menu button -->
                    <div class="flex lg:hidden ">
                        <button x-cloak @click="isOpen = !isOpen" type="button"
                            class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600 "
                            aria-label="toggle menu">

                            <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 " fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
                            </svg>

                            <svg x-show="isOpen" xmlns="http://www.w3.org/2000/svg " class="w-6 h-6 " fill="none"
                                style="display: none;" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>

                        </button>
                    </div>
                </div>

                <!-- Mobile Menu open: "block", Menu closed: "hidden" 
                    
                     
                    -->

                <div x-cloak :class="[
       isOpen ? 'translate-x-0 opacity-100' : 'opacity-0 -translate-x-full',,
        
     ]" class="absolute inset-x-0 z-20 w-full px-6 py-4 transition-all duration-300 ease-in-out -translate-x-full bg-white opacity-0 md:mt-0 md:p-0 md:top-0 md:relative md:opacity-100 md:translate-x-0 md:flex md:items-center md:justify-between">


                    <div class="flex flex-col px-2 -mx-4 md:flex-row md:mx-10 md:py-0">
                        <a href="<?php echo get_category_link(551); ?>"
                            class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white md:mx-2">
                            <?php echo get_cat_name(551); ?> </a>
                        <a href="<?php echo get_category_link(1768); ?>"
                            class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white md:mx-2">
                            <?php echo get_cat_name(1768); ?> </a>
                        <a href="<?php echo get_category_link(562); ?>"
                            class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white md:mx-2">
                            <?php echo get_cat_name(562); ?> </a>
                        <a href="<?php echo get_category_link(1098); ?>"
                            class="px-2.5 py-2 text-gray-700 transition-colors duration-300 transform rounded-lg hover:shadow-sm hover:bg-white md:mx-2">
                            <?php echo get_cat_name(1098); ?> </a>
                    </div>



                    <div class="relative flex mt-4 md:mt-0">

                        <a href="https://www.facebook.com/GDaily.org/" target="_blank" rel="noopener noreferrer">

                            <span class="inset-y-0 left-0 flex items-center pl-3 ">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512">
                                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path fill="#74C0FC"
                                        d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                                </svg>
                            </span>
                        </a>

                        <a href="https://t.me/gdaily_org" target="_blank" rel="noopener noreferrer">

                            <span class="inset-y-0 left-0 flex items-center pl-3 ">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="19.375"
                                    viewBox="0 0 496 512">
                                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path fill="#9ee2ff"
                                        d="M248 8C111 8 0 119 0 256S111 504 248 504 496 393 496 256 385 8 248 8zM363 176.7c-3.7 39.2-19.9 134.4-28.1 178.3-3.5 18.6-10.3 24.8-16.9 25.4-14.4 1.3-25.3-9.5-39.3-18.7-21.8-14.3-34.2-23.2-55.3-37.2-24.5-16.1-8.6-25 5.3-39.5 3.7-3.8 67.1-61.5 68.3-66.7 .2-.7 .3-3.1-1.2-4.4s-3.6-.8-5.1-.5q-3.3 .7-104.6 69.1-14.8 10.2-26.9 9.9c-8.9-.2-25.9-5-38.6-9.1-15.5-5-27.9-7.7-26.8-16.3q.8-6.7 18.5-13.7 108.4-47.2 144.6-62.3c68.9-28.6 83.2-33.6 92.5-33.8 2.1 0 6.6 .5 9.6 2.9a10.5 10.5 0 0 1 3.5 6.7A43.8 43.8 0 0 1 363 176.7z" />
                                </svg>
                            </span>
                        </a>






                        <!--             <input type="text"
                            class="w-full py-2 pl-10 pr-4 text-gray-700 bg-white border rounded-lg dark:text-gray-300 focus:border-blue-400 dark:focus:border-blue-300 focus:outline-none focus:ring focus:ring-opacity-40 focus:ring-blue-300"
                            placeholder="Search"> -->
                    </div>


                </div>

            </div>
        </nav>
        <!-- 		<div class="container mx-auto">
			<div class="py-6 border-b lg:flex lg:justify-between lg:items-center">
				<div class="flex items-center justify-between">
					<div>
						<?php if ( has_custom_logo() ) { ?>
                            <?php the_custom_logo(); ?>
						<?php } else { ?>
							<a href="<?php echo get_bloginfo( 'url' ); ?>" class="text-lg font-extrabold uppercase">
								<?php echo get_bloginfo( 'name' ); ?>
							</a>

							<p class="text-sm font-light text-gray-600">
								<?php echo get_bloginfo( 'description' ); ?>
							</p>

						<?php } ?>
					</div>

					<div class="lg:hidden">
						<a href="#" aria-label="Toggle navigation" id="primary-menu-toggle">
							<svg viewBox="0 0 20 20" class="inline-block w-6 h-6" version="1.1"
								 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
								<g stroke="none" stroke-width="1" fill="currentColor" fill-rule="evenodd">
									<g id="icon-shape">
										<path d="M0,3 L20,3 L20,5 L0,5 L0,3 Z M0,9 L20,9 L20,11 L0,11 L0,9 Z M0,15 L20,15 L20,17 L0,17 L0,15 Z"
											  id="Combined-Shape"></path>
									</g>
								</g>
							</svg>
						</a>
					</div>
				</div>

				<?php
				wp_nav_menu(
					array(
						'container_id'    => 'primary-menu',
						'container_class' => 'hidden bg-gray-100 mt-4 p-4 lg:mt-0 lg:p-0 lg:bg-transparent lg:block',
						'menu_class'      => 'lg:flex lg:-mx-4',
						'theme_location'  => 'primary',
						'li_class'        => 'lg:mx-4',
						'fallback_cb'     => false,
					)
				);
				?>
			</div>
		</div> -->
    </header>




    <?php if ( is_front_page() ) { ?>

    <?php } ?>

    <?php do_action( 'tailpress_content_start' ); ?>

    <main>