<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>">
<head>
<?php include(THEME_DIR_PHP.'head.php'); ?>
</head>
<body>

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
		$sidebarSearchHtml = '';
		$sidebarOtherHtml  = '';
		if (isset($plugins['siteSidebar'])) {
			foreach ($plugins['siteSidebar'] as $plugin) {
				$out = $plugin->siteSidebar();
				if (strpos($out, 'plugin-search') !== false) {
					$sidebarSearchHtml .= $out;
				} else {
					$sidebarOtherHtml .= $out;
				}
			}
		}
		// The hero (and the relocated search) only appear on the blog front page.
		$heroVisible = ($WHERE_AM_I === 'home' && Paginator::currentPage() == 1);
	?>

	<!-- Content -->
	<div class="container">
		<div class="row">

			<!-- Blog Posts -->
			<div class="col-md-8">
			<?php
				// Bludit content are pages.
				// Ordered by date, these pages behave like posts.
				//
				// $WHERE_AM_I detects where the visitor is:
				//   "page" -> viewing a single page/post
				//   "home" -> viewing the front page
				if ($WHERE_AM_I == 'page') {
					include(THEME_DIR_PHP.'page.php');
				} else {
					include(THEME_DIR_PHP.'home.php');
				}
			?>
			</div>

			<!-- Right Sidebar -->
			<div class="col-md-3 offset-md-1">
			<?php include(THEME_DIR_PHP.'sidebar.php'); ?>
			</div>

		</div>
	</div>

	<!-- Footer -->
	<?php include(THEME_DIR_PHP.'footer.php'); ?>

	<!-- Javascript -->
	<?php
		// Include Jquery file from Bludit Core
		echo Theme::jquery();

		// Include javascript Bootstrap file from Bludit Core
		echo Theme::jsBootstrap();
	?>

	<!-- Light / Dark theme toggle -->
	<script>
		(function () {
			var STORAGE_KEY = 'blowdit-theme';
			var root = document.documentElement;
			var btn = document.getElementById('theme-toggle');

			function syncIcon(theme) {
				if (!btn) return;
				var icon = btn.querySelector('i');
				if (!icon) return;
				icon.className = (theme === 'dark') ? 'bi bi-sun' : 'bi bi-moon-stars';
			}

			function apply(theme) {
				root.setAttribute('data-theme', theme);
				try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
				syncIcon(theme);
			}

			// Reflect the theme set by the inline head script.
			syncIcon(root.getAttribute('data-theme') || 'light');

			if (btn) {
				btn.addEventListener('click', function () {
					var current = root.getAttribute('data-theme') || 'light';
					apply(current === 'dark' ? 'light' : 'dark');
				});
			}

			// Follow the OS preference unless the visitor chose explicitly.
			if (window.matchMedia) {
				window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
					try {
						if (localStorage.getItem(STORAGE_KEY)) return;
					} catch (err) {}
					apply(e.matches ? 'dark' : 'light');
				});
			}
		})();
	</script>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>
