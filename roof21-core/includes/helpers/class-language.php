<?php
/**
 * Language Helper.
 *
 * @package Roof21\Core\Helpers
 */

namespace Roof21\Core\Helpers;

/**
 * Language class.
 */
class Language {

	/**
	 * Get supported languages.
	 *
	 * @since 1.0.0
	 * @return array Language codes and names.
	 */
	public function get_supported_languages() {
		$languages = array(
			'en' => __( 'English', 'roof21-core' ),
			'sk' => __( 'Slovak', 'roof21-core' ),
		);

		return apply_filters( 'roof21_supported_languages', $languages );
	}

	/**
	 * Get current language code.
	 *
	 * @since 1.0.0
	 * @return string Language code.
	 */
	public function get_current_language() {
		// Check if Polylang is active
		if ( function_exists( 'pll_current_language' ) ) {
			return pll_current_language();
		}

		// Check if WPML is active
		if ( function_exists( 'wpml_get_current_language' ) ) {
			return wpml_get_current_language();
		}

		// Fallback to WordPress locale
		$locale = get_locale();
		return substr( $locale, 0, 2 );
	}

	/**
	 * Get language switcher URL.
	 *
	 * @since 1.0.0
	 * @param string $lang Language code.
	 * @return string URL for language.
	 */
	public function get_language_url( $lang ) {
		// Check if Polylang is active
		if ( function_exists( 'pll_home_url' ) ) {
			return pll_home_url( $lang );
		}

		// Check if WPML is active
		if ( function_exists( 'wpml_get_home_url' ) ) {
			return wpml_get_home_url( $lang );
		}

		// Fallback to home URL
		return home_url( '/' );
	}

	/**
	 * Check if multilingual plugin is active.
	 *
	 * @since 1.0.0
	 * @return bool True if active.
	 */
	public function is_multilingual_active() {
		return function_exists( 'pll_current_language' ) || function_exists( 'wpml_get_current_language' );
	}
}
