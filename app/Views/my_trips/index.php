<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.my_trips_title')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$myTrips = $myTrips ?? [];
$stats = $stats ?? ['booked' => 0, 'pending' => 0, 'rejected' => 0];
$filters = $filters ?? ['status' => '', 'q' => ''];
?>
<div class="page-card p-4 p-lg-5 mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div><div class="section-title"><?= esc(lang('App.nav_my_trips')) ?></div><h1 class="page-title h2 mb-2"><?= esc(lang('App.my_trips_title')) ?></h1><p class="muted mb-0"><?= esc(lang('App.my_trips_desc')) ?></p></div>
    <a href="<?= site_url('my-trips/download') ?>" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-file-arrow-down me-2"></i><?= esc(lang('App.download_csv')) ?></a>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([['approved', 'booked', 'fa-circle-check'], ['pending', 'pending', 'fa-clock'], ['rejected', 'rejected', 'fa-circle-xmark']] as [$status, $key, $icon]): ?>
        <div class="col-md-4"><a class="stat-card rounded-4 p-3 h-100 d-flex align-items-center gap-3 text-decoration-none" href="<?= site_url('my-trips?status=' . $status) ?>"><span class="kpi-icon"><i class="fa-solid <?= esc($icon) ?>"></i></span><div><div class="muted small mb-1"><?= esc(request_status_label($status)) ?></div><div class="h4 mb-0 text-body-emphasis"><?= number_format((int) ($stats[$key] ?? 0)) ?></div></div></a></div>
    <?php endforeach; ?>
</div>

<div class="table-shell p-3 p-md-4">
    <form method="get" class="row g-3 align-items-end mb-4" role="search">
        <div class="col-lg-7"><label for="my-trip-q" class="form-label"><?= esc(lang('App.search')) ?></label><input id="my-trip-q" class="form-control" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="<?= esc(lang('App.search_my_trips_placeholder')) ?>"></div>
        <div class="col-lg-3"><label for="my-trip-status" class="form-label"><?= esc(lang('App.status')) ?></label><select id="my-trip-status" class="form-select" name="status"><option value=""><?= esc(lang('App.all')) ?></option><?php foreach (['approved', 'pending', 'rejected', 'cancelled'] as $status): ?><option value="<?= esc($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= esc(request_status_label($status)) ?></option><?php endforeach; ?></select></div>
        <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="fa-solid fa-filter me-2"></i><?= esc(lang('App.filter')) ?></button><a class="btn btn-outline-light" href="<?= site_url('my-trips') ?>" aria-label="<?= esc(lang('App.reset')) ?>"><i class="fa-solid fa-rotate-left"></i></a></div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th><?= esc(lang('App.ship')) ?></th><th><?= esc(lang('App.route')) ?></th><th><?= esc(lang('App.departure_date')) ?></th><th><?= esc(lang('App.arrival_date')) ?></th><th><?= esc(lang('App.request_type')) ?></th><th><?= esc(lang('App.request_status')) ?></th><th></th></tr></thead>
            <tbody>
            <?php if ($myTrips === []): ?><tr><td colspan="7" class="text-center py-5"><i class="fa-regular fa-calendar-xmark fa-2x muted mb-3 d-block"></i><strong><?= esc(lang('App.no_results')) ?></strong></td></tr>
            <?php else: foreach ($myTrips as $item): ?>
                <tr>
                    <td><strong><?= esc($item['ship_name'] ?? '') ?></strong></td>
                    <td><?= esc($item['departure_port_name'] ?? '') ?> <i class="fa-solid fa-arrow-<?= service('request')->getLocale() === 'ar' ? 'left' : 'right' ?>-long mx-1 muted"></i> <?= esc($item['arrival_port_name'] ?? '') ?></td>
                    <td class="text-nowrap"><?= esc(format_datetime($item['departure_date'] ?? null)) ?></td>
                    <td class="text-nowrap"><?= esc(format_datetime($item['arrival_date'] ?? null)) ?></td>
                    <td><?= esc(request_type_label($item['request_type'] ?? null)) ?></td>
                    <td><span class="badge rounded-pill text-bg-<?= esc(request_status_class($item['status'] ?? null)) ?>"><?= esc(request_status_label($item['status'] ?? null)) ?></span></td>
                    <td><a class="btn btn-sm btn-outline-light" href="<?= site_url('trips/show/' . (int) ($item['trip_id'] ?? 0)) ?>" aria-label="<?= esc(lang('App.view')) ?>"><i class="fa-regular fa-eye"></i></a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?><div class="mt-4"><?= $pager->links() ?></div><?php endif; ?>
</div>
<?= $this->endSection() ?>
