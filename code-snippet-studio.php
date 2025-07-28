<?php
/**
 * Plugin Name: Code Snippet Studio
 * Plugin URI: https://lifebrand.co/my-brand/developers/code-snippet-studio/
 * Description: Build, manage, and deploy modular PHP snippets with versioning, changelogs, settings, and logging. Designed for AI-first plugin development and advanced customization workflows. Also includes a curated library of preloaded, performance-ready snippets—making it easy for site owners to enable powerful features without writing code.
 * Version: 2.1.3
 * Author: Life Brand
 * Author URI: https://lifebrand.co/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: code-snippet-studio
 */
 
if (!defined('ABSPATH')) exit;

function code_snippet_studio_changelog() {
	
	return [
		['version' => '2.1.3', 'date' => '2025-07-19', 'note' => 'More detailed logging including IP address, agent, and URL. Single snippet support for indepentent plugins, with updated documentation. Enhanced settings schema to support both legacy select:OptionA:OptionB syntax and new structured select type with options array. Developers can now define select dropdowns with key/value pairs for cleaner labels and internal values. Maintains backward compatibility with existing snippet and plugin configurations.'],
		['version' => '2.1.2', 'date' => '2025-07-13', 'note' => 'Global setting changes now supported in Activity Log, including more stability for all activity.'],
		['version' => '2.1.1', 'date' => '2025-07-13', 'note' => 'Added permission check for all files & warnings for files that are not readable. Updated prompting documentation for developers. Minor style class name changes for consistency.'],
		['version' => '2.1.0', 'date' => '2025-07-13', 'note' => 'Global custom fields support.'],
		['version' => '2.0.1', 'date' => '2025-07-13', 'note' => 'Prefixed all option keys for better namespace isolation and consistency. Prefixed all option keys for better namespace isolation and consistency. Added AI prompting documentation.'],
		['version' => '2.0.0', 'date' => '2025-07-12', 'note' => 'Snippet setting field separators support, custom menu title & order, global notes & execution mode, extended developer guide with AI prompting for custom snippets, cleaner styles.'],
		['version' => '1.9.0', 'date' => '2025-07-06', 'note' => 'Wrapped translatable strings with esc_html__() and _e() functions. Added load_plugin_textdomain() for localization support. Ensured all input is sanitized and escaped where needed. Show only active selected snippets, snippet library cleanup.'],
		['version' => '1.8.0', 'date' => '2025-07-02', 'note' => 'Cleaner styles, mobile friendly, filter hides entire sections when no results found.'],
		['version' => '1.7.0', 'date' => '2025-06-29', 'note' => 'Global settings schema introduced for plugin-level fields (e.g., debug, API key), dynamic settings rendering and saving logic, anchor tag link shortcuts, improved settings field compatibility.'],
		['version' => '1.6.0', 'date' => '2025-06-28', 'note' => 'File upload field type, snippet thumbnails with lightbox popup, quick save button under each folder, cleaner activate checkbox UI.'],
		['version' => '1.5.0', 'date' => '2025-06-15', 'note' => 'Front-end script support, Pickr color picker integration, subfolder inclusion, thumbnails, changelog collapse, debug output on front-end, improved normalization, sub-menu page support, and support for blank color values.'],
		['version' => '1.4.0', 'date' => '2025-06-07', 'note' => 'Added debug mode, collapsible settings, and auto-scroll to active settings panel on save.'],
		['version' => '1.3.0', 'date' => '2025-05-25', 'note' => 'Added per-script settings UI with support for <code>text</code> and <code>select</code> fields.'],
		['version' => '1.2.0', 'date' => '2025-05-18', 'note' => 'Activation status now persists across versioned filenames by normalizing keys.'],
		['version' => '1.1.0', 'date' => '2025-05-17', 'note' => 'Added activity logging with timestamp, user, and action history (up to 100 recent entries).'],
		['version' => '1.0.0', 'date' => '2025-05-03', 'note' => 'Initial release with toggle UI, folder-based organization, version parsing, and override support.'],
	];

}

add_action('admin_menu', function () {
	$menu_title = get_option('code_snippet_studio_menu_title', '');
	$menu_title = !empty($menu_title) ? $menu_title : 'Code Snippet Studio';

	$menu_order = get_option('code_snippet_studio_menu_order', '');
	$menu_order = is_numeric($menu_order) ? (int) $menu_order : null; // Cast safely

	add_menu_page(
		$menu_title,                           // Page title
		$menu_title,                           // Menu title
		'manage_options',                      // Capability
		'code_snippet_studio',             // Menu slug
		'render_code_snippet_studio_page', // Function
		'dashicons-category',                // Icon (optional, can customize)
		$menu_order                            // Position in menu
	);

	add_submenu_page(
		'code_snippet_studio',
		'Main Settings',
		'Main Settings',
		'manage_options',
		'code_snippet_studio',
		'render_code_snippet_studio_page'
	);
});

function code_snippet_studio_get_global_settings_schema() {
	$schema = [
		[
			'slug'  => 'menu_title',
			'title' => 'Menu Title',
			'type'  => 'text',
			'note'  => 'Add a custom menu title for this plugin',
		],
		[
			'slug'  => 'menu_order',
			'title' => 'Menu Order Number',
			'type'  => 'number',
			'note'  => 'Change the menu order number for this plugin',
		],
		[
			'slug'  => 'global_notes',
			'title' => 'Global Notes',
			'type'  => 'textarea',
			'note'  => 'Helpful notes shown across scripts.',
		],
		[
			'slug'  => 'disable_normal_snippets',
			'title' => 'Normal Snippets',
			'type'  => 'select:Enabled:Disabled',
			'note'  => 'Globally turn off all normal snippets.',
		],
		[
			'slug'  => 'disable_custom_snippets',
			'title' => 'Custom Snippets',
			'type'  => 'select:Enabled:Disabled',
			'note'  => 'Globally turn off all custom snippets.',
		],
		[
			'slug'  => 'developer_mode',
			'title' => 'Enable Developer Mode',
			'type'  => 'checkbox',
			'note'  => 'Displays what code can access each custom setting when viewing each setting.',
		],
		[
			'slug'  => 'debug_mode',
			'title' => 'Enable Debug Mode',
			'type'  => 'checkbox',
			'note'  => 'Outputs extra logging information on both backend wp-admin screens and frontend of website.',
		],
		[
			'slug'  => 'single_snippet_mode_filename',
			'title' => 'Single Snippet Mode Filename',
			'type'  => 'text',
			'note'  => 'Enter the filename of a single snippet, without .php to simulate how the plugin will behave with only one snippet.',
		],
		[
			'slug'  => 'separator',
			'title' => 'API Keys & Secrets',
			'type'  => 'separator',
			'note'  => 'Save your credentials below to access globally throughout any of your snippets.',
		],
		[
			'slug'  => 'api_key_chatgpt',
			'title' => 'ChatGPT API Key',
			'type'  => 'text',
			'note'  => 'API Key from OpenAI.',
		],
		[
			'slug'  => 'api_key_slack_token',
			'title' => 'Slack Bot User OAuth Token',
			'type'  => 'text',
			'note'  => 'Setup via Slack.',
		],
		[
			'slug'  => 'api_key_stripe_secret',
			'title' => 'Stripe Secret',
			'type'  => 'text',
			'note'  => 'Setup via Stripe.',
		],
		[
			'slug'  => 'api_key_twilio_sid',
			'title' => 'Twilio SID',
			'type'  => 'text',
			'note'  => 'Setup via Twilio.',
		],
		[
			'slug'  => 'api_key_twilio_token',
			'title' => 'Twilio Token',
			'type'  => 'text',
			'note'  => 'Setup via Twilio.',
		],
		[
			'slug'  => 'api_key_twilio_phone_number',
			'title' => 'Twilio Phone Number',
			'type'  => 'text',
			'note'  => 'Setup via Twilio.',
		],
		[
			'slug'  => 'separator',
			'title' => 'Custom Global Fields',
			'type'  => 'separator',
			'note'  => 'Easily create your own custom global fields that can be accessed throughout all of your snippets.',
		],
		[
			'slug'  => 'global_custom_fields',
			'title' => 'Global Custom Fields',
			'type'  => 'textarea',
			'note'  => 'Create your own custom fields in this format, line by line: Field Title|text/number/textarea/checkbox/select:Enabled:Disabled/separator|field_slug|This is an optional field note...',
		],
	];

	// Append any dynamic custom fields
	$custom_fields_raw = get_option('code_snippet_studio_global_custom_fields', '');
	$lines = array_filter(array_map('trim', explode("\n", $custom_fields_raw)));

	foreach ($lines as $line) {
		$parts = array_map('trim', explode('|', $line));
		if (count($parts) >= 3) {
			$schema[] = [
				'title' => $parts[0],
				'type'  => $parts[1],
				'slug'  => $parts[2],
				'note'  => $parts[3] ?? '',
			];
		}
	}

	return $schema;
}

function render_code_snippet_studio_page() {
	
	$plugin_changelog = code_snippet_studio_changelog();
	
	$base_dir = plugin_dir_path(__FILE__);
	$statuses = get_option('code_snippet_studio_statuses', []);
	$values = get_option('code_snippet_studio_settings', []);
	$debug_mode = get_option('code_snippet_studio_debug_mode', '0');
	$log = array_reverse(get_option('code_snippet_studio_activity_log', []));
	$disable_custom_snippets = get_option('code_snippet_studio_disable_custom_snippets', '0');
	$disable_normal_snippets = get_option('code_snippet_studio_disable_normal_snippets', '0');
	$developer_mode = get_option('code_snippet_studio_developer_mode', '0');
	$plugin_dir = plugin_dir_path(__DIR__);
	$plugin_folder_name = basename(dirname(plugin_basename(__FILE__)));
	
	$all_dirs = [];
	
	if ($disable_custom_snippets !== "Disabled") {
		$custom_dir = WP_CONTENT_DIR . '/' . $plugin_folder_name . '-custom';
		if (is_dir($custom_dir)) {
			$custom_dirs = glob($custom_dir . '*/', GLOB_ONLYDIR);
			if (is_array($custom_dirs)) {
				$all_dirs = array_merge($all_dirs, $custom_dirs);
			}
		}
	}
	
	if ($disable_normal_snippets !== "Disabled") {
		if (is_dir($base_dir)) {
			$base_dirs = glob($base_dir . '*/', GLOB_ONLYDIR);
			if (is_array($base_dirs)) {
				$all_dirs = array_merge($all_dirs, $base_dirs);
			}
		}
	}
	
	$filename_filter = get_option('code_snippet_studio_single_snippet_mode_filename', '');
	$snippet_files = 0;
	foreach ($all_dirs as $folder) {
		if (basename($folder) === 'assets') continue;
	
		foreach (glob($folder . '*.php') as $file) {
			if ($filename_filter && pathinfo($file, PATHINFO_FILENAME) !== $filename_filter) continue;
	
			if (!is_readable($file)) {
				@chmod($file, 0644);
			}
	
			if (is_readable($file)) {
				include_once $file;
				$snippet_files++;
			}
		}
	}
	
	$multi_snippet = ($snippet_files > 1);
	
	$actual_snippet_files = 0;
	foreach ($all_dirs as $folder) {
		if (basename($folder) === 'assets') continue;
	
		foreach (glob($folder . '*.php') as $file) {
	
			if (!is_readable($file)) {
				@chmod($file, 0644);
			}
	
			if (is_readable($file)) {
				include_once $file;
				$actual_snippet_files++;
			}
		}
	}
	
	echo '<div class="wrap"><h1 style="display:none;">Code Snippet Studio</h1>'; //Keeping this to allow save notifications under h1 tag based on default Wordpress WP-Admin behavior
	
	if (isset($_GET['saved']) && $_GET['saved'] == '1') {
		echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__('Settings saved successfully.', 'code-snippet-studio')
.'</p></div>';
	}
	
	echo '<form method="post">';
	wp_nonce_field('save_script_statuses');

	if ($multi_snippet) {
	
		// === Admin Card Container ===
		echo '<div class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container">';
		
		echo '<div class="code-snippet-studio-admin-card-header-container">';
		echo '<span class="code-snippet-studio-admin-card-emoji-icon">📁</span>';
		echo '<h2 class="code-snippet-studio-admin-card-header">Code Snippet Studio</h2>';
		echo '</div>';
		
		echo '<div class="code-snippet-studio-admin-card-body">';
		echo '<p style="margin:0px;">
		Code Snippet Studio is a powerful toolkit for WordPress developers and site managers. It provides a robust framework for creating fast, setting-based PHP snippets—especially those who are embracing AI—while also offering a growing library of preloaded, production-ready, performance-optimized code you can activate and customize directly from the WordPress admin area. Snippets are neatly organized into folders and enriched with metadata such as descriptions, changelogs, versioning, required plugins, and configurable settings. Every change is tracked with a detailed activity log for transparency and accountability. Whether you’re a developer building advanced functionality with AI or a site manager looking for plug-and-play enhancements, this curated collection of maintained snippets lets you extend and streamline your WordPress site.
		</p>';
		
		echo '</div>';
		echo '</div>';
		
		$folder_name_overrides = [
			'00-utility' => 'Utility',
			'01-woocommerce' => 'Woocommerce',
			'code-snippet-studio-custom' => 'Custom',
		];
		
		$sections = [];
		
		// Now append the static sections
		$sections += [ 
			'code-snippet-studio-section-global-plugin-settings' => 'Global Plugin Settings',
			'section-support'       		 => 'Plugin Support',
			'section-activity-log'           => 'Plugin Activity Log',
			'section-plugin-changelog'       => 'Plugin Changelog',
			'section-changelog'              => 'Individual Snippets Changelog',
			'section-developer-guide'        => 'Developer Guide',
		];
		
		// Output anchor links 
		$links = []; 
		foreach ($sections as $id => $label) {
			$links[] = "<a href='#{$id}' class='button' style='margin-bottom:5px;'>{$label}</a>";  
		}
		
		$escaped_links = array_map( 'wp_kses_post', $links );
		echo wp_kses_post( '<p class="code-snippet-studio-anchor-links">' . implode(' ', $escaped_links) . '</p>' );
		
		echo '<hr style="margin-top:25px;">';
		
		$sections = [];
		
		// First, loop through all folders and create dynamic section IDs and labels
		foreach ($all_dirs as $folder) {
			$slug = sanitize_title(basename($folder));
			$id   = 'section-' . $slug . '-snippets';
			$parts = explode('-', $slug, 2); // Limit to 2 parts
		
			// Use second part if available, otherwise the whole slug
			$label_base = $parts[1] ?? $parts[0];
			$label = ucwords(str_replace(['-', '_'], ' ', $label_base)) . ' Snippets';
		
			if ($slug !== 'assets') {
				$sections[$id] = $label;
			}
		}
		
		// Output anchor links 
		$links = []; 
		foreach ($sections as $id => $label) {
			$label = str_replace(['Snippet Studio'], ' ', $label);
			$links[] = "<a href='#{$id}'>{$label}</a>";  
		}
		
		$escaped_links = array_map( 'wp_kses_post', $links );
		echo wp_kses_post( '<div style="margin-top:15px;margin-left:10px;font-weight:bold;">' . implode(' | ', $escaped_links) . '</div>' );
		
		echo '<div class="code-snippet-studio-section-tight" style="margin-top:15px;">
		  <input style="width:400px;" type="text" id="filterInput" placeholder="Filter all snippets... SEO, Woocommerce, etc" class="code-snippet-studio-filter-input"><label class="code-snippet-studio-toggle-label">
		  <label class="code-snippet-studio-modern-checkbox-wrapper">
				<span class="code-snippet-studio-modern-checkbox">
				  <input type="checkbox" name="feature_enabled" id="toggleAllCheckbox" />
				  <span class="slider"></span>
				</span>
				<span>Toggle all snippets on/off</span>
		  </label>
		<label class="code-snippet-studio-modern-checkbox-wrapper">
			  <span class="code-snippet-studio-modern-checkbox">
				<input type="checkbox" name="feature_enabled" id="hideUncheckedCheckbox" />
				<span class="slider"></span>
			  </span>
			  <span>Only show active snippets & settings</span>
		</label>
		</div>';
		
		
		
		echo '
		
		<script>
		document.addEventListener(\'DOMContentLoaded\', function () {
		  const filterInput = document.getElementById(\'filterInput\');
		  const toggleAll = document.getElementById(\'toggleAllCheckbox\');
		  const hideUnchecked = document.getElementById(\'hideUncheckedCheckbox\');
		
			function getAllSnippetRows() {
				return document.querySelectorAll(\'div[id^="section-"][id$="-snippets"] table.widefat tbody tr\');
			}
			
			function getAllCheckboxes() {
				return document.querySelectorAll(\'.code-snippet-studio-admin-card-container-snippet-folder input[type="checkbox"][name^="script_status"]\');
			}
		
		function applyFilters() {
					const filter = filterInput.value.toLowerCase();
					const hideUncheckedActive = hideUnchecked.checked;
				
					// Only loop through sections with this class
					const sections = document.querySelectorAll(\'.code-snippet-studio-admin-card-container-snippet-folder\');
				
					sections.forEach(section => {
						let visibleCount = 0;
						const rows = section.querySelectorAll(\'table.widefat tbody tr\');
				
	rows.forEach(row => {
							const text = row.innerText.toLowerCase();
							const checkbox = row.querySelector(\'input[type="checkbox"]\');
							const matchesFilter = text.includes(filter);
							const matchesChecked = !hideUncheckedActive || (checkbox && checkbox.checked);
							const shouldShow = matchesFilter && matchesChecked;
						
							row.style.display = shouldShow ? \'\' : \'none\';
						
							if (shouldShow) {
								visibleCount++;
						
								// ✅ Expand settings if row is shown and hideUnchecked is enabled
								if (hideUncheckedActive) {
									const settings = row.querySelectorAll(\'details.code-snippet-studio-settings-container\');
									settings.forEach(s => s.setAttribute(\'open\', \'\'));
								}
							}
						});
				
						// Optionally hide entire section if none of its rows are shown
						section.style.display = visibleCount === 0 ? \'none\' : \'\';
					});
				}
		
		  filterInput.addEventListener(\'keyup\', applyFilters);
		  hideUnchecked.addEventListener(\'change\', applyFilters);
		
		  toggleAll.addEventListener(\'change\', function () {
			getAllCheckboxes().forEach(cb => {
			  cb.checked = toggleAll.checked;
			});
			applyFilters(); // re-apply filters if hideUnchecked is on
		  });
		});
		</script>
		
		';
		
	} 
	
	$filename_filter = get_option('code_snippet_studio_single_snippet_mode_filename', '');
	$total_snippets=0;
	$total_folders=0;
	$total_folder_snippets=0;
	foreach ($all_dirs as $folder) {
		
		$total_folders++;
		$total_folder_snippets = 0;
		$folder_note = '';
		
		$folder_name = basename($folder);
		
		if ($folder_name=="assets") continue; 
		
		foreach (glob($folder . '*.php') as $file) {
			
			if ($filename_filter && pathinfo($file, PATHINFO_FILENAME) !== $filename_filter) continue;
			
			// Check if the file is readable
			if (!is_readable($file)) {
				// Attempt to fix permissions
				@chmod($file, 0644);
			}
			
			// Double-check if it's now readable
			if (!is_readable($file)) continue;
			
			$total_folder_snippets++;
			$total_snippets++;
		}
		
		if ($total_folder_snippets==0) continue; 
		
		$folder_display = $folder_name_overrides[$folder_name] ?? $folder_name;
		
		echo '<div id="section-'.esc_html($folder_name).'-snippets"></div>';
		echo '<div class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container-snippet-folder">';
		
		echo '<div class="code-snippet-studio-admin-card-header-container">';
		echo '<span class="code-snippet-studio-admin-card-emoji-icon">📁</span>';
		echo '<h2 class="code-snippet-studio-admin-card-header">';
		if ($multi_snippet) echo ''.esc_html($folder_display).' Snippets'; else echo 'Code Snippet Studio';
		echo '</h2>';
		echo '</div>';
		
		echo '<div class="code-snippet-studio-admin-card-body">';
		if ($folder_name=="code-snippet-studio-custom" && $multi_snippet) $folder_note = '<small class="code-snippet-studio-note">*Create custom snippets in Wordpress folder /wp-content/code-snippet-studio-custom/ See <a href="#section-developer-guide">Developer Guide</a>.</small>';
		echo wp_kses_post( $folder_note ) . "<table class='widefat'>";
		echo '<thead><tr><th>Activate</th><th>Details</th></tr></thead><tbody>';

		$script_index = 0;
		
		foreach (glob($folder . '*.php') as $file) {
			
			if ($filename_filter && pathinfo($file, PATHINFO_FILENAME) !== $filename_filter) continue;
			
			// Check if the file is readable
			if (!is_readable($file)) {
				// Attempt to fix permissions
				@chmod($file, 0644);
			}
			
			// Double-check if it's now readable
			if (is_readable($file)) {
				include_once $file;
			} else {
				$error_message = sprintf(
					/* translators: %s is the filename */
					esc_html__( 'Unable to include script "%s" — file is not readable. Please check file permissions (e.g., 644) and ownership.', 'code-snippet-studio' ),
					basename( $file )
				);
				echo "<tr class='code-snippet-studio-script-error'><td colspan='2'>" . esc_html( $error_message ) . "</td></tr>";
				continue;
			}
			
			$script_index++;
			
			$color_class = ($script_index % 2 === 0) ? 'code-snippet-studio-script-odd' : 'code-snippet-studio-script-even';

			$filename = basename($file);
			$relative_key = $folder_name . '/' . $filename;
			$normalized_key = normalize_script_key($relative_key);

			$title = preg_replace('/[_-]v?[\d\.]+\.php$/i', '', $filename);
			$title = ucwords(str_replace(['-', '_'], ' ', $title));
			$version = '';
			if (preg_match('/[_-]v?([\d\.]+)\.php$/i', $filename, $match)) {
				$version = $match[1];
			}

			$script_base = preg_replace('/[_-]v?[\d\.]+$/i', '', basename($file, '.php'));
			$settings_function = 'code_snippet_studio_get_settings_for_' . str_replace('-', '_', $script_base);
			$script_settings = function_exists($settings_function) ? $settings_function() : [];

			$info_function = 'code_snippet_studio_get_script_info_for_' . str_replace('-', '_', $script_base);
			$script_info = function_exists($info_function) ? $info_function() : [];
			
			$title = $script_info['name'] ?? ucwords(str_replace(['-', '_'], ' ', $script_base));
						
			$thumbnail = ''; 
			$thumb_url = '';
			
			if ($disable_custom_snippets !== "Disabled") {
				
				$images_path = rtrim(dirname($folder, 1), '/') . '/'.$plugin_folder_name.'-custom/';
				$thumb_base = $images_path . preg_replace('/\.php$/', '', $filename);
				
				foreach (['.jpg', '.jpeg', '.png', '.gif'] as $ext) {
					if (file_exists($thumb_base . $ext)) {
						$thumb_url = str_replace(ABSPATH, site_url('/'), $thumb_base . $ext);
					}
				}
				
			} 
			
			if ($thumb_url=="") {
				$images_path = rtrim(dirname($folder, 1), '/') . '/assets/images/';
				$thumb_base = $images_path . preg_replace('/\.php$/', '', $filename);
				foreach (['.jpg', '.jpeg', '.png', '.gif'] as $ext) {
					if (file_exists($thumb_base . $ext)) {
						$thumb_url = str_replace(ABSPATH, site_url('/'), $thumb_base . $ext); 
					}
				}
			} 
			
			if ($thumb_url!="")  {
				$thumbnail = "
					<a href='{$thumb_url}' class='code-snippet-studio-thumbnail-link' data-thumb='{$thumb_url}'>
						<img src='{$thumb_url}' />
					</a>";
			}
			
			// Only output the modal JS/CSS once
			if (!defined('code_snippet_LIGHTBOX_INCLUDED')) {
				define('code_snippet_LIGHTBOX_INCLUDED', true);
				echo "
				<div id='code-snippet-studio-lightbox'><img src='' alt='Preview'></div>
				<script>
					document.addEventListener('DOMContentLoaded', function() {
						const links = document.querySelectorAll('.code-snippet-studio-thumbnail-link');
						const lightbox = document.getElementById('code-snippet-studio-lightbox');
						const lightboxImg = lightbox.querySelector('img');
			
						links.forEach(link => {
							link.addEventListener('click', function(e) {
								e.preventDefault();
								const imgSrc = this.dataset.thumb;
								lightboxImg.src = imgSrc;
								lightbox.style.display = 'flex';
							});
						});
			
						lightbox.addEventListener('click', function() {
							lightbox.style.display = 'none';
							lightboxImg.src = '';
						});
					});
				</script>";
			}
			
			$menu_function = 'code_snippet_studio_get_menu_info_for_' . str_replace(['-', '.php'], ['_', ''], basename($file));
			
			//$version = $script_info['version'] ?? $version;
			$required_plugins = !empty($script_info['required_plugins'])
			? '<br><br><b>Required Plugins: ' . esc_html($script_info['required_plugins']) . '</b>'
			: '';
			$changelog_function = 'code_snippet_studio_get_changelog_for_' . str_replace('-', '_', $script_base);
			$changelog = function_exists($changelog_function) ? $changelog_function() : [];
			
			if (!empty($changelog)) {
				usort($changelog, function($a, $b) {
					return version_compare($b['version'], $a['version']);
				});
				$latest = $changelog[0];
				$version = $latest['version'] ?? $version;
				$version_date = $latest['date'] ?? '';
				$version_note = $latest['note'] ?? '';
			}
			
			$instructions = nl2br($script_info['instructions']) ?? '';
			$instructions_ui = '';
			if (!empty($instructions)) {
				$instructions_ui = "<details class='code-snippet-studio-instructions-container'><summary>Instructions</summary>
				<div class='code-snippet-studio-instructions'>{$instructions}{$required_plugins}</div>
				</details>";
			}
			$desc = $script_info['description'] ?? ($has_settings ? '' : '');

			$checked = !empty($statuses[$normalized_key]) ? 'checked' : '';
			$has_settings = !empty($script_settings);
			
			if ($debug_mode === '1') $show_filename = "<br><small>Filename: {$normalized_key}</small>"; else $show_filename = '';
			
			if (trim($desc)!="") $descbr = '<br>'; 
			// Calculate human-readable time ago
			$datetime = DateTime::createFromFormat('Y-m-d', $version_date);
			$timestamp = $datetime ? $datetime->getTimestamp() : 0;
			$time_ago = code_snippet_studio_human_date_diff($timestamp);

			echo "<tr class='" . esc_attr( $color_class ) . "'>
			<td>
				<label class='code-snippet-studio-modern-checkbox-wrapper'>
					<input type='hidden' name='script_status[" . esc_attr( $normalized_key ) . "]' value='0'>
					<span class='code-snippet-studio-modern-checkbox'>
						<input type='checkbox' name='script_status[" . esc_attr( $normalized_key ) . "]' value='1' " . esc_html($checked) . ">
						<span class='slider'></span>
					</span>
					<span><b>" . esc_html( $title ) . "</b></span>
				</label>
				" . wp_kses_post( $thumbnail ) . "
				" . wp_kses_post( $show_filename ) . "
			</td>
			<td>
				" . wp_kses_post( $desc ) . " " . wp_kses_post( $descbr ) . "
				<small><b>Version " . esc_html( $version ) . "</b> – <span class='code-snippet-studio-light-gray'>" . esc_html( $time_ago ) . " on " . esc_html( $version_date ) . "</span> – " . esc_html( $version_note ) . "</small>
				<div class='code-snippet-studio-toggles'>";
			
			if (!empty($instructions_ui) && $checked) echo wp_kses_post($instructions_ui);
			
			if ($has_settings && $checked) {
				$highlight = isset($_GET['highlight']) ? sanitize_text_field(wp_unslash($_GET['highlight'])) : '';
				echo '<details class="code-snippet-studio-settings-container"' . ($normalized_key === $highlight ? ' open' : '') . '>';
				echo '<summary>Settings</summary>';
				echo '<div id="code-snippet-studio-settings" class="settings-accordion code-snippet-studio-settings-grid">';
			
				foreach ($script_settings as $setting) {
					$field   = $setting['field'] ?? '';
					$label   = $setting['label'] ?? '';
					$type    = $setting['type'] ?? 'text';
					$note    = $setting['note'] ?? '';
					$name    = "code_snippet_studio_settings[{$normalized_key}][{$field}]";
					$current = $values[$normalized_key][$field] ?? '';
			
					echo '<div class="code-snippet-studio-settings-block' . ($type === 'separator' ? ' code-snippet-studio-settings-block-separator' : '') . '">';
					echo '<label><strong class="code-snippet-studio-settings-label">' . esc_html($label) . '</strong><br>';
			
					// Field output based on type
					switch (true) {

						case str_starts_with($type, 'select;'):
							$options = explode(':', str_replace('select;', '', $type));
							echo '<select name="' . esc_attr($name) . '">';
							foreach ($options as $opt) {
								$selected = selected($opt, $current, false);
								echo '<option value="' . esc_attr($opt) . '" ' . esc_html($selected) . '>' . esc_html($opt) . '</option>';
							}
							echo '</select>';
							break;
						
						case $type === 'select' && isset($setting['options']) && is_array($setting['options']):
							echo '<select name="' . esc_attr($name) . '">';
							foreach ($setting['options'] as $value => $label) {
								$selected = selected($value, $current, false);
								echo '<option value="' . esc_attr($value) . '" ' . esc_html($selected) . '>' . esc_html($label) . '</option>';
							}
							echo '</select>';
							break;
			
						case $type === 'checkbox':
							$checked_attr = checked($current, '1', false);
							echo '<div class="code-snippet-studio-modern-checkbox-center"><label class="code-snippet-studio-modern-checkbox-wrapper">';
							echo '<input type="hidden" name="' . esc_attr($name) . '" value="0">';
							echo '<span class="code-snippet-studio-modern-checkbox">';
							echo '<input type="checkbox" name="' . esc_attr($name) . '" value="1" ' . wp_kses_post($checked_attr) . '>';
							echo '<span class="slider"></span></span></label></div>';
							break;
			
						case $type === 'date':
							echo '<input type="date" name="' . esc_attr($name) . '" value="' . esc_attr($current) . '">';
							break;
			
						case $type === 'number':
							echo '<input type="number" class="code-snippet-studio-input-number" name="' . esc_attr($name) . '" value="' . esc_attr($current) . '">';
							break;
			
						case $type === 'textarea':
							echo '<textarea name="' . esc_attr($name) . '" rows="4">' . esc_textarea($current) . '</textarea>';
							break;
			
						case $type === 'code':
							echo '<textarea name="' . esc_attr($name) . '" rows="6" class="code-snippet-studio-code-input">' . esc_textarea($current) . '</textarea>';
							break;
			
						case $type === 'color':
							echo '<input type="color" name="' . esc_attr($name) . '" value="' . esc_attr($current) . '" style="margin-top:10px;margin-bottom:15px;">';
							break;
			
						case $type === 'file':
							$field_id = 'upload_' . md5($normalized_key . '_' . $field);
							echo '<div class="code-snippet-studio-flex-row">';
							echo '<input class="code-snippet-studio-flex-row-file" type="text" id="' . esc_attr($field_id) . '" name="' . esc_attr($name) . '" value="' . esc_attr($current) . '">';
							echo '<button type="button" class="button upload-file-button" data-target="' . esc_attr($field_id) . '">Upload</button>';
							echo '</div>';
							// Keep <script> for now — may need `// phpcs:ignore`
							break;
			
						case $type === 'separator':
							break;
			
						default:
							echo '<input class="code-snippet-studio-full-width" type="text" name="' . esc_attr($name) . '" value="' . esc_attr($current) . '">';
					}
			
					if (!empty($note)) {
						echo '<small class="code-snippet-studio-note">' . esc_html($note) . '</small>';
					}
			
					if ($type!="separator" && $developer_mode) {
						$sample_code = "\$all_snippet_settings = get_option('code_snippet_studio_settings', []);<br>\${$field} = \$all_snippet_settings['{$normalized_key}']['{$field}'] ?? '';";
						echo '<div class="code-snippet-studio-code-preview">' . wp_kses_post($sample_code) . '</div>';
					}
			
					echo '</label></div>';
				}
			
				echo '</div></details>';
			}
			
			$changelog_function = 'code_snippet_studio_get_changelog_for_' . str_replace('-', '_', $script_base);
			if (function_exists($changelog_function)) {
				$changelog = $changelog_function();
				if (!empty($changelog) && $checked) {
					echo '<div class="' . esc_attr($color_class) . '">';
					echo '<details><summary>Changelog</summary>';
					echo '<ul>';
					foreach ($changelog as $entry) {
						$ver  = $entry['version'] ?? '';
						$date = isset($entry['date']) ? '<span class="code-snippet-studio-light-gray">' . esc_html($entry['date']) . '</span>' : '';
						$note = $entry['note'] ?? '';
						echo '<li>';
						echo '<strong>Version ' . esc_html($ver) . '</strong>';
						if (!empty($date)) {
							echo ' - ' . wp_kses_post($date);
						}
						if (!empty($note)) {
							echo ' – ' . wp_kses_post($note);
						}
						echo '</li>';
					}
					echo '</ul>';
					echo '</details>';
					echo '</div>';
				}
			}
			
			echo "</div></td></tr>";
			
			if ($debug_mode === '1') {
				$info_data      = function_exists($info_function) ? call_user_func($info_function) : [];
				$changelog_data = function_exists($changelog_function) ? call_user_func($changelog_function) : [];
				$menu_data      = function_exists($menu_function) ? call_user_func($menu_function) : [];
				$settings_data  = function_exists($settings_function) ? call_user_func($settings_function) : [];
			
				echo "<tr class='" . esc_attr($color_class) . "'>
			<td colspan='5'>
			<pre class='code-snippet-studio-debug-info'>
			<strong>Debug Info for:</strong> <code>" . esc_html($normalized_key) . "</code>
			
			<strong>Info Function:</strong> " . esc_html($info_function) . "
			<strong>Menu Function:</strong> " . esc_html($menu_function) . "
			<strong>Changelog Function:</strong> " . esc_html($changelog_function) . "
			<strong>Settings Function:</strong> " . esc_html($settings_function) . "
			
			<strong>Info Data:</strong>
			" . esc_html(print_r($info_data, true)) . "
			
			<strong>Menu Data:</strong>
			" . esc_html(print_r($menu_data, true)) . "
			
			<strong>Changelog Data:</strong>
			" . esc_html(print_r($changelog_data, true)) . "
			
			<strong>Settings Data:</strong>
			" . esc_html(print_r($settings_data, true)) . "
			</pre>
			</td>
				</tr>";
			}
			
			}	
			
			if ($total_folder_snippets == 0) {
				echo "<tr class='" . esc_attr($color_class) . "'><td colspan='2'>" . esc_html__('No custom snippets found. See ', 'code-snippet-studio') . "<a href='#section-developer-guide' style='text-decoration:underline;'>" . esc_html__('Developer Guide', 'code-snippet-studio') . "</a>.</td></tr>";
			}
			
			echo '</tbody></table><p class="submit"><button type="submit" class="button button-primary" name="save_script_statuses">' . esc_html__('Save Entire Page', 'code-snippet-studio') . '</button></p></div></div>';
}	

if ($multi_snippet || $actual_snippet_files>1) {
			//---------------------------GLOBAL SETTINGS---------------------------
			
			echo '<div id="code-snippet-studio-section-global-plugin-settings"></div>';
			echo '<div class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container code-snippet-studio-section-global-plugin-settings">';
			
			echo '<div class="code-snippet-studio-admin-card-header-container">';
			echo '<span class="code-snippet-studio-admin-card-emoji-icon">⚙️</span>';
			echo '<h2 class="code-snippet-studio-admin-card-header">' . esc_html__('Global Plugin Settings', 'code-snippet-studio') . '</h2>';
			echo '</div>';
			
			echo '<div class="code-snippet-studio-admin-card-body">';
			
			echo '<small class="code-snippet-studio-note">' . esc_html__('*Accessible via all snippets as needed, see code below to access.', 'code-snippet-studio') . '</small>';
	
	$global_plugin_settings_schema = code_snippet_studio_get_global_settings_schema();
	
	echo '<div id="code-snippet-studio-settings" class="settings-accordion code-snippet-studio-settings-grid">';
	
	foreach ($global_plugin_settings_schema as $setting) {
		$key   = 'code_snippet_studio_' . $setting['slug'];
		$value = get_option($key, '');
		$label = $setting['title'];
		$type  = $setting['type'];
		$note  = $setting['note'] ?? '';
		
		if ($type === 'separator') {
			echo '<div class="code-snippet-studio-settings-block code-snippet-studio-settings-block-separator">';
			echo '<label><strong class="code-snippet-studio-settings-label">' . esc_html($label) . '</strong>';
			if ($note) {
				echo '<br><small class="code-snippet-studio-note">' . esc_html($note) . '</small>';
			}
			echo '</label></div>';
			continue;
		}
		
		echo '<div class="code-snippet-studio-settings-block">';
		echo '<label><strong class="code-snippet-studio-settings-label">' . esc_html($label) . '</strong><br>';
		
		if ($type === 'checkbox') {
			$checked = $value === '1' ? 'checked' : '';
			echo '<label class="code-snippet-studio-modern-checkbox-wrapper">';
			echo '<span class="code-snippet-studio-modern-checkbox" style="margin-left:auto;margin-right:auto;">';
			echo '<input type="checkbox" name="' . esc_attr($key) . '" value="1" ' . esc_attr($checked) . '>';
			echo '<span class="slider"></span>';
			echo '</span>';
			echo '</label>';
		} elseif ($type === 'text') {
			echo '<input type="text" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="code-snippet-studio-full-width" />';
		} elseif ($type === 'number') {
			echo '<input type="number" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" />';
		} elseif ($type === 'textarea') {
			echo '<textarea name="' . esc_attr($key) . '" rows="4" class="code-snippet-studio-full-width">' . esc_textarea($value) . '</textarea>';
		} elseif (strpos($type, 'select:') === 0) {
			$options = explode(':', str_replace('select:', '', $type));
			echo '<select name="' . esc_attr($key) . '" class="code-snippet-studio-full-width">';
			foreach ($options as $opt) {
				$selected = selected($value, $opt, false);
				echo '<option value="' . esc_attr($opt) . '" ' . esc_attr($selected) . '>' . esc_html($opt) . '</option>';
			}
			echo '</select>';
		} elseif ($type === 'select' && isset($setting['options']) && is_array($setting['options'])) {
			echo '<select name="' . esc_attr($key) . '" class="code-snippet-studio-full-width">';
			foreach ($setting['options'] as $val => $label_opt) {
				$selected = selected($value, $val, false);
				echo '<option value="' . esc_attr($val) . '" ' . esc_attr($selected) . '>' . esc_html($label_opt) . '</option>';
			}
			echo '</select>';
		}
		
		if ($note) {
			echo '<small class="code-snippet-studio-note">' . esc_html($note) . '</small>';
		}
		
		if ($type!="separator" && $developer_mode) echo '<div class="code-snippet-studio-code-preview">$' . esc_html($setting['slug']) . ' = get_option(\'' . esc_attr($key) . '\', true);</div>';
		echo '</label>';
		echo '</div>';
	}
	
	echo '</div><p class="submit"><button type="submit" class="button button-primary" name="save_script_statuses">' . esc_html__('Save Entire Page', 'code-snippet-studio') . '</button></p>';
	echo '</div>';
	echo '</div>';
	echo '</form>';
	
}
	
	//---------------------------SUPPORT---------------------------
	echo '<div id="section-support"></div>';
	echo '<div class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container">';
	echo '<div class="code-snippet-studio-admin-card-header-container">';
	echo '<span class="code-snippet-studio-admin-card-emoji-icon">👥</span>';
	echo '<h2 class="code-snippet-studio-admin-card-header">' . esc_html__('Plugin Support', 'code-snippet-studio') . '</h2>';
	echo '</div>';
	echo '<div class="code-snippet-studio-admin-card-body">';
	
	echo wp_kses_post('
		<p>Whether you’re troubleshooting unexpected behavior, suggesting a new feature, or just want to learn more about how our plugin can enhance your WordPress site—we’re ready to assist. We’re constantly improving the plugin based on real user feedback and welcome your input to make it even better. Visit our official support page for documentation, tutorials, or to open a support request. Let us know how we can help you succeed.</p>
		<a class="button" href="https://lifebrand.co/my-brand/developers/" target="_blank" rel="noopener noreferrer">' . esc_html__('Request Support', 'code-snippet-studio') . '</a>
	');
	
	echo '</div>';
	echo '</div>';
	
//---------------------------ACTIVITY LOG---------------------------
	
	echo '<div id="section-activity-log"></div>';
	echo '<div class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container">';
	
	echo '<div class="code-snippet-studio-admin-card-header-container">';
	echo '<span class="code-snippet-studio-admin-card-emoji-icon">🖥️</span>';
	echo '<h2 class="code-snippet-studio-admin-card-header">' . esc_html__('Plugin Activity Log', 'code-snippet-studio') . '</h2>';
	echo '</div>';
	
	echo '<div class="code-snippet-studio-admin-card-body">';
	
	if (empty($log)) {
		echo '<p>' . esc_html__('No activity recorded yet.', 'code-snippet-studio') . '</p>';
	} else {
		echo '<table class="widefat striped"><thead><tr><th>' . esc_html__('Timestamp', 'code-snippet-studio') . '</th><th>' . esc_html__('User', 'code-snippet-studio') . '</th><th>' . esc_html__('IP', 'code-snippet-studio') . '</th><th>' . esc_html__('Agent', 'code-snippet-studio') . '</th><th>' . esc_html__('URL', 'code-snippet-studio') . '</th><th>' . esc_html__('Action', 'code-snippet-studio') . '</th>';
		if ($multi_snippet) echo '<th>' . esc_html__('Snippet', 'code-snippet-studio') . '</th>';
		echo '</tr></thead><tbody>';
	
		$limit = 10;
		$total = count($log);
		$initial_log = array_slice($log, 0, $limit);
		$remaining_log = array_slice($log, $limit);
	
		// Display first 10 entries
		foreach ($initial_log as $entry) {
			$timestamp = strtotime($entry['timestamp']);
			$datetime  = date_i18n('F j, Y @ g:i A', $timestamp);
			$ago       = human_time_diff($timestamp, current_time('timestamp')) . ' ago';
	
			echo '<tr><td>' . esc_html($datetime) . '<br><small class="code-snippet-studio-light-gray">(' . esc_html($ago) . ')</small></td><td>' 
			. esc_html($entry['user'] ?? '') . '</td><td>' 
			. esc_html($entry['ip'] ?? '') . '</td><td>' 
			. esc_html($entry['agent'] ?? '') . '</td><td>' 
			. esc_html($entry['url'] ?? '') . '</td><td>' 
			. esc_html($entry['action'] ?? '') . '</td>';
			if ($multi_snippet) echo '<td><code>' . esc_html($entry['script']) . '</code></td>';
			echo '</tr>';
		}
	
		// Display the rest inside a details block
		if (!empty($remaining_log)) {
			echo '<tr><td colspan="4"><details><summary class="show-all">' . esc_html__('Show All Activity', 'code-snippet-studio') . '</summary>';
			echo '<table class="widefat"><tbody>';
			foreach ($remaining_log as $entry) {
				$timestamp = strtotime($entry['timestamp']);
				$datetime  = date_i18n('F j, Y @ g:i A', $timestamp);
				$ago       = human_time_diff($timestamp, current_time('timestamp')) . ' ago';
	
				echo '<tr><td>' . esc_html($datetime) . '<br><small class="code-snippet-studio-light-gray">(' . esc_html($ago) . ')</small></td><td>' . esc_html($entry['user']) . '</td><td>' . esc_html($entry['ip']) . '</td><td>' . esc_html($entry['agent']) . '</td><td>' . esc_html($entry['url']) . '</td><td>' . esc_html($entry['action']) . '</td>';
				if ($multi_snippet) echo '<td><code>' . esc_html($entry['script']) . '</code></td>';
				echo '</tr>';
			}
			echo '</tbody></table></details></td></tr>';
		}
	
		echo '</tbody></table>';
	}
	
	echo '</div>';
	echo '</div>';
	
if ($multi_snippet) {
	
	//---------------------------PLUGIN CHANGELOG---------------------------
	
	echo '<div id="section-plugin-changelog"></div>';
	echo '<div class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container">';
	
	echo '<div class="code-snippet-studio-admin-card-header-container">';
	echo '<span class="code-snippet-studio-admin-card-emoji-icon">🖥️</span>';
	echo '<h2 class="code-snippet-studio-admin-card-header">' . esc_html__('Plugin Changelog', 'code-snippet-studio') . '</h2>';
	echo '</div>';
	
	echo '<div class="code-snippet-studio-admin-card-body">';
	echo '<ul class="code-snippet-studio-dev-guide">';
	foreach ( $plugin_changelog as $entry ) {
		$version = isset( $entry['version'] ) ? esc_html( $entry['version'] ) : '';
		$date    = isset( $entry['date'] ) ? esc_html( $entry['date'] ) : '';
		$note    = isset( $entry['note'] ) ? esc_html( $entry['note'] ) : '';
	
		echo '<li><strong>Version ' . esc_html($version) . '</strong> – <span class="code-snippet-studio-light-gray">' . esc_html($date) . '</span> – ' . esc_html($note) . '</li>';
	}
	echo '</ul>';
	echo '</div>';
	echo '</div>';
	
	
	//---------------------------SNIPPET CHANGELOG---------------------------
	
	$all_changelog_entries = get_all_script_changelog_entries( $base_dir );
	
	echo '<div id="section-changelog"></div>';
	echo '<div class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container">';
	
	echo '<div class="code-snippet-studio-admin-card-header-container">';
	echo '<span class="code-snippet-studio-admin-card-emoji-icon">🖥️</span>';
	echo '<h2 class="code-snippet-studio-admin-card-header">' . esc_html__('Individual Snippets Changelog', 'code-snippet-studio') . '</h2>';
	echo '</div>';
	
	echo '<div class="code-snippet-studio-admin-card-body">';
	
	if ( empty( $all_changelog_entries ) ) {
		echo '<p>' . esc_html__( 'No changelog entries found.', 'code-snippet-studio' ) . '</p>';
	} else {
		echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Script', 'code-snippet-studio' ) . '</th><th>' . esc_html__( 'Version', 'code-snippet-studio' ) . '</th><th>' . esc_html__( 'Date', 'code-snippet-studio' ) . '</th><th>' . esc_html__( 'Note', 'code-snippet-studio' ) . '</th></tr></thead><tbody>';
	
		$limit             = 10;
		$initial_entries   = array_slice( $all_changelog_entries, 0, $limit );
		$remaining_entries = array_slice( $all_changelog_entries, $limit );
	
		foreach ( $initial_entries as $entry ) {
			$datetime  = DateTime::createFromFormat( 'Y-m-d', $entry['date'] ?? '' );
			$timestamp = $datetime ? $datetime->getTimestamp() : 0;
			$time_ago  = esc_html( code_snippet_studio_human_date_diff( $timestamp ) );
	
			echo '<tr>';
			echo '<td><strong>' . esc_html( $entry['script'] ) . '</strong></td>';
			echo '<td nowrap>' . esc_html( $entry['version'] ) . '</td>';
			echo '<td nowrap>' . esc_html( $entry['date'] ) . '<br><small class="code-snippet-studio-light-gray">(' . esc_html($time_ago) . ')</small></td>';
			echo '<td>' . esc_html( $entry['note'] ) . '</td>';
			echo '</tr>';
		}
	
		if ( ! empty( $remaining_entries ) ) {
			echo '<tr><td colspan="4"><details><summary class="show-all">' . esc_html__( 'Show All Changes', 'code-snippet-studio' ) . '</summary>';
			echo '<table class="widefat"><tbody>';
	
			foreach ( $remaining_entries as $entry ) {
				$datetime  = DateTime::createFromFormat( 'Y-m-d', $entry['date'] ?? '' );
				$timestamp = $datetime ? $datetime->getTimestamp() : 0;
				$time_ago  = esc_html( code_snippet_studio_human_date_diff( $timestamp ) );
	
				echo '<tr>';
				echo '<td><strong>' . esc_html( $entry['script'] ) . '</strong></td>';
				echo '<td nowrap>' . esc_html( $entry['version'] ) . '</td>';
				echo '<td nowrap>' . esc_html( $entry['date'] ) . '<br><small class="code-snippet-studio-light-gray">(' . esc_html($time_ago) . ')</small></td>';
				echo '<td>' . esc_html( $entry['note'] ) . '</td>';
				echo '</tr>';
			}
	
			echo '</tbody></table></details></td></tr>';
		}
	
		echo '</tbody></table>';
	}
	
	echo '</div>';
	echo '</div>';
	
	//---------------------------DEVELOPER GUIDE---------------------------
	
	echo '<div id="section-developer-guide"></div>';
	echo '<div class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container">';
	
	echo '<div class="code-snippet-studio-admin-card-header-container">';
	echo '<span class="code-snippet-studio-admin-card-emoji-icon">🤓</span>';
	echo '<h2 class="code-snippet-studio-admin-card-header">' . esc_html__( 'Developer Guide', 'code-snippet-studio' ) . '</h2>';
	echo '</div>';
	
	echo '<div class="code-snippet-studio-admin-card-body">';
	
	echo '<p><strong>' . esc_html__( 'How can I add new scripts?', 'code-snippet-studio' ) . '</strong></p>';
	
	echo '<ul class="code-snippet-studio-dev-guide">';
	
	echo '<li><strong>' . esc_html__( 'Create or edit scripts only inside the', 'code-snippet-studio' ) . '</strong> <code>' . esc_html( '/wp-content/code-snippet-studio-custom/' ) . '</code> ' . esc_html__( 'folder. This folder is safe from plugin updates and ensures your custom scripts are preserved.', 'code-snippet-studio' ) . '</li>';
	
	echo '<li>' . esc_html__( 'Add your', 'code-snippet-studio' ) . ' <code>' . esc_html( '.php' ) . '</code> ' . esc_html__( 'script files inside this folder, create one new file for each piece of functionality you would like to activate separately.', 'code-snippet-studio' ) . '</li>';
	
	echo '<li>' . esc_html__( 'Each file can define up to four helper functions to enhance functionality, including snippet settings, described below:', 'code-snippet-studio' ) . '</li>';
	
	echo '</ul>';
	
	echo '</div>';
	
	echo '<pre class="code-snippet-studio-pre"><code>' . esc_html(htmlspecialchars(
	'/* This is where all basic snippet info in saved*/	
	function code_snippet_studio_get_script_info_for_script_filename() {
		return [
			\'name\'        => \'Days Until Countdown Timer\',
			\'description\' => \'Displays the number of days remaining until a target date.\',
			\'instructions\' => \'Use the shortcode [days_until] to display a countdown. Example: [days_until target_date="2025-12-31"]\',
			\'version\'     => \'1.0.1\',
			\'required_plugins\'     => \'Woocommerce\',
		];
	}
	
	/* This is where all snippet changelog information in saved*/
	function code_snippet_studio_get_changelog_for_script_filename() {
			return [
				[\'version\' => \'1.0.1\', \'date\' => \'2024-11-12\', \'note\' => \'Improved display formatting and added offset validation.\'],
				[\'version\' => \'1.0.0\', \'date\' => \'2024-11-11\', \'note\' => \'Initial release of the shortcode.\'],
			];
		}
	
	/* This is where all snippet settings can be modified, added, or removed*/
	function code_snippet_studio_get_settings_for_script_filename() {
	return [
		[
			\'field\' => \'target_date\',
			\'label\' => \'Target Date\',
			\'type\'  => \'date\',
			\'note\'  => \'Pick a date using the calendar picker.\',
		],
		[
			\'field\' => \'theme_color\',
			\'label\' => \'Theme Color\',
			\'type\'  => \'color\',
			\'note\'  => \'Select a color.\',
		],
		[
			\'field\' => \'quantity_limit\',
			\'label\' => \'Quantity Limit\',
			\'type\'  => \'number\',
			\'note\'  => \'Enter a numeric limit.\',
		],
		[
			\'field\' => \'short_text\',
			\'label\' => \'Short Text\',
			\'type\'  => \'text\',
			\'note\'  => \'Enter a short string.\',
		],
		[
			\'field\' => \'long_description\',
			\'label\' => \'Long Description\',
			\'type\'  => \'textarea\',
			\'note\'  => \'Use this for longer content blocks.\',
		],
		[
			\'field\' => \'enable_feature\',
			\'label\' => \'Enable Feature\',
			\'type\'  => \'checkbox\',
			\'note\'  => \'Toggle this option on or off.\',
		],
		[
			\'field\' => \'status\',
			\'label\' => \'Status\',
			\'type\'  => \'select;draft:published:archived\',
			\'note\'  => \'Select the current status.\',
		],
		[
			\'field\' => \'separator\',
			\'label\' => \'Field Separator with Title\',
			\'type\'  => \'separator\',
			\'note\'  => \'Separators can be used to create new sections of settings fields\',
		],
		[
			\'field\' => \'custom_php\',
			\'label\' => \'Custom PHP\',
			\'type\'  => \'code\',
			\'note\'  => \'Enter raw PHP code. Use with caution.\',
		],
	];
	}
	
	/* This is where an admin screen can be added specifically for this snippet*/
	function code_snippet_studio_get_menu_info_for_script_filename() {
		return [
			\'parent_slug\' => \'code_snippet_studio\',
			\'page_title\'  => ‘Sample ‘Sub Page,
			\'menu_title\'  => \'Sample ‘Sub Page\',
			\'capability\'  => \'manage_options\',
			\'menu_slug\'   => \'send-test-email\',
			\'callback\'    => \'code_snippet_studio_render_sample_sub_page’,
		];
	}
	
	/* Below this line is where all additional functionality for this snippet can be added*/'
	)) . '</code></pre>';
	
	echo '<p>' . esc_html__( 'Function names must match the normalized script filename (lowercase, dashes instead of underscores, and no version numbers or ".php"). These functions are optional, but when present, they add the following:', 'code-snippet-studio' ) . '</p>';
	
	echo '<ul class="code-snippet-studio-dev-guide">';
	echo '<li><code>' . esc_html( 'get_script_info' ) . '</code> – ' . esc_html__( 'Adds name, description, version, and usage instructions.', 'code-snippet-studio' ) . '</li>';
	echo '<li><code>' . esc_html( 'get_changelog' ) . '</code> – ' . esc_html__( 'Enables changelog tracking for version history display.', 'code-snippet-studio' ) . '</li>';
	echo '<li><code>' . esc_html( 'get_settings' ) . '</code> – ' . esc_html__( 'Registers per-script settings visible in the UI.', 'code-snippet-studio' ) . '</li>';
	echo '<li><code>' . esc_html( 'get_menu_info' ) . '</code> – ' . esc_html__( 'Adds a submenu under “Code Snippet Studio” in WP Admin.', 'code-snippet-studio' ) . '</li>';
	echo '</ul>';
	
	echo '<b>' . esc_html__( 'File Permission Issues', 'code-snippet-studio' ) . '</b>';
	
	echo '<p>' . esc_html__( 'If any of your PHP snippet files are not readable, connect to your site using an FTP client like FileZilla, navigate to the folder where you placed such as script_filename.php (typically /wp-content/code-snippet-studio-custom/ for custom snippets), right-click the file, and select “File Permissions” or “Properties.” Set the permissions to 0644 to ensure the file is readable—this allows the owner to read and write, and everyone else to read. Apply the changes, then refresh your site.', 'code-snippet-studio' ) . '</p>';
	
	echo '<h3>' . esc_html__( 'Accessing Saved Settings', 'code-snippet-studio' ) . '</h3>';
	
	echo '<p>' . esc_html__( 'You can access saved values using the', 'code-snippet-studio' ) . ' <code>' . esc_html( 'get_option()' ) . '</code> ' . esc_html__( 'function and referencing the normalized script key and field name:', 'code-snippet-studio' ) . '</p>';
	
	echo '<pre class="code-snippet-studio-preview"><code>' . esc_html(htmlspecialchars(
	'$settings = get_option(\'code_snippet_studio_settings\', []);
	$value = $settings[\'my-script\'][\'field_name\'] ?? \'\';'
	)) . '</code></pre>';
	
	echo '<p>' . esc_html__( 'For example, if your script is', 'code-snippet-studio' ) . ' <code>' . esc_html( 'new-script.php' ) . '</code> ' . esc_html__( 'and you have a setting field called', 'code-snippet-studio' ) . ' <code>' . esc_html( 'new_date' ) . '</code>, ' . esc_html__( 'you can get it like this:', 'code-snippet-studio' ) . '</p>';
	
	echo '<pre class="code-snippet-studio-preview"><code>' . esc_html(htmlspecialchars(
	'$settings = get_option(\'code_snippet_studio_settings\', []);
	$my_date = $settings[\'new-script\'][\'new_date\'] ?? \'\';'
	)) . '</code></pre>';
	
	echo '<h3>' . esc_html__( 'Single Snippet Mode', 'code-snippet-studio' ) . '</h3>';
	
	echo '<p>' . esc_html__( 'If you’d like to use Code Snippet Studio as the foundation for your own standalone plugin, you can enable Single Snippet Mode by simply removing all snippets from the library except one. When only a single snippet file is detected, the plugin automatically transforms into a minimal framework that powers just that snippet — allowing you to structure and maintain it like a lightweight, modular plugin. This is ideal for turning a reusable snippet into a deployable micro-plugin. To finalize the conversion, perform a find & replace across all plugin files, including the master code-snippet-studio.php file and its foldername and filename:', 'code-snippet-studio' ) . '</p>';
	
	echo '<ul class="code-snippet-studio-dev-guide">';
	echo '<li>' . esc_html__( 'Step 1: Find & Replace in code-snippet-studio.php: ', 'code-snippet-studio' ) . '<code>' . esc_html__( 'Code Snippet Studio → Your Snippet Name', 'code-snippet-studio' ) . '</code></li>';
	echo '<li>' . esc_html__( 'Step 2: Find & Replace in code-snippet-studio.php: ', 'code-snippet-studio' ) . '<code>' . esc_html__( 'code-snippet-studio → your-snippet-name', 'code-snippet-studio' ) . '</code></li>';
	echo '<li>' . esc_html__( 'Step 3: Find & Replace in code-snippet-studio.php: ', 'code-snippet-studio' ) . '<code>' . esc_html__( 'code_snippet_studio → your_snippet_name', 'code-snippet-studio' ) . '</code></li>';
	echo '<li>' . esc_html__( 'Step 3: Find & Replace in your-snippet-name.php: ', 'code-snippet-studio' ) . '<code>' . esc_html__( 'code_snippet_studio → your_snippet_name', 'code-snippet-studio' ) . '</code></li>';
	echo '<li>' . esc_html__( 'Step 4: Rename Plugin Folder: ', 'code-snippet-studio' ) . '<code>' . esc_html__( 'code-snippet-studio → your-snippet-name', 'code-snippet-studio' ) . '</code></li>';
	echo '<li>' . esc_html__( 'Step 5: Rename Plugin PHP File: ', 'code-snippet-studio' ) . '<code>' . esc_html__( 'code-snippet-studio.php → your-snippet-name.php', 'code-snippet-studio' ) . '</code></li>';
	echo '<li>' . esc_html__( 'Step 6: Edit Plugin PHP File Header Meta Data: ', 'code-snippet-studio' ) . '<code>' . wp_kses_post(nl2br( '/**
	 * Plugin Name: Your Plugin Name
	 * Plugin URI: https://yourwebsite.com
	 * Description: Your plugin description
	 * Version: 1.0.0
	 * Author: Your Name
	 * Author URI: https://yourwebsite.com
	 * License: GPL2
	 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
	 * Text Domain: your-snippet-name
*/' )) . '</code></li>';
	echo '<li>' . esc_html__( 'Step 7: Plugin Folder readme.txt: ', 'code-snippet-studio' ) . '<code>' . esc_html__( 'Based on what you know about this snippet, can you create a detailed readme.txt based on Wordpress Plugin standards? Do not reference anything about Code Snippet Studio. Also, here are a list of image screenshot URLs that you can include: ', 'code-snippet-studio' ) . '</code></li>';
	echo '</ul>';
	
	echo '<p>' . esc_html__( 'Moving forward you can always use the latest version of Code Snippet Studio for the main framework of your plugin, you would just need to download the latest version, drop in your snippet, and then do this same find & replace. Feel free to update the header of code-snippet-studio.php to your own plugin metadata including title, description, author, version number, etc.', 'code-snippet-studio' ) . '</p>';
	
	echo '<h3>' . esc_html__( 'AI Projects', 'code-snippet-studio' ) . '</h3>';
	
	echo '<p>' . esc_html__( 'Copy & paste the following details into an AI project to help you write your snippets, with custom settings:', 'code-snippet-studio' ) . '</p>';
	echo '<pre class="code-snippet-studio-preview"><code>Every time you are asked to create a new script, please follow this format below… 
	
This includes: 

1. A great SEO focused title for the script.
2. A detailed description about what the functionality the script provides, minimum 2 sentences.
3. Specific instructions on how to use it including potential shortcodes. Minimum 2 sentences.
4. All settings, field notes.
5. A detailed changelog.
6. Even code documentation when writing actual code.

Please prefix all function names with code_snippet_studio_ and use all Wordpress coding standards for the Wordpress.org Plugin Directory. 

You can use all of the different field types that are in the example below… This includes date, color, number, text, textarea, checkbox, select, and code. Also, feel free to use the separator field type to create new sections or groupings of settings, such as Email Fields.

If you need to create a new WP Admin page, please add it as a sub menu item under parent Menu Item slug code_snippet_studio in the custom function provided, otherwise just return nothing in that function. 

Whenever I send a message “all code” you should re-display the entire up to date latest script/all code.

Whenever I send a message “php file” you should export a php file titled with the script name and all updated code.

Make sure that all standards for wordpress.org plugin library are properly prepared, including but not limited to:
•	Prefix all option keys with code_snippet_studio_ for consistency and conflict prevention
•	Wrap translatable strings with __() and _e() functions including esc_html() esc_attr() wp_kses_post() and esc_html__( \'Show All Changes\', \'code-snippet-studio\' )
•	Ensure all input is sanitized and escaped where needed

If this snippet allows the user to initiate any sort of action, with on the front end of the website, or the wp-admin backend, such as sending an email or creating a user (but not limited to these), please tap into the activity log and create a record by the following PHP function:
	
code_snippet_studio_log_activity(\'my-custom-script\', \'Sent welcome email to new user.\');

If you are asked to output the script to a PHP file, please create the file with a filename in this format "script_filename.php" that matches the function names in the sames below. Please set the file permissions of all files to Octal/CHMOD 0644 which should be readable for User, Group, and World.

Here is the code format to follow, this will be plugged into another master plugin that will be executing this code in this format, also notice the classes that start with code-snippet-studio-admin-card and always use this format when displaying anything in any wp-admin pages so the entire plugin looks consistent, also don’t put admin cards inside of other admin cards, please keep them all clean and simple, and also keep all the functions in the order below with all additional snippet functionality at the very bottom. You can also use return []; // No settings needed when no settings are needed or even when no menu item is needed:

&lt;?php
	
/* This is where all basic snippet info in saved*/	
function code_snippet_studio_get_script_info_for_script_filename() {
	return [
		\'name\'        =&gt; \'Days Until Countdown Timer\',
		\'description\' =&gt; \'Displays the number of days remaining until a target date.\',
		\'instructions\' =&gt; \'Use the shortcode [days_until] to display a countdown. Example: [days_until target_date="2025-12-31"]\',
		\'version\'     =&gt; \'1.0.1\',
		\'required_plugins\'     =&gt; \'Woocommerce\',
	];
}

/* This is where all snippet changelog information in saved*/
function code_snippet_studio_get_changelog_for_script_filename() {
		return [
			[\'version\' =&gt; \'1.0.1\', \'date\' =&gt; \'2024-11-12\', \'note\' =&gt; \'Improved display formatting and added offset validation.\'],
			[\'version\' =&gt; \'1.0.0\', \'date\' =&gt; \'2024-11-11\', \'note\' =&gt; \'Initial release of the shortcode.\'],
		];
	}

/* This is where all snippet settings can be modified, added, or removed*/
function code_snippet_studio_get_settings_for_script_filename() {
return [
	[
		\'field\' =&gt; \'target_date\',
		\'label\' =&gt; \'Target Date\',
		\'type\'  =&gt; \'date\',
		\'note\'  =&gt; \'Pick a date using the calendar picker.\',
	],
	[
		\'field\' =&gt; \'theme_color\',
		\'label\' =&gt; \'Theme Color\',
		\'type\'  =&gt; \'color\',
		\'note\'  =&gt; \'Select a color.\',
	],
	[
		\'field\' =&gt; \'quantity_limit\',
		\'label\' =&gt; \'Quantity Limit\',
		\'type\'  =&gt; \'number\',
		\'note\'  =&gt; \'Enter a numeric limit.\',
	],
	[
		\'field\' =&gt; \'short_text\',
		\'label\' =&gt; \'Short Text\',
		\'type\'  =&gt; \'text\',
		\'note\'  =&gt; \'Enter a short string.\',
	],
	[
		\'field\' =&gt; \'long_description\',
		\'label\' =&gt; \'Long Description\',
		\'type\'  =&gt; \'textarea\',
		\'note\'  =&gt; \'Use this for longer content blocks.\',
	],
	[
		\'field\' =&gt; \'enable_feature\',
		\'label\' =&gt; \'Enable Feature\',
		\'type\'  =&gt; \'checkbox\',
		\'note\'  =&gt; \'Toggle this option on or off.\',
	],
	[
		\'field\' =&gt; \'status\',
		\'label\' =&gt; \'Status\',
		\'type\'  =&gt; \'select;draft:published:archived\',
		\'note\'  =&gt; \'Select the current status.\',
	],
	[
		\'field\' =&gt; \'separator\',
		\'label\' =&gt; \'Field Separator with Title\',
		\'type\'  =&gt; \'separator\',
		\'note\'  =&gt; \'Separators can be used to create new sections of settings fields\',
	],
	[
		\'field\' =&gt; \'custom_php\',
		\'label\' =&gt; \'Custom PHP\',
		\'type\'  =&gt; \'code\',
		\'note\'  =&gt; \'Enter raw PHP code. Use with caution.\',
	],
];
}

/* This is where an admin screen can be added specifically for this snippet*/
function code_snippet_studio_get_menu_info_for_script_filename() {
	return [
		\'parent_slug\' =&gt; \'code_snippet_studio\',
		\'page_title\'  =&gt; ‘Sample ‘Sub Page,
		\'menu_title\'  =&gt; \'Sample ‘Sub Page\',
		\'capability\'  =&gt; \'manage_options\',
		\'menu_slug\'   =&gt; \'send-test-email\',
		\'callback\'    =&gt; \'code_snippet_studio_render_sample_sub_page’,
	];
}

/* Below this line is where all additional functionality for this snippet can be added*/

// Register shortcode
add_shortcode(\'days_until\', function($atts) {

	$settings = get_option(\'code_snippet_studio_settings\', []);
	$target_date = $settings[\'filename\'][\'target_date\'] ?? \'\';
	$theme_color = $settings[\'filename\'][\'theme_color\'] ?? \'\';
	$quantity_limit = $settings[\'filename\'][\'quantity_limit\'] ?? \'\';
	$short_text = $settings[\'filename\'][\'short_text\'] ?? \'\';
	$long_description = $settings[\'filename\'][\'long_description\'] ?? \'\';
	$enable_feature = $settings[\'filename\'][\'enable_feature\'] ?? \'\';
	$status = $settings[\'filename\'][\'status\'] ?? \'\';
	$custom_php = $settings[\'filename\'][\'custom_php\'] ?? \'\';

	$atts = shortcode_atts([
		\'target_date\'    =&gt; $target_date ?? \'\',
		\'label_singular\' =&gt; $short_text ?? \'day left\',
		\'label_plural\'   =&gt; $long_description ?? \'days left\',
	], $atts);

	if (empty($atts[\'target_date\'])) return \'&lt;p&gt;Please set a target date.&lt;/p&gt;\';

	$now = current_time(\'timestamp\');
	$target = strtotime($atts[\'target_date\']);

	if ($target === false) return \'&lt;p&gt;Invalid target date format.&lt;/p&gt;\';

	$diff_days = max(0, ceil(($target - $now) / DAY_IN_SECONDS));
	$label = $diff_days === 1 ? $atts[\'label_singular\'] : $atts[\'label_plural\'];

	return "&lt;span&gt;{$diff_days} {$label}&lt;/span&gt;";
});

function code_snippet_studio_render_sample_sub_page() {
echo ‘Sample Sub Page’;

echo ‘&lt;div id="section-[id]” class="code-snippet-studio-section-alt code-snippet-studio-admin-card-container’&gt;

&lt;div class=‘code-snippet-studio-admin-card-header-container’&gt;
&lt;span class=‘code-snippet-studio-admin-card-emoji-icon’&gt;[admin_card_emoji_icon]&lt;/span&gt;
&lt;h2 class=‘code-snippet-studio-admin-card-header’&gt;[admin_card_title]&lt;/h2&gt;
&lt;/div&gt;

&lt;div class=‘code-snippet-studio-admin-card-body’&gt;
&lt;p&gt;[admin_card_content]&lt;/p&gt;
&lt;/div&gt;

&lt;/div&gt;’;

}</code></pre>';

echo '<h3>' . esc_html__( 'Example AI Prompt', 'code-snippet-studio' ) . '</h3>';
echo '<p>' . esc_html__( 'Once your project is set up, please try a prompt such as the following:', 'code-snippet-studio' ) . '</p>';

echo '<pre class="code-snippet-studio-preview"><code>' . esc_html(htmlspecialchars(
'I need a new snippet called "site background color" with a color setting that will change the background color of the entire site. Please follow all project instructions, and then export the file as a php file.'
)) . '</code></pre>';

echo '<p>' . esc_html__( 'Continue to write more comprehensive prompts to build out more complex snippets. Consider trying AI voice features for describing snippets by speaking. If you are not using any AI Project based features as described above, please copy & paste the instructions provided above in with your prompt.', 'code-snippet-studio' ) . '</p>';

echo '<p>' . wp_kses_post(
	sprintf(
		/* translators: %s: Support section anchor link */
		__( 'Please reach out to <a href="%s">support</a> for custom snippet support.', 'code-snippet-studio' ),
		'#section-support'
	)
) . '</p>';

echo '</div>';
	
} 
	
	//----------------
	
	echo '</div>';
	
}

add_action('admin_init', function () {
	if (isset($_POST['save_script_statuses']) && current_user_can('manage_options')) {
		check_admin_referer('save_script_statuses');

		$user = wp_get_current_user();
		$log = get_option('code_snippet_studio_activity_log', []);
		$old_statuses = get_option('code_snippet_studio_statuses', []);
		$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
		$agent   = $_SERVER['HTTP_USER_AGENT'] ?? '';
		$request = $_SERVER['REQUEST_URI'] ?? '';

		$new_statuses_raw = isset($_POST['script_status']) && is_array($_POST['script_status']) 
			? array_map('sanitize_text_field', wp_unslash($_POST['script_status'])) 
			: [];

		$new_statuses = [];
		foreach ($new_statuses_raw as $key => $val) {
			$new_statuses[normalize_script_key($key)] = $val;
		}

		foreach ($new_statuses as $script => $new_state) {
			$old_state = $old_statuses[$script] ?? '0';
			if ($old_state !== $new_state) {
				$log[] = [
					'timestamp' => current_time('mysql'),
					'user'      => $user->user_email,
					'ip'    	=> $ip_address,
					'agent'    	=> $agent,
					'url'    	=> $request,
					'script'    => $script,
					'action'    => $new_state === '1' ? 'Activated' : 'Deactivated',
				];
			}
		}

		update_option('code_snippet_studio_statuses', $new_statuses);

		// --- Global settings ---
		$global_plugin_settings_schema = code_snippet_studio_get_global_settings_schema();
		foreach ($global_plugin_settings_schema as $setting) {
			$key   = 'code_snippet_studio_' . $setting['slug'];
			$type  = $setting['type'];
			$label = $setting['title'] ?? $setting['slug'];
			$old   = get_option($key, '');

			$new = ($type === 'checkbox') ? (isset($_POST[$key]) ? '1' : '0') : (isset($_POST[$key]) ? wp_kses_post($_POST[$key]) : '');

			if ($old !== $new && !($old === '' && $new === '0')) {
				update_option($key, $new);
				$log[] = [
					'timestamp' => current_time('mysql'),
					'user'      => $user->user_email,
					'ip'        => $ip_address,
					'agent'     => $agent,
					'url'       => $request,
					'script'    => 'code_snippet_studio',
					'action'    => "Changed global setting '{$label}' from '{$old}' to '{$new}'",
				];
			}
		}

		// --- Per-script settings ---
		$existing_settings = get_option('code_snippet_studio_settings', []);
		$new_settings = isset($_POST['code_snippet_studio_settings']) ? stripslashes_deep($_POST['code_snippet_studio_settings']) : [];
		$clear_flags = isset($_POST['code_snippet_studio_clear']) && is_array($_POST['code_snippet_studio_clear'])
			? array_map('sanitize_text_field', wp_unslash($_POST['code_snippet_studio_clear']))
			: [];

		$changed_settings = [];

		foreach ($new_settings as $script_key => $settings_group) {
			foreach ($settings_group as $field => $new_value) {
				$should_clear = !empty($clear_flags[$script_key][$field]);
				$old_value = $existing_settings[$script_key][$field] ?? '';

				if ($should_clear && $old_value !== '') {
					$changed_settings[$script_key][$field] = '';
					$log[] = [
						'timestamp' => current_time('mysql'),
						'user'      => $user->user_email,
						'ip'    	=> $ip_address,
						'agent'    	=> $agent,
						'url'    	=> $request,
						'script'    => $script_key,
						'action'    => "Cleared '{$field}' (was '{$old_value}')",
					];
				} elseif (!$should_clear && $new_value !== $old_value) {
					$changed_settings[$script_key][$field] = $new_value;
					$log[] = [
						'timestamp' => current_time('mysql'),
						'user'      => $user->user_email,
						'ip'    	=> $ip_address,
						'agent'    	=> $agent,
						'url'    	=> $request,
						'script'    => $script_key,
						'action'    => "Changed '{$field}' from '{$old_value}' to '{$new_value}'",
					];
				}
			}
		}

		if (!empty($changed_settings)) {
			update_option(
				'code_snippet_studio_settings',
				array_replace_recursive($existing_settings, $changed_settings)
			);
		}

		// Finalize log
		update_option('code_snippet_studio_activity_log', array_slice($log, -100));

		wp_redirect(admin_url('admin.php?page=code_snippet_studio&saved=1'));
		exit;
	}
});

add_action('init', 'code_snippet_studio_maybe_include_scripts');
function code_snippet_studio_maybe_include_scripts() {
	if (is_admin()) return;

	$statuses = get_option('code_snippet_studio_statuses', []);
	$settings  = get_option('code_snippet_studio_settings', []);
	$debug_mode = get_option('code_snippet_studio_debug_mode') === '1';
	$loaded_scripts = [];

	$disable_custom_snippets = get_option('code_snippet_studio_disable_custom_snippets', '0');
	$disable_normal_snippets = get_option('code_snippet_studio_disable_normal_snippets', '0');
	
	$statuses = get_option('code_snippet_studio_statuses', []);
	
	$base_dirs = [];
	
	if ($disable_normal_snippets !== "Disabled") {
		$base_dirs[] = plugin_dir_path(__FILE__);
	}
	
	if ($disable_custom_snippets !== "Disabled") {
		$base_dirs[] = WP_CONTENT_DIR . '/code-snippet-studio-custom';
	}

	$filename_filter = get_option('code_snippet_studio_single_snippet_mode_filename', '');
	foreach ($base_dirs as $base_dir) {
		if (!is_dir($base_dir)) continue;

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($base_dir, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($iterator as $file) {
			
			if ($filename_filter && pathinfo($file, PATHINFO_FILENAME) !== $filename_filter) continue;
			
			if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;

			$relative_path = str_replace($base_dir, '', $file);
			$relative_path = ltrim(str_replace('\\', '/', $relative_path), '/');

			$normalized_key = normalize_script_key($relative_path);

			if (!empty($statuses[$normalized_key]) && ($statuses[$normalized_key] === 'on' || $statuses[$normalized_key] === '1')) {
				include_once $file;
				if ($debug_mode) {
					$loaded_scripts[] = [
						'key' => $normalized_key,
						'file' => $file,
						'settings' => $settings[$normalized_key] ?? [],
					];
				}
			}
		}
	}

	if ($debug_mode && !empty($loaded_scripts)) {
		add_action('wp_footer', function () use ($loaded_scripts) {
			echo '<div class="code-snippet-studio-debug-info"><strong>Code Snippet Studio Debug Info:</strong><ul>';
			foreach ($loaded_scripts as $script) {
				echo '<li><strong>' . esc_html($script['key']) . '</strong> (' . esc_html($script['file']) . ')';
				if (!empty($script['settings'])) {
					echo '<br><em>Settings:</em><pre>' . esc_html(print_r($script['settings'], true)) . '</pre>';
				}
				echo '</li>';
			}
			echo '</ul></div>';
		});
	}
}

add_action('admin_menu', 'code_snippet_studio_register_custom_menus');
function code_snippet_studio_register_custom_menus() {

	$disable_custom_snippets = get_option('code_snippet_studio_disable_custom_snippets', '0');
	$disable_normal_snippets = get_option('code_snippet_studio_disable_normal_snippets', '0');
	
	$statuses = get_option('code_snippet_studio_statuses', []);
	
	$base_dirs = [];
	
	if ($disable_normal_snippets !== "Disabled") {
		$base_dirs[] = plugin_dir_path(__FILE__);
	}
	
	if ($disable_custom_snippets !== "Disabled") {
		$base_dirs[] = WP_CONTENT_DIR . '/code-snippet-studio-custom';
	}

	$filename_filter = get_option('code_snippet_studio_single_snippet_mode_filename', '');
	foreach ($base_dirs as $base_dir) {
		if (!is_dir($base_dir)) continue;

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($base_dir, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($iterator as $file) {
				
			if ($filename_filter && pathinfo($file, PATHINFO_FILENAME) !== $filename_filter) continue;
			
			if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;

			$relative_path = ltrim(str_replace(['\\', $base_dir], ['/', ''], $file), '/');
			$normalized_key = normalize_script_key($relative_path);

			if (!empty($statuses[$normalized_key]) && ($statuses[$normalized_key] === 'on' || $statuses[$normalized_key] === '1')) {
				include_once $file;

				$function_name = 'code_snippet_studio_get_menu_info_for_' . str_replace(['-', '.php'], ['_', ''], basename($file));
				if (function_exists($function_name)) {
					$menu = $function_name();
					if (!empty($menu['callback']) && is_callable($menu['callback'])) {
						add_submenu_page(
							$menu['parent_slug'],
							$menu['page_title'],
							$menu['menu_title'],
							$menu['capability'],
							$menu['menu_slug'],
							$menu['callback']
						);
					}
				}
			}
		}
	}
}

function get_all_script_changelog_entries($base_dir) {
	$entries = [];

	$filename_filter = get_option('code_snippet_studio_single_snippet_mode_filename', '');
	foreach (glob($base_dir . '*/', GLOB_ONLYDIR) as $folder) {
		foreach (glob($folder . '*.php') as $file) {
			
		if ($filename_filter && pathinfo($file, PATHINFO_FILENAME) !== $filename_filter) continue;
			
			include_once $file;

			$script_base = preg_replace('/[_-]v?[\d\.]+$/i', '', basename($file, '.php'));
			$changelog_func = 'code_snippet_studio_get_changelog_for_' . str_replace('-', '_', $script_base);
			$info_func = 'code_snippet_studio_get_script_info_for_' . str_replace('-', '_', $script_base);

			if (function_exists($changelog_func)) {
				$changelog = $changelog_func();
				$info = function_exists($info_func) ? $info_func() : [];
				$script_name = $info['name'] ?? ucwords(str_replace(['-', '_'], ' ', $script_base));

				foreach ($changelog as $entry) {
					$entries[] = [
						'script'  => $script_name,
						'version' => $entry['version'] ?? '',
						'date'    => $entry['date'] ?? '',
						'note'    => $entry['note'] ?? '',
					];
				}
			}
		}
	}

	// Sort by most recent date descending
	usort($entries, function($a, $b) {
		return strtotime($b['date']) - strtotime($a['date']);
	});

	return $entries;
}

function code_snippet_studio_human_date_diff($from_timestamp, $to_timestamp = null) {
	$to_timestamp = $to_timestamp ?? current_time('timestamp');
	$diff = abs($to_timestamp - $from_timestamp);
	$days = floor($diff / DAY_IN_SECONDS);

	if ($days == 0) {
		return 'Today';
	}
	if ($days < 7) {
		return $days == 1 ? '1 day ago' : "{$days} days ago";
	}
	if ($days < 30) {
		$weeks = floor($days / 7);
		return $weeks == 1 ? '1 week ago' : "{$weeks} weeks ago";
	}
	if ($days < 365) {
		$months = floor($days / 30);
		return $months == 1 ? '1 month ago' : "{$months} months ago";
	}
	$years = floor($days / 365);
	return $years == 1 ? '1 year ago' : "{$years} years ago";
}

function normalize_script_key($filename) {
	$basename = basename($filename); // Get just the file name, e.g. 'test-script.php'
	return preg_replace('/[_-]v?[\d\.]+\.php$/i', '', str_replace('.php', '', $basename));
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'code_snippet_studio_add_settings_link');
function code_snippet_studio_add_settings_link($links) {
	$settings_url = admin_url('admin.php?page=code_snippet_studio');
	$settings_link = '<a href="' . esc_url($settings_url) . '">Settings</a>';
	array_unshift($links, $settings_link); // Add to the beginning
	return $links;
}

function code_snippet_studio_log_activity($script_key, $action_description) {
	$log = get_option('code_snippet_studio_activity_log', []);
	$user = wp_get_current_user();

	$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	$agent   = $_SERVER['HTTP_USER_AGENT'] ?? '';
	$request = $_SERVER['REQUEST_URI'] ?? '';

	$log[] = [
		'timestamp' => current_time('mysql'),
		'user'      => $user->user_email,
		'ip'        => $ip_address,
		'agent'    	=> $agent,
		'url'    	=> $request,
		'script'    => $script_key,
		'action'    => $action_description,
	];

	update_option('code_snippet_studio_activity_log', array_slice($log, -100));
}

add_action('admin_head', 'code_snippet_studio_admin_css');

function code_snippet_studio_admin_css() {
		// Get current admin page
	$current_page = $_GET['page'] ?? '';
	
	// Always allow the main plugin page
	$allowed_pages = ['code_snippet_studio'];
	
	// Get all active snippets
	$statuses = get_option('code_snippet_studio_statuses', []);
	$base_dirs = [
		plugin_dir_path(__FILE__),
		WP_CONTENT_DIR . '/code-snippet-studio-custom',
	];
	
	$filename_filter = get_option('code_snippet_studio_single_snippet_mode_filename', '');
	foreach ($base_dirs as $base_dir) {
		if (!is_dir($base_dir)) continue;
	
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($base_dir, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::LEAVES_ONLY
		);
	
		foreach ($iterator as $file) {
			
			if ($filename_filter && pathinfo($file, PATHINFO_FILENAME) !== $filename_filter) continue;
			
			if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
	
			$relative_path = ltrim(str_replace(['\\', $base_dir], ['/', ''], $file), '/');
			$normalized_key = normalize_script_key($relative_path);
	
			if (!empty($statuses[$normalized_key]) && $statuses[$normalized_key] === '1') {
				include_once $file;
	
				$menu_func = 'code_snippet_studio_get_menu_info_for_' . str_replace(['-', '.php'], ['_', ''], basename($file));
				if (function_exists($menu_func)) {
					$menu = $menu_func();
					if (!empty($menu['menu_slug'])) {
						$allowed_pages[] = $menu['menu_slug'];
					}
				}
			}
		}
	}
	
	if (!in_array($current_page, $allowed_pages)) return;
	
	?>
	
	<style>
	.code-snippet-studio-thumbnail-link img {
		width: 75px;
		margin-top: 0px;
		height: auto;
		margin-right: 10px;
		border-radius: 5px;
		border: 1px solid #ccc;
	}
	
	.code-snippet-studio-script-odd {
		background-color: #f0f0f0 !important;
	}
	.code-snippet-studio-script-even {
		background-color: #ffffff !important;
	}
	.code-snippet-studio-script-error {
		background-color: rgb(255, 175, 175) !important;
	}
	.code-snippet-studio-snippet-section {
		margin-top: 20px;
		padding: 20px;
		border: 1px solid #ccc;
		border-radius: 5px;
		background: #fff;
	}
	.code-snippet-studio-instructions {
		margin-top:5px;
		margin-bottom:20px;
		margin-left:12px;
		background-color:white;
		border:1px solid #c0c0c0;
		padding:10px;
		border-radius:5px;
	}
	.code-snippet-studio-section-alt {
		margin-top: 30px;
		padding: 20px;
		border: 1px solid #ccc;
		border-radius: 5px;
		background: #f9f9f9;
	}
	.code-snippet-studio-section-tight {
		margin-bottom: 0;
		margin-left: 8px;
	}
	.code-snippet-studio-heading {
		margin-top: 0;
	}
	.code-snippet-studio-note {
		display: block;
		font-style: italic;
	}
	.code-snippet-studio-small {
		color: #666;
		line-height: 12px !important;
		display: block;
		margin-top: 10px;
	}
	.code-snippet-studio-anchor-links {
		padding-top: 20px;
		padding-left: 10px;
		padding-right: 10px;
	}
	.code-snippet-studio-submit {
		margin-top: 20px;
	}
	
	.code-snippet-studio-table { width: 100%; }
	.code-snippet-studio-th-title { width: 20%; max-width: 100px; }
	.code-snippet-studio-tr-odd { background-color: #f0f0f0 !important; }
	.code-snippet-studio-tr-even { background-color: #ffffff !important; }
	
	.code-snippet-studio-thumb-link img {
		width: 75px;
		margin-top: 10px;
		height: auto;
		margin-right: 10px;
		border-radius: 5px;
		border: 1px solid #ccc;
	}
	#code-snippet-studio-lightbox {
		display: none;
		position: fixed;
		top: 0; left: 0; width: 100%; height: 100%;
		background: rgba(0,0,0,0.8);
		justify-content: center;
		align-items: center;
		z-index: 9999;
	}
	#code-snippet-studio-lightbox img {
		max-width: 90%;
		max-height: 90%;
		box-shadow: 0 0 20px #000;
	}
	.code-snippet-studio-toggle-label {
		margin-top: 15px;
		display: block;
	}
	.code-snippet-studio-flex-row {
		display: flex;
		gap: 10px;
		align-items: center;
		margin-bottom: 6px;
	}
	
	.code-snippet-studio-details {
		margin-top: 0;
	}
	.code-snippet-studio-summary {
		cursor: pointer;
		font-weight: bold;
		margin-bottom: 10px;
	}
	.code-snippet-studio-instructions-content {
		margin-top: 5px;
		margin-bottom: 20px;
		margin-left: 12px;
		background-color: white;
		border: 1px solid #c0c0c0;
		padding: 10px;
		border-radius: 5px;
	}
	.code-snippet-studio-code-input {
		width:100%; 
		font-family: monospace; 
		background:#f5f5f5;
		border:1px solid #ccc; 
		padding:10px;
	}
	
	.code-snippet-studio-settings-grid {
		padding: 10px;
		display: grid;
		grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
		gap: 20px;
		border-radius: 5px;
	}
	.code-snippet-studio-settings-block {
		background-color: white;
		border: 1px solid #c0c0c0;
		padding: 10px;
		border-radius: 5px;
		text-align: center;
	}
	.code-snippet-studio-settings-label {
		display: block;
		margin-bottom: -10px;
		font-weight: bold;
	}
	.code-snippet-studio-code-preview {
		background: #f9f9f9;
		color: #333;
		border: 1px dashed #ccc;
		padding: 8px;
		margin-top: 5px;
		font-size: 10px;
		font-family: monospace;
	}
	.code-snippet-studio-textarea-code {
		width: 100%;
		font-family: monospace;
		background: #f5f5f5;
		border: 1px solid #ccc;
		padding: 10px;
	}
	
	.code-snippet-studio-pre {
		background: #f9f9f9;
		border: 1px solid #ddd;
		padding: 15px;
		border-radius: 5px;
		overflow: auto;
	}
	
	.code-snippet-studio-ul { padding-left: 18px; list-style: disc; }
	.code-snippet-studio-table-widefat { margin-top: 10px; }
	.code-snippet-studio-details-summary { margin: 10px 0; }
	
	#code-snippet-studio-lightbox {
		display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
		background: rgba(0,0,0,0.8); justify-content: center; align-items: center; z-index: 9999;
	}
	#code-snippet-studio-lightbox img {
		max-width: 90%; max-height: 90%; box-shadow: 0 0 20px #000;
	}
	
	.code-snippet-studio-instructions-container summary,
	.code-snippet-studio-settings-container summary,
	.code-snippet-studio-toggles summary,
	.show-all {
		cursor: pointer;
		font-weight: bold;
	}
	
	.code-snippet-studio-instructions-container summary {
		margin-bottom: 5px;
	}
	
	.code-snippet-studio-settings-container summary {
		margin-bottom: 5px;
		margin-left: 0;
	}
	
	.code-snippet-studio-settings-field-container {
		display: table;
		margin: 0 auto;
	}
	
	.code-snippet-studio-settings-field-container .pickr-container {
		display: inline-block;
	}
	
	.code-snippet-studio-flex-row .code-snippet-studio-flex-row-file {
		flex: 1;
		min-width: 0;
	}
	
	.code-snippet-studio-full-width {
		width: 100%;
	}
	
	.code-snippet-studio-light-gray {
		color: #888;
	}
	
	.code-snippet-studio-toggles {
		margin-top: 10px;
	}
	
	.code-snippet-studio-toggles details {
		margin-top: 5px;
	}
	
	.code-snippet-studio-toggles ul {
		margin-top: 5px;
		margin-left: 12px;
		background-color: white;
		border: 1px solid #c0c0c0;
		padding: 10px;
		border-radius: 5px;
	}
	
	.code-snippet-studio-debug-info {
		background: #fff0c2;
		color: #333;
		border-top: 3px solid #f0c200;
		padding: 20px;
		font-size: 13px;
		overflow: auto;
		white-space: pre-wrap;
	}
	
	.code-snippet-studio-section-global-plugin-settings {
		margin-top: 20px;
		padding: 20px;
		border: 1px solid #ccc;
		border-radius: 6px;
		background: #fff;
	}
	
	.code-snippet-studio-dev-guide {
		padding-left: 18px;
		list-style: disc;
	}
	
	.code-snippet-studio-preview {
		background: #f9f9f9;
		border: 1px solid #ddd;
		padding: 15px;
		border-radius: 5px;
		overflow: auto;
	}
	
	.code-snippet-studio-debug-info per {
		background: #f8f8f8;
		padding: 8px;
		border: 1px solid #ccc;
	}
	
	#code-snippet-studio-settings {
	  display: flex;
	  flex-wrap: wrap;
	  gap: 20px;
	  justify-content: flex-start;
		margin-left:12px;
		  margin-bottom:12px;
	}
	
	/* Each settings block should be at least 300px wide but shrink if needed */
	#code-snippet-studio-settings .code-snippet-studio-settings-block {
	  flex: 1 1 300px;
	  box-sizing: border-box;
	  background: #fff;
	  border-radius: 5px;
	  padding: 15px;
	  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
	}
	.code-snippet-studio-admin-card-header {
		width: 98%;
		border-bottom: 1px solid lightgray;
		padding-bottom: 10px;
		font-size: 1.5rem;
		line-height: 1.75rem;
		margin-top: 0px;
		margin-bottom: 15px;
		color: #222222;
	}
	.code-snippet-studio-admin-card-emoji-icon {
		float:left;
		font-size: 1.5rem;
		margin-right:10px;
		margin-top:5px;
	} 
	.code-snippet-studio-section-tight input[type="text"],
	#code-snippet-studio-settings input[type="text"],
	#code-snippet-studio-settings input[type="hidden"],
	#code-snippet-studio-settings .pickr, 
	.code-snippet-studio-admin-card-body input[type=text], 
	.code-snippet-studio-admin-card-body input[type=text], 
	.code-snippet-studio-admin-card-body input[type=text], 
	.code-snippet-studio-admin-card-body input[type=number], 
	.code-snippet-studio-admin-card-body select, 
	.code-snippet-studio-admin-card-body option, 
	.code-snippet-studio-admin-card-body textarea, 
	.code-snippet-studio-admin-card-body option {
		width: 100%;
		padding: 12px !important;
		border: 1px solid #ccc;
		border-radius: 4px;
		resize: vertical;
		background-color: white !important;
		max-width: 100%;
			box-sizing: border-box;
	}
	.code-snippet-studio-admin-card-body input[type=number] {
		width: 100px;
		text-align: center;
	}
	.submit {
		padding: 0;
	}
	
	/* Hide default checkbox */
	.code-snippet-studio-modern-checkbox {
		position: relative;
		display: inline-block;
		width: 42px;
		height: 24px;
		min-width: 42px; /* ensures the toggle doesn’t shrink */
	}
	
	.code-snippet-studio-modern-checkbox input {
	opacity: 0;
	width: 0;
	height: 0;
	}
	
	.code-snippet-studio-modern-checkbox .slider {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		width: 100%;
		background-color: #ccc;
		border-radius: 34px;
		transition: background-color 0.2s;
	}
	
	.code-snippet-studio-modern-checkbox .slider::before {
	content: "";
	position: absolute;
	height: 18px;
	width: 18px;
	left: 3px;
	top: 3px;
	background-color: white;
	border-radius: 50%;
	transition: transform 0.2s;
	}
	
	.code-snippet-studio-modern-checkbox input:checked + .slider {
	background-color: #4caf50;
	}
	
	.code-snippet-studio-modern-checkbox input:checked + .slider::before {
	transform: translateX(18px);
	}
	.code-snippet-studio-modern-checkbox-center {
		display: flex;
		justify-content: center;  /* center horizontally */
		align-items: center;      /* center vertically (optional) */
		padding: 10px;
	}
	
	.code-snippet-studio-modern-checkbox-wrapper {
		display: flex;
		align-items: center;
		cursor: pointer;
		gap: 10px;
		font-size: 14px;
		margin-bottom:10px;
	}
	
	.code-snippet-studio-modern-checkbox-wrapper {
		display: flex;
		align-items: center;
		gap: 10px;
		cursor: pointer;
	}
	.wp-core-ui .button {
		padding: 0 14px;
		line-height: 2.71428571;
		font-size: 14px;
		vertical-align: middle;
		min-height: 40px;
		margin-bottom: 0px;
	}
	p.submit {
		margin-top: 10px;
	}
	.widefat {
		border-radius:3px;
	}
	.submit {
		margin: 0px 0;
	}
	.wrap {
		margin: 25px 20px 0 2px;
	}
	.code-snippet-studio-settings-block-separator {
		min-width:100% !important;
		background-color: black !important;
		color: white;
	}
	.note {
		font-size:10px;
	}
	</style>
	<?php
}