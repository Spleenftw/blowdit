<!-- Post -->
<article class="card card-modern my-5">

	<!-- Load Bludit Plugins: Page Begin -->
	<?php Theme::plugins('pageBegin'); ?>

	<!-- Cover image -->
	<?php if ($page->coverImage()): ?>
	<div class="cover-image-wrapper">
		<img class="card-img-top" alt="<?php echo htmlspecialchars($page->title()); ?>" src="<?php echo $page->coverImage(); ?>"/>
	</div>
	<?php endif ?>

	<div class="card-body">
		<!-- Title -->
		<h1 class="title"><?php echo htmlspecialchars($page->title(), ENT_QUOTES, 'UTF-8'); ?></h1>

		<?php if (!$page->isStatic() && !$url->notFound()): ?>
		<!-- Creation date, reading time and author -->
		<div class="metadata mb-4">
			<span><i class="bi bi-calendar3"></i><?php echo $page->date(); ?></span>
			<span><i class="bi bi-clock-history"></i><?php echo $L->get('Reading time') . ': ' . $page->readingTime() ?></span>
		</div>
		<?php endif ?>

		<!-- Full content -->
		<div class="content">
			<?php echo $page->content(); ?>
		</div>

		<!-- Tags and Category -->
		<?php $tagsList = $page->tags(true); $categoryKey = $page->categoryKey(); ?>
		<?php if (!empty($tagsList) || $categoryKey) : ?>
		<div class="post-taxonomy mt-4">
			<?php if ($categoryKey) : ?>
				<a class="taxonomy-badge" href="<?php echo $page->categoryPermalink(); ?>">
					<i class="bi bi-folder2"></i><?php echo htmlspecialchars($page->category(), ENT_QUOTES, 'UTF-8'); ?>
				</a>
			<?php endif ?>
			<?php foreach ($tagsList as $tagKey => $tagName) : ?>
				<a class="taxonomy-badge" href="<?php echo DOMAIN_TAGS . $tagKey; ?>"><i class="bi bi-tag"></i><?php echo htmlspecialchars($tagName, ENT_QUOTES, 'UTF-8'); ?></a>
			<?php endforeach ?>
		</div>
		<?php endif ?>

	</div>

	<!-- Load Bludit Plugins: Page End -->
	<?php Theme::plugins('pageEnd'); ?>

</article>
