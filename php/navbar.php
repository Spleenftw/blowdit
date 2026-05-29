<nav class="navbar navbar-expand-md navbar-dark fixed-top navbar-modern">
	<div class="container">
		<a class="navbar-brand" href="<?php echo Theme::siteUrl() ?>">
			<span class="text-white"><?php echo $site->title() ?></span>
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
						<a class="nav-link<?php echo ($url->slug() == $staticPage->slug()) ? ' active' : '' ?>" href="<?php echo $staticPage->permalink() ?>"><?php echo $staticPage->title() ?></a>
					</li>
				<?php endforeach ?>

				<!-- Social Networks (SVG icons live in img/<network>.svg) -->
				<?php foreach (Theme::socialNetworks() as $key => $label) : ?>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo $site->{$key}(); ?>" target="_blank" rel="noopener" title="<?php echo $label ?>">
							<img class="d-none d-md-block nav-svg-icon" src="<?php echo DOMAIN_THEME . 'img/' . $key . '.svg' ?>" alt="<?php echo $label ?>" />
							<span class="d-inline d-md-none"><?php echo $label; ?></span>
						</a>
					</li>
				<?php endforeach; ?>

				<!-- Theme picker -->
				<li class="nav-item ml-md-2 mt-2 mt-md-0 theme-picker-wrap">
					<button type="button" id="theme-toggle" class="theme-toggle" aria-label="<?php echo $L->get('Toggle theme') ?>" title="<?php echo $L->get('Toggle theme') ?>" aria-haspopup="true" aria-expanded="false">
						<i class="bi bi-moon-stars" aria-hidden="true"></i>
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
