<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('ships')) ?> - <?= esc(portsys_text('brand')) ?><?= $this->endSection() ?>
<?= $this->section('head') ?>
<style>
    .ships-directory-hero{background:linear-gradient(135deg,#f8fbff,#edf5ff);border:1px solid var(--ps-border);border-radius:18px;padding:1.25rem;box-shadow:0 18px 42px rgba(5,42,93,.09);margin-bottom:1.4rem;overflow:hidden}.ships-directory-hero .visual{min-height:220px;border-radius:16px;background:linear-gradient(90deg,rgba(5,42,93,.08),rgba(5,42,93,.02)),url('<?= portsys_asset('ship-real-1.jpg') ?>') center/cover no-repeat}.directory-pill{display:inline-flex;align-items:center;gap:.45rem;padding:.38rem .78rem;border-radius:999px;background:#edf6ff;color:#0d56ad;font-weight:900;font-size:.78rem}.directory-stat{background:#fff;border:1px solid #dfeaf6;border-radius:14px;padding:.85rem 1rem;height:100%;box-shadow:0 10px 22px rgba(5,42,93,.05)}.directory-stat strong{display:block;font-size:1.25rem;color:#0f5bb8;line-height:1.1}.directory-stat span{color:#62758d;font-size:.78rem;font-weight:800}.ship-card-pro{background:#fff;border:1px solid #dfe8f3;border-radius:18px;overflow:hidden;height:100%;box-shadow:0 12px 30px rgba(5,42,93,.07);transition:.22s}.ship-card-pro:hover{transform:translateY(-5px);box-shadow:0 20px 45px rgba(5,42,93,.13)}.ship-img{height:170px;position:relative;overflow:hidden;background:#eaf2fb}.ship-img img{width:100%;height:100%;object-fit:cover;transition:.35s}.ship-card-pro:hover .ship-img img{transform:scale(1.045)}.ship-img:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(4,31,72,0) 35%,rgba(4,31,72,.78));}.ship-type-badge{position:absolute;inset-inline-start:1rem;bottom:1rem;z-index:1;background:rgba(255,255,255,.95);color:#063879;border-radius:999px;padding:.32rem .78rem;font-size:.78rem;font-weight:900}.ship-icon-float{position:absolute;inset-inline-end:1rem;bottom:1rem;z-index:1;width:42px;height:42px;border-radius:14px;background:#0f5bb8;color:#fff;display:grid;place-items:center;box-shadow:0 10px 24px rgba(15,91,184,.28)}.ship-body{padding:1.15rem}.ship-title-row{display:flex;justify-content:space-between;gap:.75rem;align-items:flex-start;margin-bottom:.8rem}.ship-title-row h3{font-size:1.12rem;font-weight:900;margin:0;line-height:1.45}.ship-status{white-space:nowrap;border-radius:999px;padding:.25rem .62rem;font-size:.72rem;font-weight:900}.ship-status.available{background:#eaf8f0;color:#147345}.ship-status.busy{background:#fff1f0;color:#b42318}.ship-status.scheduled{background:#fff8e6;color:#925c00}.ship-facts{display:grid;grid-template-columns:repeat(2,1fr);gap:.65rem;margin:.85rem 0}.ship-facts .fact{border:1px solid #e5edf6;background:#f8fbff;border-radius:13px;padding:.72rem}.ship-facts .fact small{display:block;color:#6b7d91;font-size:.72rem;font-weight:800}.ship-facts .fact b{display:block;color:#10294c;font-size:.94rem;margin-top:.1rem}.route-box{border-top:1px solid #e5edf6;margin-top:.7rem;padding-top:.85rem;color:#61758d;font-size:.84rem}.route-line{display:flex;align-items:center;gap:.5rem;font-weight:900;color:#10294c;margin-bottom:.25rem}.route-line i{color:#0f5bb8}.empty-public-state{border:1px dashed #b9cbe1;border-radius:18px;background:#f8fbff}.filter-box.sticky-filter{position:relative;z-index:2}.text-balance{text-wrap:balance}@media(max-width:991px){.ships-directory-hero .visual{min-height:180px}.ship-img{height:155px}}@media(max-width:575px){.ship-facts{grid-template-columns:1fr}.ship-title-row{display:block}.ship-status{display:inline-block;margin-top:.5rem}}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$ships = $ships ?? [];
$types = $types ?? [];
$filters = $filters ?? ['q' => '', 'type' => ''];
$shipTrips = $shipTrips ?? [];
$shipImages = ['ship-real-1.jpg', 'ship-real-2.jpg', 'ship-real-3.jpg', 'ship-real-4.jpg', 'ship-real-5.jpg', 'ship-real-6.jpg'];
$totalCapacity = array_sum(array_map(static fn ($ship) => (int) ($ship['capacity'] ?? 0), $ships));
$visibleTypes = count(array_unique(array_filter(array_map(static fn ($ship) => (string) ($ship['type'] ?? ''), $ships))));
?>
<section class="inner-hero">
    <div class="container">
        <h1><?= esc(portsys_text('ships')) ?></h1>
        <div class="breadcrumb-mini"><?= esc(portsys_text('breadcrumb_home')) ?> / <?= esc(portsys_text('ships')) ?></div>
    </div>
</section>
<section class="section-pad">
    <div class="container">
        <div class="ships-directory-hero">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <span class="directory-pill mb-3"><i class="fa-solid fa-ship"></i><?= esc(lang('App.public_ships')) ?></span>
                    <h2 class="fw-black fw-bold text-balance mb-2" style="font-size:clamp(1.75rem,3vw,2.55rem);color:#10294c"><?= esc(lang('App.public_ships_title')) ?></h2>
                    <p class="text-muted mb-4" style="max-width:680px"><?= esc(lang('App.public_ships_intro')) ?></p>
                    <div class="row g-3">
                        <div class="col-6 col-md-4"><div class="directory-stat"><strong><?= number_format((int) ($directoryTotal ?? count($ships))) ?></strong><span><?= esc(lang('App.directory_records')) ?></span></div></div>
                        <div class="col-6 col-md-4"><div class="directory-stat"><strong><?= number_format($totalCapacity) ?></strong><span><?= esc(lang('App.directory_capacity')) ?></span></div></div>
                        <div class="col-12 col-md-4"><div class="directory-stat"><strong><?= number_format($visibleTypes) ?></strong><span><?= esc(lang('App.directory_types')) ?></span></div></div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="visual" role="img" aria-label="<?= esc(lang('App.public_ships_title')) ?>"></div>
                </div>
            </div>
        </div>

        <form method="get" action="<?= site_url('ships-directory') ?>" class="filter-box sticky-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label for="public-ship-q" class="form-label"><?= esc(lang('App.search')) ?></label>
                    <input id="public-ship-q" type="search" name="q" class="form-control" value="<?= esc($filters['q']) ?>" placeholder="<?= esc(lang('App.ship_directory_search_placeholder')) ?>">
                </div>
                <div class="col-md-3">
                    <label for="public-ship-type" class="form-label"><?= esc(lang('App.type')) ?></label>
                    <select id="public-ship-type" name="type" class="form-select">
                        <option value=""><?= esc(lang('App.all_types')) ?></option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= esc($type) ?>" <?= $filters['type'] === $type ? 'selected' : '' ?>><?= esc($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-ps btn-ps-primary" type="submit"><i class="fa-solid fa-filter me-2"></i><?= esc(lang('App.apply_filters')) ?></button>
                </div>
            </div>
        </form>

        <?php if ($ships === []): ?>
            <div class="empty-public-state text-center py-5 px-3">
                <i class="fa-regular fa-folder-open fa-3x text-primary mb-3"></i>
                <p class="mb-0 text-muted"><?= esc(lang('App.public_ships_empty')) ?></p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($ships as $index => $ship): ?>
                    <?php
                        $shipId = (int) ($ship['id'] ?? 0);
                        $trip = $shipTrips[$shipId] ?? null;
                        $status = $trip['status'] ?? 'available';
                        $statusLabel = lang('App.ship_status_' . $status);
                        $image = $shipImages[$index % count($shipImages)];
                    ?>
                    <div class="col-md-6 col-xl-4">
                        <article class="ship-card-pro">
                            <div class="ship-img">
                                <img src="<?= portsys_asset($image) ?>" alt="<?= esc($ship['name'] ?? lang('App.ship')) ?>" loading="lazy">
                                <span class="ship-type-badge"><?= esc($ship['type'] ?? lang('App.ship')) ?></span>
                                <span class="ship-icon-float"><i class="fa-solid fa-ship"></i></span>
                            </div>
                            <div class="ship-body">
                                <div class="ship-title-row">
                                    <h3><?= esc($ship['name'] ?? '') ?></h3>
                                    <span class="ship-status <?= esc($status) ?>"><?= esc($statusLabel) ?></span>
                                </div>
                                <div class="ship-facts">
                                    <div class="fact"><small><?= esc(lang('App.capacity')) ?></small><b><?= number_format((int) ($ship['capacity'] ?? 0)) ?> <?= esc(lang('App.ship_capacity_unit')) ?></b></div>
                                    <div class="fact"><small><?= esc(lang('App.type')) ?></small><b><?= esc($ship['type'] ?? '—') ?></b></div>
                                </div>
                                <div class="route-box">
                                    <?php if ($trip): ?>
                                        <div class="route-line"><i class="fa-solid fa-route"></i><?= esc(trim(($trip['departure_name'] ?? '') . ' → ' . ($trip['arrival_name'] ?? ''), ' →')) ?></div>
                                        <div><?= esc($status === 'busy' ? lang('App.ship_current_trip') : lang('App.ship_next_departure')) ?>: <span dir="ltr"><?= esc(format_datetime($status === 'busy' ? ($trip['arrival_date'] ?? '') : ($trip['departure_date'] ?? ''))) ?></span></div>
                                    <?php else: ?>
                                        <div class="route-line"><i class="fa-solid fa-circle-check"></i><?= esc(lang('App.ship_not_scheduled')) ?></div>
                                        <div><?= esc(lang('App.ship_status_available')) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-4"><?= $pager?->links() ?></div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
