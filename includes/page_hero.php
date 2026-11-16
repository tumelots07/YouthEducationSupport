<?php
$pageHeroTitle = $pageHeroTitle ?? 'Youth Education Support';
$pageHeroText = $pageHeroText ?? 'Discover support, resources and opportunities for your educational journey.';
$pageHeroImage = $pageHeroImage ?? 'learning-community.svg';
$pageHeroImageSrc = preg_match('~^https?://~i', $pageHeroImage) ? $pageHeroImage : 'assets/illustrations/' . $pageHeroImage;
?>
<section class="inner-page-hero">
 <div class="inner-page-hero-content">
  <div class="inner-page-hero-copy">
   <span class="section-kicker">YOUTH EDUCATION SUPPORT</span>
   <h1><?= htmlspecialchars($pageHeroTitle, ENT_QUOTES, 'UTF-8') ?></h1>
   <p><?= htmlspecialchars($pageHeroText, ENT_QUOTES, 'UTF-8') ?></p>
  </div>
  <img src="<?= htmlspecialchars($pageHeroImageSrc, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" alt="<?= htmlspecialchars($pageHeroTitle, ENT_QUOTES, 'UTF-8') ?>">
 </div>
</section>
