<footer class="footer footer-modern">
    <div class="container">
        <div class="row align-items-center">
            <?php if (defined('BLUDIT_PRO')): ?>
                <div class="col-12 text-center">
                    <p class="m-0 text-uppercase"><?php echo $site->footer(); ?></p>
                </div>
            <?php else: ?>
                <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
                    <p class="m-0 text-uppercase"><?php echo $site->footer(); ?></p>
                </div>
                <div class="col-md-6 text-center text-md-right">
                    <p class="m-0">
                        Powered by <a href="https://www.bludit.com" target="_blank" rel="noopener"><strong>BLUDIT</strong></a> and <a href="https://github.com/Spleenftw/bludit-blowfish" target="_blank" rel="noopener"><strong>BLOWFISH</strong></a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php
	// Client-side search index (all published posts) for the search overlay.
	// Guarded so any Pages-API difference degrades to an empty index, not an error.
	try {
		global $pages;
		$bdSearchIndex = array();
		if (isset($pages) && method_exists($pages, 'getList')) {
			foreach ($pages->getList(1, 1000, true) as $bdSk) {
				$bdSp = new Page($bdSk);
				if ($bdSp->isStatic()) { continue; } // posts only
				$bdSearchIndex[] = array(
					't' => blowfish_plain($bdSp->title()),
					'u' => $bdSp->permalink(),
					'd' => blowfish_plain($bdSp->description()),
					'c' => blowfish_plain($bdSp->category()),
					'g' => array_values(array_map('blowfish_plain', $bdSp->tags(true))),
				);
			}
		}
		echo '<script type="application/json" id="blowfish-search-index">'
			. json_encode($bdSearchIndex, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
			. '</script>';
	} catch (\Throwable $e) {}
?>
