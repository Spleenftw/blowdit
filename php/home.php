<?php
  // ---- Profile hero (Blowfish-style) -------------------------------------
  // Shown only on the blog front page (first paginated page).
  // Drop your avatar at img/spleenftw.jpeg (change the filename below to reuse).
  $profileImage = DOMAIN_THEME . 'img/spleenftw.jpeg';
  $showProfile  = ($WHERE_AM_I === 'home' && Paginator::currentPage() == 1);
?>
<?php if ($showProfile) : ?>
  <header class="profile text-center">
    <img class="profile-avatar" src="<?php echo $profileImage; ?>" alt="<?php echo $site->title(); ?>" />
    <h1 class="profile-name"><?php echo $site->title(); ?></h1>
    <?php if ($site->slogan()) : ?>
      <p class="profile-bio"><?php echo $site->slogan(); ?></p>
    <?php elseif ($site->description()) : ?>
      <p class="profile-bio"><?php echo $site->description(); ?></p>
    <?php endif ?>

    <?php $networks = Theme::socialNetworks(); ?>
    <?php if (!empty($networks)) : ?>
      <div class="profile-social">
        <?php foreach ($networks as $key => $label) : ?>
          <a href="<?php echo $site->{$key}(); ?>" target="_blank" rel="noopener" title="<?php echo $label ?>">
            <img class="profile-social-icon" src="<?php echo DOMAIN_THEME . 'img/' . $key . '.svg' ?>" alt="<?php echo $label ?>" />
          </a>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </header>
<?php endif ?>

<?php if (empty($content)) : ?>
  <div class="mt-5">
    <?php $language->p('No pages found') ?>
  </div>
<?php endif ?>

<?php foreach ($content as $page) : ?>
  <!-- Post -->
  <article class="card card-modern my-5">

    <!-- Load Bludit Plugins: Page Begin -->
    <?php Theme::plugins('pageBegin'); ?>

    <!-- Cover image -->
    <?php if ($page->coverImage()) : ?>
      <a href="<?php echo $page->permalink(); ?>" class="cover-image-wrapper d-block">
        <img class="card-img-top" alt="<?php echo $page->title(); ?>" src="<?php echo $page->coverImage(); ?>" />
      </a>
    <?php endif ?>

    <div class="card-body">
      <!-- Title -->
      <a href="<?php echo $page->permalink(); ?>">
        <h2 class="title"><?php echo $page->title(); ?></h2>
      </a>

      <!-- Creation date and reading time -->
      <div class="metadata mb-4">
        <span><i class="bi bi-calendar3"></i><?php echo $page->date(); ?></span>
        <span><i class="bi bi-clock-history"></i><?php echo $L->get('Reading time') . ': ' . $page->readingTime(); ?></span>
      </div>

      <!-- Breaked content -->
      <?php echo $page->contentBreak(); ?>

      <!-- "Read more" button -->
      <?php if ($page->readMore()) : ?>
        <a class="btn-primary-gradient mt-3" href="<?php echo $page->permalink(); ?>">
          <?php echo $L->get('Read more'); ?>
          <i class="bi bi-arrow-right"></i>
        </a>
      <?php endif ?>

      <!-- Tags and Category -->
      <?php $tagsList = $page->tags(true); $categoryKey = $page->categoryKey(); ?>
      <?php if (!empty($tagsList) || $categoryKey) : ?>
      <div class="post-taxonomy mt-4">
        <?php if ($categoryKey) : ?>
          <a class="taxonomy-badge" href="<?php echo $page->categoryPermalink(); ?>">
            <i class="bi bi-folder2"></i><?php echo $page->category(); ?>
          </a>
        <?php endif ?>
        <?php foreach ($tagsList as $tagKey => $tagName) : ?>
          <a class="taxonomy-badge" href="<?php echo DOMAIN_TAGS . $tagKey; ?>"><i class="bi bi-tag"></i><?php echo $tagName; ?></a>
        <?php endforeach ?>
      </div>
      <?php endif ?>

    </div>

    <!-- Load Bludit Plugins: Page End -->
    <?php Theme::plugins('pageEnd'); ?>

  </article>
<?php endforeach ?>

<!-- Pagination -->
<?php if (Paginator::numberOfPages() > 1) : ?>
  <nav class="paginator mt-5">
    <ul class="pagination flex-wrap justify-content-center">

      <!-- Previous button -->
      <?php if (Paginator::showPrev()) : ?>
        <li class="page-item mr-2">
          <a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>" tabindex="-1">
            <i class="bi bi-chevron-left"></i> <?php echo $L->get('Previous'); ?>
          </a>
        </li>
      <?php endif; ?>

      <!-- Home button -->
      <li class="page-item mx-2 <?php if (Paginator::currentPage() == 1) echo 'disabled' ?>">
        <a class="page-link" href="<?php echo Theme::siteUrl() ?>">
          <i class="bi bi-house-door"></i> Home
        </a>
      </li>

      <!-- Next button -->
      <?php if (Paginator::showNext()) : ?>
        <li class="page-item ml-2">
          <a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>">
            <?php echo $L->get('Next'); ?> <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      <?php endif; ?>

    </ul>
  </nav>
<?php endif ?>
