<?php
/**
 * Custom template tags for this theme.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'elektron_posted_on' ) ) :
	/**
	 * Prints the post date.
	 */
	function elektron_posted_on() {
		$time = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated screen-reader-text" datetime="%3$s">%4$s</time>';
		}

		$time = sprintf(
			$time,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		printf(
			'<span class="posted-on"><a href="%1$s" rel="bookmark">%2$s</a></span>',
			esc_url( get_permalink() ),
			$time // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
		);
	}
endif;

if ( ! function_exists( 'elektron_posted_by' ) ) :
	/**
	 * Prints the post author.
	 */
	function elektron_posted_by() {
		if ( '' === get_the_author() ) {
			return;
		}
		printf(
			/* translators: %s: post author */
			'<span class="byline">' . esc_html_x( 'by %s', 'post author', 'elektron' ) . '</span>',
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);
	}
endif;

if ( ! function_exists( 'elektron_entry_meta' ) ) :
	/**
	 * Prints the meta line of a post.
	 */
	function elektron_entry_meta() {
		if ( 'post' !== get_post_type() ) {
			return;
		}
		echo '<div class="entry-meta">';
		elektron_posted_on();
		elektron_posted_by();
		echo '</div>';
	}
endif;

if ( ! function_exists( 'elektron_entry_footer' ) ) :
	/**
	 * Prints categories, tags, comment link and edit link.
	 */
	function elektron_entry_footer() {
		if ( 'post' === get_post_type() ) {
			$categories = get_the_category_list( esc_html_x( ', ', 'list item separator', 'elektron' ) );
			if ( $categories ) {
				/* translators: %s: list of categories */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %s', 'elektron' ) . '</span>', $categories ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			$tags = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'elektron' ) );
			if ( $tags ) {
				/* translators: %s: list of tags */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %s', 'elektron' ) . '</span>', $tags ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'elektron' ),
						array( 'span' => array( 'class' => array() ) )
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: post title */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'elektron' ),
					array( 'span' => array( 'class' => array() ) )
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'elektron_post_thumbnail' ) ) :
	/**
	 * Prints the post thumbnail, linked on archives.
	 *
	 * @param string $size Image size.
	 */
	function elektron_post_thumbnail( $size = 'post-thumbnail' ) {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) {
			echo '<div class="post-thumbnail">';
			the_post_thumbnail( $size );
			echo '</div>';
			return;
		}

		echo '<a class="post-thumbnail" href="' . esc_url( get_permalink() ) . '" aria-hidden="true" tabindex="-1">';
		the_post_thumbnail(
			$size,
			array(
				'alt' => the_title_attribute( array( 'echo' => false ) ),
			)
		);
		echo '</a>';
	}
endif;

if ( ! function_exists( 'elektron_site_branding' ) ) :
	/**
	 * Prints the logo and site title.
	 */
	function elektron_site_branding() {
		echo '<div class="site-branding">';

		if ( has_custom_logo() ) {
			echo '<span class="site-logo">';
			the_custom_logo();
			echo '</span>';
		} else {
			printf(
				'<a class="site-logo site-logo-default" href="%1$s" rel="home" aria-hidden="true" tabindex="-1"><img src="%2$s" width="44" height="44" alt=""></a>',
				esc_url( home_url( '/' ) ),
				esc_url( get_template_directory_uri() . '/assets/images/logo.png' )
			);
		}

		echo '<div class="site-identity">';
		if ( is_front_page() && is_home() ) {
			echo '<h1 class="site-title">';
		} else {
			echo '<p class="site-title">';
		}
		echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
		echo ( is_front_page() && is_home() ) ? '</h1>' : '</p>';

		$description = get_bloginfo( 'description', 'display' );
		if ( $description ) {
			echo '<p class="site-description">' . esc_html( $description ) . '</p>';
		}
		echo '</div></div>';
	}
endif;
