<?php
$place = $place ?? [];
$action = $action ?? site_url('places');
$submitLabel = $submitLabel ?? lang('App.create_place');
$submitIcon = $submitIcon ?? 'fa-plus';
?>
<div class="page-card p-4 p-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
        <div>
            <div class="section-title"><?= esc(lang('App.nav_places')) ?></div>
            <h1 class="page-title h2 mb-2"><?= esc($pageHeading ?? lang('App.add_place_title')) ?></h1>
            <p class="muted mb-0"><?= esc($pageDescription ?? lang('App.add_place_desc')) ?></p>
        </div>
        <span class="badge badge-soft rounded-pill px-3 py-2"><i class="fa-solid fa-language me-1"></i><?= esc(lang('App.place_form_bilingual_hint')) ?></span>
    </div>

    <form action="<?= esc($action) ?>" method="post" id="placeForm">
        <?= csrf_field() ?>

        <div class="form-shell p-3 p-lg-4 mb-4">
            <div class="d-flex align-items-center gap-2 mb-3"><span class="kpi-icon" style="width:40px;height:40px"><i class="fa-solid fa-id-card"></i></span><h2 class="h5 panel-title mb-0"><?= esc(lang('App.location_identity')) ?></h2></div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="code" class="form-label"><?= esc(lang('App.location_code')) ?></label>
                    <input id="code" name="code" class="form-control text-uppercase" maxlength="12" value="<?= esc(old('code', $place['code'] ?? '')) ?>" placeholder="AEJEA" dir="ltr">
                    <div class="form-text"><?= esc(lang('App.location_code_hint')) ?></div>
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label"><?= esc(lang('App.location_type')) ?></label>
                    <?php $selectedType = old('type', $place['type'] ?? 'port'); ?>
                    <select id="type" name="type" class="form-select" required>
                        <?php foreach (['port','terminal','anchorage','city','other'] as $type): ?>
                            <option value="<?= esc($type) ?>" <?= $selectedType === $type ? 'selected' : '' ?>><?= esc(place_type_label($type)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label"><?= esc(lang('App.location_status')) ?></label>
                    <?php $selectedStatus = old('status', $place['status'] ?? 'active'); ?>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>><?= esc(lang('App.active')) ?></option>
                        <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>><?= esc(lang('App.inactive')) ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="name_en" class="form-label"><?= esc(lang('App.name_en')) ?></label>
                    <input id="name_en" name="name_en" class="form-control" maxlength="255" value="<?= esc(old('name_en', $place['name_en'] ?? $place['name'] ?? '')) ?>" required dir="ltr">
                </div>
                <div class="col-md-6">
                    <label for="name_ar" class="form-label"><?= esc(lang('App.name_ar')) ?></label>
                    <input id="name_ar" name="name_ar" class="form-control" maxlength="255" value="<?= esc(old('name_ar', $place['name_ar'] ?? '')) ?>" required dir="rtl">
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-6">
                <div class="form-shell p-3 p-lg-4 h-100">
                    <div class="d-flex align-items-center gap-2 mb-3"><span class="kpi-icon" style="width:40px;height:40px"><i class="fa-solid fa-earth-americas"></i></span><h2 class="h5 panel-title mb-0"><?= esc(lang('App.location_geography')) ?></h2></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="country_code" class="form-label"><?= esc(lang('App.country_code')) ?></label>
                            <input id="country_code" name="country_code" class="form-control text-uppercase" maxlength="2" value="<?= esc(old('country_code', $place['country_code'] ?? '')) ?>" placeholder="AE" dir="ltr">
                        </div>
                        <div class="col-md-4">
                            <label for="country_en" class="form-label"><?= esc(lang('App.country_en')) ?></label>
                            <input id="country_en" name="country_en" class="form-control" maxlength="100" value="<?= esc(old('country_en', $place['country_en'] ?? $place['country'] ?? '')) ?>" required dir="ltr">
                        </div>
                        <div class="col-md-4">
                            <label for="country_ar" class="form-label"><?= esc(lang('App.country_ar')) ?></label>
                            <input id="country_ar" name="country_ar" class="form-control" maxlength="100" value="<?= esc(old('country_ar', $place['country_ar'] ?? '')) ?>" required dir="rtl">
                        </div>
                        <div class="col-md-6">
                            <label for="city_en" class="form-label"><?= esc(lang('App.city_en')) ?></label>
                            <input id="city_en" name="city_en" class="form-control" maxlength="100" value="<?= esc(old('city_en', $place['city_en'] ?? '')) ?>" dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label for="city_ar" class="form-label"><?= esc(lang('App.city_ar')) ?></label>
                            <input id="city_ar" name="city_ar" class="form-control" maxlength="100" value="<?= esc(old('city_ar', $place['city_ar'] ?? '')) ?>" dir="rtl">
                        </div>
                        <div class="col-md-6">
                            <label for="latitude" class="form-label"><?= esc(lang('App.latitude')) ?></label>
                            <input type="number" step="0.000001" min="-90" max="90" class="form-control" id="latitude" name="latitude" value="<?= esc(old('latitude', $place['latitude'] ?? '')) ?>" required dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label"><?= esc(lang('App.longitude')) ?></label>
                            <input type="number" step="0.000001" min="-180" max="180" class="form-control" id="longitude" name="longitude" value="<?= esc(old('longitude', $place['longitude'] ?? '')) ?>" required dir="ltr">
                        </div>
                        <div class="col-12">
                            <label for="timezone" class="form-label"><?= esc(lang('App.timezone')) ?></label>
                            <input id="timezone" name="timezone" class="form-control" maxlength="64" value="<?= esc(old('timezone', $place['timezone'] ?? '')) ?>" placeholder="Asia/Dubai" dir="ltr">
                            <div class="form-text"><?= esc(lang('App.timezone_hint')) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="form-shell p-3 p-lg-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div><div class="section-title mb-1"><?= esc(lang('App.location_preview')) ?></div><h2 class="h5 panel-title mb-0"><?= esc(lang('App.use_map_to_select')) ?></h2></div>
                        <a id="openPlaceMap" class="btn btn-sm btn-outline-light disabled" href="#" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i><?= esc(lang('App.open_in_map')) ?></a>
                    </div>
                    <div id="placePickerMap" class="rounded-4 overflow-hidden border" style="height:390px;background:var(--surface-2)"><div class="h-100 d-flex align-items-center justify-content-center text-center p-4 muted"><div><i class="fa-solid fa-map-location-dot fa-2x mb-2"></i><div><?= esc(lang('App.map_loading')) ?></div></div></div></div>
                    <p class="small muted mt-3 mb-1"><i class="fa-solid fa-location-crosshairs me-1"></i><?= esc(lang('App.map_hint')) ?></p>
                    <p class="small text-warning-emphasis mb-0"><i class="fa-solid fa-triangle-exclamation me-1"></i><?= esc(lang('App.coordinate_disclaimer')) ?></p>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid <?= esc($submitIcon) ?> me-2"></i><?= esc($submitLabel) ?></button>
            <a href="<?= site_url('places') ?>" class="btn btn-outline-light px-4"><?= esc(lang('App.cancel')) ?></a>
        </div>
    </form>
</div>
