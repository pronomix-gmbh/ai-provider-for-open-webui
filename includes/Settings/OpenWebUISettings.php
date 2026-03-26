<?php
/**
 * OpenWebUI admin settings implementation.
 *
 * @package OBenWeb\AiProviderForOpenWebUI
 */

declare( strict_types=1 );

namespace OBenWeb\AiProviderForOpenWebUI\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WordPress\AiClient\AiClient;

/**
 * Class for the OpenWebUI settings in the WordPress admin.
 *
 * @since 1.0.0
 */
class OpenWebUISettings {

	private const OPTION_GROUP = 'ai-provider-for-openwebui-settings';
	private const OPTION_NAME  = 'ai_provider_for_openwebui_settings';
	private const PAGE_SLUG    = 'ai-provider-for-openwebui';
	private const SECTION_ID   = 'ai_provider_for_openwebui_main';
	private const AJAX_ACTION  = 'ai_provider_for_openwebui_list_models';
	private const NONCE_ACTION = 'ai_provider_for_openwebui_nonce';

	/**
	 * Initializes the settings.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_settings_script' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'ajax_list_models' ) );
	}

	/**
	 * Registers the setting and settings fields.
	 *
	 * @since 1.0.0
	 */
	public function register_settings(): void {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'default'           => array(),
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		add_settings_section(
			self::SECTION_ID,
			'',
			'__return_empty_string',
			self::PAGE_SLUG
		);

		add_settings_field(
			self::OPTION_NAME . '_host',
			__( 'Open WebUI URL', 'ai-provider-for-openwebui' ),
			array( $this, 'render_host_field' ),
			self::PAGE_SLUG,
			self::SECTION_ID,
			array( 'label_for' => self::OPTION_NAME . '-host' )
		);

		add_settings_field(
			self::OPTION_NAME . '_api_key',
			__( 'API Key', 'ai-provider-for-openwebui' ),
			array( $this, 'render_api_key_field' ),
			self::PAGE_SLUG,
			self::SECTION_ID,
			array( 'label_for' => self::OPTION_NAME . '-api-key' )
		);

		add_settings_field(
			self::OPTION_NAME . '_model',
			__( 'Available Models', 'ai-provider-for-openwebui' ),
			array( $this, 'render_available_models_field' ),
			self::PAGE_SLUG,
			self::SECTION_ID,
			array( 'label_for' => self::OPTION_NAME . '-model' )
		);
	}

	/**
	 * Registers the settings screen.
	 *
	 * @since 1.0.0
	 */
	public function register_settings_screen(): void {
		add_options_page(
			__( 'Open WebUI Settings', 'ai-provider-for-openwebui' ),
			__( 'Open WebUI', 'ai-provider-for-openwebui' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_screen' )
		);
	}

	/**
	 * Sanitizes the settings array.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The input value.
	 * @return array<string, string> The sanitized settings.
	 */
	public function sanitize_settings( $value ): array {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$host = isset( $value['host'] ) ? trim( (string) $value['host'] ) : '';
		if ( '' !== $host ) {
			$host = rtrim( esc_url_raw( $host ), '/' );
		}

		$api_key = isset( $value['api_key'] ) ? trim( (string) $value['api_key'] ) : '';
		if ( '' !== $api_key ) {
			$api_key = sanitize_text_field( $api_key );
		}

		return array(
			'host'    => $host,
			'api_key' => $api_key,
		);
	}

	/**
	 * Renders the settings screen.
	 *
	 * @since 1.0.0
	 */
	public function render_screen(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>

		<div class="wrap" style="max-width: 50rem;">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p>
				<?php
				echo esc_html__( 'Configure your Open WebUI URL and API key for model access. You can create an API key in Open WebUI under Settings > Account.', 'ai-provider-for-openwebui' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: code tag, 2: closing code tag */
					esc_html__( 'Default URL is %1$shttp://localhost:3000%2$s. The endpoint path %1$s/api%2$s is handled automatically by this plugin.', 'ai-provider-for-openwebui' ),
					'<code>',
					'</code>'
				);
				?>
			</p>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>
		</div>

		<?php
	}

	/**
	 * Renders the host URL field.
	 *
	 * @since 1.0.0
	 */
	public function render_host_field(): void {
		$settings = self::get_settings();
		$value    = isset( $settings['host'] ) ? $settings['host'] : '';
		?>

		<input
			type="url"
			id="<?php echo esc_attr( self::OPTION_NAME . '-host' ); ?>"
			name="<?php echo esc_attr( self::OPTION_NAME . '[host]' ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
			placeholder="http://localhost:3000"
		/>
		<p class="description">
			<?php
			printf(
				/* translators: 1: code tag, 2: closing code tag */
				esc_html__( 'Enter the Open WebUI base URL only (for example %1$shttp://localhost:3000%2$s). Do not append %1$s/api%2$s.', 'ai-provider-for-openwebui' ),
				'<code>',
				'</code>'
			);
			?>
		</p>

		<?php
	}

	/**
	 * Renders the API key field.
	 *
	 * @since 1.0.0
	 */
	public function render_api_key_field(): void {
		$settings = self::get_settings();
		$value    = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
		?>

		<input
			type="password"
			id="<?php echo esc_attr( self::OPTION_NAME . '-api-key' ); ?>"
			name="<?php echo esc_attr( self::OPTION_NAME . '[api_key]' ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
			autocomplete="off"
			placeholder="sk-..."
		/>
		<p class="description">
			<?php
			echo esc_html__( 'Create the key in Open WebUI (Settings > Account). If AI Client credentials are configured separately, that takes precedence.', 'ai-provider-for-openwebui' );
			?>
		</p>

		<?php
	}

	/**
	 * Renders the available models list.
	 *
	 * @since 1.0.0
	 */
	public function render_available_models_field(): void {
		?>

		<div id="openwebui-models-container">
			<span id="openwebui-model-status"></span>
		</div>
		<p class="description">
			<?php
			echo esc_html__( 'Models are loaded from your Open WebUI instance.', 'ai-provider-for-openwebui' );
			?>
		</p>

		<?php
	}

	/**
	 * Enqueues the settings page script.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 */
	public function enqueue_settings_script( string $hook_suffix ): void {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		$plugin_dir = AI_PROVIDER_FOR_OPENWEBUI_PLUGIN_DIR;
		$asset_file = $plugin_dir . 'build/admin/settings.asset.php';
		$asset      = file_exists( $asset_file ) ? require $asset_file : array(); // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable -- Asset file path is built from a known constant.

		$dependencies = isset( $asset['dependencies'] ) ? $asset['dependencies'] : array();
		$version      = isset( $asset['version'] ) ? $asset['version'] : false;

		wp_enqueue_script(
			'ai-provider-for-openwebui-settings',
			plugins_url( 'build/admin/settings.js', $plugin_dir . 'plugin.php' ),
			$dependencies,
			$version,
			true
		);

		wp_set_script_translations(
			'ai-provider-for-openwebui-settings',
			'ai-provider-for-openwebui',
			AI_PROVIDER_FOR_OPENWEBUI_PLUGIN_DIR . 'languages'
		);

		wp_localize_script(
			'ai-provider-for-openwebui-settings',
			'aiProviderForOpenWebUISettings',
			array(
				'ajaxUrl' => esc_url( admin_url( 'admin-ajax.php' ) . '?action=' . self::AJAX_ACTION . '&_wpnonce=' . wp_create_nonce( self::NONCE_ACTION ) ),
			)
		);
	}

	/**
	 * Handles the AJAX request to list available OpenWebUI models.
	 *
	 * @since 1.0.0
	 */
	public function ajax_list_models(): void {
		check_ajax_referer( self::NONCE_ACTION );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Insufficient permissions.', 'ai-provider-for-openwebui' ), 403 );
		}

		if ( ! class_exists( AiClient::class ) ) {
			wp_send_json_error( __( 'WordPress AI Client is not available.', 'ai-provider-for-openwebui' ), 500 );
		}

		$provider_id = 'openwebui';
		$registry    = AiClient::defaultRegistry();

		if ( ! $registry->hasProvider( $provider_id ) ) {
			wp_send_json_error( __( 'AI provider not found.', 'ai-provider-for-openwebui' ), 404 );
		}

		$provider_classname = $registry->getProviderClassName( $provider_id );

		try {
			// phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$model_metadata_directory = $provider_classname::modelMetadataDirectory();
			$model_metadata_objects   = $model_metadata_directory->listModelMetadata();

			wp_send_json_success( $model_metadata_objects );
		} catch ( \Throwable $e ) {
			/* translators: %s: Error message. */
			wp_send_json_error( sprintf( __( 'Could not list models from Open WebUI. Check URL/API key. Error: %s', 'ai-provider-for-openwebui' ), $e->getMessage() ), 500 );
		}
	}

	/**
	 * Gets the settings from the WordPress option.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string> The settings.
	 */
	public static function get_settings(): array {
		return (array) get_option( self::OPTION_NAME, array() );
	}
}
