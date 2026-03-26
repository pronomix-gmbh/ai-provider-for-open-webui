<?php
/**
 * OpenWebUI path utility.
 *
 * @package OBenWeb\AiProviderForOpenWebUI
 */

declare( strict_types=1 );

namespace OBenWeb\AiProviderForOpenWebUI\Util;

/**
 * Utility methods for API path handling.
 *
 * @since 1.0.0
 */
class OpenWebUIPath {

	/**
	 * Normalizes any incoming provider path to an OpenWebUI chat completions path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Incoming path from the OpenAI-compatible model implementation.
	 * @return string Normalized path.
	 */
	public static function normalize_for_chat_completions( string $path ): string {
		$path = ltrim( $path, '/' );
		$path = ltrim( (string) preg_replace( '#^v1/?#', '', $path ), '/' );

		if ( '' === $path ) {
			$path = 'chat/completions';
		}

		if ( 0 !== strpos( $path, 'chat/' ) ) {
			$path = 'chat/completions';
		}

		return '/api/' . $path;
	}
}
