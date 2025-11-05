<?php 

/**
 * 分頁導航（優化版）
 */

function wpbeginner_numeric_posts_nav() {

    if (is_singular())
        return;

    global $wp_query;

    /** Stop execution if there's only 1 page */
    if ($wp_query->max_num_pages <= 1)
        return;

    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max   = intval($wp_query->max_num_pages);

    // 定義 CSS class 常數
    $classes = [
        'container' => 'flex justify-center pt-10',
        'wrapper' => 'flex py-3 rounded-3xl px-5 bg-white space-x-1',
        'button_base' => 'text-center text-sm transition-all ml-2',
        'button_nav' => 'rounded-full py-2 px-3 text-slate-700 hover:bg-gray-200 bg-gray-100',
        'button_page' => 'w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 text-slate-700 hover:bg-gray-200',
        'button_active' => '!bg-gray-500 text-white',
        'button_inactive' => 'bg-gray-100 text-slate-700 hover:bg-gray-200',
        'ellipsis' => 'flex items-center px-2'
    ];

    /** Helper function to get button classes */
    $get_page_button_class = function($is_active) use ($classes) {
        $base_class = $classes['button_base'] . ' ' . $classes['button_page'];
        return $base_class . ' ' . ($is_active ? $classes['button_active'] : $classes['button_inactive']);
    };

    /** Add current page to the array */
    if ($paged >= 1)
        $links[] = $paged;

    /** Add the pages around the current page to the array */
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }

    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }

    echo '<div class="' . $classes['container'] . '"><div class="' . $classes['wrapper'] . '">' . "\n";

    /** Previous Post Link */
    if (get_previous_posts_link()) {
        printf(
            '<button class="%s">%s</button>' . "\n",
            $classes['button_base'] . ' ' . $classes['button_nav'],
            get_previous_posts_link('Prev')
        );
    }

    /** Link to first page, plus ellipses if necessary */
    if (!in_array(1, $links)) {
        printf(
            '<button class="%s" onclick="location.href=\'%s\'">%s</button>' . "\n",
            $get_page_button_class(1 == $paged),
            esc_url(get_pagenum_link(1)),
            '1'
        );

        if (!in_array(2, $links))
            echo '<span class="' . $classes['ellipsis'] . '">…</span>';
    }

    /** Link to current page, plus 2 pages in either direction if necessary */
    sort($links);
    foreach ((array)$links as $link) {
        printf(
            '<button class="%s" onclick="location.href=\'%s\'">%s</button>' . "\n",
            $get_page_button_class($paged == $link),
            esc_url(get_pagenum_link($link)),
            $link
        );
    }

    /** Link to last page, plus ellipses if necessary */
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links))
            echo '<span class="' . $classes['ellipsis'] . '">…</span>' . "\n";

        printf(
            '<button class="%s" onclick="location.href=\'%s\'">%s</button>' . "\n",
            $get_page_button_class($paged == $max),
            esc_url(get_pagenum_link($max)),
            $max
        );
    }

    /** Next Post Link */
    if (get_next_posts_link()) {
        printf(
            '<button class="%s">%s</button>' . "\n",
            $classes['button_base'] . ' ' . $classes['button_nav'],
            get_next_posts_link('Next')
        );
    }

    echo '</div></div>' . "\n";
}

?>