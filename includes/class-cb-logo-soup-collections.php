<?php
/**
 * Logo collections custom post type and admin UI.
 *
 * @package CooperBoldLogoSoup
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages named logo collections stored as a private CPT.
 */
final class CB_Logo_Soup_Collections {

	public const POST_TYPE     = 'cb_logo_collection';
	public const META_LOGOS    = '_cb_logo_soup_logos';
	public const META_SETTINGS = '_cb_logo_soup_settings';

	/** @var CB_Logo_Soup_Renderer|null */
	private static ?CB_Logo_Soup_Renderer $renderer = null;

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'prune_admin_submenus' ), 99 );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_post' ), 10, 2 );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'list_columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'render_list_column' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	public function register_post_type(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'               => __( 'Logo Collections', 'cooper-bold-logo-soup' ),
					'singular_name'      => __( 'Logo Collection', 'cooper-bold-logo-soup' ),
					'add_new'            => __( 'Add New', 'cooper-bold-logo-soup' ),
					'add_new_item'       => __( 'Add New Collection', 'cooper-bold-logo-soup' ),
					'edit_item'          => __( 'Edit Collection', 'cooper-bold-logo-soup' ),
					'new_item'           => __( 'New Collection', 'cooper-bold-logo-soup' ),
					'view_item'          => __( 'View Collection', 'cooper-bold-logo-soup' ),
					'search_items'       => __( 'Search Collections', 'cooper-bold-logo-soup' ),
					'not_found'          => __( 'No collections found.', 'cooper-bold-logo-soup' ),
					'not_found_in_trash' => __( 'No collections found in Trash.', 'cooper-bold-logo-soup' ),
					'all_items'          => __( 'All Collections', 'cooper-bold-logo-soup' ),
					'menu_name'          => __( 'Logo Collections', 'cooper-bold-logo-soup' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => self::parent_menu_slug(),
				'show_in_rest'        => false,
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'hierarchical'        => false,
				'supports'            => array( 'title' ),
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'can_export'          => true,
				'delete_with_user'    => false,
			)
		);
	}

	public function register_admin_menu(): void {
		$parent_slug = self::parent_menu_slug();

		add_menu_page(
			__( 'Logo Soup', 'cooper-bold-logo-soup' ),
			__( 'Logo Soup', 'cooper-bold-logo-soup' ),
			'edit_posts',
			$parent_slug,
			'',
			'dashicons-images-alt2',
			58
		);
	}

	/**
	 * Remove WordPress's auto-duplicated first submenu (same slug as parent).
	 */
	public function prune_admin_submenus(): void {
		$parent_slug = self::parent_menu_slug();
		remove_submenu_page( $parent_slug, $parent_slug );
	}

	/**
	 * Admin parent menu slug — CPT list screen URL (WordPress standard).
	 */
	public static function parent_menu_slug(): string {
		return 'edit.php?post_type=' . self::POST_TYPE;
	}

	public function register_meta_boxes(): void {
		add_meta_box(
			'cb-logo-soup-collection-logos',
			__( 'Logos', 'cooper-bold-logo-soup' ),
			array( $this, 'render_logos_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'cb-logo-soup-collection-settings',
			__( 'Collection Settings', 'cooper-bold-logo-soup' ),
			array( $this, 'render_settings_meta_box' ),
			self::POST_TYPE,
			'normal',
			'default'
		);

		add_meta_box(
			'cb-logo-soup-collection-preview',
			__( 'Live preview', 'cooper-bold-logo-soup' ),
			array( $this, 'render_preview_meta_box' ),
			self::POST_TYPE,
			'normal',
			'low'
		);

		add_meta_box(
			'cb-logo-soup-collection-shortcode',
			__( 'Shortcode', 'cooper-bold-logo-soup' ),
			array( $this, 'render_shortcode_meta_box' ),
			self::POST_TYPE,
			'side',
			'default'
		);
	}

	/**
	 * @param WP_Post $post Current post.
	 */
	public function render_logos_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'cb_logo_soup_save_collection', 'cb_logo_soup_collection_nonce' );
		$logos = self::get_logos_for_post( $post->ID );
		?>
		<div id="cb-logo-soup-collection-editor" class="cb-logo-soup-collection-editor">
			<p class="description">
				<?php esc_html_e( 'Add logos from the Media Library. Drag to reorder.', 'cooper-bold-logo-soup' ); ?>
			</p>
			<p>
				<button type="button" class="button button-primary" id="cb-logo-soup-add-logos">
					<?php esc_html_e( 'Add / edit logos', 'cooper-bold-logo-soup' ); ?>
				</button>
			</p>
			<ul id="cb-logo-soup-logo-list" class="cb-logo-soup-logo-list">
				<?php foreach ( $logos as $index => $logo ) : ?>
					<li class="cb-logo-soup-logo-item" data-index="<?php echo esc_attr( (string) $index ); ?>">
						<span class="cb-logo-soup-logo-handle dashicons dashicons-menu" aria-hidden="true"></span>
						<?php if ( ! empty( $logo['url'] ) ) : ?>
							<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="" class="cb-logo-soup-logo-thumb" />
						<?php endif; ?>
						<div class="cb-logo-soup-logo-fields">
							<input type="hidden" class="cb-logo-soup-logo-id" name="cb_logo_soup_logos[<?php echo esc_attr( (string) $index ); ?>][id]" value="<?php echo esc_attr( (string) ( $logo['id'] ?? '' ) ); ?>" />
							<input type="hidden" class="cb-logo-soup-logo-url" name="cb_logo_soup_logos[<?php echo esc_attr( (string) $index ); ?>][url]" value="<?php echo esc_attr( $logo['url'] ?? '' ); ?>" />
							<label>
								<?php esc_html_e( 'Alt text', 'cooper-bold-logo-soup' ); ?>
								<input type="text" class="widefat cb-logo-soup-logo-alt" name="cb_logo_soup_logos[<?php echo esc_attr( (string) $index ); ?>][alt]" value="<?php echo esc_attr( $logo['alt'] ?? '' ); ?>" />
							</label>
							<label>
								<?php esc_html_e( 'Link URL (optional)', 'cooper-bold-logo-soup' ); ?>
								<input type="url" class="widefat cb-logo-soup-logo-link" name="cb_logo_soup_logos[<?php echo esc_attr( (string) $index ); ?>][link]" value="<?php echo esc_attr( $logo['link'] ?? '' ); ?>" placeholder="https://" />
							</label>
							<button type="button" class="button-link-delete cb-logo-soup-remove-logo">
								<?php esc_html_e( 'Remove', 'cooper-bold-logo-soup' ); ?>
							</button>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
			<script type="text/html" id="tmpl-cb-logo-soup-logo-item">
				<li class="cb-logo-soup-logo-item" data-index="{{ data.index }}">
					<span class="cb-logo-soup-logo-handle dashicons dashicons-menu" aria-hidden="true"></span>
					<# if ( data.url ) { #>
						<img src="{{ data.url }}" alt="" class="cb-logo-soup-logo-thumb" />
					<# } #>
					<div class="cb-logo-soup-logo-fields">
						<input type="hidden" class="cb-logo-soup-logo-id" name="cb_logo_soup_logos[{{ data.index }}][id]" value="{{ data.id }}" />
						<input type="hidden" class="cb-logo-soup-logo-url" name="cb_logo_soup_logos[{{ data.index }}][url]" value="{{ data.url }}" />
						<label>
							<?php esc_html_e( 'Alt text', 'cooper-bold-logo-soup' ); ?>
							<input type="text" class="widefat cb-logo-soup-logo-alt" name="cb_logo_soup_logos[{{ data.index }}][alt]" value="{{ data.alt }}" />
						</label>
						<label>
							<?php esc_html_e( 'Link URL (optional)', 'cooper-bold-logo-soup' ); ?>
							<input type="url" class="widefat cb-logo-soup-logo-link" name="cb_logo_soup_logos[{{ data.index }}][link]" value="{{ data.link }}" placeholder="https://" />
						</label>
						<button type="button" class="button-link-delete cb-logo-soup-remove-logo">
							<?php esc_html_e( 'Remove', 'cooper-bold-logo-soup' ); ?>
						</button>
					</div>
				</li>
			</script>
		</div>
		<?php
	}

	/**
	 * @param WP_Post $post Current post.
	 */
	public function render_settings_meta_box( WP_Post $post ): void {
		$settings = self::get_settings_for_post( $post->ID );
		$align_options = array(
			'bounds'            => __( 'Bounds', 'cooper-bold-logo-soup' ),
			'visual-center'     => __( 'Visual center', 'cooper-bold-logo-soup' ),
			'visual-center-x'   => __( 'Visual center (X)', 'cooper-bold-logo-soup' ),
			'visual-center-y'   => __( 'Visual center (Y)', 'cooper-bold-logo-soup' ),
		);
		?>
		<table class="form-table cb-logo-soup-settings-table cb-logo-soup-settings-essential" role="presentation">
			<tr>
				<th scope="row"><label for="cb_logo_soup_base_size"><?php esc_html_e( 'Size', 'cooper-bold-logo-soup' ); ?></label></th>
				<td>
					<input type="number" id="cb_logo_soup_base_size" name="cb_logo_soup_settings[baseSize]" value="<?php echo esc_attr( (string) $settings['baseSize'] ); ?>" min="16" max="256" step="4" class="small-text" /> px
					<p class="description"><?php esc_html_e( 'Base height for each logo before normalization.', 'cooper-bold-logo-soup' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cb_logo_soup_gap"><?php esc_html_e( 'Gap', 'cooper-bold-logo-soup' ); ?></label></th>
				<td>
					<input type="number" id="cb_logo_soup_gap" name="cb_logo_soup_settings[gap]" value="<?php echo esc_attr( (string) $settings['gap'] ); ?>" min="0" max="96" step="4" class="small-text" /> px
					<p class="description"><?php esc_html_e( 'Space between logos in pixels.', 'cooper-bold-logo-soup' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cb_logo_soup_background_color"><?php esc_html_e( 'Background', 'cooper-bold-logo-soup' ); ?></label></th>
				<td>
					<input type="text" id="cb_logo_soup_background_color" name="cb_logo_soup_settings[backgroundColor]" value="<?php echo esc_attr( $settings['backgroundColor'] ); ?>" class="regular-text" placeholder="#fff" />
					<p class="description"><?php esc_html_e( 'Strip background color (helps contrast detection for light logos).', 'cooper-bold-logo-soup' ); ?></p>
				</td>
			</tr>
		</table>
		<details class="cb-logo-soup-advanced-settings">
			<summary><?php esc_html_e( 'Advanced settings', 'cooper-bold-logo-soup' ); ?></summary>
			<table class="form-table cb-logo-soup-settings-table cb-logo-soup-settings-advanced" role="presentation">
				<tr>
					<th scope="row"><label for="cb_logo_soup_scale_factor"><?php esc_html_e( 'Scale factor', 'cooper-bold-logo-soup' ); ?></label></th>
					<td>
						<input type="number" id="cb_logo_soup_scale_factor" name="cb_logo_soup_settings[scaleFactor]" value="<?php echo esc_attr( (string) $settings['scaleFactor'] ); ?>" min="0" max="1" step="0.1" class="small-text" />
						<p class="description"><?php esc_html_e( 'How much smaller logos can be relative to the largest mark (0–1).', 'cooper-bold-logo-soup' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cb_logo_soup_contrast_threshold"><?php esc_html_e( 'Contrast threshold', 'cooper-bold-logo-soup' ); ?></label></th>
					<td>
						<input type="number" id="cb_logo_soup_contrast_threshold" name="cb_logo_soup_settings[contrastThreshold]" value="<?php echo esc_attr( (string) $settings['contrastThreshold'] ); ?>" min="0" max="255" step="1" class="small-text" />
						<p class="description"><?php esc_html_e( 'Minimum contrast used when detecting logo edges (0–255).', 'cooper-bold-logo-soup' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Density aware', 'cooper-bold-logo-soup' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="cb_logo_soup_settings[densityAware]" value="1" <?php checked( $settings['densityAware'] ); ?> />
							<?php esc_html_e( 'Adjust for visual density', 'cooper-bold-logo-soup' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Scale logos based on how visually dense each mark appears.', 'cooper-bold-logo-soup' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cb_logo_soup_density_factor"><?php esc_html_e( 'Density factor', 'cooper-bold-logo-soup' ); ?></label></th>
					<td>
						<input type="number" id="cb_logo_soup_density_factor" name="cb_logo_soup_settings[densityFactor]" value="<?php echo esc_attr( (string) $settings['densityFactor'] ); ?>" min="0" max="1" step="0.1" class="small-text" />
						<p class="description"><?php esc_html_e( 'Strength of density-based scaling when density aware is on (0–1).', 'cooper-bold-logo-soup' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Crop to content', 'cooper-bold-logo-soup' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="cb_logo_soup_settings[cropToContent]" value="1" <?php checked( $settings['cropToContent'] ); ?> />
							<?php esc_html_e( 'Crop to detected content bounds', 'cooper-bold-logo-soup' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Trim transparent padding around each logo before sizing.', 'cooper-bold-logo-soup' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cb_logo_soup_align_by"><?php esc_html_e( 'Align by', 'cooper-bold-logo-soup' ); ?></label></th>
					<td>
						<select id="cb_logo_soup_align_by" name="cb_logo_soup_settings[alignBy]">
							<?php foreach ( $align_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['alignBy'], $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'How logos are vertically aligned in the strip.', 'cooper-bold-logo-soup' ); ?></p>
					</td>
				</tr>
			</table>
		</details>
		<?php
	}

	/**
	 * @param WP_Post $post Current post.
	 */
	public function render_preview_meta_box( WP_Post $post ): void {
		unset( $post );
		?>
		<div id="cb-logo-soup-preview-root" class="cb-logo-soup-preview-wrap" aria-live="polite"></div>
		<?php
	}

	/**
	 * @param WP_Post $post Current post.
	 */
	public function render_shortcode_meta_box( WP_Post $post ): void {
		$slug_snippet = self::get_shortcode_snippet( $post );
		$id_snippet   = sprintf( '[logo_soup id="%d"]', (int) $post->ID );
		?>
		<div class="cb-logo-soup-shortcode-panel">
			<?php self::render_shortcode_field( $slug_snippet, 'cb-logo-soup-shortcode-slug', true ); ?>
			<details class="cb-logo-soup-shortcode-advanced">
				<summary><?php esc_html_e( 'By ID', 'cooper-bold-logo-soup' ); ?></summary>
				<?php self::render_shortcode_field( $id_snippet, 'cb-logo-soup-shortcode-id', true ); ?>
			</details>
		</div>
		<?php
	}

	/**
	 * @param int $post_id Post ID.
	 * @param WP_Post $post Post object.
	 */
	public function save_post( int $post_id, WP_Post $post ): void {
		if ( ! isset( $_POST['cb_logo_soup_collection_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cb_logo_soup_collection_nonce'] ) ), 'cb_logo_soup_save_collection' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$raw_logos = isset( $_POST['cb_logo_soup_logos'] ) && is_array( $_POST['cb_logo_soup_logos'] )
			? wp_unslash( $_POST['cb_logo_soup_logos'] )
			: array();

		$logos = self::renderer()->sanitize_logos( self::normalize_logo_rows( $raw_logos ) );

		$raw_settings = isset( $_POST['cb_logo_soup_settings'] ) && is_array( $_POST['cb_logo_soup_settings'] )
			? wp_unslash( $_POST['cb_logo_soup_settings'] )
			: array();

		$settings = self::sanitize_settings( $raw_settings );

		update_post_meta( $post_id, self::META_LOGOS, $logos );
		update_post_meta( $post_id, self::META_SETTINGS, $settings );
	}

	/**
	 * @param array<int, mixed> $columns List columns.
	 * @return array<int, mixed>
	 */
	public function list_columns( array $columns ): array {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['cb_logo_count']    = __( 'Logos', 'cooper-bold-logo-soup' );
				$new['cb_shortcode']     = __( 'Shortcode', 'cooper-bold-logo-soup' );
			}
		}
		return $new;
	}

	/**
	 * @param string $column Column key.
	 * @param int    $post_id Post ID.
	 */
	public function render_list_column( string $column, int $post_id ): void {
		if ( 'cb_logo_count' === $column ) {
			echo esc_html( (string) count( self::get_logos_for_post( $post_id ) ) );
			return;
		}
		if ( 'cb_shortcode' === $column ) {
			$post = get_post( $post_id );
			if ( $post instanceof WP_Post ) {
				self::render_shortcode_field(
					self::get_shortcode_snippet( $post ),
					'cb-logo-soup-shortcode-list-' . (int) $post_id,
					false,
					true
				);
			}
		}
	}

	/**
	 * @param string $hook Current admin hook.
	 */
	public function enqueue_admin_assets( string $hook ): void {
		$screen = get_current_screen();
		if ( ! $screen || self::POST_TYPE !== $screen->post_type ) {
			return;
		}

		$is_edit_screen = in_array( $hook, array( 'post.php', 'post-new.php' ), true );
		$is_list_screen = 'edit.php' === $hook;

		if ( ! $is_edit_screen && ! $is_list_screen ) {
			return;
		}

		wp_enqueue_style(
			'cb-logo-soup-collection-editor',
			CB_LOGO_SOUP_URL . 'admin/css/collection-editor.css',
			array(),
			CB_LOGO_SOUP_VERSION
		);

		$script_deps = array( 'jquery' );
		if ( $is_edit_screen ) {
			wp_enqueue_media();
			wp_enqueue_script( 'jquery-ui-sortable' );
			$script_deps = array( 'jquery', 'jquery-ui-sortable', 'wp-util' );
		}

		wp_enqueue_script(
			'cb-logo-soup-collection-editor',
			CB_LOGO_SOUP_URL . 'admin/js/collection-editor.js',
			$script_deps,
			CB_LOGO_SOUP_VERSION,
			true
		);

		if ( $is_edit_screen ) {
			$preview_asset_file = CB_LOGO_SOUP_PATH . 'build/collection-preview.asset.php';
			$preview_asset      = file_exists( $preview_asset_file )
				? require $preview_asset_file
				: array(
					'dependencies' => array( 'react', 'react-jsx-runtime', 'wp-element', 'wp-i18n' ),
					'version'      => CB_LOGO_SOUP_VERSION,
				);

			wp_enqueue_script(
				'cb-logo-soup-collection-preview',
				CB_LOGO_SOUP_URL . 'build/collection-preview.js',
				$preview_asset['dependencies'],
				$preview_asset['version'],
				true
			);
		}
	}

	public function register_rest_routes(): void {
		register_rest_route(
			'cb-logo-soup/v1',
			'/collections',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_list_collections' ),
				'permission_callback' => static function (): bool {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * @return WP_REST_Response
	 */
	public function rest_list_collections(): WP_REST_Response {
		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$items = array();
		foreach ( $posts as $post ) {
			if ( ! $post instanceof WP_Post ) {
				continue;
			}
			$attrs = self::build_attributes_from_post( $post );
			if ( null === $attrs ) {
				continue;
			}
			$items[] = array_merge(
				array(
					'id'    => (int) $post->ID,
					'title' => $post->post_title,
					'slug'  => $post->post_name,
				),
				$attrs
			);
		}

		return new WP_REST_Response( $items, 200 );
	}

	/**
	 * Resolve collection by numeric ID or post slug.
	 *
	 * @param int|string $id_or_slug Collection post ID or slug.
	 * @return array<string, mixed>|null Block-style attributes or null when not found.
	 */
	public static function get_attributes( $id_or_slug ): ?array {
		$post = null;
		if ( is_numeric( $id_or_slug ) && (int) $id_or_slug > 0 ) {
			$candidate = get_post( (int) $id_or_slug );
			if ( $candidate instanceof WP_Post && self::POST_TYPE === $candidate->post_type && 'publish' === $candidate->post_status ) {
				$post = $candidate;
			}
		} elseif ( is_string( $id_or_slug ) && '' !== trim( $id_or_slug ) ) {
			$posts = get_posts(
				array(
					'post_type'      => self::POST_TYPE,
					'post_status'    => 'publish',
					'name'           => sanitize_title( $id_or_slug ),
					'posts_per_page' => 1,
				)
			);
			if ( ! empty( $posts[0] ) && $posts[0] instanceof WP_Post ) {
				$post = $posts[0];
			}
		}

		if ( ! $post instanceof WP_Post ) {
			return null;
		}

		return self::build_attributes_from_post( $post );
	}

	/**
	 * @param WP_Post $post Collection post.
	 * @return array<string, mixed>|null
	 */
	public static function build_attributes_from_post( WP_Post $post ): ?array {
		if ( self::POST_TYPE !== $post->post_type || 'publish' !== $post->post_status ) {
			return null;
		}

		$logos    = self::get_logos_for_post( (int) $post->ID );
		$settings = self::get_settings_for_post( (int) $post->ID );

		if ( empty( $logos ) ) {
			return null;
		}

		return array_merge(
			$settings,
			array(
				'logos'        => $logos,
				'collectionId' => (int) $post->ID,
			)
		);
	}

	/**
	 * Render a readonly shortcode field with a one-click Copy button.
	 *
	 * @param string $snippet Shortcode text.
	 * @param string $input_id Optional input element ID.
	 * @param bool   $widefat Whether to apply the widefat class (meta box).
	 * @param bool   $compact Compact list-table layout (truncated text, icon-only copy).
	 */
	public static function render_shortcode_field( string $snippet, string $input_id = '', bool $widefat = false, bool $compact = false ): void {
		$row_classes = 'cb-logo-soup-shortcode-row';
		if ( $compact ) {
			$row_classes .= ' cb-logo-soup-shortcode-row--compact';
		}

		$input_classes = 'code cb-logo-soup-shortcode-input';
		if ( $widefat ) {
			$input_classes .= ' widefat';
		}
		if ( $compact ) {
			$input_classes .= ' screen-reader-text';
		}

		$copy_label = __( 'Copy shortcode', 'cooper-bold-logo-soup' );
		?>
		<div class="<?php echo esc_attr( $row_classes ); ?>">
			<?php if ( $compact ) : ?>
				<code class="cb-logo-soup-shortcode-display" title="<?php echo esc_attr( $snippet ); ?>">
					<?php echo esc_html( self::truncate_shortcode_display( $snippet ) ); ?>
				</code>
			<?php endif; ?>
			<input
				type="text"
				<?php if ( '' !== $input_id ) : ?>
					id="<?php echo esc_attr( $input_id ); ?>"
				<?php endif; ?>
				class="<?php echo esc_attr( $input_classes ); ?>"
				readonly
				value="<?php echo esc_attr( $snippet ); ?>"
				<?php if ( ! $compact ) : ?>
					onclick="this.select();"
				<?php endif; ?>
			/>
			<button
				type="button"
				class="button cb-logo-soup-copy-shortcode"
				title="<?php echo esc_attr( $copy_label ); ?>"
				aria-label="<?php echo esc_attr( $copy_label ); ?>"
			>
				<span class="dashicons dashicons-clipboard" aria-hidden="true"></span>
				<span class="cb-logo-soup-copy-label screen-reader-text"><?php echo esc_html( $copy_label ); ?></span>
			</button>
		</div>
		<?php
	}

	/**
	 * Truncate a shortcode string for compact admin display.
	 *
	 * @param string $snippet Full shortcode.
	 */
	public static function truncate_shortcode_display( string $snippet, int $max = 28 ): string {
		if ( strlen( $snippet ) <= $max ) {
			return $snippet;
		}
		return substr( $snippet, 0, $max - 1 ) . '…';
	}

	public static function get_shortcode_snippet( WP_Post $post ): string {
		$slug = $post->post_name;
		if ( '' === $slug && '' !== $post->post_title ) {
			$slug = sanitize_title( $post->post_title );
		}
		if ( '' === $slug ) {
			return sprintf( '[logo_soup id="%d"]', (int) $post->ID );
		}
		return sprintf( '[logo_soup collection="%s"]', $slug );
	}

	/**
	 * @param int $post_id Post ID.
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_logos_for_post( int $post_id ): array {
		$stored = get_post_meta( $post_id, self::META_LOGOS, true );
		if ( ! is_array( $stored ) ) {
			return array();
		}
		return self::renderer()->sanitize_logos( $stored );
	}

	/**
	 * @param int $post_id Post ID.
	 * @return array<string, mixed>
	 */
	public static function get_settings_for_post( int $post_id ): array {
		$stored = get_post_meta( $post_id, self::META_SETTINGS, true );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		return self::sanitize_settings( $stored );
	}

	/**
	 * @param array<string, mixed> $raw Raw settings from POST or meta.
	 * @return array<string, mixed>
	 */
	public static function sanitize_settings( array $raw ): array {
		$defaults = self::renderer()->get_defaults();
		unset( $defaults['logos'], $defaults['className'] );

		$attrs = array(
			'baseSize'            => $raw['baseSize'] ?? $defaults['baseSize'],
			'scaleFactor'         => $raw['scaleFactor'] ?? $defaults['scaleFactor'],
			'contrastThreshold'   => $raw['contrastThreshold'] ?? $defaults['contrastThreshold'],
			'densityAware'        => ! empty( $raw['densityAware'] ),
			'densityFactor'       => $raw['densityFactor'] ?? $defaults['densityFactor'],
			'cropToContent'       => ! empty( $raw['cropToContent'] ),
			'backgroundColor'     => $raw['backgroundColor'] ?? '',
			'alignBy'             => $raw['alignBy'] ?? $defaults['alignBy'],
			'gap'                 => $raw['gap'] ?? $defaults['gap'],
		);

		$sanitized = self::renderer()->sanitize_attributes( array_merge( $attrs, array( 'logos' => array() ) ) );
		unset( $sanitized['logos'], $sanitized['className'] );

		return $sanitized;
	}

	/**
	 * @param array<int, mixed> $raw Raw logo rows from POST.
	 * @return array<int, array<string, mixed>>
	 */
	public static function normalize_logo_rows( array $raw ): array {
		$rows = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$rows[] = array(
				'id'   => isset( $row['id'] ) ? absint( $row['id'] ) : 0,
				'url'  => isset( $row['url'] ) ? (string) $row['url'] : '',
				'alt'  => isset( $row['alt'] ) ? (string) $row['alt'] : '',
				'link' => isset( $row['link'] ) ? (string) $row['link'] : '',
			);
		}
		return $rows;
	}

	private static function renderer(): CB_Logo_Soup_Renderer {
		if ( null === self::$renderer ) {
			self::$renderer = new CB_Logo_Soup_Renderer();
		}
		return self::$renderer;
	}
}
