# Context7 library IDs — Logo Soup WP Plugin

Pre-resolved IDs for stack docs. Use **`resolve-library-id`** if an ID fails or the topic is outside this table (limit ~3 resolve/query rounds per question).

| Stack piece | Library | Context7 library ID | Notes |
| --- | --- | --- | --- |
| WordPress | WordPress Plugin Handbook | `/websites/developer_wordpress_plugins` | Structure, hooks, security. |
| WordPress REST | WordPress REST API | `/wp-api/docs` | Routes, Application Passwords. |
| WordPress | WordPress functions reference | `/websites/developer_wordpress_reference_functions` | Hooks, APIs, helpers. |
| PHP | PHP manual | `/websites/php_net_manual_en` | Language and stdlib. |
| Blocks | Gutenberg / Block Editor | `/wordpress/gutenberg` | Block registration, block.json. |
| JS build | @wordpress/scripts | `/wordpress/gutenberg` | `wp-scripts build`, webpack config. |
| Testing | WordPress PHPUnit | `/websites/make_wordpress_core_handbook` | Core handbook patterns when adding tests. |

**Credentials:** Context7 MCP requires `CONTEXT7_API_KEY` in Cursor MCP config (`~/.cursor/mcp.json`) or env. Enable the `user-context7` server in Cursor Settings → MCP when needed.
