<?php
/**
 * The template for displaying comments.
 *
 * @package Supernova
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$supernova_comment_count = get_comments_number();
			printf(
				esc_html( _n( '%1$s comment', '%1$s comments', $supernova_comment_count, 'supernova' ) ),
				number_format_i18n( $supernova_comment_count )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation();

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'supernova' ); ?></p>
			<?php
		endif;

	endif;

	comment_form();
	?>
</div>
