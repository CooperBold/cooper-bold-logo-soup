<?php
/**
 * Frontend script and style registration and conditional enqueue.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and enqueues view.js and view styles when Cooper Bold Logo Wrangler renders.
 */
final class COOPBOLO_Assets {

	public const VIEW_SCRIPT_HANDLE  = 'cooper-bold-logo-wrangler-view';
	public const VIEW_STYLE_HANDLE   = 'cooper-bold-logo-wrangler-view-style';
	public const SPLIDE_SCRIPT_HANDLE          = 'cooper-bold-logo-wrangler-splide';
	public const SPLIDE_STYLE_HANDLE           = 'cooper-bold-logo-wrangler-splide';
	public const SPLIDE_AUTOSCROLL_SCRIPT_HANDLE = 'cooper-bold-logo-wrangler-splide-autoscroll';
	public const SPLIDE_VERSION                = '4.1.4';
	public const SPLIDE_AUTOSCROLL_VERSION     = '0.5.3';

	/** @var bool Whether a standalone Splide carousel (wrapper=full) is on the page. */
	private static bool $needs_standalone_splide = false;

	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue' ) );
		add_action( 'wp_print_footer_scripts', array( $this, 'reconcile_autoscroll' ), 1 );
	}

	/**
	 * Drop the bundled Auto Scroll when something else loads it.
	 *
	 * Block themes render content before wp_enqueue_scripts, so a theme that enqueues its own
	 * Auto Scroll there is not visible yet when the carousel renders. Footer scripts print later,
	 * when every handle is registered, so settle it here.
	 */
	public function reconcile_autoscroll(): void {
		$external = self::external_autoscroll_handle();
		if ( '' === $external ) {
			return;
		}

		wp_dequeue_script( self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE );

		$wp_scripts = wp_scripts();
		if ( ! isset( $wp_scripts->registered[ self::VIEW_SCRIPT_HANDLE ] ) ) {
			return;
		}
		$deps = array();
		foreach ( $wp_scripts->registered[ self::VIEW_SCRIPT_HANDLE ]->deps as $dep ) {
			$deps[] = self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE === $dep ? $external : $dep;
		}
		$wp_scripts->registered[ self::VIEW_SCRIPT_HANDLE ]->deps = array_values( array_unique( $deps ) );
	}

	/**
	 * Register frontend script and style handles on init.
	 */
	public function register(): void {
		$this->register_splide_assets();

		$script = COOPBOLO_PATH . 'build/view.asset.php';
		if ( file_exists( $script ) ) {
			$asset = include $script;
			wp_register_script(
				self::VIEW_SCRIPT_HANDLE,
				COOPBOLO_URL . 'build/view.js',
				$asset['dependencies'],
				$asset['version'],
				true
			);
		}

		$style_candidates = array(
			array(
				'path' => COOPBOLO_PATH . 'build/view.scss.asset.php',
				'url'  => COOPBOLO_URL . 'build/view.scss.css',
			),
			array(
				'path' => COOPBOLO_PATH . 'build/view.asset.php',
				'url'  => COOPBOLO_URL . 'build/view.css',
			),
		);
		foreach ( $style_candidates as $candidate ) {
			if ( ! file_exists( $candidate['path'] ) ) {
				continue;
			}
			$asset = include $candidate['path'];
			wp_register_style(
				self::VIEW_STYLE_HANDLE,
				$candidate['url'],
				$asset['dependencies'],
				$asset['version']
			);
			break;
		}
	}

	/**
	 * Register Splide 4.x core assets (bundled under lib/splide/) under plugin-owned handles.
	 */
	private function register_splide_assets(): void {
		if ( wp_style_is( self::SPLIDE_STYLE_HANDLE, 'registered' )
			&& wp_script_is( self::SPLIDE_SCRIPT_HANDLE, 'registered' ) ) {
			return;
		}

		$splide_base = COOPBOLO_URL . 'lib/splide/';

		wp_register_style(
			self::SPLIDE_STYLE_HANDLE,
			$splide_base . 'css/splide.min.css',
			array(),
			self::SPLIDE_VERSION
		);

		wp_register_script(
			self::SPLIDE_SCRIPT_HANDLE,
			$splide_base . 'js/splide.min.js',
			array(),
			self::SPLIDE_VERSION,
			true
		);

		wp_register_script(
			self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE,
			$splide_base . 'js/splide-extension-auto-scroll.min.js',
			array( self::SPLIDE_SCRIPT_HANDLE ),
			self::SPLIDE_AUTOSCROLL_VERSION,
			true
		);
	}

	/**
	 * Whether standalone Splide assets were requested for this request.
	 */
	public static function needs_standalone_splide(): bool {
		return self::$needs_standalone_splide;
	}

	/**
	 * Enqueue frontend assets when a block or shortcode renders (widgets, FSE, etc.).
	 *
	 * @param bool $standalone_splide When true, also enqueue Splide 4.x for wrapper=full carousels.
	 */
	public static function enqueue_frontend( bool $standalone_splide = false ): void {
		if ( is_admin() ) {
			return;
		}

		if ( $standalone_splide ) {
			self::$needs_standalone_splide = true;
		}

		if ( self::$needs_standalone_splide ) {
			self::enqueue_splide_assets();
			self::ensure_view_script_depends_on_splide();
		}

		if ( wp_script_is( self::VIEW_SCRIPT_HANDLE, 'registered' ) ) {
			wp_enqueue_script( self::VIEW_SCRIPT_HANDLE );
		}
		if ( wp_style_is( self::VIEW_STYLE_HANDLE, 'registered' ) ) {
			wp_enqueue_style( self::VIEW_STYLE_HANDLE );
		}
	}

	/**
	 * Enqueue Splide core for standalone carousels.
	 *
	 * Bricks registers a `splide` handle without a src on every page; prefer that
	 * handle only when it points at a real script. Otherwise load bundled Splide 4.x.
	 */
	private static function enqueue_splide_assets(): void {
		$script_handle = self::resolve_splide_script_handle();
		$style_handle  = self::resolve_splide_style_handle( $script_handle );

		wp_enqueue_style( $style_handle );
		wp_enqueue_script( $script_handle );
		self::enqueue_splide_autoscroll_extension( $script_handle );
	}

	/**
	 * Enqueue Auto Scroll extension unless the theme or another plugin already provides it.
	 *
	 * @param string $splide_script_handle Resolved Splide core handle.
	 */
	private static function enqueue_splide_autoscroll_extension( string $splide_script_handle ): void {
		$external = self::external_autoscroll_handle();
		if ( '' !== $external ) {
			wp_enqueue_script( $external );
			return;
		}

		if ( ! wp_script_is( self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, 'registered' ) ) {
			return;
		}

		$wp_scripts = wp_scripts();
		if ( isset( $wp_scripts->registered[ self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE ] ) ) {
			$deps = $wp_scripts->registered[ self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE ]->deps;
			if ( ! in_array( $splide_script_handle, $deps, true ) ) {
				$wp_scripts->registered[ self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE ]->deps[] = $splide_script_handle;
			}
		}

		wp_enqueue_script( self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE );
	}

	/**
	 * Script handle that will output Splide core on this request.
	 */
	private static function resolve_splide_script_handle(): string {
		if ( self::registered_script_has_src( 'splide' ) ) {
			return 'splide';
		}

		return self::SPLIDE_SCRIPT_HANDLE;
	}

	/**
	 * Style handle paired with the resolved Splide script handle.
	 *
	 * @param string $script_handle Resolved script handle.
	 */
	private static function resolve_splide_style_handle( string $script_handle ): string {
		if ( 'splide' === $script_handle && self::registered_style_has_src( 'splide' ) ) {
			return 'splide';
		}

		return self::SPLIDE_STYLE_HANDLE;
	}

	/**
	 * Handle of an Auto Scroll script that something else registered, or '' when there is none.
	 *
	 * Matches by src so a theme that registers the same file under its own handle is found.
	 * The plugin's own handle never counts: its src contains the same file name.
	 */
	private static function external_autoscroll_handle(): string {
		if ( self::registered_script_has_src( 'splide-extension-auto-scroll' ) ) {
			return 'splide-extension-auto-scroll';
		}

		foreach ( wp_scripts()->registered as $handle => $script ) {
			if ( self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE === $handle || ! $script instanceof _WP_Dependency ) {
				continue;
			}
			$src = (string) $script->src;
			if ( '' !== $src && false !== strpos( $src, 'splide-extension-auto-scroll' ) ) {
				return (string) $handle;
			}
		}

		return '';
	}

	/**
	 * @param string $handle Registered script handle.
	 */
	private static function registered_script_has_src( string $handle ): bool {
		if ( ! wp_script_is( $handle, 'registered' ) ) {
			return false;
		}

		$wp_scripts = wp_scripts();
		$script     = $wp_scripts->registered[ $handle ] ?? null;

		return $script instanceof _WP_Dependency && '' !== (string) $script->src;
	}

	/**
	 * @param string $handle Registered style handle.
	 */
	private static function registered_style_has_src( string $handle ): bool {
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			return false;
		}

		$wp_styles = wp_styles();
		$style     = $wp_styles->registered[ $handle ] ?? null;

		return $style instanceof _WP_Dependency && '' !== (string) $style->src;
	}

	/**
	 * Ensure view.js loads after Splide when a standalone carousel is present.
	 */
	private static function ensure_view_script_depends_on_splide(): void {
		$wp_scripts = wp_scripts();
		if ( ! isset( $wp_scripts->registered[ self::VIEW_SCRIPT_HANDLE ] ) ) {
			return;
		}

		$splide_handle    = self::resolve_splide_script_handle();
		$autoscroll_handle = self::external_autoscroll_handle();
		if ( '' === $autoscroll_handle ) {
			$autoscroll_handle = self::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE;
		}
		$deps             = $wp_scripts->registered[ self::VIEW_SCRIPT_HANDLE ]->deps;

		foreach ( array( $splide_handle, $autoscroll_handle ) as $handle ) {
			if ( ! in_array( $handle, $deps, true ) ) {
				$wp_scripts->registered[ self::VIEW_SCRIPT_HANDLE ]->deps[] = $handle;
			}
		}
	}

	/**
	 * Enqueue on singular posts that contain the block or shortcode.
	 */
	public function maybe_enqueue(): void {
		if ( is_admin() ) {
			return;
		}
		$post = get_post();
		if ( ! $post instanceof WP_Post ) {
			return;
		}
		$c             = self::get_post_content_for_detection( $post );
		$has_shortcode = has_shortcode( $c, 'logo_wrangler' )
			|| self::raw_content_has_logo_wrangler_shortcode( $c );

		if ( ! has_block( 'cooper-bold/logo-wrangler', $post ) && ! $has_shortcode ) {
			return;
		}

		self::enqueue_frontend( self::content_needs_standalone_splide( $c ) );
	}

	/**
	 * Post content plus Bricks serialized layout meta for asset detection.
	 *
	 * @param WP_Post $post Current singular post.
	 */
	public static function get_post_content_for_detection( WP_Post $post ): string {
		$content = (string) $post->post_content;

		$bricks = get_post_meta( $post->ID, '_bricks_page_content_2', true );
		if ( is_string( $bricks ) && '' !== $bricks ) {
			$content .= "\n" . $bricks;
		}

		return $content;
	}

	/**
	 * Detect shortcode strings embedded in builder JSON / serialized meta.
	 *
	 * @param string $content Combined post and builder content.
	 */
	private static function raw_content_has_logo_wrangler_shortcode( string $content ): bool {
		if ( '' === $content ) {
			return false;
		}

		return (bool) preg_match( '/\[logo_wrangler(?:\s|])/i', $content );
	}

	/**
	 * Detect standalone carousel (layout=carousel, wrapper=full) in post content.
	 *
	 * @param string $content Post content HTML / block markup.
	 */
	public static function content_needs_standalone_splide( string $content ): bool {
		if ( '' === $content ) {
			return false;
		}

		if ( function_exists( 'parse_blocks' ) ) {
			$blocks = parse_blocks( $content );
			if ( self::blocks_need_standalone_splide( $blocks ) ) {
				return true;
			}
		}

		if ( preg_match_all( '/\[logo_wrangler([^\]]*)\]/i', $content, $matches ) ) {
			foreach ( $matches[1] as $attrs_fragment ) {
				$attrs_fragment = (string) $attrs_fragment;
				if ( self::shortcode_attrs_need_standalone_splide( $attrs_fragment ) ) {
					return true;
				}
				if ( self::raw_shortcode_attrs_need_standalone_splide( $attrs_fragment ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param array<int, array<string, mixed>> $blocks Parsed block tree.
	 */
	private static function blocks_need_standalone_splide( array $blocks ): bool {
		foreach ( $blocks as $block ) {
			if ( ! empty( $block['innerBlocks'] ) && self::blocks_need_standalone_splide( $block['innerBlocks'] ) ) {
				return true;
			}

			if ( ! in_array( ( $block['blockName'] ?? '' ), array( 'cooper-bold/logo-wrangler' ), true ) ) {
				continue;
			}

			$attrs = is_array( $block['attrs'] ?? null ) ? $block['attrs'] : array();

			if ( self::attributes_need_standalone_splide( $attrs ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array<string, mixed> $attrs Block or shortcode attributes.
	 */
	private static function attributes_need_standalone_splide( array $attrs ): bool {
		$layout  = isset( $attrs['layout'] ) ? (string) $attrs['layout'] : '';
		$wrapper = isset( $attrs['wrapper'] ) ? (string) $attrs['wrapper'] : '';

		if ( 'slides' === $wrapper ) {
			return false;
		}

		if ( 'carousel' === $layout ) {
			return true;
		}

		if ( '' !== $layout && 'carousel' !== $layout ) {
			return false;
		}

		$collection_id = isset( $attrs['collectionId'] ) ? absint( $attrs['collectionId'] ) : 0;
		if ( $collection_id > 0 ) {
			$from_collection = COOPBOLO_Collections::get_attributes( $collection_id );
			return null !== $from_collection
				&& 'carousel' === ( $from_collection['layout'] ?? 'strip' )
				&& 'slides' !== ( $wrapper ?: ( $from_collection['wrapper'] ?? 'full' ) );
		}

		return false;
	}

	/**
	 * @param string $attrs_fragment Shortcode attribute string (inside brackets).
	 */
	private static function shortcode_attrs_need_standalone_splide( string $attrs_fragment ): bool {
		$parsed = shortcode_parse_atts( $attrs_fragment );
		if ( ! is_array( $parsed ) ) {
			return false;
		}

		$attrs = array();
		if ( ! empty( $parsed['id'] ) ) {
			$attrs['collectionId'] = absint( $parsed['id'] );
		}
		if ( ! empty( $parsed['layout'] ) ) {
			$attrs['layout'] = sanitize_key( (string) $parsed['layout'] );
		}
		if ( ! empty( $parsed['wrapper'] ) ) {
			$attrs['wrapper'] = sanitize_key( (string) $parsed['wrapper'] );
		}

		if ( self::attributes_need_standalone_splide( $attrs ) ) {
			return true;
		}

		if ( ! empty( $parsed['collection'] ) && empty( $parsed['layout'] ) ) {
			$from_collection = COOPBOLO_Collections::get_attributes(
				sanitize_title( (string) $parsed['collection'] )
			);
			if ( null === $from_collection ) {
				return false;
			}

			$wrapper = ! empty( $parsed['wrapper'] )
				? sanitize_key( (string) $parsed['wrapper'] )
				: (string) ( $from_collection['wrapper'] ?? 'full' );

			return 'carousel' === ( $from_collection['layout'] ?? 'strip' ) && 'slides' !== $wrapper;
		}

		return false;
	}

	/**
	 * Fallback when builder meta escapes quotes and shortcode_parse_atts fails.
	 *
	 * @param string $attrs_fragment Shortcode attribute string (inside brackets).
	 */
	private static function raw_shortcode_attrs_need_standalone_splide( string $attrs_fragment ): bool {
		if ( ! preg_match( '/\blayout\s*=\s*(?:\\\\"|["\'])carousel\b/i', $attrs_fragment ) ) {
			return false;
		}

		return ! preg_match( '/\bwrapper\s*=\s*(?:\\\\"|["\'])slides\b/i', $attrs_fragment );
	}
}
