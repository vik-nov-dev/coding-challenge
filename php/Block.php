<?php
/**
 * Block class.
 *
 * @package SiteCounts
 */

namespace XWP\SiteCounts;

use WP_Block;

/**
 * The Site Counts dynamic block.
 *
 * Registers and renders the dynamic block.
 */
class Block {

	/**
	 * The Plugin instance.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Instantiates the class.
	 *
	 * @param Plugin $plugin The plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Adds the action to register the block.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_block' ] );
	}

	/**
	 * Registers the block.
	 */
	public function register_block() {
		register_block_type_from_metadata(
			$this->plugin->dir(),
			[
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	/**
	 * Renders the block.
	 *
	 * @param array    $attributes The attributes for the block.
	 * @param string   $content    The block content, if any.
	 * @param WP_Block $block      The instance of this block.
	 * @return string The markup of the block.
	 */
	public function render_callback( $attributes, $content, $block ) {
		$post_types = get_post_types( array( 'public' => true ) );

		$class_name = '';
		if ( isset( $attributes['className'] ) ) {
			$class_name = $attributes['className'];
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $class_name ); ?>">
			<h2><?php esc_html_e( 'Post Counts', 'site-counts' ); ?></h2>
			<?php
			foreach ( $post_types as $post_type_slug ) :
				$post_type_object = get_post_type_object( $post_type_slug );
				$post_count       = wp_count_posts( $post_type_slug );
				?>
				<p>
					<?php
						echo wp_sprintf(
							/* translators: 1: Posts count, 2: Post type name. */
							esc_html__( 'There are %1$s %2$s.', 'site-counts' ),
							number_format_i18n( $post_count->publish ),
							_n(
								esc_html__( $post_type_object->labels->singular_name ),
								esc_html__( $post_type_object->labels->name ),
								$post_count->publish
							)
						);
					?>
			<?php endforeach; ?>
			<?php if ( isset( $_GET['post_id'] ) && intval( $_GET['post_id'] ) ): ?>
			<p>
				<?php
					echo wp_sprintf(
						/* translators: %d: Post ID. */
						esc_html__( 'The current post ID is %d.', 'site-counts' ),
						number_format_i18n( $_GET['post_id'] )
					);
				?>
			</p>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}
}
