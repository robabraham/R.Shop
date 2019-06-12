<article class="post <?php if(has_post_thumbnail()){ echo "hast-thumbnail";}?>">

			<div class="post-thumbnail">
				<?php the_post_thumbnail('small-img');?>
			</div>

			<h2>	
				<a href="<?php the_permalink();?>">
					<?php the_title();?>		
				</a>
			</h2>

			<p class="post-info">
				<?php the_time("l, F j, Y"); ?> | by				

				<a href="<?php echo get_author_posts_url(get_the_author_meta('ID'));?>">	
					<?php the_author(); ?>
				</a> | posted in 

				<?php
				$cats = get_the_category();

				if( $cats ){
					foreach( $cats as $category){
						$link = get_category_link($category->term_id);
						echo "<a href='$link'>" . $category->name . "</a> ";
					}
				}

				?>

			</p>
			<?php 
				if(is_search() OR is_archive() OR is_home()){
					the_excerpt(); ?>
					<p>
						<?php get_the_excerpt(); ?>
						<a href="<?php the_permalink(); ?>">Read more&raquo</a>
					</p>
			<?php
				}
				else{
					the_content();
				}
			?>
</article>