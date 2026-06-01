<aside class="sidebar-container">

	<?php
		// Compact profile card — shown only while reading an article
		// (a single, non-static page), not on the homepage or static pages.
		$isArticle = ($WHERE_AM_I === 'page' && isset($page) && !$page->isStatic() && !$url->notFound());
	?>
	<?php if ($isArticle) : ?>
		<div class="sidebar-profile text-center">
			<a href="<?php echo Theme::siteUrl() ?>">
				<img class="sidebar-profile-avatar" src="<?php echo DOMAIN_THEME . 'img/spleenftw.jpeg' ?>" alt="<?php echo htmlspecialchars($site->title(), ENT_QUOTES, 'UTF-8') ?>" />
			</a>
			<div class="sidebar-profile-name"><?php echo htmlspecialchars($site->title(), ENT_QUOTES, 'UTF-8') ?></div>
			<?php if ($site->slogan()) : ?>
				<p class="sidebar-profile-bio"><?php echo htmlspecialchars($site->slogan(), ENT_QUOTES, 'UTF-8') ?></p>
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
