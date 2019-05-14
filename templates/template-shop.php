<?php
/**
* Template name: Shop Page Template
*/
get_header( 'shop' ); ?>
	<?php do_action('before_loop') ?>
		<?php get_template_part('template-parts/title-page');?>
		<?php get_template_part('template-parts/loops/loop-common'); ?>
		<?php get_template_part('template-parts/page-nav'); ?>
	<?php do_action('after_loop') ?>
<?php
get_footer( 'shop' );
