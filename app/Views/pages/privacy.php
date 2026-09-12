<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('privacy')) ?> - <?= esc(portsys_text('brand')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="inner-hero"><div class="container"><h1><?= esc(portsys_text('privacy')) ?></h1><div class="breadcrumb-mini"><?= esc(portsys_text('breadcrumb_home')) ?> / <?= esc(portsys_text('privacy')) ?></div></div></section>
<section class="section-pad"><div class="container" style="max-width:900px"><article class="form-box"><p class="lead text-muted"><?= esc(lang('App.privacy_intro')) ?></p><?php foreach (['collection','usage','security','retention'] as $section): ?><section class="mt-4"><h2 class="h5 fw-bold"><i class="fa-solid fa-circle-check text-primary me-2"></i><?= esc(lang('App.privacy_' . $section . '_title')) ?></h2><p class="text-muted mb-0"><?= esc(lang('App.privacy_' . $section . '_text')) ?></p></section><?php endforeach; ?></article></div></section>
<?= $this->endSection() ?>
