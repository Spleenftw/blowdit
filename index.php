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
					// Mark the navigation / pages plugin so its list can be
					// shuffled client-side (see the script near the body end).
					if (strpos($out, 'plugin-navigation') !== false || strpos($out, 'plugin-pages') !== false) {
						$out = '<div class="js-random-nav">' . $out . '</div>';
					}
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

	<!-- Theme rotation: light -> dark -> nord -> dracula -->
	<script>
		(function () {
			var STORAGE_KEY = 'blowdit-theme';
			var THEMES = ['light', 'dark', 'nord', 'dracula'];
			var ICONS = {
				light:   'bi-brightness-high',
				dark:    'bi-moon-stars',
				nord:    'bi-snow',
				dracula: 'bi-droplet'
			};
			var root = document.documentElement;
			var btn = document.getElementById('theme-toggle');

			function syncIcon(theme) {
				if (!btn) return;
				var icon = btn.querySelector('i');
				if (!icon) return;
				icon.className = 'bi ' + (ICONS[theme] || 'bi-circle-half');
			}

			function apply(theme) {
				root.setAttribute('data-theme', theme);
				try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
				syncIcon(theme);
			}

			// Reflect the theme set by the inline head script.
			var current = root.getAttribute('data-theme') || 'light';
			if (THEMES.indexOf(current) === -1) current = 'light';
			syncIcon(current);

			if (btn) {
				btn.addEventListener('click', function () {
					var cur = root.getAttribute('data-theme') || 'light';
					var i = THEMES.indexOf(cur);
					apply(THEMES[(i + 1) % THEMES.length]);
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

	<!-- Randomise the order of the navigation / pages list on each load -->
	<script>
		(function () {
			function shuffle(arr) {
				for (var i = arr.length - 1; i > 0; i--) {
					var j = Math.floor(Math.random() * (i + 1));
					var tmp = arr[i]; arr[i] = arr[j]; arr[j] = tmp;
				}
				return arr;
			}
			var lists = document.querySelectorAll('.js-random-nav ul');
			Array.prototype.forEach.call(lists, function (ul) {
				var items = Array.prototype.filter.call(ul.children, function (el) {
					return el.tagName === 'LI';
				});
				shuffle(items).forEach(function (li) { ul.appendChild(li); });
			});
		})();
	</script>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>
