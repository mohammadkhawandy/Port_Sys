<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.places_management')) ?><?= $this->endSection() ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
.location-kpi{display:flex;align-items:center;gap:.85rem;padding:1rem 1.1rem;background:var(--surface);border:1px solid var(--border);border-radius:14px;height:100%}.location-kpi .icon{width:44px;height:44px;border-radius:13px;display:grid;place-items:center;background:var(--primary-soft);color:var(--primary)}.location-kpi strong{display:block;font-size:1.25rem}.location-kpi small{color:var(--muted)}
.location-code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-weight:800;color:var(--primary)}.location-name small{display:block;color:var(--muted);font-weight:500}.map-shell{height:430px;border-radius:16px;overflow:hidden;border:1px solid var(--border);background:var(--surface-2)}.leaflet-popup-content-wrapper,.leaflet-popup-tip{background:var(--surface);color:var(--text)}.source-pill{display:inline-flex;align-items:center;gap:.35rem;border-radius:999px;padding:.28rem .55rem;font-size:.68rem;font-weight:800}.source-catalog{background:#e9f8f1;color:#16734b}.source-manual{background:var(--surface-2);color:var(--muted);border:1px solid var(--border)}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$places = $places ?? [];
$mapPlaces = $mapPlaces ?? [];
$filters = $filters ?? ['q'=>'','country'=>'','type'=>'','status'=>''];
$stats = $stats ?? ['total'=>0,'active'=>0,'countries'=>0,'seeded'=>0];
$mapData = array_map(static fn(array $place): array => [
    'id' => (int) ($place['id'] ?? 0),
    'code' => (string) ($place['code'] ?? ''),
    'name' => place_display_name($place),
    'country' => place_display_country($place),
    'city' => place_display_city($place),
    'latitude' => (float) ($place['latitude'] ?? 0),
    'longitude' => (float) ($place['longitude'] ?? 0),
    'type' => place_type_label($place['type'] ?? 'port'),
    'status' => place_status_label($place['status'] ?? 'active'),
    'editUrl' => site_url('places/edit/' . (int) ($place['id'] ?? 0)),
], $mapPlaces);
?>
<div class="page-card p-4 p-lg-5 mb-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-4">
        <div>
            <div class="section-title"><?= esc(lang('App.nav_places')) ?></div>
            <h1 class="page-title h2 mb-2"><?= esc(lang('App.places_management')) ?></h1>
            <p class="muted mb-0"><?= esc(lang('App.place_catalog_note')) ?></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="post" action="<?= site_url('places/import-defaults') ?>" onsubmit="return confirm(<?= json_encode(lang('App.import_real_locations_confirm'), JSON_UNESCAPED_UNICODE) ?>)">
                <?= csrf_field() ?>
                <button class="btn btn-outline-light px-3" type="submit"><i class="fa-solid fa-rotate me-2"></i><?= esc(lang('App.restore_catalog')) ?></button>
            </form>
            <a href="<?= site_url('places/create') ?>" class="btn btn-primary px-4"><i class="fa-solid fa-plus me-2"></i><?= esc(lang('App.add_new_place')) ?></a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['icon'=>'fa-location-dot','value'=>$stats['total'],'label'=>lang('App.places_total_label')],
        ['icon'=>'fa-circle-check','value'=>$stats['active'],'label'=>lang('App.places_active_label')],
        ['icon'=>'fa-earth-americas','value'=>$stats['countries'],'label'=>lang('App.places_countries_label')],
        ['icon'=>'fa-anchor','value'=>$stats['seeded'],'label'=>lang('App.places_catalog_label')],
    ] as $item): ?>
        <div class="col-6 col-xl-3"><div class="location-kpi"><span class="icon"><i class="fa-solid <?= esc($item['icon']) ?>"></i></span><span><strong><?= number_format((int) $item['value']) ?></strong><small><?= esc($item['label']) ?></small></span></div></div>
    <?php endforeach; ?>
</div>

<div class="table-shell p-3 p-lg-4 mb-4">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><div class="section-title mb-1"><?= esc(lang('App.filter_locations')) ?></div><h2 class="h5 panel-title mb-0"><?= esc(lang('App.location_summary')) ?></h2></div><span class="small muted"><i class="fa-solid fa-circle-info me-1"></i><?= esc(lang('App.catalog_up_to_date')) ?></span></div>
    <form method="get" class="row g-3 align-items-end">
        <div class="col-lg-4"><label class="form-label" for="place-q"><?= esc(lang('App.search')) ?></label><input id="place-q" class="form-control" name="q" value="<?= esc($filters['q']) ?>" placeholder="<?= esc(lang('App.search')) ?>"></div>
        <div class="col-sm-6 col-lg-2"><label class="form-label" for="place-country"><?= esc(lang('App.country')) ?></label><select id="place-country" class="form-select" name="country"><option value=""><?= esc(lang('App.all_countries')) ?></option><?php foreach (($countries ?? []) as $country): ?><option value="<?= esc($country['country_code']) ?>" <?= $filters['country'] === ($country['country_code'] ?? '') ? 'selected' : '' ?>><?= esc(portsys_is_rtl() ? ($country['country_ar'] ?: $country['country']) : ($country['country_en'] ?: $country['country'])) ?></option><?php endforeach; ?></select></div>
        <div class="col-sm-6 col-lg-2"><label class="form-label" for="place-type"><?= esc(lang('App.location_type')) ?></label><select id="place-type" class="form-select" name="type"><option value=""><?= esc(lang('App.all_types')) ?></option><?php foreach (['port','terminal','anchorage','city','other'] as $type): ?><option value="<?= esc($type) ?>" <?= $filters['type'] === $type ? 'selected' : '' ?>><?= esc(place_type_label($type)) ?></option><?php endforeach; ?></select></div>
        <div class="col-sm-6 col-lg-2"><label class="form-label" for="place-status"><?= esc(lang('App.status')) ?></label><select id="place-status" class="form-select" name="status"><option value=""><?= esc(lang('App.all_statuses')) ?></option><option value="active" <?= $filters['status']==='active'?'selected':'' ?>><?= esc(lang('App.active')) ?></option><option value="inactive" <?= $filters['status']==='inactive'?'selected':'' ?>><?= esc(lang('App.inactive')) ?></option></select></div>
        <div class="col-sm-6 col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="fa-solid fa-filter me-1"></i><?= esc(lang('App.apply_filters')) ?></button><a class="btn btn-outline-light" href="<?= site_url('places') ?>" title="<?= esc(lang('App.clear_filters')) ?>" aria-label="<?= esc(lang('App.clear_filters')) ?>"><i class="fa-solid fa-rotate-left"></i></a></div>
    </form>
</div>

<div class="panel-card p-3 p-lg-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3"><div><div class="section-title mb-1"><?= esc(lang('App.locations_map')) ?></div><h2 class="h5 panel-title mb-0"><?= esc(lang('App.locations_map_desc')) ?></h2></div><span class="badge badge-soft rounded-pill px-3 py-2"><i class="fa-solid fa-map-pin me-1"></i><?= count($mapData) ?> <?= esc(lang('App.map_points')) ?></span></div>
    <div id="locationsMap" class="map-shell"><div class="h-100 d-flex align-items-center justify-content-center text-center p-4 muted"><div><i class="fa-solid fa-map-location-dot fa-3x mb-3"></i><div><?= esc($mapData === [] ? lang('App.place_map_empty') : lang('App.map_loading')) ?></div></div></div></div>
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-3 small muted"><span><i class="fa-solid fa-magnifying-glass-plus me-1"></i><?= esc(lang('App.map_zoom_note')) ?></span><span><i class="fa-solid fa-triangle-exclamation me-1"></i><?= esc(lang('App.coordinate_disclaimer')) ?></span></div>
</div>

<div class="table-shell p-3 p-lg-4">
    <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th><?= esc(lang('App.location_code')) ?></th><th><?= esc(lang('App.name')) ?></th><th><?= esc(lang('App.country_and_city')) ?></th><th><?= esc(lang('App.location_type')) ?></th><th><?= esc(lang('App.coordinates')) ?></th><th><?= esc(lang('App.location_source')) ?></th><th><?= esc(lang('App.status')) ?></th><th><?= esc(lang('App.actions')) ?></th></tr></thead><tbody>
    <?php if ($places === []): ?><tr><td colspan="8" class="text-center muted py-5"><i class="fa-regular fa-folder-open fa-2x d-block mb-2"></i><?= esc(lang('App.no_results')) ?></td></tr>
    <?php else: foreach ($places as $place): ?>
        <tr>
            <td><span class="location-code" dir="ltr"><?= esc($place['code'] ?: '—') ?></span></td>
            <td class="location-name"><strong><?= esc(place_display_name($place)) ?></strong><?php $secondary = portsys_is_rtl() ? ($place['name_en'] ?? '') : ($place['name_ar'] ?? ''); if ($secondary): ?><small><?= esc($secondary) ?></small><?php endif; ?></td>
            <td><strong><?= esc(place_display_country($place)) ?></strong><div class="small muted"><?= esc(place_display_city($place) ?: '—') ?></div></td>
            <td><span class="badge badge-soft rounded-pill"><?= esc(place_type_label($place['type'] ?? 'port')) ?></span></td>
            <td><a class="text-decoration-none" href="<?= esc(place_map_url($place)) ?>" target="_blank" rel="noopener"><code dir="ltr"><?= number_format((float) $place['latitude'], 6, '.', '') ?>, <?= number_format((float) $place['longitude'], 6, '.', '') ?></code></a></td>
            <td><span class="source-pill <?= (int)($place['is_seeded'] ?? 0) === 1 ? 'source-catalog' : 'source-manual' ?>"><i class="fa-solid <?= (int)($place['is_seeded'] ?? 0) === 1 ? 'fa-earth-americas' : 'fa-pen' ?>"></i><?= esc((int)($place['is_seeded'] ?? 0) === 1 ? lang('App.catalog_entry') : lang('App.manual_entry')) ?></span></td>
            <td><span class="badge text-bg-<?= ($place['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>"><?= esc(place_status_label($place['status'] ?? 'active')) ?></span></td>
            <td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-light" href="<?= esc(place_map_url($place)) ?>" target="_blank" rel="noopener" aria-label="<?= esc(lang('App.open_in_map')) ?>"><i class="fa-solid fa-map-location-dot"></i></a><a class="btn btn-sm btn-outline-light" href="<?= site_url('places/edit/' . (int) $place['id']) ?>" aria-label="<?= esc(lang('App.edit')) ?>"><i class="fa-regular fa-pen-to-square"></i></a><form method="post" action="<?= site_url('places/delete/' . (int) $place['id']) ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_delete'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit" aria-label="<?= esc(lang('App.delete')) ?>"><i class="fa-regular fa-trash-can"></i></button></form></div></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody></table></div>
    <div class="mt-4"><?= $pager?->links() ?></div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const points = <?= json_encode($mapData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK) ?>;
    const element = document.getElementById('locationsMap');
    if (!element || points.length === 0) return;
    if (typeof L === 'undefined') {
        element.innerHTML = `<div class="h-100 d-flex align-items-center justify-content-center text-center p-4 muted"><div><i class="fa-solid fa-map-location-dot fa-3x mb-3"></i><div><?= esc(lang('App.map_unavailable_short')) ?></div></div></div>`;
        return;
    }
    element.innerHTML = '';
    const map = L.map(element, {scrollWheelZoom: false}).setView([20, 15], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19, attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
    const bounds = [];
    const escapeHtml = value => { const node=document.createElement('div'); node.textContent=value ?? ''; return node.innerHTML; };
    points.forEach(point => {
        if (!Number.isFinite(point.latitude) || !Number.isFinite(point.longitude)) return;
        bounds.push([point.latitude, point.longitude]);
        const location = [point.city, point.country].filter(Boolean).join(' — ');
        const popup = `<div style="min-width:200px"><div class="small text-primary fw-bold">${escapeHtml(point.code || '')}</div><strong>${escapeHtml(point.name)}</strong><div class="small text-muted my-1">${escapeHtml(location)}</div><div class="small mb-2">${escapeHtml(point.type)} · ${escapeHtml(point.status)}</div><a class="btn btn-sm btn-primary w-100" href="${escapeHtml(point.editUrl)}"><?= esc(lang('App.edit')) ?></a></div>`;
        L.marker([point.latitude, point.longitude]).addTo(map).bindPopup(popup);
    });
    if (bounds.length === 1) map.setView(bounds[0], 9);
    else if (bounds.length > 1) map.fitBounds(bounds, {padding:[30,30], maxZoom:6});
    window.setTimeout(() => map.invalidateSize(), 150);
});
</script>
<?= $this->endSection() ?>
