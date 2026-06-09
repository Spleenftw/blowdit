/* ============================================================
   BLOWFISH — theme behaviour
   Deferred, cacheable bundle of all client-side enhancements.
   Loaded with <script defer> from index.php, so the DOM is fully
   parsed before any of these IIFEs run (no DOMContentLoaded needed).

   NOTE: the anti-flash theme bootstrap stays inline in head.php —
   it must run before the first paint and cannot be deferred.
   ============================================================ */
(function () {
	'use strict';

	// Localised UI strings (overridable via window.BLOWFISH_I18N, set in index.php).
	var I18N = window.BLOWFISH_I18N || {};
	var T = {
		copy:        I18N.copy        || 'Copy',
		copied:      I18N.copied      || 'Copied',
		copyCode:    I18N.copyCode    || 'Copy code',
		backToTop:   I18N.backToTop   || 'Back to top',
		prevSlide:   I18N.prevSlide   || 'Previous slide',
		nextSlide:   I18N.nextSlide   || 'Next slide',
		goToSlide:   I18N.goToSlide   || 'Go to slide',
		prevImage:   I18N.prevImage   || 'Previous image',
		nextImage:   I18N.nextImage   || 'Next image',
		imageViewer: I18N.imageViewer || 'Image viewer',
		onThisPage:  I18N.onThisPage  || 'On this page',
		search:           I18N.search           || 'Search',
		searchPlaceholder: I18N.searchPlaceholder || 'Search posts…',
		noResults:        I18N.noResults        || 'No results'
	};

	// Clipboard helper (Clipboard API with a legacy fallback).
	function copyToClipboard(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text);
		}
		return new Promise(function (resolve, reject) {
			try {
				var ta = document.createElement('textarea');
				ta.value = text;
				ta.style.position = 'fixed';
				ta.style.opacity = '0';
				document.body.appendChild(ta);
				ta.select();
				document.execCommand('copy');
				document.body.removeChild(ta);
				resolve();
			} catch (e) { reject(e); }
		});
	}

	/* --------------------------------------------------------
	   Mobile navbar toggle (replaces Bootstrap's collapse JS)
	   Toggles the .show class that Bootstrap's CSS already styles.
	   -------------------------------------------------------- */
	(function () {
		var toggler = document.querySelector('.navbar-toggler');
		var target  = document.getElementById('navbarResponsive');
		if (!toggler || !target) return;

		function setOpen(open) {
			target.classList.toggle('show', open);
			toggler.classList.toggle('collapsed', !open);
			toggler.setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		toggler.addEventListener('click', function () {
			setOpen(!target.classList.contains('show'));
		});

		// Collapse the menu after tapping a link (mobile)
		Array.prototype.forEach.call(target.querySelectorAll('.nav-link'), function (link) {
			link.addEventListener('click', function () {
				if (target.classList.contains('show')) setOpen(false);
			});
		});
	})();

	/* --------------------------------------------------------
	   Theme picker
	   -------------------------------------------------------- */
	(function () {
		var STORAGE_KEY = 'blowfish-theme';
		var THEMES = ['light', 'dark', 'nord', 'dracula', 'catppuccin'];
		// Inline-SVG icons — mirror of php/icons.php (keep the two in sync).
		function svg(inner) {
			return '<svg class="bd-icon" viewBox="0 0 24 24" width="1em" height="1em" ' +
				'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" ' +
				'stroke-linejoin="round" aria-hidden="true">' + inner + '</svg>';
		}
		var ICONS = {
			light:      svg('<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>'),
			dark:       svg('<path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z"/>'),
			nord:       svg('<path d="M12 2v20M2 12h20M5 5l14 14M19 5 5 19"/>'),
			dracula:    svg('<path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11z"/>'),
			catppuccin: svg('<path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M17 9h2.5a2.5 2.5 0 0 1 0 5H17"/><path d="M7 2v2M11 2v2M15 2v2"/>')
		};
		var ICON_FALLBACK = svg('<circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor" stroke="none"/>');
		var root   = document.documentElement;
		var btn    = document.getElementById('theme-toggle');
		var picker = document.getElementById('theme-picker');

		function syncIcon(theme) {
			if (!btn) return;
			btn.innerHTML = ICONS[theme] || ICON_FALLBACK;
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
			document.cookie = 'blowfish-theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
			var bg = window.BLOWFISH_THEME_BG || {};
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

	/* --------------------------------------------------------
	   Sidebar nav: show a random selection of links on each load.
	   The plugin renders the full list (set its "amount" high); we shuffle it
	   and reveal only SHOW_COUNT items — so it rotates through ALL articles
	   while keeping the sidebar short. All links stay in the DOM (good for
	   internal linking/crawl); the extras are just visually hidden.
	   -------------------------------------------------------- */
	(function () {
		var SHOW_COUNT = 3; // how many random links to display; change to taste

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
			shuffle(items);
			items.forEach(function (li, i) {
				ul.appendChild(li);                       // apply shuffled order
				li.style.display = (i < SHOW_COUNT) ? '' : 'none'; // reveal only the first N
			});
		});
	})();

	/* --------------------------------------------------------
	   Table of Contents: generate links from article headings + scroll-spy
	   -------------------------------------------------------- */
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

	/* --------------------------------------------------------
	   Tabbed code blocks: transforms ```tabs fences into a tab UI
	   -------------------------------------------------------- */
	(function () {
		var tabGroupSeq = 0;
		var TAB_COPY = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" ' +
			'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ' +
			'aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/>' +
			'<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>';
		var TAB_CHECK = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" ' +
			'stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" ' +
			'aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
		// Match both "language-tabs" (Parsedown >= 1.7) and bare "tabs" (older Bludit builds)
		var blocks = document.querySelectorAll(
			'pre > code.language-tabs, pre > code.tabs'
		);
		Array.prototype.forEach.call(blocks, function (code) {
			var pre  = code.parentNode;
			var raw  = code.textContent;

			// Supports "[Name]" and "@tab Name" as tab headers
			var tabs = [];
			var current = null;
			raw.split('\n').forEach(function (line) {
				line = line.replace(/\r$/, ''); // strip Windows CR
				var m = line.match(/^\[([^\]]+)\]\s*$/) ||
				        line.match(/^@tab\s+(.+?)\s*$/);
				if (m) {
					current = { name: m[1], lines: [] };
					tabs.push(current);
				} else if (current) {
					current.lines.push(line);
				}
			});
			if (!tabs.length) return;

			// Trim leading / trailing blank lines from each tab
			tabs.forEach(function (t) {
				while (t.lines.length && !t.lines[0].trim())            t.lines.shift();
				while (t.lines.length && !t.lines[t.lines.length - 1].trim()) t.lines.pop();
			});

			// Build the tab group
			var groupId = 'tabgroup-' + (++tabGroupSeq);
			var group = document.createElement('div');
			group.className = 'tab-group';

			var list = document.createElement('div');
			list.className = 'tab-list';
			list.setAttribute('role', 'tablist');
			group.appendChild(list);

			var panels = [];
			tabs.forEach(function (t, i) {
				var active = (i === 0);
				var tabId   = groupId + '-tab-' + i;
				var panelId = groupId + '-panel-' + i;

				// Tab button
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'tab-btn' + (active ? ' is-active' : '');
				btn.id = tabId;
				btn.setAttribute('role', 'tab');
				btn.setAttribute('aria-selected', active ? 'true' : 'false');
				btn.setAttribute('aria-controls', panelId);
				btn.setAttribute('tabindex', active ? '0' : '-1');
				btn.textContent = t.name;
				list.appendChild(btn);

				// Tab panel
				var panel = document.createElement('div');
				panel.className = 'tab-panel' + (active ? ' is-active' : '');
				panel.id = panelId;
				panel.setAttribute('role', 'tabpanel');
				panel.setAttribute('aria-labelledby', tabId);
				panel.setAttribute('tabindex', '0');
				if (!active) panel.hidden = true;
				var panelPre  = document.createElement('pre');
				var panelCode = document.createElement('code');
				var panelSrc  = t.lines.join('\n');
				panelCode.textContent = panelSrc;
				panelPre.appendChild(panelCode);
				panel.appendChild(panelPre);
				group.appendChild(panel);
				panels.push({ btn: btn, panel: panel, src: panelSrc });

				function activate() {
					panels.forEach(function (p) {
						p.btn.classList.remove('is-active');
						p.btn.setAttribute('aria-selected', 'false');
						p.btn.setAttribute('tabindex', '-1');
						p.panel.classList.remove('is-active');
						p.panel.hidden = true;
					});
					btn.classList.add('is-active');
					btn.setAttribute('aria-selected', 'true');
					btn.setAttribute('tabindex', '0');
					panel.classList.add('is-active');
					panel.hidden = false;
				}

				btn.addEventListener('click', activate);
				btn.addEventListener('keydown', function (e) {
					var nextIdx = null;
					if (e.key === 'ArrowRight') nextIdx = (i + 1) % panels.length;
					else if (e.key === 'ArrowLeft') nextIdx = (i - 1 + panels.length) % panels.length;
					if (nextIdx !== null) {
						e.preventDefault();
						panels[nextIdx].btn.click();
						panels[nextIdx].btn.focus();
					}
				});
			});

			// One copy button in the tab bar — copies whichever tab is active.
			var tabCopy = document.createElement('button');
			tabCopy.type = 'button';
			tabCopy.className = 'tab-copy';
			tabCopy.setAttribute('aria-label', T.copyCode);
			tabCopy.innerHTML = TAB_COPY + '<span class="tab-copy-label">' + T.copy + '</span>';
			list.appendChild(tabCopy);

			var tabCopyTimer = null;
			tabCopy.addEventListener('click', function () {
				var src = '';
				for (var pi = 0; pi < panels.length; pi++) {
					if (panels[pi].panel.classList.contains('is-active')) { src = panels[pi].src; break; }
				}
				copyToClipboard(src).then(function () {
					tabCopy.classList.add('is-copied');
					tabCopy.innerHTML = TAB_CHECK + '<span class="tab-copy-label">' + T.copied + '</span>';
					clearTimeout(tabCopyTimer);
					tabCopyTimer = setTimeout(function () {
						tabCopy.classList.remove('is-copied');
						tabCopy.innerHTML = TAB_COPY + '<span class="tab-copy-label">' + T.copy + '</span>';
					}, 1200);
				}).catch(function () {});
			});

			pre.parentNode.replaceChild(group, pre);
		});
	})();

	/* --------------------------------------------------------
	   Image carousel: transforms ```carousel fences into a slideshow
	   -------------------------------------------------------- */
	(function () {
		var blocks = document.querySelectorAll('pre > code.language-carousel');
		Array.prototype.forEach.call(blocks, function (code) {
			var pre = code.parentNode;

			// Accepts "![alt](url)", "url | caption", or bare "url"
			var slides = [];
			code.textContent.split('\n').forEach(function (line) {
				line = line.replace(/\r$/, '').trim();
				if (!line) return;
				// Markdown image syntax: ![alt text](url)
				var md = line.match(/^!\[([^\]]*)\]\(([^)]+)\)\s*$/);
				if (md) {
					slides.push({ url: md[2].trim(), caption: md[1].trim() });
					return;
				}
				// Plain: "url | caption" or bare url
				var parts = line.split('|');
				var url = parts[0].trim();
				if (url) slides.push({ url: url, caption: (parts[1] || '').trim() });
			});
			if (!slides.length) return;

			var car   = document.createElement('div');
			car.className = 'carousel';
			car.setAttribute('role', 'group');
			car.setAttribute('aria-roledescription', 'carousel');

			var track = document.createElement('div');
			track.className = 'carousel-track';
			car.appendChild(track);

			var slideEls = [];
			var dotEls   = [];
			var current  = 0;

			slides.forEach(function (s, i) {
				var slide = document.createElement('div');
				slide.className = 'carousel-slide' + (i === 0 ? ' is-active' : '');
				var img = document.createElement('img');
				img.src = s.url;
				img.alt = s.caption;
				img.loading = 'lazy';
				slide.appendChild(img);
				if (s.caption) {
					var cap = document.createElement('p');
					cap.className = 'carousel-caption';
					cap.textContent = s.caption;
					slide.appendChild(cap);
				}
				track.appendChild(slide);
				slideEls.push(slide);
			});

			function goTo(idx) {
				slideEls[current].classList.remove('is-active');
				if (dotEls[current]) dotEls[current].classList.remove('is-active');
				current = (idx + slides.length) % slides.length;
				slideEls[current].classList.add('is-active');
				if (dotEls[current]) dotEls[current].classList.add('is-active');
			}

			if (slides.length > 1) {
				var prev = document.createElement('button');
				prev.type = 'button'; prev.className = 'carousel-arrow carousel-prev';
				prev.setAttribute('aria-label', T.prevSlide);
				prev.textContent = '‹';
				prev.addEventListener('click', function () { goTo(current - 1); });
				car.appendChild(prev);

				var next = document.createElement('button');
				next.type = 'button'; next.className = 'carousel-arrow carousel-next';
				next.setAttribute('aria-label', T.nextSlide);
				next.textContent = '›';
				next.addEventListener('click', function () { goTo(current + 1); });
				car.appendChild(next);

				var dots = document.createElement('div');
				dots.className = 'carousel-dots';
				slides.forEach(function (_, i) {
					var dot = document.createElement('button');
					dot.type = 'button';
					dot.className = 'carousel-dot' + (i === 0 ? ' is-active' : '');
					dot.setAttribute('aria-label', T.goToSlide + ' ' + (i + 1));
					dot.addEventListener('click', function () { goTo(i); });
					dots.appendChild(dot);
					dotEls.push(dot);
				});
				car.appendChild(dots);

				// Swipe support
				var startX = 0;
				track.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
				track.addEventListener('touchend', function (e) {
					var dx = e.changedTouches[0].clientX - startX;
					if (Math.abs(dx) > 40) goTo(dx < 0 ? current + 1 : current - 1);
				}, { passive: true });
			}

			pre.parentNode.replaceChild(car, pre);
		});
	})();

	/* --------------------------------------------------------
	   Lightbox: click any article image to zoom; carousel-aware prev/next
	   -------------------------------------------------------- */
	(function () {
		var content = document.querySelector('.content');
		if (!content) return;

		var images = content.querySelectorAll('img');
		if (!images.length) return;

		// Build overlay with optional prev/next arrows for carousel galleries
		var overlay = document.createElement('div');
		overlay.className = 'lightbox-overlay';
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.setAttribute('aria-label', T.imageViewer);

		var prevBtn = document.createElement('button');
		prevBtn.type = 'button';
		prevBtn.className = 'lightbox-arrow lightbox-prev';
		prevBtn.setAttribute('aria-label', T.prevImage);
		prevBtn.textContent = '‹'; // ‹

		var zoomed = document.createElement('img');

		var nextBtn = document.createElement('button');
		nextBtn.type = 'button';
		nextBtn.className = 'lightbox-arrow lightbox-next';
		nextBtn.setAttribute('aria-label', T.nextImage);
		nextBtn.textContent = '›'; // ›

		overlay.appendChild(prevBtn);
		overlay.appendChild(zoomed);
		overlay.appendChild(nextBtn);
		document.body.appendChild(overlay);

		var gallery = [];   // images in the current carousel (empty = single image)
		var galIdx  = 0;

		function showAt(idx) {
			galIdx = (idx + gallery.length) % gallery.length;
			zoomed.src = gallery[galIdx].src;
			zoomed.alt = gallery[galIdx].alt || '';
		}

		function open(el) {
			// If the image lives inside a carousel, collect all slides for navigation
			var car = el.closest ? el.closest('.carousel') : null;
			if (car) {
				gallery = Array.prototype.slice.call(car.querySelectorAll('.carousel-slide img'));
				galIdx  = gallery.indexOf(el);
				if (galIdx === -1) galIdx = 0;
			} else {
				gallery = [el];
				galIdx  = 0;
			}
			var hasMultiple = gallery.length > 1;
			prevBtn.style.display = hasMultiple ? '' : 'none';
			nextBtn.style.display = hasMultiple ? '' : 'none';
			zoomed.src = el.src;
			zoomed.alt = el.alt || '';
			overlay.classList.add('is-open');
			document.body.style.overflow = 'hidden';
		}

		function close() {
			overlay.classList.remove('is-open');
			document.body.style.overflow = '';
		}

		prevBtn.addEventListener('click', function (e) {
			e.stopPropagation();
			showAt(galIdx - 1);
		});
		nextBtn.addEventListener('click', function (e) {
			e.stopPropagation();
			showAt(galIdx + 1);
		});

		function wrapImage(el) {
			if (el.naturalWidth > 0 && el.naturalWidth < 100) return;
			if (el.naturalHeight > 0 && el.naturalHeight < 100) return;
			var wrap = document.createElement('span');
			wrap.className = 'zoomable-wrap';
			el.parentNode.insertBefore(wrap, el);
			wrap.appendChild(el);
			el.addEventListener('click', function () { open(el); });
		}

		Array.prototype.forEach.call(images, function (el) {
			if (el.complete && el.naturalWidth) {
				wrapImage(el);
			} else {
				el.addEventListener('load', function () { wrapImage(el); });
			}
		});

		// Close on backdrop click (not on arrows or image)
		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) close();
		});

		document.addEventListener('keydown', function (e) {
			if (!overlay.classList.contains('is-open')) return;
			if (e.key === 'Escape' || e.key === 'Esc')   close();
			if (e.key === 'ArrowLeft'  && gallery.length > 1) showAt(galIdx - 1);
			if (e.key === 'ArrowRight' && gallery.length > 1) showAt(galIdx + 1);
		});
	})();

	/* --------------------------------------------------------
	   Copy-to-clipboard buttons on code blocks
	   Runs after the tabs/carousel transforms so it only targets real
	   code blocks (including the generated tab panels), not raw fences.
	   -------------------------------------------------------- */
	(function () {
		function copyText(text) {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				return navigator.clipboard.writeText(text);
			}
			// Fallback for older / non-secure-context browsers
			return new Promise(function (resolve, reject) {
				try {
					var ta = document.createElement('textarea');
					ta.value = text;
					ta.style.position = 'fixed';
					ta.style.opacity = '0';
					document.body.appendChild(ta);
					ta.select();
					document.execCommand('copy');
					document.body.removeChild(ta);
					resolve();
				} catch (e) { reject(e); }
			});
		}

		// Inline SVGs — centred in their own viewBox.
		var SVG_COPY = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" ' +
			'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ' +
			'aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/>' +
			'<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>';
		var SVG_CHECK = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" ' +
			'stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" ' +
			'aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';

		// Pretty display names for common language classes.
		var LANG = {
			js: 'JavaScript', javascript: 'JavaScript', ts: 'TypeScript', typescript: 'TypeScript',
			json: 'JSON', html: 'HTML', xml: 'XML', css: 'CSS', scss: 'SCSS', bash: 'Bash',
			sh: 'Shell', shell: 'Shell', zsh: 'Zsh', console: 'Shell', powershell: 'PowerShell',
			ps1: 'PowerShell', python: 'Python', py: 'Python', php: 'PHP', go: 'Go', golang: 'Go',
			rust: 'Rust', rs: 'Rust', c: 'C', cpp: 'C++', csharp: 'C#', cs: 'C#', java: 'Java',
			kotlin: 'Kotlin', ruby: 'Ruby', rb: 'Ruby', sql: 'SQL', yaml: 'YAML', yml: 'YAML',
			toml: 'TOML', ini: 'INI', dockerfile: 'Dockerfile', docker: 'Dockerfile', nginx: 'Nginx',
			apache: 'Apache', diff: 'Diff', md: 'Markdown', markdown: 'Markdown', text: 'Text',
			plaintext: 'Text'
		};

		function langName(code) {
			if (!code) return '';
			var m = (code.className || '').match(/language-([\w-]+)/i);
			if (!m) return '';
			var key = m[1].toLowerCase();
			if (key === 'tabs' || key === 'carousel') return ''; // theme fences, not languages
			return LANG[key] || (key.charAt(0).toUpperCase() + key.slice(1));
		}

		function enhance(pre) {
			// Skip if already wrapped
			if (pre.parentNode && pre.parentNode.classList &&
			    pre.parentNode.classList.contains('code-block')) return;

			var code = pre.querySelector('code');
			// Capture now — before highlighting/line-numbers rewrite the DOM (the
			// line-numbers table would otherwise mangle textContent on copy).
			var sourceText = (code ? code.textContent : pre.textContent) || '';

			var wrap = document.createElement('div');
			wrap.className = 'code-block';
			pre.parentNode.insertBefore(wrap, pre);
			wrap.appendChild(pre);

			// Header bar: language label (left) + copy button (right)
			var header = document.createElement('div');
			header.className = 'code-header';

			var lang = document.createElement('span');
			lang.className = 'code-lang';
			lang.textContent = langName(code);
			header.appendChild(lang);

			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'code-copy';
			btn.setAttribute('aria-label', T.copyCode);
			btn.innerHTML = SVG_COPY + '<span class="code-copy-label">' + T.copy + '</span>';
			header.appendChild(btn);

			wrap.insertBefore(header, pre);

			var resetTimer = null;
			btn.addEventListener('click', function () {
				copyText(sourceText).then(function () {
					btn.classList.add('is-copied');
					btn.innerHTML = SVG_CHECK + '<span class="code-copy-label">' + T.copied + '</span>';
					btn.setAttribute('aria-label', T.copied);
					clearTimeout(resetTimer);
					resetTimer = setTimeout(function () {
						btn.classList.remove('is-copied');
						btn.innerHTML = SVG_COPY + '<span class="code-copy-label">' + T.copy + '</span>';
						btn.setAttribute('aria-label', T.copyCode);
					}, 1200);
				}).catch(function () {});
			});
		}

		// Regular code blocks only — the tabbed component (.tab-group) has its
		// own frame, so adding a header inside each panel would double-frame it.
		var pres = document.querySelectorAll('.content pre');
		Array.prototype.forEach.call(pres, function (pre) {
			if (pre.closest && pre.closest('.tab-group')) return;
			enhance(pre);
		});
	})();

	/* --------------------------------------------------------
	   Syntax highlighting (highlight.js, from CDN)
	   Loaded ONLY when the page actually contains code. Just the token
	   colours are used — backgrounds/padding stay on the theme's --code-bg
	   (see style.css). The colour theme swaps with data-theme.
	   -------------------------------------------------------- */
	(function () {
		var codes = document.querySelectorAll('.content pre code, .tab-panel pre code');
		if (!codes.length) return;

		// Self-hosted under the theme directory (no third-party request).
		// Each site theme maps to a matching highlight.js palette. Palettes are
		// loaded lazily (only the themes actually viewed) and toggled via
		// link.disabled. style.css forces the code background to stay --code-bg,
		// so only the palette's token colours are used.
		var BASE = window.BLOWFISH_THEME_URL || '';
		var THEME_CSS = {
			light:      'github',
			dark:       'github-dark',
			nord:       'nord',
			dracula:    'dracula',
			catppuccin: 'catppuccin-mocha'
		};
		var loaded = {}; // file name -> <link> element

		function ensure(file) {
			if (loaded[file]) return loaded[file];
			var l = document.createElement('link');
			l.rel = 'stylesheet';
			l.href = BASE + 'css/hljs/' + file + '.min.css';
			document.head.appendChild(l);
			loaded[file] = l;
			return l;
		}

		function syncTheme() {
			var t = document.documentElement.getAttribute('data-theme') || 'light';
			var want = THEME_CSS[t] || 'github-dark';
			ensure(want).disabled = false;
			Object.keys(loaded).forEach(function (f) {
				if (f !== want) loaded[f].disabled = true;
			});
		}
		syncTheme();
		new MutationObserver(syncTheme).observe(document.documentElement, {
			attributes: true, attributeFilter: ['data-theme']
		});

		var s = document.createElement('script');
		s.src = BASE + 'js/highlight.min.js';
		s.onload = function () {
			if (!window.hljs) return;
			Array.prototype.forEach.call(codes, function (code) {
				try { window.hljs.highlightElement(code); } catch (e) {}
			});
		};
		document.head.appendChild(s);
	})();

	/* --------------------------------------------------------
	   Reading progress bar — tracks scroll position over the page.
	   Only shown when there's article content to read.
	   -------------------------------------------------------- */
	(function () {
		if (!document.querySelector('.content')) return;

		var bar = document.createElement('div');
		bar.className = 'reading-progress';
		bar.setAttribute('role', 'progressbar');
		bar.setAttribute('aria-hidden', 'true');
		var fill = document.createElement('div');
		fill.className = 'reading-progress-fill';
		bar.appendChild(fill);
		document.body.appendChild(bar);

		var ticking = false;
		function update() {
			var h = document.documentElement;
			var max = h.scrollHeight - h.clientHeight;
			var pct = max > 0 ? (h.scrollTop / max) * 100 : 0;
			fill.style.width = pct + '%';
			ticking = false;
		}
		function onScroll() {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		}
		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll, { passive: true });
		update();
	})();

	/* --------------------------------------------------------
	   Back-to-top button — appears after scrolling down.
	   Only on article/page views (where there's .content to read), not on the
	   homepage post list.
	   -------------------------------------------------------- */
	(function () {
		if (!document.querySelector('.content')) return;

		var btn = document.createElement('button');
		btn.type = 'button';
		btn.className = 'back-to-top';
		btn.setAttribute('aria-label', T.backToTop);
		btn.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" ' +
			'stroke="currentColor" stroke-width="2" stroke-linecap="round" ' +
			'stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
		document.body.appendChild(btn);

		btn.addEventListener('click', function () {
			var reduce = window.matchMedia &&
				window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
		});

		var ticking = false;
		function update() {
			btn.classList.toggle('is-visible', (window.scrollY || window.pageYOffset) > 400);
			ticking = false;
		}
		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		}, { passive: true });
		update();
	})();

	/* --------------------------------------------------------
	   Twitter / X embeds — upgrade <blockquote class="twitter-tweet"> into the
	   rich card (with media). widgets.js auto-scans on load, but that's fragile
	   when the embed's own <script> and the content load in an unexpected order,
	   so we explicitly (re)load. Idempotent if it already rendered.
	   -------------------------------------------------------- */
	(function () {
		if (!document.querySelector('.twitter-tweet, .twitter-timeline')) return;

		function render() {
			if (window.twttr && window.twttr.widgets && window.twttr.widgets.load) {
				window.twttr.widgets.load();
				return true;
			}
			return false;
		}

		// Already loaded (e.g. the embed's script ran but scanned too early)? Just load.
		if (render()) return;

		// Otherwise make sure widgets.js is present, then render once it's ready.
		var existing = document.querySelector('script[src*="widgets.js"]');
		if (existing) {
			existing.addEventListener('load', render);
		} else {
			var s = document.createElement('script');
			s.src = 'https://platform.twitter.com/widgets.js';
			s.async = true;
			s.charset = 'utf-8';
			s.onload = render;
			document.body.appendChild(s);
		}

		// Safety net in case the load event was missed (cached script, etc.).
		var tries = 0;
		var poll = setInterval(function () {
			if (render() || ++tries > 40) clearInterval(poll);
		}, 250);
	})();

	/* --------------------------------------------------------
	   Utterances comments: keep the widget's theme in sync with the site theme,
	   on load and on every theme switch (the plugin only sets a fixed theme).
	   -------------------------------------------------------- */
	(function () {
		if (!document.querySelector('script[src*="utteranc.es"], .utterances')) return;

		var UT_ORIGIN = 'https://utteranc.es';

		function utterancesTheme() {
			// Map the site theme to an Utterances theme (light vs. the dark family).
			return (document.documentElement.getAttribute('data-theme') === 'light')
				? 'github-light'
				: 'github-dark';
		}

		function applyTheme() {
			var frame = document.querySelector('iframe.utterances-frame');
			if (frame && frame.contentWindow) {
				frame.contentWindow.postMessage(
					{ type: 'set-theme', theme: utterancesTheme() },
					UT_ORIGIN
				);
			}
		}

		// utteranc.es posts messages once its iframe is ready (and on resize) —
		// push the current theme whenever we hear from it.
		window.addEventListener('message', function (e) {
			if (e.origin === UT_ORIGIN) applyTheme();
		});

		// React to live theme changes from the picker.
		new MutationObserver(applyTheme).observe(document.documentElement, {
			attributes: true, attributeFilter: ['data-theme']
		});
	})();

	/* --------------------------------------------------------
	   Heading anchor links — hover a heading to reveal a "#" that links to it.
	   IDs are assigned by the ToC script; assign any missing ones here too.
	   -------------------------------------------------------- */
	(function () {
		var content = document.querySelector('.content');
		if (!content) return;
		var headings = content.querySelectorAll('h2, h3, h4');
		if (!headings.length) return;

		var used = {};
		Array.prototype.forEach.call(content.querySelectorAll('[id]'), function (el) {
			used[el.id] = true;
		});

		Array.prototype.forEach.call(headings, function (h) {
			if (!h.id) {
				var base = h.textContent.trim().toLowerCase()
					.replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-')
					.replace(/^-+|-+$/g, '') || 'section';
				var id = base, n = 2;
				while (used[id]) { id = base + '-' + (n++); }
				used[id] = true;
				h.id = id;
			}
			if (h.querySelector('.heading-anchor')) return;
			var a = document.createElement('a');
			a.className = 'heading-anchor';
			a.href = '#' + h.id;
			a.setAttribute('aria-label', 'Link to this section');
			a.textContent = '#';
			h.appendChild(a);
		});
	})();

	/* --------------------------------------------------------
	   Harden outbound links in article content: rel="noopener noreferrer"
	   (anti reverse-tabnabbing + no referrer leak) and open in a new tab.
	   -------------------------------------------------------- */
	(function () {
		var content = document.querySelector('.content');
		if (!content) return;
		Array.prototype.forEach.call(content.querySelectorAll('a[href]'), function (a) {
			if (!/^https?:\/\//i.test(a.getAttribute('href') || '')) return;
			if (a.host === window.location.host) return; // same-site: leave alone
			var rel = (a.getAttribute('rel') || '').split(/\s+/).filter(Boolean);
			['noopener', 'noreferrer'].forEach(function (r) {
				if (rel.indexOf(r) === -1) rel.push(r);
			});
			a.setAttribute('rel', rel.join(' '));
			if (!a.getAttribute('target')) a.setAttribute('target', '_blank');
		});
	})();

	/* --------------------------------------------------------
	   "Copy link" share button.
	   -------------------------------------------------------- */
	(function () {
		var btns = document.querySelectorAll('.js-copy-link');
		if (!btns.length) return;
		Array.prototype.forEach.call(btns, function (btn) {
			var label = btn.querySelector('.post-share-copy-label');
			var resetTimer = null;
			btn.addEventListener('click', function () {
				var url = btn.getAttribute('data-url') || window.location.href;
				copyToClipboard(url).then(function () {
					btn.classList.add('is-copied');
					var original = label ? label.textContent : '';
					if (label) label.textContent = T.copied;
					clearTimeout(resetTimer);
					resetTimer = setTimeout(function () {
						btn.classList.remove('is-copied');
						if (label) label.textContent = original;
					}, 1500);
				}).catch(function () {});
			});
		});
	})();

	/* --------------------------------------------------------
	   Callout / admonition boxes from GitHub-style blockquotes:
	   > [!NOTE] / [!TIP] / [!IMPORTANT] / [!WARNING] / [!CAUTION] (or [!DANGER]).
	   -------------------------------------------------------- */
	(function () {
		var content = document.querySelector('.content');
		if (!content) return;

		function svg(inner) {
			return '<svg class="callout-icon" viewBox="0 0 24 24" width="18" height="18" ' +
				'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" ' +
				'stroke-linejoin="round" aria-hidden="true">' + inner + '</svg>';
		}
		var ICONS = {
			note:      svg('<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>'),
			tip:       svg('<path d="M9 18h6M10 21h4"/><path d="M12 3a6 6 0 0 0-3.8 10.6c.5.5 1 1.4 1 2.4h5.6c0-1 .5-1.9 1-2.4A6 6 0 0 0 12 3z"/>'),
			important: svg('<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>'),
			warning:   svg('<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h16.9a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/>'),
			caution:   svg('<path d="M7.9 2h8.2L22 7.9v8.2L16.1 22H7.9L2 16.1V7.9z"/><path d="M12 8v4M12 16h.01"/>')
		};
		var LABELS = { note: 'Note', tip: 'Tip', important: 'Important', warning: 'Warning', caution: 'Caution' };
		var ALIAS  = { danger: 'caution' };

		Array.prototype.forEach.call(content.querySelectorAll('blockquote'), function (bq) {
			var first = bq.querySelector('p');
			if (!first) return;
			var m = first.innerHTML.match(/^\s*\[!(\w+)\]\s*(<br\s*\/?>)?\s*/i);
			if (!m) return;
			var type = m[1].toLowerCase();
			if (ALIAS[type]) type = ALIAS[type];
			if (!ICONS[type]) return;

			first.innerHTML = first.innerHTML.replace(m[0], '');
			if (!first.innerHTML.trim()) { first.parentNode.removeChild(first); }

			bq.classList.add('callout', 'callout-' + type);
			var title = document.createElement('div');
			title.className = 'callout-title';
			title.innerHTML = ICONS[type] + '<span>' + LABELS[type] + '</span>';
			bq.insertBefore(title, bq.firstChild);
		});
	})();

	/* --------------------------------------------------------
	   Figures: turn a standalone content image WITH a title into a
	   <figure> + <figcaption> (the markdown title becomes the caption).
	   Runs after the lightbox so it can wrap the .zoomable-wrap.
	   -------------------------------------------------------- */
	(function () {
		var content = document.querySelector('.content');
		if (!content) return;
		Array.prototype.forEach.call(content.querySelectorAll('img'), function (img) {
			var cap = img.getAttribute('title');
			if (!cap) return;
			if (img.closest('figure')) return;
			var node = img.closest('.zoomable-wrap') || img;
			var p = node.parentNode;
			// Only convert images that sit alone in their paragraph.
			if (!p || p.tagName !== 'P' || p.textContent.trim() !== '' || p.querySelectorAll('img').length !== 1) return;
			var fig = document.createElement('figure');
			fig.className = 'content-figure';
			fig.appendChild(node);
			var capEl = document.createElement('figcaption');
			capEl.textContent = cap;
			fig.appendChild(capEl);
			p.parentNode.replaceChild(fig, p);
		});
	})();

	/* --------------------------------------------------------
	   Mobile ToC — a floating button that opens the table of contents in a
	   drawer (the sidebar ToC is desktop-only). Mirrors the generated list.
	   -------------------------------------------------------- */
	(function () {
		var tocNav = document.getElementById('toc-nav');
		if (!tocNav) return;
		var list = tocNav.querySelector('.toc-list');
		if (!list) return; // no headings -> no ToC

		var fab = document.createElement('button');
		fab.type = 'button';
		fab.className = 'toc-fab';
		fab.setAttribute('aria-label', T.onThisPage);
		fab.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" ' +
			'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ' +
			'aria-hidden="true"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>';

		var drawer = document.createElement('div');
		drawer.className = 'toc-drawer';
		var panel = document.createElement('div');
		panel.className = 'toc-drawer-panel';
		var title = document.createElement('div');
		title.className = 'toc-drawer-title';
		title.textContent = T.onThisPage;
		panel.appendChild(title);
		var clone = list.cloneNode(true);
		panel.appendChild(clone);
		drawer.appendChild(panel);

		document.body.appendChild(fab);
		document.body.appendChild(drawer);

		function open()  { drawer.classList.add('is-open'); document.body.style.overflow = 'hidden'; }
		function close() { drawer.classList.remove('is-open'); document.body.style.overflow = ''; }

		fab.addEventListener('click', open);
		drawer.addEventListener('click', function (e) { if (e.target === drawer) close(); });
		Array.prototype.forEach.call(clone.querySelectorAll('a'), function (a) {
			a.addEventListener('click', close);
		});
		document.addEventListener('keydown', function (e) {
			if ((e.key === 'Escape' || e.key === 'Esc') && drawer.classList.contains('is-open')) close();
		});
	})();

	/* --------------------------------------------------------
	   Task lists — turn "- [ ] / - [x]" list items into real checkboxes.
	   -------------------------------------------------------- */
	(function () {
		var content = document.querySelector('.content');
		if (!content) return;
		Array.prototype.forEach.call(content.querySelectorAll('li'), function (li) {
			var m = li.innerHTML.match(/^\s*\[([ xX])\]\s+/);
			if (!m) return;
			var checked = m[1].toLowerCase() === 'x';
			li.innerHTML = li.innerHTML.replace(m[0], '');
			li.classList.add('task-list-item');
			if (li.parentNode && li.parentNode.tagName === 'UL') {
				li.parentNode.classList.add('task-list');
			}
			var box = document.createElement('input');
			box.type = 'checkbox';
			box.disabled = true;
			box.checked = checked;
			li.insertBefore(box, li.firstChild);
		});
	})();

	/* --------------------------------------------------------
	   Search overlay — client-side fuzzy search over a JSON index of posts
	   (embedded in the footer). Opened by the navbar button or the "/" key.
	   -------------------------------------------------------- */
	(function () {
		var data = [];
		var el = document.getElementById('blowfish-search-index');
		if (el) { try { data = JSON.parse(el.textContent) || []; } catch (e) { data = []; } }

		var ICON = '<svg class="search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" ' +
			'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ' +
			'aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>';

		var overlay = document.createElement('div');
		overlay.className = 'search-overlay';
		overlay.innerHTML =
			'<div class="search-box" role="dialog" aria-modal="true" aria-label="' + T.search + '">' +
				'<div class="search-field">' + ICON +
					'<input type="text" class="search-input" autocomplete="off" spellcheck="false" placeholder="' + T.searchPlaceholder + '" aria-label="' + T.search + '">' +
				'</div>' +
				'<ul class="search-results"></ul>' +
				'<div class="search-empty" hidden>' + T.noResults + '</div>' +
				'<div class="search-hint"><kbd>↑</kbd><kbd>↓</kbd> &nbsp;<kbd>↵</kbd> &nbsp;<kbd>esc</kbd></div>' +
			'</div>';
		document.body.appendChild(overlay);

		var input   = overlay.querySelector('.search-input');
		var results = overlay.querySelector('.search-results');
		var empty   = overlay.querySelector('.search-empty');
		var active  = -1;

		function open() {
			overlay.classList.add('is-open');
			document.body.style.overflow = 'hidden';
			input.value = '';
			update();
			setTimeout(function () { input.focus(); }, 30);
		}
		function close() {
			overlay.classList.remove('is-open');
			document.body.style.overflow = '';
		}
		function isOpen() { return overlay.classList.contains('is-open'); }

		function matches(item, q) {
			var hay = (item.t + ' ' + (item.c || '') + ' ' + (item.g || []).join(' ') + ' ' + (item.d || '')).toLowerCase();
			return hay.indexOf(q) !== -1;
		}

		function render(list) {
			active = list.length ? 0 : -1;
			results.innerHTML = '';
			empty.hidden = !(input.value.trim() && list.length === 0);
			list.forEach(function (item, i) {
				var li = document.createElement('li');
				li.className = 'search-result' + (i === 0 ? ' is-active' : '');
				li.innerHTML = '<a href="' + item.u + '"><span class="search-result-title"></span>' +
					(item.c ? '<span class="search-result-meta"></span>' : '') + '</a>';
				li.querySelector('.search-result-title').textContent = item.t;
				if (item.c) li.querySelector('.search-result-meta').textContent = item.c;
				results.appendChild(li);
			});
		}

		function update() {
			var q = input.value.trim().toLowerCase();
			if (!q) { render(data.slice(0, 6)); return; } // recent posts as suggestions
			render(data.filter(function (item) { return matches(item, q); }).slice(0, 8));
		}

		function move(delta) {
			var items = results.querySelectorAll('.search-result');
			if (!items.length) return;
			if (items[active]) items[active].classList.remove('is-active');
			active = (active + delta + items.length) % items.length;
			items[active].classList.add('is-active');
			items[active].scrollIntoView({ block: 'nearest' });
		}

		function go() {
			var items = results.querySelectorAll('.search-result');
			if (items[active]) { var a = items[active].querySelector('a'); if (a) window.location.href = a.href; }
		}

		input.addEventListener('input', update);
		input.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
			else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
			else if (e.key === 'Enter') { e.preventDefault(); go(); }
		});
		overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });

		// Navbar button(s) open the overlay.
		Array.prototype.forEach.call(document.querySelectorAll('.js-search-open'), function (b) {
			b.addEventListener('click', function (e) { e.preventDefault(); open(); });
		});

		// Navbar loupe: show only when no on-page search box is visible (avoids a
		// duplicate). Appears once the hero/sidebar search scrolls out of view.
		var navItems = document.querySelectorAll('.nav-search');
		var pageSearch = document.querySelector('.home-search, .sidebar-container .plugin-search');
		function setNav(v) {
			Array.prototype.forEach.call(navItems, function (n) { n.classList.toggle('is-visible', v); });
		}
		if (navItems.length) {
			if (!pageSearch || !('IntersectionObserver' in window)) {
				setNav(true); // nothing on-page to duplicate -> always available
			} else {
				setNav(false);
				new IntersectionObserver(function (entries) {
					setNav(!entries[0].isIntersecting);
				}, { rootMargin: '-64px 0px 0px 0px' }).observe(pageSearch);
			}
		}

		// Expose for the keyboard-shortcuts handler.
		window.BLOWFISH_SEARCH = { open: open, close: close, isOpen: isOpen };
	})();

	/* --------------------------------------------------------
	   Keyboard shortcuts:  /  search · g h home · t top · Esc close.
	   -------------------------------------------------------- */
	(function () {
		var lastG = 0;
		function typing(t) {
			return t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.isContentEditable);
		}
		document.addEventListener('keydown', function (e) {
			var s = window.BLOWFISH_SEARCH;
			if (e.key === 'Escape' || e.key === 'Esc') {
				if (s && s.isOpen()) s.close();
				return;
			}
			if (typing(e.target) || e.metaKey || e.ctrlKey || e.altKey) return;

			if (e.key === '/') { e.preventDefault(); if (s) s.open(); return; }
			if (e.key === 't' || e.key === 'T') {
				var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
				window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
				return;
			}
			// "g" then "h" -> home
			if (e.key === 'g' || e.key === 'G') { lastG = Date.now(); return; }
			if ((e.key === 'h' || e.key === 'H') && Date.now() - lastG < 600) {
				window.location.href = (window.BLOWFISH_HOME || '/');
			}
		});
	})();

})();
