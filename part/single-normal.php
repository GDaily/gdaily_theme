<article class="mx-auto bg-opacity-20" itemscope itemtype="https://schema.org/Article">

    <!-- Article featured image with structured data -->
    <figure class="px-3 py-10 md:px-auto"
        style="background-color: <?php echo esc_attr($args['final_light_color'] ?? '#f9fafb'); ?>;">
        <img src="<?php echo esc_url($args['thumbnail_url'] ?? ''); ?>"
            srcset="<?php echo esc_attr($args['imagePath'] ?? ''); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"
            loading="lazy" decoding="async" class="h-auto max-h-full mx-auto rounded-xl" itemprop="image">
    </figure>

    <!-- Article headline with structured data -->
    <h1 class="pt-5 pb-10 text-4xl font-extrabold text-center" itemprop="headline"
        style="color: <?php echo esc_attr($args['final_base_color'] ?? '#6b7280'); ?>; background-color: <?php echo esc_attr($args['final_light_color'] ?? '#f9fafb'); ?>;">
        <?php the_title(); ?>
    </h1>

    <div class="mt-10 mx-5 md:mx-0">
        <?php while (have_posts()) : the_post(); ?>
            <div class="entry-content mx-auto max-w-4xl select-none text-lg leading-10 tracking-wider"
                itemprop="articleBody">
                <?php the_content(); ?>
                <?php if ($args['adsense_enable']) : ?>
                    <?php get_template_part('part/adsense/adsense_content'); ?>
                <?php endif; ?>
            </div>

            <?php get_template_part('part/article-meta'); ?>

            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        <?php endwhile; ?>
    </div>

</article>