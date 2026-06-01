<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="generator" content="Bludit">

<!-- Tint the mobile browser chrome to match the navbar (--chrome-bg per theme).
     Set server-side from the theme cookie so it's correct on the first paint. -->
<?php
	$blowditChromeColors = array(
		'light'      => '#1f1f1f',
		'dark'       => '#1f1f1f',
		'nord'       => '#3b4252',
		'dracula'    => '#343746',
		'catppuccin' => '#313244',
	);
	$blowditChrome = isset($blowditChromeColors[$blowditTheme]) ? $blowditChromeColors[$blowditTheme] : '#1f1f1f';
?>
<meta name="theme-color" content="<?php echo $blowditChrome; ?>">

<!-- Set the colour theme as early as possible to avoid a white flash.
     Sets data-theme AND paints the html background/color-scheme inline, so the
     very first frame (before style.css loads) already uses the theme colour. -->
<script data-cfasync="false">
	window.BLOWDIT_THEME_BG = {
		light:      '#ffffff',
		dark:       '#171717',
		nord:       '#2e3440',
		dracula:    '#282a36',
		catppuccin: '#1e1e2e'
	};
	(function () {
		try {
			var bg = window.BLOWDIT_THEME_BG;
			var stored = localStorage.getItem('blowdit-theme');
			if (!bg[stored]) {
				stored = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
			}
			var root = document.documentElement;
			root.setAttribute('data-theme', stored);
			root.style.colorScheme = (stored === 'light') ? 'light' : 'dark';
			root.style.backgroundColor = bg[stored];
			// Persist to a cookie so PHP can pre-colour the next page server-side.
			document.cookie = 'blowdit-theme=' + stored + '; path=/; max-age=31536000; SameSite=Lax';
		} catch (e) {}
	})();
</script>

<!-- Dynamic title tag -->
<?php echo Theme::metaTags('title'); ?>

<!-- Dynamic description tag -->
<?php echo Theme::metaTags('description'); ?>

<?php
	// ---- Social sharing metadata (Open Graph + Twitter Card) ----
	// $blowditIsArticle is pre-computed in index.php before this file is included.
	$ogIsArticle = !empty($blowditIsArticle);

	// ISO 8601 publish date — formatted explicitly so it's valid regardless of
	// the admin's configured date format. Falls back to dateRaw() if Date is unavailable.
	if (!function_exists('blowdit_iso_date')) {
		function blowdit_iso_date($page, $field) {
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
		$ogPublishedIso = blowdit_iso_date($page, 'date');
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
<!-- Open Graph -->
<meta property="og:type" content="<?php echo htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:site_name" content="<?php echo htmlspecialchars($ogSiteName, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($ogDesc, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($ogIsArticle): ?>
<meta property="article:published_time" content="<?php echo htmlspecialchars($ogPublishedIso, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="article:author" content="<?php echo htmlspecialchars($page->username(), ENT_QUOTES, 'UTF-8'); ?>">
<?php endif ?>
<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($ogDesc, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">

<?php
	// ---- JSON-LD structured data (BlogPosting) — articles only ----
	if ($ogIsArticle) {
		$ldDescription = $page->description() ?: ($site->description() ?: $site->slogan());
		$ldImage       = $page->coverImage(true) ?: (DOMAIN_THEME . 'img/spleenftw.jpeg');
		$ldPublished   = $ogPublishedIso;

		// dateModified: fall back to published date when not tracked.
		$ldModified = blowdit_iso_date($page, 'dateModified');
		if (!$ldModified) { $ldModified = $ldPublished; }

		// timeRequired as an ISO 8601 duration (e.g. "PT8M") parsed from readingTime().
		$ldTimeRequired = '';
		if (preg_match('/\d+/', $page->readingTime(), $rtMatch)) {
			$ldTimeRequired = 'PT' . $rtMatch[0] . 'M';
		}

		$jsonLd = array(
			'@context' => 'https://schema.org',
			'@type'    => 'BlogPosting',
			'headline' => $page->title(),
			'description' => $ldDescription,
			'image'    => $ldImage,
			'datePublished' => $ldPublished,
			'dateModified'  => $ldModified,
			'author'   => array(
				'@type' => 'Person',
				'name'  => $page->username(),
				'url'   => Theme::siteUrl(),
			),
			'publisher' => array(
				'@type' => 'Organization',
				'name'  => $site->title(),
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

		echo '<script type="application/ld+json">'
			. json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
			. '</script>' . "\n";
	}
?>

<!-- Include Favicon -->
<?php echo Theme::favicon('img/favicon.png'); ?>

<!-- Inter typeface (Blowfish-like typography) — loaded non-render-blocking:
     fetched as print media, then flipped to all once it arrives. -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>

<!-- Include Bootstrap CSS file bootstrap.css -->
<?php echo Theme::cssBootstrap(); ?>

<!-- Include CSS Bootstrap ICONS file from Bludit Core -->
<?php echo Theme::cssBootstrapIcons(); ?>

<!-- Include CSS Styles from this theme (cache-busted by file mtime) -->
<?php
	$styleVersion = @filemtime(THEME_DIR_CSS . 'style.css');
	$styleHref = DOMAIN_THEME . 'css/style.css' . ($styleVersion ? '?v=' . $styleVersion : '');
?>
<link rel="stylesheet" type="text/css" href="<?php echo $styleHref; ?>">

<!-- Load Bludit Plugins: Site head -->
<?php Theme::plugins('siteHead'); ?>
