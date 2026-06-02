<?php
/**
 * Inline SVG icons — replaces the Bootstrap Icons webfont (~120 KB font +
 * ~80 KB CSS) that was loaded just to render a handful of glyphs.
 *
 * Returns a 24×24, currentColor, stroke-based <svg> sized to 1em so existing
 * font-size rules (.navbar .nav-link .bd-icon, .metadata .bd-icon, …) scale it.
 *
 * Theme-picker icons (sun/moon/snow/droplet/cup/half) are duplicated in
 * blowdit.js so the toggle can swap them client-side; keep the two in sync.
 */
if (!function_exists('blowdit_icon')) {
	function blowdit_icon($name) {
		static $paths = array(
			// --- UI ---
			'calendar'      => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
			'clock'         => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
			'folder'        => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
			'tag'           => '<path d="M20.59 13.41 13.42 20.6a2 2 0 0 1-2.83 0L3 13V3h10l7.59 7.59a2 2 0 0 1 0 2.82z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
			'chevron-left'  => '<path d="M15 18l-6-6 6-6"/>',
			'chevron-right' => '<path d="M9 18l6-6-6-6"/>',
			'house'         => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/>',
			// --- Theme picker (mirror in blowdit.js) ---
			'sun'           => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
			'moon'          => '<path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z"/>',
			'snow'          => '<path d="M12 2v20M2 12h20M5 5l14 14M19 5 5 19"/>',
			'droplet'       => '<path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11z"/>',
			'cup'           => '<path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M17 9h2.5a2.5 2.5 0 0 1 0 5H17"/><path d="M7 2v2M11 2v2M15 2v2"/>',
			'half'          => '<circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor" stroke="none"/>',
		);
		if (!isset($paths[$name])) { return ''; }
		return '<svg class="bd-icon" viewBox="0 0 24 24" width="1em" height="1em" '
			. 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" '
			. 'stroke-linejoin="round" aria-hidden="true">' . $paths[$name] . '</svg>';
	}

	// Bludit already HTML-escapes titles/names on save (Sanitize::html), so a
	// plain htmlspecialchars() in the theme double-encodes ("&" -> "&amp;amp;",
	// shown as "&amp;"). blowdit_text() decodes any existing entities first, then
	// encodes exactly once — correct whether the value is pre-encoded or raw, and
	// always output-safe. blowdit_plain() returns decoded text (for JSON-LD etc.).
	function blowdit_plain($s) {
		return html_entity_decode((string) $s, ENT_QUOTES, 'UTF-8');
	}
	function blowdit_text($s) {
		return htmlspecialchars(blowdit_plain($s), ENT_QUOTES, 'UTF-8');
	}

	// Theme asset URL with an mtime cache-buster (so edited images/SVGs refetch).
	function blowdit_asset($rel) {
		$v = @filemtime(THEME_DIR . $rel);
		return DOMAIN_THEME . $rel . ($v ? '?v=' . $v : '');
	}

	// Map a theme key to its toggle icon name.
	function blowdit_theme_icon($theme) {
		$map = array(
			'light'      => 'sun',
			'dark'       => 'moon',
			'nord'       => 'snow',
			'dracula'    => 'droplet',
			'catppuccin' => 'cup',
		);
		return isset($map[$theme]) ? $map[$theme] : 'half';
	}
}
