<?php
/**
 * Unit tests for logo collection resolution.
 *
 * @package CooperBoldBalancedLogos
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers CB_Balanced_Logos_Collections
 * @covers CB_Balanced_Logos_Renderer::resolve_attributes
 */
final class CB_Balanced_Logos_Collections_Test extends TestCase {

	/** @var CB_Balanced_Logos_Renderer */
	private $renderer;

	protected function setUp(): void {
		parent::setUp();
		$this->renderer = new CB_Balanced_Logos_Renderer();
		$GLOBALS['cb_test_posts']         = array();
		$GLOBALS['cb_test_post_meta']     = array();
		$GLOBALS['cb_test_posts_by_slug'] = array();
	}

	protected function tearDown(): void {
		unset( $GLOBALS['cb_test_posts'], $GLOBALS['cb_test_post_meta'], $GLOBALS['cb_test_posts_by_slug'] );
		parent::tearDown();
	}

	public function test_build_attributes_from_post_returns_merged_settings(): void {
		$post = $this->make_post(
			42,
			'homepage-partners',
			array(
				CB_Balanced_Logos_Collections::META_LOGOS => array(
					array(
						'url' => 'https://example.com/logo.png',
						'alt' => 'Partner',
					),
				),
				CB_Balanced_Logos_Collections::META_SETTINGS => array(
					'baseSize' => 40,
					'gap'      => 32,
				),
			)
		);

		$attrs = CB_Balanced_Logos_Collections::build_attributes_from_post( $post );

		$this->assertNotNull( $attrs );
		$this->assertSame( 42, $attrs['collectionId'] );
		$this->assertCount( 1, $attrs['logos'] );
		$this->assertSame( 40, $attrs['baseSize'] );
		$this->assertSame( 32, $attrs['gap'] );
	}

	public function test_get_attributes_by_id_and_slug(): void {
		$post = $this->make_post(
			7,
			'enterprise-logos',
			array(
				CB_Balanced_Logos_Collections::META_LOGOS => array(
					array( 'url' => 'https://example.com/a.svg', 'alt' => 'A' ),
				),
			)
		);
		$GLOBALS['cb_test_posts'][7] = $post;

		$by_id = CB_Balanced_Logos_Collections::get_attributes( 7 );
		$this->assertNotNull( $by_id );
		$this->assertSame( 'A', $by_id['logos'][0]['alt'] );

		$GLOBALS['cb_test_posts_by_slug']['enterprise-logos'] = $post;
		$by_slug = CB_Balanced_Logos_Collections::get_attributes( 'enterprise-logos' );
		$this->assertNotNull( $by_slug );
		$this->assertSame( 'https://example.com/a.svg', $by_slug['logos'][0]['url'] );
	}

	public function test_get_attributes_returns_null_for_missing_collection(): void {
		$this->assertNull( CB_Balanced_Logos_Collections::get_attributes( 999 ) );
		$this->assertNull( CB_Balanced_Logos_Collections::get_attributes( 'missing-slug' ) );
	}

	public function test_resolve_attributes_uses_collection_logos_and_overrides_gap(): void {
		$post = $this->make_post(
			3,
			'partners',
			array(
				CB_Balanced_Logos_Collections::META_LOGOS => array(
					array( 'url' => 'https://example.com/logo.png', 'alt' => 'Logo' ),
				),
				CB_Balanced_Logos_Collections::META_SETTINGS => array(
					'gap' => 28,
				),
			)
		);
		$GLOBALS['cb_test_posts'][3] = $post;

		$resolved = $this->renderer->resolve_attributes(
			array(
				'collectionId' => 3,
				'gap'          => 48,
				'logos'        => array(
					array( 'url' => 'https://ignored.test/x.png', 'alt' => 'Ignored' ),
				),
			)
		);

		$this->assertSame( 'https://example.com/logo.png', $resolved['logos'][0]['url'] );
		$this->assertSame( 48, $resolved['gap'] );
	}

	public function test_resolve_attributes_by_slug(): void {
		$post = $this->make_post(
			5,
			'homepage-partners',
			array(
				CB_Balanced_Logos_Collections::META_LOGOS => array(
					array( 'url' => 'https://example.com/p.png', 'alt' => 'P' ),
				),
			)
		);
		$GLOBALS['cb_test_posts_by_slug']['homepage-partners'] = $post;

		$resolved = $this->renderer->resolve_attributes(
			array( 'collection' => 'homepage-partners' )
		);

		$this->assertCount( 1, $resolved['logos'] );
		$this->assertSame( 'P', $resolved['logos'][0]['alt'] );
	}

	public function test_get_shortcode_snippet_prefers_slug(): void {
		$post = $this->make_post( 11, 'homepage-partners', array() );
		$this->assertSame(
			'[balanced_logos collection="homepage-partners"]',
			CB_Balanced_Logos_Collections::get_shortcode_snippet( $post )
		);
	}

	public function test_sanitize_settings_clamps_values(): void {
		$settings = CB_Balanced_Logos_Collections::sanitize_settings(
			array(
				'baseSize'    => 999,
				'scaleFactor' => -1,
				'gap'         => 200,
			)
		);

		$this->assertSame( 256, $settings['baseSize'] );
		$this->assertEquals( 0.0, $settings['scaleFactor'] );
		$this->assertSame( 96, $settings['gap'] );
	}

	public function test_preview_meta_box_outputs_mount_root(): void {
		$reflection = new ReflectionClass( CB_Balanced_Logos_Collections::class );
		$method     = $reflection->getMethod( 'render_preview_meta_box' );
		$instance   = $reflection->newInstanceWithoutConstructor();
		$post       = $this->make_post( 1, 'preview-test', array() );

		ob_start();
		$method->invoke( $instance, $post );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'id="cb-balanced-logos-preview-root"', $html );
		$this->assertStringContainsString( 'cb-balanced-logos-preview-wrap', $html );
	}

	/**
	 * @param int                  $id Post ID.
	 * @param string               $slug Post slug.
	 * @param array<string, mixed> $meta Meta values.
	 */
	private function make_post( int $id, string $slug, array $meta ): WP_Post {
		$post = new WP_Post(
			(object) array(
				'ID'          => $id,
				'post_type'   => CB_Balanced_Logos_Collections::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => 'Test Collection',
				'post_name'   => $slug,
			)
		);

		foreach ( $meta as $key => $value ) {
			$GLOBALS['cb_test_post_meta'][ $id ][ $key ] = $value;
		}

		return $post;
	}
}
