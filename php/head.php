<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="generator" content="Bludit">

<!-- Set the colour theme as early as possible to avoid a white flash.
     Sets data-theme AND paints the html background/color-scheme inline, so the
     very first frame (before style.css loads) already uses the theme colour. -->
<script>
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
		} catch (e) {}
	})();
</script>

<!-- Dynamic title tag -->
<?php echo Theme::metaTags('title'); ?>

<!-- Dynamic description tag -->
<?php echo Theme::metaTags('description'); ?>

<!-- Include Favicon -->
<?php echo Theme::favicon('img/favicon.png'); ?>

<!-- Inter typeface (Blowfish-like typography) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
