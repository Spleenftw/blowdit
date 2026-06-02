<?php
  // ---- Profile hero (Blowfish-style) -------------------------------------
  // Shown only on the blog front page (first paginated page).
  // Drop your avatar at img/spleenftw.jpeg (change the filename below to reuse).
  $profileImage = DOMAIN_THEME . 'img/spleenftw.jpeg';
  $showProfile  = ($WHERE_AM_I === 'home');
?>
<?php if ($showProfile) : ?>
  <header class="profile text-center">
    <img class="profile-avatar" src="<?php echo $profileImage; ?>" alt="<?php echo blowdit_text($site->title()); ?>" />
    <h1 class="profile-name"><?php echo blowdit_text($site->title()); ?></h1>
    <?php if ($site->slogan()) : ?>
      <p class="profile-bio"><?php echo blowdit_text($site->slogan()); ?></p>
    <?php elseif ($site->description()) : ?>
      <p class="profile-bio"><?php echo blowdit_text($site->description()); ?></p>
    <?php endif ?>

    <?php $networks = Theme::socialNetworks(); ?>
    <div class="profile-social">
      <?php foreach ($networks as $key => $label) : ?>
        <a href="<?php echo $site->{$key}(); ?>" target="_blank" rel="noopener" title="<?php echo blowdit_text($label) ?>">
          <img class="profile-social-icon" src="<?php echo blowdit_asset('img/' . $key . '.svg') ?>" alt="<?php echo blowdit_text($label) ?>" />
        </a>
      <?php endforeach ?>
      <!-- RSS feed (served by the RSS plugin at /rss.xml) -->
      <a href="<?php echo rtrim(Theme::siteUrl(), '/') . '/rss.xml'; ?>" target="_blank" rel="noopener" title="<?php echo $L->get('RSS'); ?>">
        <img class="profile-social-icon" src="<?php echo blowdit_asset('img/rss.svg') ?>" alt="<?php echo $L->get('RSS'); ?>" />
      </a>
    </div>
  </header>

  <!-- Search box, relocated from the sidebar (Popeye-style) -->
  <?php if (!empty($sidebarSearchHtml)) : ?>
    <div class="home-search">
      <?php echo $sidebarSearchHtml; ?>
    </div>
  <?php endif ?>
<?php endif ?>

<?php if (empty($content)) : ?>
  <div class="mt-5">
    <?php $language->p('No pages found') ?>
  </div>
<?php endif ?>

<!-- Post list (titles only) -->
<div class="post-list">
  <?php foreach ($content as $page) : ?>
    <article class="post-list-item">

      <!-- Load Bludit Plugins: Page Begin -->
      <?php Theme::plugins('pageBegin'); ?>

      <div class="post-list-head">
        <a href="<?php echo $page->permalink(); ?>">
          <h2 class="post-list-title"><?php echo blowdit_text($page->title()); ?></h2>
        </a>
        <div class="post-list-meta">
          <span><?php echo blowdit_icon('calendar'); ?><?php echo $page->date(); ?></span>
          <?php if (!$page->isStatic()) : ?>
          <span><?php echo blowdit_icon('clock'); ?><?php echo $page->readingTime(); ?></span>
          <?php endif ?>
        </div>
      </div>

      <!-- Tags and Category -->
      <?php $tagsList = $page->tags(true); $categoryKey = $page->categoryKey(); ?>
      <?php if (!empty($tagsList) || $categoryKey) : ?>
        <div class="post-taxonomy">
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

      <!-- Load Bludit Plugins: Page End -->
      <?php Theme::plugins('pageEnd'); ?>

    </article>
  <?php endforeach ?>
</div>

<!-- Pagination -->
<?php if (Paginator::numberOfPages() > 1) : ?>
  <?php
    $totalPages  = Paginator::numberOfPages();
    $currentPage = Paginator::currentPage();

    // Derive a per-page URL template by borrowing Bludit's own next/prev URL
    // (always of the form ...?page=N) and turning the number into a token. Use
    // a page >= 2 as the source so the "page=N" token is always present.
    $pageTemplate = null;
    if (Paginator::showNext()) {
      $pageTemplate = str_replace('page=' . ($currentPage + 1), 'page={N}', Paginator::nextPageUrl());
    } elseif (Paginator::showPrev() && ($currentPage - 1) >= 2) {
      $pageTemplate = str_replace('page=' . ($currentPage - 1), 'page={N}', Paginator::previousPageUrl());
    }
    // Adjacent pages use Bludit's exact URLs; non-adjacent use the template.
    $pageUrl = function ($n) use ($currentPage, $pageTemplate) {
      if ($n == $currentPage - 1) { return Paginator::previousPageUrl(); }
      if ($n == $currentPage + 1) { return Paginator::nextPageUrl(); }
      return $pageTemplate ? str_replace('{N}', $n, $pageTemplate) : '#';
    };
  ?>
  <nav class="paginator mt-5" aria-label="<?php echo $L->get('Pagination'); ?>">
    <ul class="pagination flex-wrap justify-content-center">

      <!-- Previous -->
      <?php if (Paginator::showPrev()) : ?>
        <li class="page-item">
          <a class="page-link is-arrow" href="<?php echo Paginator::previousPageUrl() ?>" aria-label="<?php echo $L->get('Previous'); ?>">
            <?php echo blowdit_icon('chevron-left'); ?>
          </a>
        </li>
      <?php endif; ?>

      <!-- Numbered pages -->
      <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <li class="page-item<?php echo ($i == $currentPage) ? ' active' : ''; ?>">
          <?php if ($i == $currentPage) : ?>
            <span class="page-link" aria-current="page"><?php echo $i; ?></span>
          <?php else : ?>
            <a class="page-link" href="<?php echo $pageUrl($i); ?>"><?php echo $i; ?></a>
          <?php endif; ?>
        </li>
      <?php endfor; ?>

      <!-- Next -->
      <?php if (Paginator::showNext()) : ?>
        <li class="page-item">
          <a class="page-link is-arrow" href="<?php echo Paginator::nextPageUrl() ?>" aria-label="<?php echo $L->get('Next'); ?>">
            <?php echo blowdit_icon('chevron-right'); ?>
          </a>
        </li>
      <?php endif; ?>

    </ul>
  </nav>
<?php endif ?>
