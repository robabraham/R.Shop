<?php get_header(); ?>

<div class="site-content clearfix">
	
	<div class="main-column">
		<?php
		if( have_posts() ){
			while( have_posts() ){
				the_post();		

				get_template_part("content", get_post_format() );
			
			}
		}
		else{
			echo "No content found";
		}
		?>
	</div>

</div>

<?php get_footer(); ?>