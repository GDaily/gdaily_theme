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

    echo '<div class="flex justify-center pt-10"><div class="flex space-x-1">' . "\n";

    /** Previous Post Link */
    if (get_previous_posts_link()) {
        printf(
            '<button class="rounded-full py-2 px-3 text-center text-sm transition-all shadow-sm white text-slate-700 hover:bg-gray-200 ml-2">%s</button>' . "\n",
            get_previous_posts_link('Prev')
        );
    }

    /** Link to first page, plus ellipses if necessary */
    if (!in_array(1, $links)) {
        $is_active = (1 == $paged) ? 'bg-black text-white' : 'white text-slate-700 hover:bg-gray-200';
        printf(
            '<button class="w-10 h-10 rounded-full flex items-center justify-center text-center text-sm transition-all shadow-lg ml-2 bg-white text-slate-700 hover:bg-gray-200 %s" onclick="location.href=\'%s\'">%s</button>' . "\n",
            $is_active,
            esc_url(get_pagenum_link(1)),
            '1'
        );

        if (!in_array(2, $links))
            echo '<span class="flex items-center px-2">…</span>';
    }

    /** Link to current page, plus 2 pages in either direction if necessary */
    sort($links);
    foreach ((array)$links as $link) {
        $is_active = ($paged == $link) ? 'bg-black text-white' : ' bg-white text-slate-700 hover:bg-gray-200';
        printf(
            '<button class="w-10 h-10  rounded-full flex items-center justify-center text-center text-sm transition-all shadow-sm ml-2 %s" onclick="location.href=\'%s\'">%s</button>' . "\n",
            $is_active,
            esc_url(get_pagenum_link($link)),
            $link
        );
    }

    /** Link to last page, plus ellipses if necessary */
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links))
            echo '<span class="flex items-center px-2">…</span>' . "\n";

        $is_active = ($paged == $max) ? 'bg-black text-white' : 'white text-slate-700 hover:bg-gray-200';
        printf(
            '<button class="w-10 h-10 rounded-full flex items-center bg-white justify-center text-center text-sm transition-all shadow-sm ml-2 %s" onclick="location.href=\'%s\'">%s</button>' . "\n",
            $is_active,
            esc_url(get_pagenum_link($max)),
            $max
        );
    }

    /** Next Post Link */
    if (get_next_posts_link()) {
        printf(
            '<button class="rounded-full py-2 px-3 text-center text-sm transition-all shadow-sm white text-slate-700 hover:bg-gray-200 ml-2">%s</button>' . "\n",
            get_next_posts_link('Next')
        );
    }

    echo '</div></div>' . "\n";
}

?>