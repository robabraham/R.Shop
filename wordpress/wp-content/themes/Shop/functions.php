<?php 
function webStyle(){
	wp_enqueue_style('style', get_stylesheet_uri() );
}

add_action('wp_enqueue_scripts','webStyle');

register_nav_menus(array(
	'primary' => 'Primary menu',
	'footer' => 'Footer menu'
));

/*function get_parent_id(){

	global $post;

	if($post->post_parent){
		return $post->post_parent;
	}
	else{
		return $post->ID;
	}

} */

function custom_excerpt_length(){
	return 20;
}
add_filter('excerpt_length','custom_excerpt_length');

function custom_theme_setup(){
	add_theme_support('post-thumbnails');
	add_image_size('small-thumbnail',50,35,true);
}
add_action('after_setup_theme','custom_theme_setup');

function widgetsInit(){

	register_sidebar( array(
		"id" => "sidebar1",
		"name" => "Sidebar",
		"before_widget" => "<div class='muraba'>",
		"after_widget" => "</div>"
	) );

	register_sidebar( array(
		"id" => "footer1",
		"name" => "Footer 1"		
	) );

	register_sidebar( array(
		"id" => "footer2",
		"name" => "Footer 2"		
	) );

}

add_action("widgets_init", "widgetsInit");

?>