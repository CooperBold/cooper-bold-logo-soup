<?php
/**
 * Unit tests for CB_Balanced_Logos_Assets conditional Splide enqueue.
 *
 * @package CooperBoldBalancedLogos
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers CB_Balanced_Logos_Assets
 */
final class CB_Balanced_Logos_Assets_Test extends TestCase {

	protected function tearDown(): void {
		parent::tearDown();
		$ref  = new ReflectionClass( CB_Balanced_Logos_Assets::class );
		$prop = $ref->getProperty( 'needs_standalone_splide' );
		$prop->setValue( null, false );
	}

	public function test_content_detects_carousel_shortcode(): void {
		$content = '[balanced_logos layout="carousel" logos="https://example.com/a.svg|A"]';
		$this->assertTrue( CB_Balanced_Logos_Assets::content_needs_standalone_splide( $content ) );
	}

	public function test_content_skips_carousel_slides_wrapper(): void {
		$content = '[logo_soup layout="carousel" wrapper="slides" logos="https://example.com/a.svg|A"]';
		$this->assertFalse( CB_Balanced_Logos_Assets::content_needs_standalone_splide( $content ) );
	}

	public function test_content_skips_strip_layout(): void {
		$content = '[logo_soup layout="strip" logos="https://example.com/a.svg|A"]';
		$this->assertFalse( CB_Balanced_Logos_Assets::content_needs_standalone_splide( $content ) );
	}

	public function test_enqueue_frontend_sets_standalone_flag(): void {
		$this->assertFalse( CB_Balanced_Logos_Assets::needs_standalone_splide() );
		CB_Balanced_Logos_Assets::enqueue_frontend( true );
		$this->assertTrue( CB_Balanced_Logos_Assets::needs_standalone_splide() );
	}

	public function test_enqueue_frontend_without_carousel_keeps_flag_false(): void {
		CB_Balanced_Logos_Assets::enqueue_frontend( false );
		$this->assertFalse( CB_Balanced_Logos_Assets::needs_standalone_splide() );
	}

	public function test_raw_content_detects_shortcode_in_bricks_meta(): void {
		$bricks_json = '{"name":"shortcode","settings":{"shortcode":"[logo_soup collection=\\"test-slide\\" layout=\\"carousel\\"]"}}';
		$this->assertTrue( CB_Balanced_Logos_Assets::content_needs_standalone_splide( $bricks_json ) );
	}

	public function test_content_detects_collection_carousel_shortcode(): void {
		$content = '[logo_soup collection="test-slide" layout="carousel"]';
		$this->assertTrue( CB_Balanced_Logos_Assets::content_needs_standalone_splide( $content ) );
	}
}
