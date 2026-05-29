<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="generator" content="Bludit">

<!-- Set the colour theme as early as possible to avoid a flash -->
<script>
	(function () {
		try {
			var stored = localStorage.getItem('blowdit-theme');
			var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
			var theme = stored || (prefersDark ? 'dark' : 'light');
			document.documentElement.setAttribute('data-theme', theme);
		} catch (e) {
			document.documentElement.setAttribute('data-theme', 'light');
		}
	})();
</script>

<!-- Dynamic title tag -->
<?php echo Theme::metaTags('title'); ?>

<!-- Dynamic description tag -->
<?php echo Theme::metaTags('description'); ?>

<!-- Include Favicon -->
<?php echo Theme::favicon('img/spleenftw.jpeg'); ?>

<!-- Inter typeface (Blowfish-like typography) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Include Bootstrap CSS file bootstrap.css -->
<?php echo Theme::cssBootstrap(); ?>

<!-- Include CSS Bootstrap ICONS file from Bludit Core -->
<?php echo Theme::cssBootstrapIcons(); ?>

<!-- Include CSS Styles from this theme -->
<?php echo Theme::css('css/style.css'); ?>

<!-- Load Bludit Plugins: Site head -->
<?php Theme::plugins('siteHead'); ?>
