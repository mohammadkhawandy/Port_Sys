<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.ships_management')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$ships = $ships ?? [];
$filters = $filters ?? ['q' => '', 'type' => ''];
$types = $types ?? [];
$stats = $stats ?? ['total_ships' => 0, 'filtered_ships' => 0, 'total_capacity' => 0, 'filtered_capacity' => 0];
$isAdmin = is_admin_user();
?>
<div class="page-card p-4 p-lg-5 mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div><div class="section-title"><?= esc(lang('App.nav_ships')) ?></div><h1 class="page-title h2 mb-2"><?= esc(lang('App.ships_management')) ?></h1><p class="muted mb-0"><?= esc(lang('App.ships_management_desc')) ?></p></div>
    <?php if ($isAdmin): ?><a href="<?= site_url('ships/create') ?>" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-plus me-2"></i><?= esc(lang('App.add_new_ship')) ?></a><?php endif; ?>
</div>
<div class="row g-3 mb-4">
    <?php foreach ([
        [lang('App.total'), (int) $stats['total_ships'], 'fa-ship'],
        [lang('App.filtered_results'), (int) $stats['filtered_ships'], 'fa-filter'],
        [lang('App.total_capacity'), (int) $stats['total_capacity'], 'fa-boxes-stacked'],
        [lang('App.filtered_capacity'), (int) $stats['filtered_capacity'], 'fa-scale-balanced'],
    ] as [$label, $value, $icon]): ?><div class="col-md-6 col-xl-3"><div class="stat-card rounded-4 p-3 h-100 d-flex align-items-center gap-3"><span class="metric-icon"><i class="fa-solid <?= esc($icon) ?>"></i></span><div><div class="muted small mb-1"><?= esc($label) ?></div><div class="h4 mb-0"><?= number_format($value) ?></div></div></div></div><?php endforeach; ?>
</div>
<div class="form-shell p-3 p-md-4 mb-4"><form method="get" action="<?= site_url('ships') ?>" class="row g-3 align-items-end"><div class="col-md-6"><label class="form-label" for="ship-q"><?= esc(lang('App.search')) ?></label><input id="ship-q" type="search" name="q" value="<?= esc($filters['q']) ?>" class="form-control" placeholder="<?= esc(lang('App.search_ships_placeholder')) ?>"></div><div class="col-md-4"><label class="form-label" for="ship-type"><?= esc(lang('App.type')) ?></label><select id="ship-type" name="type" class="form-select"><option value=""><?= esc(lang('App.all_types')) ?></option><?php foreach ($types as $type): ?><option value="<?= esc($type) ?>" <?= $filters['type'] === $type ? 'selected' : '' ?>><?= esc($type) ?></option><?php endforeach; ?></select></div><div class="col-md-2 d-grid"><button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass me-2"></i><?= esc(lang('App.apply_filters')) ?></button></div></form></div>
<div class="table-shell p-3 p-md-4"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th><?= esc(lang('App.id')) ?></th><th><?= esc(lang('App.name')) ?></th><th><?= esc(lang('App.type')) ?></th><th><?= esc(lang('App.capacity')) ?></th><th><?= esc(lang('App.status')) ?></th><?php if ($isAdmin): ?><th class="text-end"><?= esc(lang('App.actions')) ?></th><?php endif; ?></tr></thead><tbody>
<?php if ($ships === []): ?><tr><td colspan="<?= $isAdmin ? 6 : 5 ?>" class="text-center py-5"><i class="fa-regular fa-folder-open fa-2x muted mb-3 d-block"></i><strong><?= esc(lang('App.no_results')) ?></strong></td></tr><?php else: foreach ($ships as $ship): ?><tr><td class="muted">#<?= (int) ($ship['id'] ?? 0) ?></td><td><strong><?= esc($ship['name'] ?? '') ?></strong></td><td><?= esc($ship['type'] ?? '') ?></td><td><?= number_format((int) ($ship['capacity'] ?? 0)) ?></td><td><span class="badge rounded-pill text-bg-success-subtle text-success-emphasis"><?= esc(lang('App.ship_status_operational')) ?></span></td><?php if ($isAdmin): ?><td class="text-end text-nowrap"><div class="d-inline-flex gap-2"><a href="<?= site_url('ships/edit/' . (int) $ship['id']) ?>" class="btn btn-sm btn-outline-warning" aria-label="<?= esc(lang('App.edit')) ?>"><i class="fa-regular fa-pen-to-square"></i></a><form method="post" action="<?= site_url('ships/delete/' . (int) $ship['id']) ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_delete'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger" aria-label="<?= esc(lang('App.delete')) ?>"><i class="fa-regular fa-trash-can"></i></button></form></div></td><?php endif; ?></tr><?php endforeach; endif; ?>
</tbody></table></div><?php if (isset($pager)): ?><div class="mt-4"><?= $pager->links() ?></div><?php endif; ?></div>
<?= $this->endSection() ?>
