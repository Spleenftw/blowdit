<?php if ($url->notFound()) : ?>

<!-- Custom 404 -->
<article class="card card-modern my-5 page-404">
	<div class="card-body error-body">
		<p class="error-code">404</p>
		<h1 class="title"><?php echo $L->get('Page not found'); ?></h1>
		<p class="error-text"><?php echo $L->get('The page you are looking for does not exist or has moved.'); ?></p>
		<?php if (!empty($sidebarSearchHtml)) : ?>
		<div class="error-search"><?php echo $sidebarSearchHtml; ?></div>
		<?php endif ?>
		<a class="btn-primary-gradient error-home" href="<?php echo Theme::siteUrl(); ?>"><?php echo blowfish_icon('house'); ?> <?php echo $L->get('Back to home'); ?></a>
	</div>
</article>

<?php else : ?>

<!-- Post -->
<article class="card card-modern my-5">

	<!-- Load Bludit Plugins: Page Begin -->
	<?php Theme::plugins('pageBegin'); ?>

	<!-- Cover image -->
	<?php if ($page->coverImage()): ?>
	<div class="cover-image-wrapper">
		<img class="card-img-top" alt="<?php echo blowfish_text($page->title()); ?>" src="<?php echo $page->coverImage(); ?>" fetchpriority="high" decoding="async"/>
	</div>
	<?php endif ?>

	<div class="card-body">

		<!-- Breadcrumbs (articles only) -->
		<?php if (!$page->isStatic() && !$url->notFound()) : ?>
		<nav class="breadcrumbs" aria-label="Breadcrumb">
			<a href="<?php echo Theme::siteUrl(); ?>"><?php echo $L->get('Home'); ?></a>
			<?php if ($page->categoryKey()) : ?>
			<span class="breadcrumb-sep" aria-hidden="true">›</span>
			<a href="<?php echo $page->categoryPermalink(); ?>"><?php echo blowfish_text($page->category()); ?></a>
			<?php endif ?>
			<span class="breadcrumb-sep" aria-hidden="true">›</span>
			<span class="breadcrumb-current" aria-current="page"><?php echo blowfish_text($page->title()); ?></span>
		</nav>
		<?php endif ?>

		<!-- Title -->
		<h1 class="title"><?php echo blowfish_text($page->title()); ?></h1>

		<?php if (!$page->isStatic() && !$url->notFound()): ?>
		<!-- Creation date and reading time -->
		<div class="metadata mb-4">
			<span><?php echo blowfish_icon('calendar'); ?><?php echo $page->date(); ?></span>
			<span><?php echo blowfish_icon('clock'); ?><?php echo $L->get('Reading time') . ': ' . $page->readingTime() ?></span>
		</div>
		<?php endif ?>

		<!-- Series box: posts sharing a tag whose key starts with "series-"
		     (e.g. tag "Series: Homelab" -> key "series-homelab"), ordered oldest-first. -->
		<?php if (!$page->isStatic() && !$url->notFound()) :
			$bdSeriesTag = ''; $bdSeriesName = '';
			foreach ($page->tags(true) as $bdTk => $bdTn) {
				if (preg_match('/^series[-_]/i', $bdTk)) { $bdSeriesTag = $bdTk; $bdSeriesName = $bdTn; break; }
			}
			$bdSeriesPosts = array();
			if ($bdSeriesTag) {
				try {
					global $pages;
					if (isset($pages) && method_exists($pages, 'getList')) {
						foreach ($pages->getList(1, 1000, true) as $bdSk) {
							$bdSp = new Page($bdSk);
							if (in_array($bdSeriesTag, array_keys($bdSp->tags(true)), true)) { $bdSeriesPosts[] = $bdSp; }
						}
						usort($bdSeriesPosts, function ($a, $b) { return strcmp($a->getValue('date'), $b->getValue('date')); });
					}
				} catch (\Throwable $e) { $bdSeriesPosts = array(); }
			}
			$bdSeriesLabel = trim(preg_replace('/^series\s*[:\-_]?\s*/i', '', blowfish_plain($bdSeriesName)));
		?>
		<?php if (count($bdSeriesPosts) > 1) : ?>
		<aside class="series-box">
			<div class="series-box-title"><?php echo blowfish_icon('folder'); ?><span><?php echo $L->get('Part of a series'); ?><?php echo $bdSeriesLabel !== '' ? ': ' . htmlspecialchars($bdSeriesLabel, ENT_QUOTES, 'UTF-8') : ''; ?></span></div>
			<ol class="series-list">
				<?php foreach ($bdSeriesPosts as $bdSp) : $bdIsCur = ($bdSp->key() === $page->key()); ?>
				<li class="series-item<?php echo $bdIsCur ? ' is-current' : ''; ?>">
					<?php if ($bdIsCur) : ?><span><?php echo blowfish_text($bdSp->title()); ?></span>
					<?php else : ?><a href="<?php echo $bdSp->permalink(); ?>"><?php echo blowfish_text($bdSp->title()); ?></a><?php endif ?>
				</li>
				<?php endforeach ?>
			</ol>
		</aside>
		<?php endif ?>
		<?php endif ?>

		<!-- Full content -->
		<div class="content">
			<?php
				// Lazy-load + async-decode in-content images that don't already
				// set a loading attribute. Operates on the rendered HTML, so
				// carousel/tabs fences (still raw text inside <pre>) are untouched.
				echo preg_replace(
					'/<img(?![^>]*\bloading=)/i',
					'<img loading="lazy" decoding="async"',
					$page->content()
				);
			?>
		</div>

		<!-- Tags and Category -->
		<?php $tagsList = $page->tags(true); $categoryKey = $page->categoryKey(); ?>
		<?php if (!empty($tagsList) || $categoryKey) : ?>
		<div class="post-taxonomy mt-4">
			<?php if ($categoryKey) : ?>
				<a class="taxonomy-badge" href="<?php echo $page->categoryPermalink(); ?>">
					<?php echo blowfish_icon('folder'); ?><?php echo blowfish_text($page->category()); ?>
				</a>
			<?php endif ?>
			<?php foreach ($tagsList as $tagKey => $tagName) : ?>
				<a class="taxonomy-badge" href="<?php echo DOMAIN_TAGS . $tagKey; ?>"><?php echo blowfish_icon('tag'); ?><?php echo blowfish_text($tagName); ?></a>
			<?php endforeach ?>
		</div>
		<?php endif ?>

		<!-- Share -->
		<?php if (!$page->isStatic() && !$url->notFound()) : ?>
		<?php $bdShareUrl = $page->permalink(true); $bdShareTitle = blowfish_plain($page->title()); ?>
		<div class="post-share mt-4">
			<span class="post-share-label"><?php echo $L->get('Share'); ?></span>
			<a class="post-share-btn" href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode($bdShareUrl); ?>&amp;text=<?php echo rawurlencode($bdShareTitle); ?>" target="_blank" rel="noopener" aria-label="<?php echo $L->get('Share on X (Twitter)'); ?>">X (Twitter)</a>
			<a class="post-share-btn" href="https://www.reddit.com/submit?url=<?php echo rawurlencode($bdShareUrl); ?>&amp;title=<?php echo rawurlencode($bdShareTitle); ?>" target="_blank" rel="noopener" aria-label="<?php echo $L->get('Share on Reddit'); ?>">Reddit</a>
			<a class="post-share-btn" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode($bdShareUrl); ?>" target="_blank" rel="noopener" aria-label="<?php echo $L->get('Share on LinkedIn'); ?>">LinkedIn</a>
			<button type="button" class="post-share-btn js-copy-link" data-url="<?php echo htmlspecialchars($bdShareUrl, ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo $L->get('Copy link'); ?>"><?php echo blowfish_icon('link'); ?><span class="post-share-copy-label"><?php echo $L->get('Copy link'); ?></span></button>
		</div>
		<?php endif ?>

	</div>

	<!-- Load Bludit Plugins: Page End -->
	<?php Theme::plugins('pageEnd'); ?>

</article>

<?php
	// ---- Related posts ----
	// Best-effort via Bludit's Pages API; wrapped so any API difference degrades
	// to nothing instead of erroring the page.
	if (!$page->isStatic() && !$url->notFound()) {
		$bdRelated = array();
		try {
			global $pages;
			if (isset($pages) && method_exists($pages, 'getList')) {
				$bdKeys = $pages->getList(1, 10000, true); // published
				if (is_array($bdKeys)) {
					$bdCur  = $page->key();
					$bdCat  = $page->categoryKey();
					$bdTags = array_keys($page->tags(true));
					foreach ($bdKeys as $bdK) {
						if ($bdK === $bdCur) { continue; }
						if (count($bdRelated) >= 3) { break; }
						$bdCand = new Page($bdK);
						$bdMatch = ($bdCat && $bdCand->categoryKey() === $bdCat);
						if (!$bdMatch && $bdTags) {
							$bdMatch = (bool) array_intersect($bdTags, array_keys($bdCand->tags(true)));
						}
						if ($bdMatch) { $bdRelated[] = $bdCand; }
					}
				}
			}
		} catch (\Throwable $e) {
			$bdRelated = array();
		}
	?>

	<?php if (!empty($bdRelated)) : ?>
	<section class="related-posts">
		<h2 class="related-posts-title"><?php echo $L->get('Related posts'); ?></h2>
		<div class="related-grid">
			<?php foreach ($bdRelated as $bdRp) : ?>
			<a class="related-card" href="<?php echo $bdRp->permalink(); ?>">
				<?php if ($bdRp->coverImage()) : ?>
				<span class="related-cover" style="background-image:url('<?php echo htmlspecialchars($bdRp->coverImage(), ENT_QUOTES, 'UTF-8'); ?>')"></span>
				<?php endif ?>
				<span class="related-card-title"><?php echo blowfish_text($bdRp->title()); ?></span>
			</a>
			<?php endforeach ?>
		</div>
	</section>
	<?php endif ?>

<?php } ?>

<?php endif ?>
