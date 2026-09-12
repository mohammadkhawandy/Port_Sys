<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.accepted_participants')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $requests=$requests??[]; ?>
<div class="page-card p-4 p-lg-5 mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div><div class="section-title"><?= esc(lang('App.nav_trip_requests')) ?></div><h1 class="page-title h2 mb-2"><?= esc(lang('App.accepted_participants')) ?></h1><p class="muted mb-0"><?= esc(lang('App.accepted_participants_desc')) ?></p></div>
    <a class="btn btn-outline-light" href="<?= site_url('trips/show/'.(int)($tripId??0)) ?>"><i class="fa-solid fa-arrow-left me-2"></i><?= esc(lang('App.back')) ?></a>
</div>
<div class="table-shell p-3 p-md-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th><?= esc(lang('App.user')) ?></th><th><?= esc(lang('App.request_contact_information')) ?></th><th><?= esc(lang('App.request_type')) ?></th><th><?= esc(lang('App.people_count')) ?></th><th><?= esc(lang('App.message')) ?></th><th><?= esc(lang('App.created_at')) ?></th></tr></thead>
            <tbody>
            <?php if($requests===[]): ?>
                <tr><td colspan="6" class="text-center py-5"><i class="fa-solid fa-users-slash fa-2x muted mb-3 d-block"></i><?= esc(lang('App.no_accepted_participants')) ?></td></tr>
            <?php else: foreach($requests as $item): ?>
                <tr>
                    <td><strong><?= esc(($item['user_name']??'')?:($item['user_email']??'')) ?></strong><small class="muted d-block"><?= esc($item['user_email']??'') ?></small></td>
                    <td><strong class="small d-block"><?= esc(($item['contact_name']??'')?:'—') ?></strong><small class="muted d-block" dir="ltr"><?= esc(($item['contact_phone']??'')?:'—') ?></small><small class="muted d-block"><?= esc(($item['contact_email']??'')?:'—') ?></small><?php if(!empty($item['organization'])): ?><small class="badge badge-soft rounded-pill mt-1"><?= esc($item['organization']) ?></small><?php endif; ?></td>
                    <td><?= esc(request_type_label($item['request_type']??null)) ?></td>
                    <td><span class="badge badge-soft rounded-pill"><i class="fa-solid fa-users me-1"></i><?= number_format(max(1, (int) ($item['people_count'] ?? 1))) ?></span></td>
                    <td><?= nl2br(esc(($item['message']??'')?:'—')) ?></td>
                    <td><?= esc(format_datetime($item['created_at']??null)) ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
