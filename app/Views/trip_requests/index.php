<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.trip_requests_management')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$requests = $requests ?? [];
$filters = $filters ?? ['status' => '', 'q' => ''];
$stats = $stats ?? ['pending' => 0, 'approved' => 0, 'rejected' => 0];
?>
<div class="page-card p-4 p-lg-5 mb-4">
    <div class="section-title"><?= esc(lang('App.nav_trip_requests')) ?></div>
    <h1 class="page-title h2 mb-2"><?= esc(lang('App.trip_requests_management')) ?></h1>
    <p class="muted mb-0"><?= esc(lang('App.trip_requests_management_desc')) ?></p>
</div>

<div class="row g-3 mb-4">
    <?php foreach (['pending' => 'fa-clock', 'approved' => 'fa-circle-check', 'rejected' => 'fa-circle-xmark'] as $status => $icon): ?>
        <div class="col-md-4"><div class="stat-card rounded-4 p-3 h-100 d-flex align-items-center gap-3"><span class="metric-icon"><i class="fa-solid <?= esc($icon) ?>"></i></span><div><div class="muted small mb-1"><?= esc(request_status_label($status)) ?></div><div class="h4 mb-0"><?= number_format((int) ($stats[$status] ?? 0)) ?></div></div></div></div>
    <?php endforeach; ?>
</div>

<div class="form-shell p-3 p-md-4 mb-4">
    <form method="get" action="<?= site_url('trip-requests') ?>" class="row g-3 align-items-end">
        <div class="col-md-7">
            <label for="request-q" class="form-label"><?= esc(lang('App.search')) ?></label>
            <input id="request-q" type="search" name="q" class="form-control" value="<?= esc($filters['q']) ?>" placeholder="<?= esc(lang('App.search_requests_placeholder')) ?>">
        </div>
        <div class="col-md-3">
            <label for="request-status" class="form-label"><?= esc(lang('App.request_status')) ?></label>
            <select id="request-status" name="status" class="form-select">
                <option value=""><?= esc(lang('App.all_statuses')) ?></option>
                <?php foreach (['pending','approved','rejected','cancelled'] as $status): ?><option value="<?= $status ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= esc(request_status_label($status)) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid"><button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-2"></i><?= esc(lang('App.apply_filters')) ?></button></div>
    </form>
</div>

<div class="table-shell p-3 p-md-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th><?= esc(lang('App.user')) ?></th>
                    <th><?= esc(lang('App.request_contact_information')) ?></th>
                    <th><?= esc(lang('App.ship')) ?></th>
                    <th><?= esc(lang('App.route')) ?></th>
                    <th><?= esc(lang('App.departure_date')) ?></th>
                    <th><?= esc(lang('App.request_type')) ?></th>
                    <th><?= esc(lang('App.people_count')) ?></th>
                    <th><?= esc(lang('App.request_status')) ?></th>
                    <th class="text-end"><?= esc(lang('App.actions')) ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($requests === []): ?>
                <tr><td colspan="9" class="text-center py-5"><i class="fa-regular fa-folder-open fa-2x muted mb-3 d-block"></i><strong><?= esc(lang('App.no_requests_yet')) ?></strong></td></tr>
            <?php else: foreach ($requests as $item): ?>
                <?php
                $contactName = trim((string) ($item['contact_name'] ?? ''));
                $contactPhone = trim((string) ($item['contact_phone'] ?? ''));
                $contactEmail = trim((string) ($item['contact_email'] ?? ''));
                $organization = trim((string) ($item['organization'] ?? ''));
                ?>
                <tr id="req-<?= (int) ($item['id'] ?? 0) ?>">
                    <td><strong><?= esc(($item['user_name'] ?? '') ?: ($item['user_email'] ?? '')) ?></strong><small class="d-block muted"><?= esc($item['user_email'] ?? '') ?></small></td>
                    <td>
                        <strong class="small d-block"><?= esc($contactName !== '' ? $contactName : '—') ?></strong>
                        <small class="muted d-block" dir="ltr"><?= esc($contactPhone !== '' ? $contactPhone : '—') ?></small>
                        <small class="muted d-block"><?= esc($contactEmail !== '' ? $contactEmail : '—') ?></small>
                        <?php if ($organization !== ''): ?><small class="badge badge-soft rounded-pill mt-1"><?= esc($organization) ?></small><?php endif; ?>
                    </td>
                    <td><?= esc($item['ship_name'] ?? '') ?></td>
                    <td><?= esc($item['departure_port_name'] ?? '') ?> <i class="fa-solid fa-arrow-right-long mx-1 muted"></i> <?= esc($item['arrival_port_name'] ?? '') ?></td>
                    <td class="text-nowrap"><?= esc(format_datetime($item['departure_date'] ?? null)) ?></td>
                    <td><?= esc(request_type_label($item['request_type'] ?? null)) ?></td>
                    <td><span class="badge badge-soft rounded-pill"><i class="fa-solid fa-users me-1"></i><?= number_format(max(1, (int) ($item['people_count'] ?? 1))) ?></span></td>
                    <td><span class="badge rounded-pill text-bg-<?= esc(request_status_class($item['status'] ?? null)) ?>"><?= esc(request_status_label($item['status'] ?? null)) ?></span></td>
                    <td class="text-end text-nowrap">
                        <?php if (($item['status'] ?? '') === 'pending'): ?>
                            <div class="d-inline-flex gap-2">
                                <form method="post" action="<?= site_url('trip-requests/' . (int) $item['id'] . '/status/approved') ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_approve_request'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-check me-1"></i><?= esc(lang('App.approve')) ?></button></form>
                                <form method="post" action="<?= site_url('trip-requests/' . (int) $item['id'] . '/status/rejected') ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_reject_request'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-xmark me-1"></i><?= esc(lang('App.reject')) ?></button></form>
                            </div>
                        <?php else: ?><span class="muted">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php if (! empty($item['message'])): ?>
                    <tr class="small"><td></td><td colspan="8" class="muted"><i class="fa-regular fa-message me-1"></i><?= nl2br(esc($item['message'])) ?></td></tr>
                <?php endif; ?>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?><div class="mt-4"><?= $pager->links() ?></div><?php endif; ?>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?><script>document.addEventListener('DOMContentLoaded',()=>{const id=location.hash;if(!id)return;try{const row=document.querySelector(id);if(row){row.scrollIntoView({behavior:'smooth',block:'center'});row.classList.add('table-primary');setTimeout(()=>row.classList.remove('table-primary'),3000)}}catch(e){}});</script><?= $this->endSection() ?>
