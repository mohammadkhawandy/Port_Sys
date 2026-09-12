<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.ports_management')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$ports = $ports ?? [];
$filters = $filters ?? ['q' => '', 'country' => ''];
$countries = $countries ?? [];
$availablePlaces = $availablePlaces ?? [];
$stats = $stats ?? ['total_ports' => 0, 'filtered_ports' => 0, 'countries_count' => 0];
$isAdmin = is_admin_user();
?>
<div class="page-card p-4 p-lg-5 mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div>
        <div class="section-title"><?= esc(lang('App.nav_ports')) ?></div>
        <h1 class="page-title h2 mb-2"><?= esc(lang('App.ports_management')) ?></h1>
        <p class="muted mb-0"><?= esc(lang('App.ports_management_desc')) ?></p>
    </div>
    <?php if ($isAdmin): ?>
        <a href="<?= site_url('ports/create') ?>" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-plus me-2"></i><?= esc(lang('App.add_new_port')) ?></a>
    <?php endif; ?>
</div>

<?php if ($isAdmin): ?>
    <div class="form-shell p-3 p-md-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-xl-4">
                <div class="section-title mb-1"><?= esc(lang('App.import_from_locations')) ?></div>
                <h2 class="panel-title h5 mb-1"><?= esc(lang('App.auto_create_ports_from_locations')) ?></h2>
                <p class="muted small mb-0"><?= esc(lang('App.ports_auto_sync_desc')) ?></p>
            </div>
            <div class="col-xl-8">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4 d-grid">
                        <form method="post" action="<?= site_url('ports/sync-places') ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_auto_create_ports'), JSON_UNESCAPED_UNICODE) ?>)">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary w-100" <?= $availablePlaces === [] ? 'disabled' : '' ?>>
                                <i class="fa-solid fa-wand-magic-sparkles me-2"></i><?= esc(lang('App.auto_create_all_missing_ports')) ?>
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-8">
                        <form method="post" action="<?= site_url('ports/import-place') ?>" class="row g-2 align-items-end">
                            <?= csrf_field() ?>
                            <div class="col-md-8">
                                <label for="quick-place-id" class="form-label"><?= esc(lang('App.source_location')) ?></label>
                                <select id="quick-place-id" name="place_id" class="form-select" required>
                                    <option value=""><?= esc(lang('App.select_location_to_create_port')) ?></option>
                                    <?php foreach ($availablePlaces as $place): ?>
                                        <option value="<?= (int) ($place['id'] ?? 0) ?>">
                                            <?= esc(place_display_name($place)) ?> — <?= esc(place_display_city($place)) ?>، <?= esc(place_display_country($place)) ?><?= !empty($place['code']) ? ' (' . esc($place['code']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-grid">
                                <button type="submit" class="btn btn-outline-light" <?= $availablePlaces === [] ? 'disabled' : '' ?>><i class="fa-solid fa-link me-2"></i><?= esc(lang('App.create_one_from_location')) ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if ($availablePlaces === []): ?>
                    <div class="form-text mt-2"><?= esc(lang('App.no_available_locations_for_import')) ?></div>
                <?php else: ?>
                    <div class="form-text mt-2"><?= esc(sprintf(lang('App.available_locations_for_auto_ports'), count($availablePlaces))) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <?php foreach ([
        [lang('App.total'), (int) $stats['total_ports'], 'fa-anchor'],
        [lang('App.filtered_results'), (int) $stats['filtered_ports'], 'fa-filter'],
        [lang('App.countries_covered'), (int) $stats['countries_count'], 'fa-earth-americas'],
    ] as [$label, $value, $icon]): ?>
        <div class="col-md-4"><div class="stat-card rounded-4 p-3 h-100 d-flex align-items-center gap-3"><span class="metric-icon"><i class="fa-solid <?= esc($icon) ?>"></i></span><div><div class="muted small mb-1"><?= esc($label) ?></div><div class="h4 mb-0"><?= number_format($value) ?></div></div></div></div>
    <?php endforeach; ?>
</div>

<div class="form-shell p-3 p-md-4 mb-4">
    <form method="get" action="<?= site_url('ports') ?>" class="row g-3 align-items-end">
        <div class="col-md-6"><label class="form-label" for="port-q"><?= esc(lang('App.search')) ?></label><input id="port-q" type="search" name="q" value="<?= esc($filters['q']) ?>" class="form-control" placeholder="<?= esc(lang('App.search_ports_placeholder')) ?>"></div>
        <div class="col-md-4"><label class="form-label" for="port-country"><?= esc(lang('App.country')) ?></label><select id="port-country" name="country" class="form-select"><option value=""><?= esc(lang('App.all_countries')) ?></option><?php foreach ($countries as $country): ?><option value="<?= esc($country) ?>" <?= $filters['country'] === $country ? 'selected' : '' ?>><?= esc($country) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2 d-grid"><button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass me-2"></i><?= esc(lang('App.apply_filters')) ?></button></div>
    </form>
</div>

<div class="table-shell p-3 p-md-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th><?= esc(lang('App.id')) ?></th><th><?= esc(lang('App.name')) ?></th><th><?= esc(lang('App.country')) ?></th><th><?= esc(lang('App.city')) ?></th><th><?= esc(lang('App.coordinates')) ?></th><th><?= esc(lang('App.source_location')) ?></th><?php if ($isAdmin): ?><th class="text-end"><?= esc(lang('App.actions')) ?></th><?php endif; ?></tr></thead>
            <tbody>
            <?php if ($ports === []): ?>
                <tr><td colspan="<?= $isAdmin ? 7 : 6 ?>" class="text-center py-5"><i class="fa-regular fa-folder-open fa-2x muted mb-3 d-block"></i><strong><?= esc(lang('App.no_results')) ?></strong></td></tr>
            <?php else: foreach ($ports as $port): ?>
                <tr>
                    <td class="muted">#<?= (int) ($port['id'] ?? 0) ?></td>
                    <td><strong><?= esc($port['name'] ?? '') ?></strong></td>
                    <td><?= esc($port['country'] ?? '') ?></td>
                    <td><?= esc($port['city'] ?? '') ?></td>
                    <td><code dir="ltr"><?= esc((string) ($port['latitude'] ?? '')) ?>, <?= esc((string) ($port['longitude'] ?? '')) ?></code></td>
                    <td>
                        <?php if (!empty($port['place_id'])): ?>
                            <span class="badge text-bg-success-subtle rounded-pill"><i class="fa-solid fa-link me-1"></i><?= esc(lang('App.linked_location')) ?></span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary rounded-pill"><i class="fa-solid fa-pen-ruler me-1"></i><?= esc(lang('App.manual_entry')) ?></span>
                        <?php endif; ?>
                    </td>
                    <?php if ($isAdmin): ?><td class="text-end text-nowrap"><div class="d-inline-flex gap-2"><a href="<?= site_url('ports/edit/' . (int) $port['id']) ?>" class="btn btn-sm btn-outline-warning" aria-label="<?= esc(lang('App.edit')) ?>"><i class="fa-regular fa-pen-to-square"></i></a><form method="post" action="<?= site_url('ports/delete/' . (int) $port['id']) ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_delete'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" aria-label="<?= esc(lang('App.delete')) ?>"><i class="fa-regular fa-trash-can"></i></button></form></div></td><?php endif; ?>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?><div class="mt-4"><?= $pager->links() ?></div><?php endif; ?>
</div>
<?= $this->endSection() ?>
