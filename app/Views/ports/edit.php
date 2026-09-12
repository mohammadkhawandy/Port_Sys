<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.edit_port_title')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $port = $port ?? []; $places = $places ?? []; $currentPlaceId = (int) old('place_id', $port['place_id'] ?? 0); ?>
<div class="page-card p-4 p-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
        <div>
            <div class="section-title"><?= esc(lang('App.nav_ports')) ?></div>
            <h1 class="page-title h2 mb-2"><?= esc(lang('App.edit_port_title')) ?></h1>
            <p class="muted mb-0"><?= esc(lang('App.edit_port_desc')) ?></p>
        </div>
        <span class="badge badge-soft rounded-pill px-3 py-2"><i class="fa-solid fa-link me-1"></i><?= esc(lang('App.ports_linked_to_locations')) ?></span>
    </div>
    <form action="<?= site_url('ports/update/' . (int) ($port['id'] ?? 0)) ?>" method="post" id="portForm">
        <?= csrf_field() ?>
        <div class="form-shell p-3 p-lg-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-lg-9">
                    <label for="place" class="form-label"><?= esc(lang('App.source_location')) ?></label>
                    <select id="place" name="place_id" class="form-select">
                        <option value=""><?= esc(lang('App.manual_entry')) ?></option>
                        <?php foreach ($places as $place): ?>
                            <option
                                value="<?= (int) ($place['id'] ?? 0) ?>"
                                data-code="<?= esc($place['code'] ?? '') ?>"
                                data-name="<?= esc($place['name_en'] ?? $place['name'] ?? '') ?>"
                                data-country="<?= esc($place['country_en'] ?? $place['country'] ?? '') ?>"
                                data-city="<?= esc($place['city_en'] ?? '') ?>"
                                data-lat="<?= esc($place['latitude'] ?? '') ?>"
                                data-lng="<?= esc($place['longitude'] ?? '') ?>"
                                <?= $currentPlaceId === (int) ($place['id'] ?? 0) ? 'selected' : '' ?>>
                                <?= esc(place_display_name($place)) ?> — <?= esc(place_display_city($place)) ?>، <?= esc(place_display_country($place)) ?><?= !empty($place['code']) ? ' (' . esc($place['code']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 d-grid">
                    <a href="<?= site_url('places') ?>" class="btn btn-outline-light"><i class="fa-solid fa-location-dot me-2"></i><?= esc(lang('App.nav_places')) ?></a>
                </div>
            </div>
            <div class="form-text mt-2"><?= esc(lang('App.place_coordinates_hint')) ?> <?= esc(lang('App.manual_port_creation_still_available')) ?></div>
        </div>
        <div class="row g-3">
            <div class="col-md-6"><label for="name" class="form-label"><?= esc(lang('App.name')) ?></label><input type="text" class="form-control" id="name" name="name" value="<?= esc(old('name', $port['name'] ?? '')) ?>" minlength="2" maxlength="255" required></div>
            <div class="col-md-3"><label for="country" class="form-label"><?= esc(lang('App.country')) ?></label><input type="text" class="form-control" id="country" name="country" value="<?= esc(old('country', $port['country'] ?? '')) ?>" minlength="2" maxlength="100" required></div>
            <div class="col-md-3"><label for="city" class="form-label"><?= esc(lang('App.city')) ?></label><input type="text" class="form-control" id="city" name="city" value="<?= esc(old('city', $port['city'] ?? '')) ?>" minlength="2" maxlength="100" required></div>
            <div class="col-md-6"><label for="latitude" class="form-label"><?= esc(lang('App.latitude')) ?></label><input type="number" step="0.000001" min="-90" max="90" class="form-control" id="latitude" name="latitude" value="<?= esc(old('latitude', $port['latitude'] ?? '')) ?>" required dir="ltr"></div>
            <div class="col-md-6"><label for="longitude" class="form-label"><?= esc(lang('App.longitude')) ?></label><input type="number" step="0.000001" min="-180" max="180" class="form-control" id="longitude" name="longitude" value="<?= esc(old('longitude', $port['longitude'] ?? '')) ?>" required dir="ltr"></div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-2"></i><?= esc(lang('App.update_port')) ?></button>
            <a href="<?= site_url('ports') ?>" class="btn btn-outline-light px-4"><?= esc(lang('App.cancel')) ?></a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const selector = document.getElementById('place');
    const fields = ['name','country','city','latitude','longitude'].reduce((all,id) => ({...all,[id]:document.getElementById(id)}),{});
    selector?.addEventListener('change', () => {
        const option = selector.selectedOptions[0];
        if (!option?.dataset.lat || !option?.dataset.lng) return;
        fields.name.value = option.dataset.name || '';
        fields.country.value = option.dataset.country || '';
        fields.city.value = option.dataset.city || option.dataset.country || '';
        fields.latitude.value = option.dataset.lat;
        fields.longitude.value = option.dataset.lng;
        fields.name.focus();
    });
});
</script>
<?= $this->endSection() ?>
