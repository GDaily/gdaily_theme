<article class="mx-auto bg-opacity-20 " itemscope itemtype="https://schema.org/Article" id="post-<?php the_ID(); ?>"
    <?php post_class(); ?>>

    <?php if ( have_posts() ) : ?>
    <?php
        $post_id = get_the_ID();
        $adsense_enable = $args['adsense_enable'] ?? false;
        $tailwind_hex_base_color = esc_attr($args['final_base_color'] ?? '#6b7280');
        $tailwind_hex_light_color = esc_attr($args['final_light_color'] ?? '#f9fafb');
        $thumbnail_url = esc_url($args['thumbnail_url'] ?? '');
        $image_path = esc_attr($args['thumbnail_app_url'] ?? '');
        $scale = esc_attr($args['scale'] ?? 1);
        $app_name = esc_html(carbon_get_post_meta($post_id, 'app_name'));
    ?>

    <!-- Article featured figure (保留原模板設置) -->
    <figure class="py-3 pt-10 text-center md:py-10" style="background-color: <?php echo $tailwind_hex_light_color; ?>;">

        <div class="flex mx-auto w-[256px] h-[256px] overflow-hidden bg-white rounded-full">
            <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $app_name; ?>" loading="lazy" decoding="async"
                srcset="<?php echo $image_path; ?>" class="h-auto max-h-full mx-auto rounded-xl"
                style="transform: scale(<?php echo $scale; ?>);" itemprop="image">
        </div>

        <figcaption class="py-5 mt-5 text-4xl font-extrabold text-center" itemprop="name"
            style="color: <?php echo $tailwind_hex_base_color; ?>;">
            <?php echo $app_name; ?>
        </figcaption>

    </figure>

    <!-- Article title -->
    <h1 class="pt-5 pb-10 text-4xl font-extrabold text-center" itemprop="headline"
        style="color: <?php echo $tailwind_hex_base_color; ?>;  ">
        <?php the_title(); ?>
    </h1>

    <div class="mt-10 mx-5 md:mx-0">
        <?php while ( have_posts() ) : the_post(); ?>

        <div class="entry-content mx-auto max-w-4xl select-none text-lg leading-10 tracking-wider"
            itemprop="articleBody">
            <?php the_content(); ?>

            <?php if ( $adsense_enable ) : ?>
            <?php get_template_part('part/adsense/adsense_content'); ?>
            <?php endif; ?>
        </div>

        <?php get_template_part('part/article-meta'); ?>

        <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
        ?>

        <?php endwhile; ?>
    </div>

    <?php endif; ?>
</article>