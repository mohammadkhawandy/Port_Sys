<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('ports')) ?> - <?= esc(portsys_text('brand')) ?><?= $this->endSection() ?>
<?= $this->section('head') ?>
<style>
    .ports-directory-hero{background:linear-gradient(135deg,#f8fbff,#edf5ff);border:1px solid var(--ps-border);border-radius:18px;padding:1.25rem;box-shadow:0 18px 42px rgba(5,42,93,.09);margin-bottom:1.4rem;overflow:hidden}.ports-directory-hero .visual{min-height:230px;border-radius:16px;background:linear-gradient(90deg,rgba(5,42,93,.05),rgba(5,42,93,.01)),url('<?= portsys_asset('port-real-2.jpg') ?>') center/cover no-repeat}.directory-pill{display:inline-flex;align-items:center;gap:.45rem;padding:.38rem .78rem;border-radius:999px;background:#edf6ff;color:#0d56ad;font-weight:900;font-size:.78rem}.directory-stat{background:#fff;border:1px solid #dfeaf6;border-radius:14px;padding:.85rem 1rem;height:100%;box-shadow:0 10px 22px rgba(5,42,93,.05)}.directory-stat strong{display:block;font-size:1.25rem;color:#0f5bb8;line-height:1.1}.directory-stat span{color:#62758d;font-size:.78rem;font-weight:800}.port-card-modern{height:100%;background:#fff;border:1px solid #dfe8f3;border-radius:18px;overflow:hidden;box-shadow:0 12px 30px rgba(5,42,93,.07);transition:.22s}.port-card-modern:hover{transform:translateY(-5px);box-shadow:0 20px 45px rgba(5,42,93,.13)}.port-photo{height:178px;position:relative;overflow:hidden;background:#eaf2fb}.port-photo img{width:100%;height:100%;object-fit:cover;transition:.35s}.port-card-modern:hover .port-photo img{transform:scale(1.045)}.port-photo:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(4,31,72,0) 40%,rgba(4,31,72,.76));}.port-code{position:absolute;z-index:1;inset-inline-start:1rem;bottom:1rem;background:rgba(255,255,255,.96);color:#063879;border-radius:999px;padding:.34rem .78rem;font-size:.76rem;font-weight:900;direction:ltr}.port-anchor{position:absolute;z-index:1;inset-inline-end:1rem;bottom:1rem;width:42px;height:42px;border-radius:14px;background:#0f5bb8;color:#fff;display:grid;place-items:center;box-shadow:0 10px 24px rgba(15,91,184,.28)}.port-modern-body{padding:1.15rem}.port-modern-body h3{font-size:1.12rem;font-weight:900;margin:0 0 .35rem;line-height:1.45}.port-location{font-weight:800;color:#60758e;font-size:.88rem;display:flex;align-items:center;gap:.45rem}.port-location i{color:#0f5bb8}.port-facts{display:grid;grid-template-columns:repeat(2,1fr);gap:.65rem;margin-top:1rem}.port-fact{border:1px solid #e5edf6;background:#f8fbff;border-radius:13px;padding:.7rem}.port-fact small{display:block;color:#6b7d91;font-size:.72rem;font-weight:800}.port-fact b{display:block;color:#10294c;font-size:.88rem;margin-top:.1rem}.port-card-modern .coord{direction:ltr;unicode-bidi:embed}.filter-box.sticky-filter{position:relative;z-index:2}.empty-public-state{border:1px dashed #b9cbe1;border-radius:18px;background:#f8fbff}.text-balance{text-wrap:balance}@media(max-width:991px){.ports-directory-hero .visual{min-height:190px}.port-photo{height:165px}}@media(max-width:575px){.port-facts{grid-template-columns:1fr}}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$ports = $ports ?? [];
$countries = $countries ?? [];
$filters = $filters ?? ['q' => '', 'country' => ''];
$usesPlacesDirectory = (bool) ($usesPlacesDirectory ?? false);
$isRtl = portsys_is_rtl();
$portImages = ['port-real-1.jpg', 'port-real-2.jpg', 'port-real-3.jpg', 'port-real-4.jpg', 'port-real-5.jpg', 'port-real-6.jpg', 'port-real-7.jpg', 'port-real-8.jpg', 'port-real-9.jpg'];
$visibleCountries = count(array_unique(array_filter(array_map(static function ($port) use ($usesPlacesDirectory, $isRtl) {
    if ($usesPlacesDirectory) {
        return $isRtl ? (string) ($port['country_ar'] ?? $port['country'] ?? '') : (string) ($port['country_en'] ?? $port['country'] ?? '');
    }
    return (string) ($port['country'] ?? '');
}, $ports))));
$portName = static function (array $port) use ($usesPlacesDirectory, $isRtl): string {
    if ($usesPlacesDirectory) {
        return $isRtl
            ? (string) ($port['name_ar'] ?? $port['name'] ?? $port['name_en'] ?? '')
            : (string) ($port['name_en'] ?? $port['name'] ?? $port['name_ar'] ?? '');
    }
    return (string) ($port['name'] ?? '');
};
$portCountry = static function (array $port) use ($usesPlacesDirectory, $isRtl): string {
    if ($usesPlacesDirectory) {
        return $isRtl
            ? (string) ($port['country_ar'] ?? $port['country'] ?? $port['country_en'] ?? '')
            : (string) ($port['country_en'] ?? $port['country'] ?? $port['country_ar'] ?? '');
    }
    return (string) ($port['country'] ?? '');
};
$portCity = static function (array $port) use ($usesPlacesDirectory, $isRtl): string {
    if ($usesPlacesDirectory) {
        return $isRtl
            ? (string) ($port['city_ar'] ?? $port['city_en'] ?? '')
            : (string) ($port['city_en'] ?? $port['city_ar'] ?? '');
    }
    return (string) ($port['city'] ?? '');
};
?>
<section class="inner-hero">
    <div class="container">
        <h1><?= esc(portsys_text('ports')) ?></h1>
        <div class="breadcrumb-mini"><?= esc(portsys_text('breadcrumb_home')) ?> / <?= esc(portsys_text('ports')) ?></div>
    </div>
</section>
<section class="section-pad">
    <div class="container">
        <div class="ports-directory-hero">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <span class="directory-pill mb-3"><i class="fa-solid fa-anchor"></i><?= esc(lang('App.public_ports')) ?></span>
                    <h2 class="fw-bold text-balance mb-2" style="font-size:clamp(1.75rem,3vw,2.55rem);color:#10294c"><?= esc(lang('App.public_ports_title')) ?></h2>
                    <p class="text-muted mb-4" style="max-width:680px"><?= esc(lang('App.public_ports_intro')) ?></p>
                    <div class="row g-3">
                        <div class="col-6 col-md-4"><div class="directory-stat"><strong><?= number_format((int) ($directoryTotal ?? count($ports))) ?></strong><span><?= esc(lang('App.directory_locations')) ?></span></div></div>
                        <div class="col-6 col-md-4"><div class="directory-stat"><strong><?= number_format($visibleCountries) ?></strong><span><?= esc(lang('App.directory_countries')) ?></span></div></div>
                        <div class="col-12 col-md-4"><div class="directory-stat"><strong>GPS</strong><span><?= esc(lang('App.port_coordinates')) ?></span></div></div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="visual" role="img" aria-label="<?= esc(lang('App.public_ports_title')) ?>"></div>
                </div>
            </div>
        </div>

        <form method="get" action="<?= site_url('ports-directory') ?>" class="filter-box sticky-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label for="public-port-q" class="form-label"><?= esc(lang('App.search')) ?></label>
                    <input id="public-port-q" type="search" name="q" class="form-control" value="<?= esc($filters['q']) ?>" placeholder="<?= esc(lang('App.directory_search_placeholder')) ?>">
                </div>
                <div class="col-md-3">
                    <label for="public-port-country" class="form-label"><?= esc(lang('App.country')) ?></label>
                    <select id="public-port-country" name="country" class="form-select">
                        <option value=""><?= esc(lang('App.all_countries')) ?></option>
                        <?php foreach ($countries as $country): ?>
                            <?php $countryValue = is_array($country) ? (string) ($country['value'] ?? '') : (string) $country; $countryLabel = is_array($country) ? (string) ($country['label'] ?? $countryValue) : (string) $country; ?>
                            <option value="<?= esc($countryValue) ?>" <?= $filters['country'] === $countryValue ? 'selected' : '' ?>><?= esc($countryLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-ps btn-ps-primary" type="submit"><i class="fa-solid fa-filter me-2"></i><?= esc(lang('App.apply_filters')) ?></button>
                </div>
            </div>
        </form>

        <?php if ($ports === []): ?>
            <div class="empty-public-state text-center py-5 px-3">
                <i class="fa-regular fa-folder-open fa-3x text-primary mb-3"></i>
                <p class="mb-0 text-muted"><?= esc(lang('App.public_ports_empty')) ?></p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($ports as $index => $port): ?>
                    <?php
                        $image = $portImages[$index % count($portImages)];
                        $name = $portName($port);
                        $city = $portCity($port);
                        $country = $portCountry($port);
                        $code = trim((string) ($port['code'] ?? '')) ?: strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4));
                        $lat = (string) ($port['latitude'] ?? '');
                        $lng = (string) ($port['longitude'] ?? '');
                    ?>
                    <div class="col-md-6 col-xl-4">
                        <article class="port-card-modern">
                            <div class="port-photo">
                                <img src="<?= portsys_asset($image) ?>" alt="<?= esc(sprintf(lang('App.port_real_photo_alt'), $name)) ?>" loading="lazy">
                                <span class="port-code"><?= esc($code) ?></span>
                                <span class="port-anchor"><i class="fa-solid fa-anchor"></i></span>
                            </div>
                            <div class="port-modern-body">
                                <h3><?= esc($name) ?></h3>
                                <div class="port-location"><i class="fa-solid fa-location-dot"></i><?= esc(implode('، ', array_filter([$city, $country]))) ?></div>
                                <div class="port-facts">
                                    <div class="port-fact"><small><?= esc(lang('App.latitude')) ?></small><b class="coord"><?= esc($lat) ?></b></div>
                                    <div class="port-fact"><small><?= esc(lang('App.longitude')) ?></small><b class="coord"><?= esc($lng) ?></b></div>
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
