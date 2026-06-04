<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="generator" content="Bludit">

<!-- Tint the mobile browser chrome to match the navbar (--chrome-bg per theme).
     Set server-side from the theme cookie so it's correct on the first paint. -->
<?php
	$blowfishChromeColors = array(
		'light'      => '#1f1f1f',
		'dark'       => '#1f1f1f',
		'nord'       => '#3b4252',
		'dracula'    => '#343746',
		'catppuccin' => '#313244',
	);
	$blowfishChrome = isset($blowfishChromeColors[$blowfishTheme]) ? $blowfishChromeColors[$blowfishTheme] : '#1f1f1f';
?>
<meta name="theme-color" content="<?php echo $blowfishChrome; ?>">

<?php if (!empty($blowfishIsArticle) && $page->coverImage()): ?>
<!-- Preload the article cover image (typically the LCP element) -->
<link rel="preload" as="image" href="<?php echo htmlspecialchars($page->coverImage(true), ENT_QUOTES, 'UTF-8'); ?>" fetchpriority="high">
<?php endif ?>

<?php if (!empty($blowfishIsArticle)): ?>
<!-- Warm up connections to article third-parties (comments + tweet embeds) -->
<link rel="preconnect" href="https://utteranc.es" crossorigin>
<link rel="dns-prefetch" href="https://platform.twitter.com">
<link rel="dns-prefetch" href="https://cdn.syndication.twimg.com">
<?php endif ?>

<!-- Set the colour theme as early as possible to avoid a white flash.
     Sets data-theme AND paints the html background/color-scheme inline, so the
     very first frame (before style.css loads) already uses the theme colour. -->
<script data-cfasync="false">
	window.BLOWFISH_THEME_BG = {
		light:      '#ffffff',
		dark:       '#171717',
		nord:       '#2e3440',
		dracula:    '#282a36',
		catppuccin: '#1e1e2e'
	};
	(function () {
		try {
			var bg = window.BLOWFISH_THEME_BG;
			var stored = localStorage.getItem('blowfish-theme');
			if (!bg[stored]) {
				stored = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
			}
			var root = document.documentElement;
			root.setAttribute('data-theme', stored);
			root.style.colorScheme = (stored === 'light') ? 'light' : 'dark';
			root.style.backgroundColor = bg[stored];
			// Persist to a cookie so PHP can pre-colour the next page server-side.
			document.cookie = 'blowfish-theme=' + stored + '; path=/; max-age=31536000; SameSite=Lax';
		} catch (e) {}
	})();
</script>

<!-- Dynamic title tag -->
<?php echo Theme::metaTags('title'); ?>

<!-- Dynamic description tag -->
<?php echo Theme::metaTags('description'); ?>

<?php
	// ---- Social sharing metadata (Open Graph + Twitter Card) ----
	// $blowfishIsArticle is pre-computed in index.php before this file is included.
	$ogIsArticle = !empty($blowfishIsArticle);

	// ISO 8601 publish date — formatted explicitly so it's valid regardless of
	// the admin's configured date format. Falls back to dateRaw() if Date is unavailable.
	if (!function_exists('blowfish_iso_date')) {
		function blowfish_iso_date($page, $field) {
			$raw = $page->getValue($field);
			if (!$raw) { return ''; }
			if (class_exists('Date')) {
				$fmt = Date::format($raw, DB_DATE_FORMAT, 'c');
				if ($fmt) { return $fmt; }
			}
			return $raw;
		}
	}

	if ($ogIsArticle) {
		$ogTitle = $page->title();
		$ogDesc  = $page->description();
		$ogUrl   = $page->permalink(true);
		$ogImage = $page->coverImage(true);
		$ogPublishedIso = blowfish_iso_date($page, 'date');
	} else {
		$ogTitle = $site->title();
		$ogDesc  = '';
		$ogUrl   = Theme::siteUrl();
		$ogImage = '';
	}
	// Fallbacks
	if (!$ogDesc)  { $ogDesc  = $site->description() ?: $site->slogan(); }
	if (!$ogImage) { $ogImage = DOMAIN_THEME . 'img/spleenftw.jpeg'; }

	$ogType     = $ogIsArticle ? 'article' : 'website';
	$ogSiteName = $site->title();
?>
<!-- Canonical URL -->
<link rel="canonical" href="<?php echo htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8'); ?>">

<!-- Open Graph -->
<meta property="og:type" content="<?php echo htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:site_name" content="<?php echo blowfish_text($ogSiteName); ?>">
<meta property="og:title" content="<?php echo blowfish_text($ogTitle); ?>">
<meta property="og:description" content="<?php echo blowfish_text($ogDesc); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image:alt" content="<?php echo blowfish_text($ogTitle); ?>">
<meta property="og:locale" content="<?php echo htmlspecialchars(str_replace('-', '_', Theme::lang()), ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($ogIsArticle): ?>
<meta property="article:published_time" content="<?php echo htmlspecialchars($ogPublishedIso, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="article:author" content="<?php echo blowfish_text($page->username()); ?>">
<?php endif ?>
<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<?php if (!empty($blowfishTwitterHandle)): ?>
<meta name="twitter:site" content="<?php echo htmlspecialchars($blowfishTwitterHandle, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:creator" content="<?php echo htmlspecialchars($blowfishTwitterHandle, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif ?>
<meta name="twitter:title" content="<?php echo blowfish_text($ogTitle); ?>">
<meta name="twitter:description" content="<?php echo blowfish_text($ogDesc); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image:alt" content="<?php echo blowfish_text($ogTitle); ?>">

<?php
	// ---- JSON-LD structured data ----
	$blowfishEmitLd = function ($doc) {
		echo '<script type="application/ld+json">'
			. json_encode($doc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
			. '</script>' . "\n";
	};

	// Social profile URLs, used as schema.org "sameAs".
	$blowfishSameAs = array();
	foreach (Theme::socialNetworks() as $snKey => $snLabel) {
		$snUrl = $site->{$snKey}();
		if (!empty($snUrl)) { $blowfishSameAs[] = $snUrl; }
	}

	// WebSite (every page) — declares the site + a sitelinks search box.
	$siteLd = array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'name'     => blowfish_plain($site->title()),
		'url'      => Theme::siteUrl(),
	);
	$siteDesc = $site->description() ?: $site->slogan();
	if ($siteDesc) { $siteLd['description'] = blowfish_plain($siteDesc); }
	$siteLd['potentialAction'] = array(
		'@type'  => 'SearchAction',
		'target' => array(
			'@type'       => 'EntryPoint',
			'urlTemplate' => rtrim(Theme::siteUrl(), '/') . '/search/{search_term_string}',
		),
		'query-input' => 'required name=search_term_string',
	);
	$blowfishEmitLd($siteLd);

	// BlogPosting + BreadcrumbList — articles only.
	if ($ogIsArticle) {
		$ldDescription = $page->description() ?: ($site->description() ?: $site->slogan());
		$ldImage       = $page->coverImage(true) ?: (DOMAIN_THEME . 'img/spleenftw.jpeg');
		$ldPublished   = $ogPublishedIso;

		// dateModified: fall back to published date when not tracked.
		$ldModified = blowfish_iso_date($page, 'dateModified');
		if (!$ldModified) { $ldModified = $ldPublished; }

		// timeRequired as an ISO 8601 duration (e.g. "PT8M") parsed from readingTime().
		$ldTimeRequired = '';
		if (preg_match('/\d+/', $page->readingTime(), $rtMatch)) {
			$ldTimeRequired = 'PT' . $rtMatch[0] . 'M';
		}

		$author = array(
			'@type' => 'Person',
			'name'  => blowfish_plain($page->username()),
			'url'   => Theme::siteUrl(),
		);
		if (!empty($blowfishSameAs)) { $author['sameAs'] = $blowfishSameAs; }

		$jsonLd = array(
			'@context' => 'https://schema.org',
			'@type'    => 'BlogPosting',
			'headline' => blowfish_plain($page->title()),
			'description' => blowfish_plain($ldDescription),
			'image'    => $ldImage,
			'datePublished' => $ldPublished,
			'dateModified'  => $ldModified,
			'author'   => $author,
			'publisher' => array(
				'@type' => 'Organization',
				'name'  => blowfish_plain($site->title()),
				'logo'  => array(
					'@type' => 'ImageObject',
					'url'   => DOMAIN_THEME . 'img/favicon.png',
				),
			),
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id'   => $page->permalink(true),
			),
			'wordCount' => str_word_count(strip_tags($page->content())),
		);
		if ($ldTimeRequired) { $jsonLd['timeRequired'] = $ldTimeRequired; }
		$blowfishEmitLd($jsonLd);

		// Breadcrumb: Home > [Category] > Article
		$crumbs = array(array(
			'@type' => 'ListItem', 'position' => 1,
			'name'  => blowfish_plain($site->title()), 'item' => Theme::siteUrl(),
		));
		$pos = 2;
		if ($page->categoryKey()) {
			$crumbs[] = array(
				'@type' => 'ListItem', 'position' => $pos++,
				'name'  => blowfish_plain($page->category()), 'item' => $page->categoryPermalink(),
			);
		}
		$crumbs[] = array(
			'@type' => 'ListItem', 'position' => $pos,
			'name'  => blowfish_plain($page->title()), 'item' => $page->permalink(true),
		);
		$blowfishEmitLd(array(
			'@context' => 'https://schema.org',
			'@type'    => 'BreadcrumbList',
			'itemListElement' => $crumbs,
		));
	}
?>

<!-- RSS feed auto-discovery (RSS plugin serves /rss.xml) -->
<link rel="alternate" type="application/rss+xml" title="<?php echo blowfish_text($site->title()); ?>" href="<?php echo rtrim(Theme::siteUrl(), '/') . '/rss.xml'; ?>">

<!-- Include Favicon -->
<?php echo Theme::favicon('img/favicon.png'); ?>

<!-- PWA web app manifest (installable; uses theme-color above) -->
<link rel="manifest" href="<?php echo DOMAIN_THEME . 'manifest.webmanifest'; ?>">
<link rel="apple-touch-icon" href="<?php echo DOMAIN_THEME . 'img/favicon.png'; ?>">

<!-- Inter typeface — self-hosted (see @font-face in style.css). No third-party
     request; preload the body weight for a faster first text paint. -->
<link rel="preload" as="font" type="font/woff2" href="<?php echo DOMAIN_THEME . 'fonts/inter-400.woff2'; ?>" crossorigin>

<!-- Include Bootstrap CSS file bootstrap.css -->
<?php echo Theme::cssBootstrap(); ?>

<!-- Bootstrap Icons webfont removed: the few icons used are inline SVGs
     (see php/icons.php + blowfish.js), saving ~120 KB font + ~80 KB CSS. -->

<!-- Include CSS Styles from this theme (cache-busted by file mtime) -->
<?php
	$styleVersion = @filemtime(THEME_DIR_CSS . 'style.css');
	$styleHref = DOMAIN_THEME . 'css/style.css' . ($styleVersion ? '?v=' . $styleVersion : '');
?>
<link rel="stylesheet" type="text/css" href="<?php echo $styleHref; ?>">

<!-- Load Bludit Plugins: Site head -->
<?php Theme::plugins('siteHead'); ?>
