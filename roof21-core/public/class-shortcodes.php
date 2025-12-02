<?php
/**
 * Shortcodes.
 *
 * @package Roof21\Core\PublicFrontend
 */

namespace Roof21\Core\PublicFrontend;

/**
 * Shortcodes class.
 */
class Shortcodes {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_shortcode( 'roof21_search_form', array( $this, 'search_form' ) );
		add_shortcode( 'roof21_properties', array( $this, 'properties_grid' ) );
		add_shortcode( 'roof21_featured_properties', array( $this, 'featured_properties' ) );
	}

	/**
	 * Property search form shortcode.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function search_form( $atts ) {
		ob_start();
		?>
		<div class="roof21-search-form">
			<form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'roof21_property' ) ); ?>">
				<!-- Search form fields here -->
				<button type="submit"><?php esc_html_e( 'Search', 'roof21-core' ); ?></button>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Properties grid shortcode.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function properties_grid( $atts ) {
		$atts = shortcode_atts( array(
			'per_page' => 12,
			'listing_type' => '',
		), $atts );

		$args = array(
			'post_type' => 'roof21_property',
			'posts_per_page' => intval( $atts['per_page'] ),
		);

		$query = new \WP_Query( $args );

		ob_start();
		if ( $query->have_posts() ) {
			echo '<div class="roof21-properties-grid">';
			while ( $query->have_posts() ) {
				$query->the_post();
				// Property card template
				echo '<div class="property-card">';
				echo '<h3>' . esc_html( get_the_title() ) . '</h3>';
				echo '</div>';
			}
			echo '</div>';
			wp_reset_postdata();
		}
		return ob_get_clean();
	}

	/**
	 * Featured properties shortcode.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function featured_properties( $atts ) {
		$atts = shortcode_atts( array(
			'limit' => 6,
		), $atts );

		$args = array(
			'post_type' => 'roof21_property',
			'posts_per_page' => intval( $atts['limit'] ),
			'meta_query' => array(
				array(
					'key' => '_roof21_featured',
					'value' => '1',
				),
			),
		);

		$query = new \WP_Query( $args );

		ob_start();
		if ( $query->have_posts() ) {
			echo '<div class="roof21-featured-properties">';
			while ( $query->have_posts() ) {
				$query->the_post();
				echo '<div class="featured-property-card">';
				echo '<h3>' . esc_html( get_the_title() ) . '</h3>';
				echo '</div>';
			}
			echo '</div>';
			wp_reset_postdata();
		}
		return ob_get_clean();
	}
}
