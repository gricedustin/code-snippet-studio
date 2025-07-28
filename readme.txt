=== Code Snippet Studio ===
Contributors: dustingrice
Tags: code snippets, admin tools, developer tools, custom code, plugin development, AI, settings
Requires at least: 5.6
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build, manage, and deploy modular PHP snippets with versioning, changelogs, settings, and logging. Designed for AI-first plugin development and advanced customization workflows. Also includes a curated library of preloaded, performance-ready snippets—making it easy for site owners to enable powerful features without writing code.

== Description ==

Code Snippet Studio is a professional developer toolkit for managing modular, version-controlled PHP snippets within WordPress. Each snippet can include its own settings, automatic setting change logging, version history and custom UI — enabling developers to treat every script like a self-contained micro-plugin. While the plugin includes a small set of production-ready snippets, these serve as examples and quick-start tools. The true value lies in the framework itself: a powerful system for writing, organizing, and documenting custom code. Perfect for agencies, freelancers, and advanced users building tailored solutions across multiple projects.

== Features ==

* 🧰 **Developer-first snippet framework** with versioning, changelogs, structured settings, and folder organization
* ✅ Includes preloaded example snippets, ready to enable or customize
* 🧠 Built-in **AI prompting workflow** for generating new scripts with custom settings
* 🔌 **Global and per-snippet settings UI** (text, selects, checkboxes, color, date, file upload, etc.)
* 💾 Stores settings and activation states in the database
* 🧩 Optional per-script admin submenus, shortcode rendering, and UI customization
* 📁 Custom folder support (`/code-snippet-studio-custom/`) for update-safe storage
* 🛡️ Secure, PHPCS-compliant output and sanitization
* 🧯 **Debug mode** for viewing loaded snippets and their settings
* 📚 **Built-in developer guide** with examples and code scaffolding help

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install via the Plugins screen.
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Go to **Snippet Studio** to browse and activate snippets.
4. Use the built-in **Developer Guide** section to create your own snippets in `/wp-content/code-snippet-studio-custom/`.

== Frequently Asked Questions ==

= Can I write my own custom PHP snippets? =
Yes! Create new `.php` files inside `/wp-content/code-snippet-studio-custom/` and they will appear in the UI. Follow the developer guide for naming conventions and optional functions for metadata, changelogs, and settings.

= Will my custom snippets be lost on plugin update? =
No. Custom snippets are stored outside the plugin directory (in `/wp-content/code-snippet-studio-custom/`) so they're safe from updates.

= Can I use this plugin on multisite? =
Yes, but currently it does not support network-wide activation or settings. Activate it individually per site.

= Can I export my settings or snippets? =
Not yet — but exporting and importing capabilities are on our roadmap.

== Screenshots ==

https://lifebrand.co/wp-content/blogs.dir/147/files/2025/07/Code-Snippet-Studio-Screenshot-1.png
https://lifebrand.co/wp-content/blogs.dir/147/files/2025/07/Code-Snippet-Studio-Screenshot-2.png
https://lifebrand.co/wp-content/blogs.dir/147/files/2025/07/Code-Snippet-Studio-Screenshot-3.png
https://lifebrand.co/wp-content/blogs.dir/147/files/2025/07/Code-Snippet-Studio-Screenshot-4.png

== Changelog ==

Version 2.1.1 – 2025-07-13 – Added permission check for all files & warnings for files that are not readable. Updated prompting documentation for developers. Minor style class name changes for consistency.
Version 2.1.0 – 2025-07-13 – Global custom fields support.
Version 2.0.1 – 2025-07-13 – Prefixed all option keys for better namespace isolation and consistency. Prefixed all option keys for better namespace isolation and consistency. Added AI prompting documentation.
Version 2.0.0 – 2025-07-12 – Snippet setting field separators support, custom menu title & order, global notes & execution mode, extended developer guide with AI prompting for custom snippets, cleaner styles.
Version 1.9.0 – 2025-07-06 – Wrapped translatable strings with esc_html__() and _e() functions. Added load_plugin_textdomain() for localization support. Ensured all input is sanitized and escaped where needed. Show only active selected snippets, snippet library cleanup.
Version 1.8.0 – 2025-07-02 – Cleaner styles, mobile friendly, filter hides entire sections when no results found.
Version 1.7.0 – 2025-06-29 – Global settings schema introduced for plugin-level fields (e.g., debug, API key), dynamic settings rendering and saving logic, anchor tag link shortcuts, improved settings field compatibility.
Version 1.6.0 – 2025-06-28 – File upload field type, snippet thumbnails with lightbox popup, quick save button under each folder, cleaner activate checkbox UI.
Version 1.5.0 – 2025-06-15 – Front-end script support, Pickr color picker integration, subfolder inclusion, thumbnails, changelog collapse, debug output on front-end, improved normalization, sub-menu page support, and support for blank color values.
Version 1.4.0 – 2025-06-07 – Added debug mode, collapsible settings, and auto-scroll to active settings panel on save.
Version 1.3.0 – 2025-05-25 – Added per-script settings UI with support for <code>text</code> and <code>select</code> fields.
Version 1.2.0 – 2025-05-18 – Activation status now persists across versioned filenames by normalizing keys.
Version 1.1.0 – 2025-05-17 – Added activity logging with timestamp, user, and action history (up to 100 recent entries).
Version 1.0.0 – 2025-05-03 – Initial release with toggle UI, folder-based organization, version parsing, and override support.

== Upgrade Notice ==

= 2.0.1 =
Recommended update for WordPress.org compatibility and improved security compliance. Adds escaping, translation support, and finalizes plugin header for submission.

== Credits ==

Developed by [Life Brand](https://lifebrand.co/)

== License ==

This plugin is licensed under the GPLv2 or later.