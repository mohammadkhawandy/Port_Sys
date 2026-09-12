<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.my_requests_title')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $requests = $requests ?? []; $filterStatus = $filterStatus ?? ''; ?>
<div class="page-card p-4 p-lg-5 mb-4">
    <div class="section-title"><?= esc(lang('App.nav_my_requests')) ?></div>
    <h1 class="page-title h2 mb-2"><?= esc(lang('App.my_requests_title')) ?></h1>
    <p class="muted mb-0"><?= esc(lang('App.my_requests_desc')) ?></p>
</div>
<div class="panel-card p-3 p-md-4 mb-4">
    <div class="d-flex gap-2 flex-wrap"><?php foreach (['' => lang('App.all'), 'pending' => request_status_label('pending'), 'approved' => request_status_label('approved'), 'rejected' => request_status_label('rejected'), 'cancelled' => request_status_label('cancelled')] as $value => $label): ?><a class="btn btn-sm <?= $filterStatus === $value ? 'btn-primary' : 'btn-outline-light' ?>" href="<?= site_url('trip-requests/my' . ($value !== '' ? '?status=' . rawurlencode($value) : '')) ?>"><?= esc($label) ?></a><?php endforeach; ?></div>
</div>
<div class="table-shell p-3 p-md-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th><?= esc(lang('App.ship')) ?></th><th><?= esc(lang('App.route')) ?></th><th><?= esc(lang('App.departure_date')) ?></th><th><?= esc(lang('App.request_type')) ?></th><th><?= esc(lang('App.people_count')) ?></th><th><?= esc(lang('App.request_contact_information')) ?></th><th><?= esc(lang('App.request_status')) ?></th><th><?= esc(lang('App.message')) ?></th><th class="text-end"><?= esc(lang('App.actions')) ?></th></tr></thead>
            <tbody>
            <?php if ($requests === []): ?>
                <tr><td colspan="9" class="text-center py-5"><i class="fa-regular fa-folder-open fa-2x muted mb-3 d-block"></i><strong><?= esc(lang('App.no_requests_yet')) ?></strong></td></tr>
            <?php else: foreach ($requests as $item): ?>
                <tr id="req-<?= (int) ($item['id'] ?? 0) ?>">
                    <td><strong><?= esc($item['ship_name'] ?? '') ?></strong></td>
                    <td><?= esc($item['departure_port_name'] ?? '') ?> <i class="fa-solid fa-arrow-right-long mx-1 muted"></i> <?= esc($item['arrival_port_name'] ?? '') ?></td>
                    <td class="text-nowrap"><?= esc(format_datetime($item['departure_date'] ?? null)) ?></td>
                    <td><?= esc(request_type_label($item['request_type'] ?? null)) ?></td>
                    <td><span class="badge badge-soft rounded-pill"><i class="fa-solid fa-users me-1"></i><?= number_format(max(1, (int) ($item['people_count'] ?? 1))) ?></span></td>
                    <td><strong class="small d-block"><?= esc(($item['contact_name'] ?? '') ?: '—') ?></strong><small class="muted d-block" dir="ltr"><?= esc(($item['contact_phone'] ?? '') ?: '—') ?></small><small class="muted d-block"><?= esc(($item['contact_email'] ?? '') ?: '—') ?></small></td>
                    <td><span class="badge rounded-pill text-bg-<?= esc(request_status_class($item['status'] ?? null)) ?>"><?= esc(request_status_label($item['status'] ?? null)) ?></span></td>
                    <td><span class="d-inline-block text-truncate" style="max-width:260px" title="<?= esc($item['message'] ?? '') ?>"><?= esc(($item['message'] ?? '') ?: '—') ?></span></td>
                    <td class="text-end"><?php if (($item['status'] ?? '') === 'pending'): ?><form method="post" action="<?= site_url('trip-requests/' . (int) $item['id'] . '/cancel') ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_cancel_request'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-outline-danger"><?= esc(lang('App.cancel_request')) ?></button></form><?php else: ?><span class="muted">—</span><?php endif; ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?><div class="mt-4"><?= $pager->links() ?></div><?php endif; ?>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?><script>document.addEventListener('DOMContentLoaded',()=>{const id=location.hash;if(!id)return;try{const row=document.querySelector(id);if(row){row.scrollIntoView({behavior:'smooth',block:'center'});row.classList.add('table-primary');setTimeout(()=>row.classList.remove('table-primary'),3000)}}catch(e){}});</script><?= $this->endSection() ?>
