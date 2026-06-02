/* ============================================================
   BLOWDIT — theme behaviour
   Deferred, cacheable bundle of all client-side enhancements.
   Loaded with <script defer> from index.php, so the DOM is fully
   parsed before any of these IIFEs run (no DOMContentLoaded needed).

   NOTE: the anti-flash theme bootstrap stays inline in head.php —
   it must run before the first paint and cannot be deferred.
   ============================================================ */
(function () {
	'use strict';

	// Localised UI strings (overridable via window.BLOWDIT_I18N, set in index.php).
	var I18N = window.BLOWDIT_I18N || {};
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
		imageViewer: I18N.imageViewer || 'Image viewer'
	};

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
		var STORAGE_KEY = 'blowdit-theme';
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

	/* --------------------------------------------------------
	   Randomise the order of the navigation / pages list on each load
	   -------------------------------------------------------- */
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
				panelCode.textContent = t.lines.join('\n');
				panelPre.appendChild(panelCode);
				panel.appendChild(panelPre);
				group.appendChild(panel);
				panels.push({ btn: btn, panel: panel });

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

		// Inline SVGs — centred in their own viewBox, so no icon-font baseline /
		// side-bearing offsets to fight.
		var SVG_COPY = '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" ' +
			'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ' +
			'aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/>' +
			'<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>';
		var SVG_CHECK = '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" ' +
			'stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" ' +
			'aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';

		function enhance(pre) {
			// Skip if already wrapped
			if (pre.parentNode && pre.parentNode.classList &&
			    pre.parentNode.classList.contains('code-block')) return;

			var wrap = document.createElement('div');
			wrap.className = 'code-block';
			pre.parentNode.insertBefore(wrap, pre);
			wrap.appendChild(pre);

			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'code-copy';
			btn.setAttribute('aria-label', T.copyCode);
			btn.innerHTML = SVG_COPY;

			// "Copied" toast that pops up next to the button
			var toast = document.createElement('span');
			toast.className = 'code-copy-toast';
			toast.textContent = T.copied;

			var resetTimer = null;
			btn.addEventListener('click', function () {
				var code = pre.querySelector('code');
				var text = (code ? code.textContent : pre.textContent) || '';
				copyText(text).then(function () {
					btn.classList.add('is-copied');
					btn.innerHTML = SVG_CHECK;
					btn.setAttribute('aria-label', T.copied);
					toast.classList.add('is-visible');
					clearTimeout(resetTimer);
					resetTimer = setTimeout(function () {
						btn.classList.remove('is-copied');
						btn.innerHTML = SVG_COPY;
						btn.setAttribute('aria-label', T.copyCode);
						toast.classList.remove('is-visible');
					}, 1000);
				}).catch(function () {});
			});

			wrap.appendChild(btn);
			wrap.appendChild(toast);
		}

		var pres = document.querySelectorAll('.content pre, .tab-panel pre');
		Array.prototype.forEach.call(pres, enhance);
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
		var BASE = window.BLOWDIT_THEME_URL || '';
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
	   -------------------------------------------------------- */
	(function () {
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

})();
