<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('about')) ?> - <?= esc(portsys_text('brand')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$stats = [
    ['98%', lang('App.about_security_level'), 'fa-shield-halved'],
    ['24/7', lang('App.about_technical_support'), 'fa-headset'],
    ['500+', lang('App.about_active_users'), 'fa-users'],
    ['5+', lang('App.about_years_experience'), 'fa-calendar-check'],
];
$team = [
    ['محمد الخاوندي', '193347', 'مطور البرمجيات', 'fa-code'],
    ['لانا الحاتم', '205886', 'مصممة الواجهات', 'fa-pen-nib'],
];
?>
<section class="inner-hero">
    <div class="container">
        <h1><?= esc(portsys_text('about')) ?></h1>
        <div class="breadcrumb-mini"><?= esc(portsys_text('breadcrumb_home')) ?> / <?= esc(portsys_text('about')) ?></div>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="section-title text-start mb-4">
                    <h2><?= esc(portsys_text('about_title')) ?></h2>
                    <p class="mx-0"><?= esc(portsys_text('about_intro')) ?></p>
                </div>
                <div class="row g-3">
                    <?php foreach ($stats as [$value, $label, $icon]): ?>
                        <div class="col-6 col-md-3">
                            <div class="feature-card px-2 py-3">
                                <div class="circle-icon mb-2"><i class="fa-solid <?= esc($icon) ?>"></i></div>
                                <strong class="stat-number d-block"><?= esc($value) ?></strong>
                                <span class="stat-label"><?= esc($label) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <img class="about-img" src="<?= portsys_asset('about-port.jpg') ?>" alt="<?= esc(lang('App.about_image_alt')) ?>" loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="section-pad service-strip">
    <div class="container">
        <div class="row g-4">
            <?php foreach ([
                [portsys_text('vision'), portsys_text('vision_text'), 'fa-eye'],
                [portsys_text('mission'), portsys_text('mission_text'), 'fa-bullseye'],
                [portsys_text('values'), portsys_text('values_text'), 'fa-gem'],
            ] as [$title, $description, $icon]): ?>
                <div class="col-md-4">
                    <div class="value-card">
                        <div class="big-icon"><i class="fa-solid <?= esc($icon) ?>"></i></div>
                        <div><h3><?= esc($title) ?></h3><p><?= esc($description) ?></p></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="section-title"><h2><?= esc(portsys_text('team')) ?></h2></div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($team as [$name, $code, $role, $icon]): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="team-card h-100 p-4">
                        <div class="team-avatar" aria-hidden="true"><i class="fa-solid <?= esc($icon) ?>"></i></div>
                        <h3 class="h5 fw-bold mb-2"><?= esc($name) ?></h3>
                        <p class="small text-muted mb-2"><?= esc($role) ?></p>
                        <span class="badge rounded-pill text-bg-light border px-3 py-2" dir="ltr"><?= esc($code) ?></span>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
