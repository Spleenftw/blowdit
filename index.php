<?php
	// Inline-SVG icon helper (replaces the Bootstrap Icons webfont). Included
	// first so blowdit_icon() is available to head.php and every body template.
	include(THEME_DIR_PHP.'icons.php');

	// Read the theme from a cookie so the root element can be rendered
	// pre-coloured. This paints the page in the theme colour from the first
	// byte of every response, eliminating the white flash between navigations.
	$blowditThemeBg = array(
		'light'      => '#ffffff',
		'dark'       => '#171717',
		'nord'       => '#2e3440',
		'dracula'    => '#282a36',
		'catppuccin' => '#1e1e2e',
	);
	$blowditTheme = (isset($_COOKIE['blowdit-theme']) && isset($blowditThemeBg[$_COOKIE['blowdit-theme']]))
		? $_COOKIE['blowdit-theme']
		: 'light';
	$blowditBg     = $blowditThemeBg[$blowditTheme];
	$blowditScheme = ($blowditTheme === 'light') ? 'light' : 'dark';

	// Pre-compute before <body> so we can stamp the has-toc class on it,
	// which lets CSS align the navbar container with the wider article container.
	$blowditIsArticle  = ($WHERE_AM_I === 'page' && isset($page) && !$page->isStatic() && !$url->notFound());
	$blowditHasHeadings = $blowditIsArticle && (bool) preg_match('/<h[234]/i', $page->content());
?>
<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>" data-theme="<?php echo $blowditTheme ?>" style="background-color: <?php echo $blowditBg ?>; color-scheme: <?php echo $blowditScheme ?>;">
<head>
<?php include(THEME_DIR_PHP.'head.php'); ?>
</head>
<body<?php echo $blowditHasHeadings ? ' class="has-toc"' : ''; ?>>

	<!-- Skip to content (keyboard / screen-reader navigation) -->
	<a class="skip-link" href="#main-content"><?php echo $L->get('Skip to content'); ?></a>

	<!-- Load Bludit Plugins: Site Body Begin -->
	<?php Theme::plugins('siteBodyBegin'); ?>

	<!-- Navbar -->
	<?php include(THEME_DIR_PHP.'navbar.php'); ?>

	<?php
		// Split the siteSidebar plugins into "search" and "everything else".
		// This lets us relocate the search box under the hero on the homepage
		// (Popeye-style) while keeping the other plugins in the right sidebar.
		// Mirrors Bludit's own Theme::plugins() loop, but captures the output.
		global $plugins;
		$sidebarSearchHtml = '';   // search box, rendered under the hero on the homepage
		$sidebarItems = array();   // every sidebar plugin, to be sorted into a fixed order
		$sidebarIndex = 0;
		if (isset($plugins['siteSidebar'])) {
			foreach ($plugins['siteSidebar'] as $plugin) {
				$out = $plugin->siteSidebar();

				$isSearch = (strpos($out, 'plugin-search') !== false);
				$isNav    = (strpos($out, 'plugin-navigation') !== false || strpos($out, 'plugin-pages') !== false);

				// Fixed sidebar order (lower = higher up). Unknown plugins such as
				// the Hit Counter default to 90, i.e. pinned to the bottom.
				$priority = 90;
				if (strpos($out, 'plugin-about') !== false)          $priority = 10; // Blog / About
				elseif ($isSearch)                                   $priority = 20; // Search
				elseif ($isNav)                                      $priority = 30; // Navigation
				elseif (strpos($out, 'plugin-categories') !== false) $priority = 40; // Category

				if ($isSearch) {
					$sidebarSearchHtml .= $out;
				}
				// Mark the navigation / pages plugin so its list can be shuffled
				// client-side (see the script near the body end).
				if ($isNav) {
					$out = '<div class="js-random-nav">' . $out . '</div>';
				}

				$sidebarItems[] = array(
					'priority' => $priority,
					'index'    => $sidebarIndex++,
					'search'   => $isSearch,
					'nav'      => $isNav,
					'html'     => $out,
				);
			}
		}

		// Stable sort by priority, then original order (PHP < 8 usort isn't stable).
		usort($sidebarItems, function ($a, $b) {
			if ($a['priority'] === $b['priority']) {
				return $a['index'] - $b['index'];
			}
			return $a['priority'] - $b['priority'];
		});

		$sidebarFullHtml     = '';   // all plugins, ordered (used on article/page views)
		$sidebarNoSearchHtml = '';   // same, minus search (kept for potential future use)
		$sidebarHomeHtml     = '';   // about + categories + hit counter only (homepage, no nav/search)
		foreach ($sidebarItems as $item) {
			$sidebarFullHtml .= $item['html'];
			if (!$item['search']) {
				$sidebarNoSearchHtml .= $item['html'];
			}
			if (!$item['search'] && !$item['nav']) {
				$sidebarHomeHtml .= $item['html'];
			}
		}

		// The hero (and the relocated search) appear on every paginated home page.
		$heroVisible   = ($WHERE_AM_I === 'home');
		// Reuse the values pre-computed before <body> (avoids calling $page->content() twice).
		$isArticlePage = $blowditIsArticle;
		$hasHeadings   = $blowditHasHeadings;
	?>

	<!-- Content -->
	<div id="main-content" tabindex="-1" class="<?php echo $hasHeadings ? 'container-wide' : 'container'; ?>">
		<div class="row">

			<?php if ($hasHeadings): ?>

			<!-- Left ToC Sidebar — visible on md+ only; hidden on mobile.
			     No align-self-start so the column stretches to article height, enabling sticky. -->
			<div class="col-md-3 d-none d-md-block">
			<?php include(THEME_DIR_PHP.'toc.php'); ?>
			</div>

			<!-- Article Content (narrower when ToC is present) -->
			<div class="col-md-6">
			<?php include(THEME_DIR_PHP.'page.php'); ?>
			</div>

			<?php else: ?>

			<!-- Blog Posts / Page (full width when no ToC) -->
			<div class="col-md-8">
			<?php
				if ($WHERE_AM_I == 'page') {
					include(THEME_DIR_PHP.'page.php');
				} else {
					include(THEME_DIR_PHP.'home.php');
				}
			?>
			</div>

			<?php endif ?>

			<!-- Right Sidebar (align-self-start: card fits its content, doesn't
			     stretch to match the main column's height) -->
			<div class="<?php echo $hasHeadings ? 'col-md-3' : 'col-md-3 offset-md-1'; ?> align-self-start">
			<?php include(THEME_DIR_PHP.'sidebar.php'); ?>
			</div>

		</div>
	</div>

	<!-- Footer -->
	<?php include(THEME_DIR_PHP.'footer.php'); ?>

	<!-- Javascript -->
	<?php
		// jQuery and Bootstrap's JS bundle are intentionally NOT loaded: every
		// interactive piece in this theme is vanilla JS (theme picker, tabs,
		// carousel, lightbox), and the only Bootstrap-JS feature used — the
		// mobile navbar collapse — is reimplemented in blowdit.js. This drops
		// ~115 KB. Re-add Theme::jquery() / Theme::jsBootstrap() here if you
		// install a plugin that depends on them.
	?>

	<!-- Theme behaviour: deferred, cached external bundle.
	     The anti-flash theme bootstrap stays inline in head.php — it must
	     run before the first paint and so cannot be deferred. -->
	<script>
		// Theme directory URL, so the deferred bundle can load self-hosted assets
		// (highlight.js + its theme CSS) relative to the theme.
		window.BLOWDIT_THEME_URL = <?php echo json_encode(DOMAIN_THEME); ?>;
		// Localised UI strings for the deferred bundle. $L->get() falls back to
		// the key itself when a translation is missing, so this is safe in any language.
		window.BLOWDIT_I18N = {
			copy:        <?php echo json_encode($L->get('Copy')); ?>,
			copied:      <?php echo json_encode($L->get('Copied')); ?>,
			copyCode:    <?php echo json_encode($L->get('Copy code')); ?>,
			backToTop:   <?php echo json_encode($L->get('Back to top')); ?>,
			prevSlide:   <?php echo json_encode($L->get('Previous slide')); ?>,
			nextSlide:   <?php echo json_encode($L->get('Next slide')); ?>,
			goToSlide:   <?php echo json_encode($L->get('Go to slide')); ?>,
			prevImage:   <?php echo json_encode($L->get('Previous image')); ?>,
			nextImage:   <?php echo json_encode($L->get('Next image')); ?>,
			imageViewer: <?php echo json_encode($L->get('Image viewer')); ?>
		};
	</script>
	<?php
		// Cache-busted by file mtime (mirrors the style.css approach in head.php).
		$jsVersion = @filemtime(THEME_DIR_JS . 'blowdit.js');
		$jsSrc = DOMAIN_THEME . 'js/blowdit.js' . ($jsVersion ? '?v=' . $jsVersion : '');
	?>
	<script defer src="<?php echo $jsSrc; ?>"></script>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>
