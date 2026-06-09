<nav class="navbar navbar-expand-md navbar-dark fixed-top navbar-modern">
	<div class="container">
		<a class="navbar-brand" href="<?php echo Theme::siteUrl() ?>">
			<span class="text-white"><?php echo blowfish_text($site->title()) ?></span>
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav ml-auto align-items-md-center">

				<!-- Blog link (when homepage is set to a static page) -->
				<?php if ($site->homepage()): ?>
					<li class="nav-item">
						<a class="nav-link<?php echo ($WHERE_AM_I === 'blog') ? ' active' : '' ?>" href="<?php echo DOMAIN_BASE . ltrim($url->filters('blog'), '/') ?>"><?php echo $L->get('Blog') ?></a>
					</li>
				<?php endif; ?>

				<!-- Static pages -->
				<?php foreach ($staticContent as $staticPage) : ?>
					<li class="nav-item">
						<a class="nav-link<?php echo ($url->slug() == $staticPage->slug()) ? ' active' : '' ?>" href="<?php echo $staticPage->permalink() ?>"><?php echo blowfish_text($staticPage->title()) ?></a>
					</li>
				<?php endforeach ?>

				<?php
					// The homepage hero (home.php) already shows the social row in
					// its central block on every paginated home page, so hide the
					// navbar copy there to avoid duplication. Show it elsewhere.
					$heroVisible = ($WHERE_AM_I === 'home');
				?>
				<?php if (!$heroVisible) : ?>
					<!-- Social Networks (SVG icons live in img/<network>.svg) -->
					<?php foreach (Theme::socialNetworks() as $key => $label) : ?>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo $site->{$key}(); ?>" target="_blank" rel="noopener" title="<?php echo blowfish_text($label) ?>">
								<img class="d-none d-md-block nav-svg-icon" src="<?php echo blowfish_asset('img/' . $key . '.svg') ?>" alt="<?php echo blowfish_text($label) ?>" />
								<span class="d-inline d-md-none"><?php echo blowfish_text($label); ?></span>
							</a>
						</li>
					<?php endforeach; ?>

					<!-- RSS feed (served by the RSS plugin at /rss.xml) -->
					<li class="nav-item">
						<a class="nav-link" href="<?php echo rtrim(Theme::siteUrl(), '/') . '/rss.xml'; ?>" target="_blank" rel="noopener" title="<?php echo $L->get('RSS'); ?>">
							<img class="d-none d-md-block nav-svg-icon" src="<?php echo blowfish_asset('img/rss.svg') ?>" alt="<?php echo $L->get('RSS'); ?>" />
							<span class="d-inline d-md-none"><?php echo $L->get('RSS'); ?></span>
						</a>
					</li>

					<!-- Email (mailto) -->
					<?php if (!empty($blowfishEmail)) : ?>
					<li class="nav-item">
						<a class="nav-link" href="mailto:<?php echo htmlspecialchars($blowfishEmail, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo $L->get('Email'); ?>">
							<img class="d-none d-md-block nav-svg-icon" src="<?php echo blowfish_asset('img/mail.svg') ?>" alt="<?php echo $L->get('Email'); ?>" />
							<span class="d-inline d-md-none"><?php echo $L->get('Email'); ?></span>
						</a>
					</li>
					<?php endif; ?>
				<?php endif; ?>

				<!-- Search (opens the overlay; also bound to the "/" key). Shown by
				     blowfish.js only when no on-page search box is visible. -->
				<li class="nav-item nav-search ml-md-2 mt-2 mt-md-0">
					<button type="button" class="theme-toggle js-search-open" aria-label="<?php echo $L->get('Search'); ?>" title="<?php echo $L->get('Search'); ?> (/)">
						<?php echo blowfish_icon('search'); ?>
					</button>
				</li>

				<!-- Theme picker -->
				<li class="nav-item ml-md-2 mt-2 mt-md-0 theme-picker-wrap">
					<button type="button" id="theme-toggle" class="theme-toggle" aria-label="<?php echo $L->get('Toggle theme') ?>" title="<?php echo $L->get('Toggle theme') ?>" aria-haspopup="true" aria-expanded="false">
						<?php echo blowfish_icon(blowfish_theme_icon($blowfishTheme)); ?>
					</button>
					<div id="theme-picker" class="theme-picker" role="menu">
						<button class="swatch" data-pick="light"      title="Light"      style="--sb:#ffffff;--sa:#1f1f1f"><span class="swatch-dot"></span><span class="swatch-label">Light</span></button>
						<button class="swatch" data-pick="dark"       title="Dark"       style="--sb:#171717;--sa:#e5e5e5"><span class="swatch-dot"></span><span class="swatch-label">Dark</span></button>
						<button class="swatch" data-pick="nord"       title="Nord"       style="--sb:#2e3440;--sa:#88c0d0"><span class="swatch-dot"></span><span class="swatch-label">Nord</span></button>
						<button class="swatch" data-pick="dracula"    title="Dracula"    style="--sb:#282a36;--sa:#bd93f9"><span class="swatch-dot"></span><span class="swatch-label">Dracula</span></button>
						<button class="swatch" data-pick="catppuccin" title="Catppuccin" style="--sb:#1e1e2e;--sa:#cba6f7"><span class="swatch-dot"></span><span class="swatch-label">Cat</span></button>
					</div>
				</li>

			</ul>
		</div>
	</div>
</nav>
