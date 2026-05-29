<?php
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

		// The hero (and the relocated search) only appear on the blog front page.
		$heroVisible   = ($WHERE_AM_I === 'home' && Paginator::currentPage() == 1);
		// Reuse the values pre-computed before <body> (avoids calling $page->content() twice).
		$isArticlePage = $blowditIsArticle;
		$hasHeadings   = $blowditHasHeadings;
	?>

	<!-- Content -->
	<div class="<?php echo $hasHeadings ? 'container-wide' : 'container'; ?>">
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
		// Include Jquery file from Bludit Core
		echo Theme::jquery();

		// Include javascript Bootstrap file from Bludit Core
		echo Theme::jsBootstrap();
	?>

	<!-- Theme picker -->
	<script>
		(function () {
			var STORAGE_KEY = 'blowdit-theme';
			var THEMES = ['light', 'dark', 'nord', 'dracula', 'catppuccin'];
			var ICONS = {
				light:      'bi-brightness-high',
				dark:       'bi-moon-stars',
				nord:       'bi-snow',
				dracula:    'bi-droplet',
				catppuccin: 'bi-cup'
			};
			var root   = document.documentElement;
			var btn    = document.getElementById('theme-toggle');
			var picker = document.getElementById('theme-picker');

			function syncIcon(theme) {
				if (!btn) return;
				var icon = btn.querySelector('i');
				if (!icon) return;
				icon.className = 'bi ' + (ICONS[theme] || 'bi-circle-half');
			}

			function syncSwatches(theme) {
				if (!picker) return;
				Array.prototype.forEach.call(picker.querySelectorAll('.swatch'), function (s) {
					s.classList.toggle('is-active', s.getAttribute('data-pick') === theme);
				});
			}

			function apply(theme) {
				root.setAttribute('data-theme', theme);
				try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
				document.cookie = 'blowdit-theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
				var bg = window.BLOWDIT_THEME_BG || {};
				if (bg[theme]) { root.style.backgroundColor = bg[theme]; }
				root.style.colorScheme = (theme === 'light') ? 'light' : 'dark';
				syncIcon(theme);
				syncSwatches(theme);
			}

			function openPicker() {
				if (!picker) return;
				picker.classList.add('is-open');
				if (btn) btn.setAttribute('aria-expanded', 'true');
			}

			function closePicker() {
				if (!picker) return;
				picker.classList.remove('is-open');
				if (btn) btn.setAttribute('aria-expanded', 'false');
			}

			// Swatch clicks
			if (picker) {
				Array.prototype.forEach.call(picker.querySelectorAll('.swatch'), function (s) {
					s.addEventListener('click', function () {
						apply(s.getAttribute('data-pick'));
						closePicker();
					});
				});
			}

			// Toggle button: open / close picker
			if (btn) {
				btn.addEventListener('click', function (e) {
					e.stopPropagation();
					picker && picker.classList.contains('is-open') ? closePicker() : openPicker();
				});
			}

			// Close on outside click
			document.addEventListener('click', function (e) {
				if (!picker || !picker.classList.contains('is-open')) return;
				if (btn && btn.contains(e.target)) return;
				if (picker.contains(e.target)) return;
				closePicker();
			});

			// Initialise icons + active swatch
			var current = root.getAttribute('data-theme') || 'light';
			if (THEMES.indexOf(current) === -1) current = 'light';
			syncIcon(current);
			syncSwatches(current);

			// Follow OS preference unless the visitor chose explicitly
			if (window.matchMedia) {
				window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
					try { if (localStorage.getItem(STORAGE_KEY)) return; } catch (err) {}
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

	<!-- Table of Contents: generate links from article headings + scroll-spy -->
	<script>
		(function () {
			var tocNav = document.getElementById('toc-nav');
			if (!tocNav) return;

			var content = document.querySelector('.content');
			if (!content) return;

			var headings = content.querySelectorAll('h2, h3, h4');
			if (headings.length === 0) return;

			// Ensure every heading has a stable anchor ID
			var usedIds = {};
			Array.prototype.forEach.call(headings, function (h) {
				if (!h.id) {
					var base = h.textContent.trim().toLowerCase()
						.replace(/[^a-z0-9\s-]/g, '')
						.replace(/\s+/g, '-')
						.replace(/^-+|-+$/g, '') || 'heading';
					var id = base, n = 2;
					while (usedIds[id]) { id = base + '-' + (n++); }
					usedIds[id] = true;
					h.id = id;
				} else {
					usedIds[h.id] = true;
				}
			});

			// Build the list
			var ul = document.createElement('ul');
			ul.className = 'toc-list';
			Array.prototype.forEach.call(headings, function (h) {
				var li = document.createElement('li');
				li.className = 'toc-item toc-' + h.tagName.toLowerCase();
				var a = document.createElement('a');
				a.href = '#' + h.id;
				a.textContent = h.textContent;
				a.className = 'toc-link';
				li.appendChild(a);
				ul.appendChild(li);
			});
			tocNav.appendChild(ul);

			// Scroll-spy: highlight the last heading that has scrolled past the top
			var headingArr = Array.prototype.slice.call(headings);

			function updateActive() {
				var scrollY = window.scrollY || window.pageYOffset;
				var threshold = scrollY + 120; // offset for navbar height
				var active = null;
				headingArr.forEach(function (h) {
					var top = h.getBoundingClientRect().top + scrollY;
					if (top <= threshold) active = h;
				});
				var links = tocNav.querySelectorAll('.toc-link');
				Array.prototype.forEach.call(links, function (a) { a.classList.remove('active'); });
				if (active) {
					var link = tocNav.querySelector('a[href="#' + active.id + '"]');
					if (link) link.classList.add('active');
				}
			}

			window.addEventListener('scroll', updateActive, { passive: true });
			updateActive();
		})();
	</script>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>
