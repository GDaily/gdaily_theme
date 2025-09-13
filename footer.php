</main>

<?php do_action( 'tailpress_content_end' ); ?>

</div>

<?php do_action( 'tailpress_content_after' ); ?>

<footer id="colophon" class="site-footer bg-gray-100 py-12" role="contentinfo">
    <?php do_action( 'tailpress_footer' ); ?>

    <div class="container mx-auto text-center text-gray-500">
        <?php echo "<p>GDaily @ 2012-" . date("Y") . " (" . (date("Y") - 2012) . " years)</p>"; ?>

    </div>
</footer>



<?php wp_footer(); ?>

</body>

</html>