<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('brand_subtitle')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$publicMetrics = $publicMetrics ?? ['ports'=>0,'ships'=>0,'trips'=>0,'activeTrips'=>0,'capacity'=>0];
$latestTrips = $latestTrips ?? [];
$features = [
    [lang('App.feature_support_title'), lang('App.feature_support_desc'), 'fa-headset'],
    [lang('App.feature_integrated_title'), lang('App.feature_integrated_desc'), 'fa-gears'],
    [lang('App.feature_reports_title'), lang('App.feature_reports_desc'), 'fa-chart-line'],
    [lang('App.feature_speed_title'), lang('App.feature_speed_desc'), 'fa-gauge-high'],
    [lang('App.feature_security_title'), lang('App.feature_security_desc'), 'fa-shield-halved'],
];
?>
<section class="hero-home"><div class="container hero-content"><div class="kicker"><?= esc(portsys_text('hero_kicker')) ?></div><h1><?= esc(portsys_text('hero_title')) ?></h1><p><?= esc(portsys_text('hero_text')) ?></p><div class="hero-actions"><a href="<?= site_url('services') ?>" class="btn btn-ps btn-ps-primary"><i class="fa-solid fa-briefcase me-1"></i><?= esc(portsys_text('explore_services')) ?></a><a href="<?= site_url($isLoggedIn ? 'dashboard' : 'login') ?>" class="btn btn-ps btn-ps-outline"><i class="fa-solid fa-right-to-bracket me-1"></i><?= esc($isLoggedIn ? portsys_text('dashboard') : portsys_text('start_login')) ?></a></div></div></section>
<section class="stats-float"><div class="container"><div class="stats-panel">
<?php foreach ([
    ['fa-anchor', $publicMetrics['ports'], lang('App.home_registered_ports')],
    ['fa-ship', $publicMetrics['ships'], lang('App.home_registered_ships')],
    ['fa-route', $publicMetrics['trips'], lang('App.home_total_trips')],
    ['fa-water', $publicMetrics['activeTrips'], lang('App.home_active_trips')],
] as [$icon,$value,$label]): ?><div class="stat-item"><div class="stat-icon"><i class="fa-solid <?= esc($icon) ?>"></i></div><div><div class="stat-number"><?= number_format((int) $value) ?></div><div class="stat-label"><?= esc($label) ?></div></div></div><?php endforeach; ?>
</div></div></section>
<section class="section-pad"><div class="container"><div class="section-title"><h2><?= esc(portsys_text('why_us')) ?></h2><p><?= esc(portsys_text('why_us_text')) ?></p></div><div class="row g-4"><?php foreach($features as $feature): ?><div class="col-md-6 col-xl"><div class="feature-card"><div class="circle-icon"><i class="fa-solid <?= esc($feature[2]) ?>"></i></div><h3><?= esc($feature[0]) ?></h3><p><?= esc($feature[1]) ?></p></div></div><?php endforeach; ?></div></div></section>
<section class="section-pad pt-0"><div class="container"><div class="section-title"><h2><?= esc(lang('App.latest_scheduled_trips')) ?></h2><p><?= esc(lang('App.latest_scheduled_trips_desc')) ?></p></div><?php if ($latestTrips === []): ?><div class="form-box text-center py-5"><i class="fa-regular fa-calendar-xmark fa-3x text-primary mb-3"></i><p class="mb-0 text-muted"><?= esc(lang('App.no_upcoming_trips')) ?></p></div><?php else: ?><div class="row g-4"><?php foreach($latestTrips as $index=>$trip): ?><div class="col-md-6 col-lg-3"><article class="news-card h-100"><img src="<?= portsys_asset('news-' . (($index % 4) + 1) . '.jpg') ?>" alt="<?= esc($trip['ship_name'] ?? '') ?>"><div class="body"><div class="text-primary small fw-bold mb-2"><i class="fa-solid fa-ship me-1"></i><?= esc($trip['ship_name'] ?? '') ?></div><h3><?= esc($trip['departure_port_name'] ?? '') ?> <i class="fa-solid fa-arrow-right-long mx-1"></i> <?= esc($trip['arrival_port_name'] ?? '') ?></h3><p><i class="fa-regular fa-calendar me-1"></i><?= esc(format_datetime($trip['departure_date'] ?? null)) ?></p><a href="<?= site_url($isLoggedIn ? 'trips/show/' . (int) $trip['id'] : 'login') ?>" class="btn btn-sm btn-ps-outline w-100"><?= esc(lang('App.public_view_details')) ?></a></div></article></div><?php endforeach; ?></div><?php endif; ?><div class="text-center mt-4"><a href="<?= site_url('news') ?>" class="btn btn-ps btn-ps-primary"><?= esc(portsys_text('read_more')) ?></a></div></div></section>
<?= $this->endSection() ?>
