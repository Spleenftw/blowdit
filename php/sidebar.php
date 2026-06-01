<aside class="sidebar-container">

	<?php
		// Compact profile card — shown only while reading an article
		// (a single, non-static page), not on the homepage or static pages.
		$isArticle = ($WHERE_AM_I === 'page' && isset($page) && !$page->isStatic() && !$url->notFound());
	?>
	<?php if ($isArticle) : ?>
		<div class="sidebar-author">
			<span class="sidebar-author-label">Author</span>
			<div class="sidebar-author-identity">
				<a href="<?php echo Theme::siteUrl() ?>">
					<img class="sidebar-author-avatar" src="<?php echo DOMAIN_THEME . 'img/spleenftw.jpeg' ?>" alt="<?php echo htmlspecialchars($page->username(), ENT_QUOTES, 'UTF-8') ?>" />
				</a>
				<div>
					<div class="sidebar-author-name"><?php echo htmlspecialchars($page->username(), ENT_QUOTES, 'UTF-8') ?></div>
					<?php
						// Bio: set in Bludit Admin → Settings → General → Description
						// Falls back to Slogan if Description is empty.
						$_authorBio = $site->description() ?: $site->slogan();
					?>
					<?php if ($_authorBio) : ?>
						<p class="sidebar-author-bio"><?php echo htmlspecialchars($_authorBio, ENT_QUOTES, 'UTF-8') ?></p>
					<?php endif ?>
				</div>
			</div>
			<?php $networks = Theme::socialNetworks(); ?>
			<?php if (!empty($networks)) : ?>
			<div class="sidebar-author-social">
				<?php foreach ($networks as $key => $label) : ?>
				<a href="<?php echo $site->{$key}(); ?>" target="_blank" rel="noopener" title="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
					<img class="sidebar-social-icon" src="<?php echo DOMAIN_THEME . 'img/' . $key . '.svg' ?>" alt="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>" />
				</a>
				<?php endforeach ?>
			</div>
			<?php endif ?>
		</div>
	<?php endif ?>

	<?php
		// Plugins are rendered in a fixed order (see index.php):
		//   Blog/About > Search > Navigation > Category > … > Hit Counter (bottom)
		// On the homepage the navigation is redundant (posts are already listed),
		// and the search box lives under the hero, so show only about/categories/counters.
		if ($WHERE_AM_I === 'home') {
			echo $sidebarHomeHtml;
		} else {
			echo $sidebarFullHtml;
		}
	?>
</aside>
