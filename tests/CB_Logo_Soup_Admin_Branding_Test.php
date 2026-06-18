<?php
/**
 * Unit tests for sparse admin branding credit.
 *
 * @package CooperBoldLogoSoup
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers CB_Logo_Soup_Admin_Branding
 */
final class CB_Logo_Soup_Admin_Branding_Test extends TestCase {

	public function test_render_credit_outputs_text_links_not_image(): void {
		ob_start();
		CB_Logo_Soup_Admin_Branding::render_credit();
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( 'cb-logo-soup-admin-credit', $html );
		$this->assertStringContainsString( 'href="https://cooperbold.com"', $html );
		$this->assertStringContainsString( 'Cooper Bold', $html );
		$this->assertStringContainsString( 'cooperbold.com', $html );
		$this->assertStringNotContainsString( '<img', $html );
		$this->assertStringNotContainsString( 'cooper-bold-wordmark.png', $html );
	}

	public function test_branding_class_has_no_footer_hooks(): void {
		$this->assertFalse( method_exists( CB_Logo_Soup_Admin_Branding::class, 'render_footer' ) );
		$this->assertFalse( method_exists( CB_Logo_Soup_Admin_Branding::class, 'filter_footer_text' ) );
		$this->assertTrue( method_exists( CB_Logo_Soup_Admin_Branding::class, 'render_credit' ) );
	}
}
