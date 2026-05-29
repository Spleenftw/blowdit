<aside class="sidebar-container">

	<?php
		// Compact profile card — shown only while reading an article
		// (a single, non-static page), not on the homepage or static pages.
		$isArticle = ($WHERE_AM_I === 'page' && isset($page) && !$page->isStatic() && !$url->notFound());
	?>
	<?php if ($isArticle) : ?>
		<div class="sidebar-profile text-center">
			<a href="<?php echo Theme::siteUrl() ?>">
				<img class="sidebar-profile-avatar" src="<?php echo DOMAIN_THEME . 'img/spleenftw.jpeg' ?>" alt="<?php echo $site->title() ?>" />
			</a>
			<div class="sidebar-profile-name"><?php echo $site->title() ?></div>
			<?php if ($site->slogan()) : ?>
				<p class="sidebar-profile-bio"><?php echo $site->slogan() ?></p>
			<?php endif ?>
		</div>
	<?php endif ?>

	<?php
		// Plugins are rendered in a fixed order (see index.php):
		//   Blog/About > Search > Navigation > Category > … > Hit Counter (bottom)
		// On the homepage front page the search box is shown under the hero, so
		// it is omitted from the sidebar there.
		if ($heroVisible) {
			echo $sidebarNoSearchHtml;
		} else {
			echo $sidebarFullHtml;
		}
	?>
</aside>
