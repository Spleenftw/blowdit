<!-- Post -->
<article class="card card-modern my-5">

	<!-- Load Bludit Plugins: Page Begin -->
	<?php Theme::plugins('pageBegin'); ?>

	<!-- Cover image -->
	<?php if ($page->coverImage()): ?>
	<div class="cover-image-wrapper">
		<img class="card-img-top" alt="<?php echo blowdit_text($page->title()); ?>" src="<?php echo $page->coverImage(); ?>" fetchpriority="high" decoding="async"/>
	</div>
	<?php endif ?>

	<div class="card-body">
		<!-- Title -->
		<h1 class="title"><?php echo blowdit_text($page->title()); ?></h1>

		<?php if (!$page->isStatic() && !$url->notFound()): ?>
		<!-- Creation date, reading time and author -->
		<div class="metadata mb-4">
			<span><?php echo blowdit_icon('calendar'); ?><?php echo $page->date(); ?></span>
			<span><?php echo blowdit_icon('clock'); ?><?php echo $L->get('Reading time') . ': ' . $page->readingTime() ?></span>
		</div>
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
					<?php echo blowdit_icon('folder'); ?><?php echo blowdit_text($page->category()); ?>
				</a>
			<?php endif ?>
			<?php foreach ($tagsList as $tagKey => $tagName) : ?>
				<a class="taxonomy-badge" href="<?php echo DOMAIN_TAGS . $tagKey; ?>"><?php echo blowdit_icon('tag'); ?><?php echo blowdit_text($tagName); ?></a>
			<?php endforeach ?>
		</div>
		<?php endif ?>

	</div>

	<!-- Load Bludit Plugins: Page End -->
	<?php Theme::plugins('pageEnd'); ?>

</article>
