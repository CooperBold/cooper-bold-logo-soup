<?php
/**
 * Unit tests for sparse admin branding footer.
 *
 * @package CooperBoldLogoSoup
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers CB_Logo_Soup_Admin_Branding
 */
final class CB_Logo_Soup_Admin_Branding_Test extends TestCase {

	public function test_filter_footer_text_returns_plain_text_link_on_logo_soup_screen(): void {
		$GLOBALS['cb_test_current_screen'] = (object) array(
			'post_type' => CB_Logo_Soup_Collections::POST_TYPE,
		);

		$branding = new CB_Logo_Soup_Admin_Branding();
		$html     = $branding->filter_footer_text( 'Thank you for creating with WordPress.' );

		$this->assertStringContainsString( 'cb-logo-soup-footer-brand', $html );
		$this->assertStringContainsString( 'href="https://cooperbold.com"', $html );
		$this->assertStringContainsString( 'target="_blank"', $html );
		$this->assertStringContainsString( 'rel="noopener noreferrer"', $html );
		$this->assertStringContainsString( '>CooperBold</a>', $html );
		$this->assertStringNotContainsString( '<img', $html );
		$this->assertStringNotContainsString( 'cooper-bold-wordmark.png', $html );
		$this->assertStringNotContainsString( 'Thank you for creating with WordPress.', $html );
	}

	public function test_filter_footer_text_leaves_default_on_other_screens(): void {
		$GLOBALS['cb_test_current_screen'] = (object) array(
			'post_type' => 'post',
		);

		$default  = 'Thank you for creating with WordPress.';
		$branding = new CB_Logo_Soup_Admin_Branding();

		$this->assertSame( $default, $branding->filter_footer_text( $default ) );
	}

	public function test_filter_update_footer_clears_text_on_logo_soup_screen(): void {
		$GLOBALS['cb_test_current_screen'] = (object) array(
			'post_type' => CB_Logo_Soup_Collections::POST_TYPE,
		);

		$branding = new CB_Logo_Soup_Admin_Branding();

		$this->assertSame( '', $branding->filter_update_footer( 'Get Version 7.0' ) );
	}

	public function test_filter_update_footer_leaves_default_on_other_screens(): void {
		$GLOBALS['cb_test_current_screen'] = (object) array(
			'post_type' => 'post',
		);

		$default  = 'Version 6.7';
		$branding = new CB_Logo_Soup_Admin_Branding();

		$this->assertSame( $default, $branding->filter_update_footer( $default ) );
	}

	public function test_branding_class_has_no_footer_action_renderer(): void {
		$this->assertFalse( method_exists( CB_Logo_Soup_Admin_Branding::class, 'render_footer' ) );
		$this->assertFalse( method_exists( CB_Logo_Soup_Admin_Branding::class, 'render_credit' ) );
		$this->assertTrue( method_exists( CB_Logo_Soup_Admin_Branding::class, 'filter_footer_text' ) );
		$this->assertTrue( method_exists( CB_Logo_Soup_Admin_Branding::class, 'filter_update_footer' ) );
	}
}
