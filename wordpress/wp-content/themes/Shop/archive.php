
<?php 
	get_header();
?>
<h2>Archive Result for:
	<?php 
		if(is_category()){
			single_cat_title();
		}
		else if(is_tag()){
			single_cat_title();
		}
		else if(is_author()){
			echo "Author";
		}
		else if(is_day()){
			echo "Dayly Archive:" . get_the_date();
		}
	 ?>
</h2>
<?php
	if(have_posts()){
		while(have_posts()){
			the_post();
			get_template_part('content',get_post_format());
		}
	}
	else{
		echo "<p>No content found</p>";
	}
	get_footer();
 ?>