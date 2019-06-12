<!DOCTYPE html>
<html>
	<head>
		<title><?php bloginfo('name'); ?></title>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div class = "glxavor">
			<header class = "site-header">
				<div class = "hd-search">
					<?php  get_search_form(); ?>
				</div>
				<nav class = "site-nav">
					<?php 
						$args = array(
							"theme location" => "primary"
						);
						wp_nav_menu($args);
					 ?>
				</nav>
				<h1><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a></h1>
				<h4><?php bloginfo('description'); ?></h4>
				<?php echo do_shortcode('[gtranslate]'); ?>
			</header>

