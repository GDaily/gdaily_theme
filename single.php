<?php 

 
$imagePath = '';  
$parent_category_id =get_the_category()[0]->parent;
$maxSize= ''; 
$size  = [800, 400];
$tailwind_color = carbon_get_post_meta($post->ID, 'tailwind_color'); 
$tailwind_background_custom = getColorName(carbon_get_post_meta($post->ID, 'tailwind_background_custom'));
$thumbnail_url =  '';
$thumbnail_app_url = '';


  
if ( has_post_thumbnail( $post->ID ) ) {
    $thumbnail_url =  wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size)[0];
}  
 


if ($parent_category_id == 1768) { 
    $size =  [192, 192];
    $thumbnail_app_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size)[0];
    $parsedUrl = parse_url($thumbnail_app_url);
    $relativeImagePath = str_replace($parsedUrl['scheme'] . '://' . $parsedUrl['host'], '.', $thumbnail_app_url);
    $severFilePath = $_SERVER['DOCUMENT_ROOT'] . $relativeImagePath; 
    $maxSize =   trimImageWhitespace($severFilePath) ;
}


 
 
?>


<?php get_header(); ?>



<?php 
if($parent_category_id == 1768){
   get_template_part('part/single-app', get_post_format(),	array(
        'tailwind_color' => !empty($tailwind_background_custom) ? $tailwind_background_custom : $tailwind_color, 
        'scale'=>0.6+1- $maxSize/192,
        'thumbnail_app_url'=>$thumbnail_app_url,
        'thumbnail_url'=>$thumbnail_url
	)); 
}
else{
 get_template_part('part/single-normal', get_post_format(),	array(
        'tailwind_color' => !empty($tailwind_background_custom) ? $tailwind_background_custom : $tailwind_color, 
		'imagePath'	=> $thumbnail_url, 
        'bg_color'=>"bg-$tailwind_color-50", 
        'thumbnail_url'=>$thumbnail_url
	));  
} ?>










<?php
get_footer();