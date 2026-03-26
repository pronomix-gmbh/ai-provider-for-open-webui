<?php
/**
 * Unit tests for OpenWebUI path utilities.
 *
 * @package OBenWeb\AiProviderForOpenWebUI\Tests
 */

declare( strict_types=1 );

namespace OBenWeb\AiProviderForOpenWebUI\Tests\Unit;

use OBenWeb\AiProviderForOpenWebUI\Util\OpenWebUIPath;
use PHPUnit\Framework\TestCase;

/**
 * Tests OpenWebUI API path normalization.
 *
 * @since 1.0.0
 */
class OpenWebUIPathTest extends TestCase {

	/**
	 * @return array<string, array{input: string, expected: string}>
	 */
	public function path_provider(): array {
		return array(
			'empty path falls back to chat completions' => array(
				'input'    => '',
				'expected' => '/api/chat/completions',
			),
			'v1 prefix is stripped' => array(
				'input'    => 'v1/chat/completions',
				'expected' => '/api/chat/completions',
			),
			'leading slash and v1 prefix are stripped' => array(
				'input'    => '/v1/chat/completions',
				'expected' => '/api/chat/completions',
			),
			'non chat path falls back to chat completions' => array(
				'input'    => 'models',
				'expected' => '/api/chat/completions',
			),
			'chat subpath is accepted' => array(
				'input'    => 'chat/stream',
				'expected' => '/api/chat/stream',
			),
		);
	}

	/**
	 * @dataProvider path_provider
	 *
	 * @param string $input Input path.
	 * @param string $expected Expected normalized path.
	 */
	public function test_normalize_for_chat_completions( string $input, string $expected ): void {
		self::assertSame(
			$expected,
			OpenWebUIPath::normalize_for_chat_completions( $input )
		);
	}
}
