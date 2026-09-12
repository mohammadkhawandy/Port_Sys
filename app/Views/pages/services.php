<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('services')) ?> - <?= esc(portsys_text('brand')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$services = [
    ['port', 'fa-anchor'], ['ship', 'fa-ship'], ['cargo', 'fa-boxes-stacked'],
    ['trip', 'fa-route'], ['warehouse', 'fa-warehouse'], ['reports', 'fa-chart-pie'],
];
$advantages = [
    ['security', 'fa-shield-halved'], ['usability', 'fa-thumbs-up'],
    ['support', 'fa-headset'], ['updates', 'fa-arrows-rotate'],
];
?>
<section class="inner-hero"><div class="container"><h1><?= esc(portsys_text('services')) ?></h1><div class="breadcrumb-mini"><?= esc(portsys_text('breadcrumb_home')) ?> / <?= esc(portsys_text('services')) ?></div></div></section>
<section class="section-pad">
    <div class="container">
        <div class="section-title"><h2><?= esc(portsys_text('services_title')) ?></h2><p><?= esc(portsys_text('services_intro')) ?></p></div>
        <div class="row g-4">
            <?php foreach ($services as [$key, $icon]): ?>
                <div class="col-md-6 col-lg-4" id="service-<?= esc($key) ?>">
                    <article class="service-card">
                        <div class="circle-icon"><i class="fa-solid <?= esc($icon) ?>"></i></div>
                        <div><h3><?= esc(lang('App.service_' . $key . '_title')) ?></h3><p><?= esc(lang('App.service_' . $key . '_desc')) ?></p></div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="service-strip py-5">
    <div class="container"><div class="row g-3">
        <?php foreach ($advantages as [$key, $icon]): ?>
            <div class="col-sm-6 col-lg-3"><article class="feature-card"><div class="circle-icon"><i class="fa-solid <?= esc($icon) ?>"></i></div><h3><?= esc(lang('App.service_' . $key . '_title')) ?></h3><p><?= esc(lang('App.service_' . $key . '_desc')) ?></p></article></div>
        <?php endforeach; ?>
    </div></div>
</section>
<section class="cta-band">
    <div class="container"><div class="row align-items-center g-3"><div class="col-lg-7"><h2 class="h3 fw-bold mb-1"><?= esc(lang('App.services_cta_title')) ?></h2><p class="mb-0"><?= esc(lang('App.services_cta_desc')) ?></p></div><div class="col-lg-5 text-lg-end"><a href="<?= site_url('register') ?>" class="btn btn-light btn-ps me-2"><?= esc(portsys_text('register')) ?></a><a href="<?= site_url('contact') ?>" class="btn btn-outline-light btn-ps"><?= esc(portsys_text('contact')) ?></a></div></div></div>
</section>
<?= $this->endSection() ?>
