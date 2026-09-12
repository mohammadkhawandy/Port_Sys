<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('terms')) ?> - <?= esc(portsys_text('brand')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="inner-hero"><div class="container"><h1><?= esc(portsys_text('terms')) ?></h1><div class="breadcrumb-mini"><?= esc(portsys_text('breadcrumb_home')) ?> / <?= esc(portsys_text('terms')) ?></div></div></section>
<section class="section-pad"><div class="container" style="max-width:900px"><article class="form-box"><p class="lead text-muted"><?= esc(lang('App.terms_intro')) ?></p><?php foreach (['usage','account','data','availability'] as $section): ?><section class="mt-4"><h2 class="h5 fw-bold"><i class="fa-solid fa-scale-balanced text-primary me-2"></i><?= esc(lang('App.terms_' . $section . '_title')) ?></h2><p class="text-muted mb-0"><?= esc(lang('App.terms_' . $section . '_text')) ?></p></section><?php endforeach; ?></article></div></section>
<?= $this->endSection() ?>
