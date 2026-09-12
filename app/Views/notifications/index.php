<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= lang('App.notification_center') ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $notifications = $notifications ?? []; $filterStatus = $filterStatus ?? ''; ?>
<div class="page-card p-4 p-lg-5 mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
    <div><div class="section-title"><?= esc(lang('App.nav_notifications')) ?></div><h1 class="page-title h2 mb-2"><?= esc(lang('App.notification_center')) ?></h1><p class="muted mb-0"><?= esc(lang('App.notification_center_desc')) ?></p></div>
    <div class="d-flex gap-2 flex-wrap">
        <?php if (($unreadCount ?? 0) > 0): ?><form method="post" action="<?= site_url('notifications/read-all') ?>"><?= csrf_field() ?><button class="btn btn-primary" type="submit"><i class="fa-solid fa-check-double me-2"></i><?= esc(lang('App.mark_all_read')) ?></button></form><?php endif; ?>
        <form method="post" action="<?= site_url('notifications/clear-read') ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_clear_read'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><button class="btn btn-outline-light" type="submit"><i class="fa-regular fa-trash-can me-2"></i><?= esc(lang('App.clear_read')) ?></button></form>
    </div>
</div>
<div class="panel-card p-3 p-lg-4">
    <div class="d-flex gap-2 flex-wrap mb-4">
        <?php foreach (['' => lang('App.all'), 'unread' => lang('App.unread'), 'read' => lang('App.read')] as $value => $label): ?><a class="btn btn-sm <?= $filterStatus === $value ? 'btn-primary' : 'btn-outline-light' ?>" href="<?= site_url('notifications' . ($value !== '' ? '?status=' . $value : '')) ?>"><?= esc($label) ?></a><?php endforeach; ?>
    </div>
    <?php if ($notifications === []): ?>
        <div class="text-center py-5"><div class="notification-icon mx-auto mb-3" style="width:56px;height:56px"><i class="fa-regular fa-bell-slash fa-lg"></i></div><h2 class="h6 fw-bold"><?= esc(lang('App.no_notifications')) ?></h2><p class="muted small mb-0"><?= esc(lang('App.no_notifications_desc')) ?></p></div>
    <?php else: ?>
        <div class="vstack gap-2">
            <?php foreach ($notifications as $notification): ?>
                <a href="<?= site_url('notifications/read/' . (int) $notification['id']) ?>" class="d-flex gap-3 align-items-start p-3 rounded-4 text-decoration-none <?= (int) ($notification['is_read'] ?? 0) === 0 ? 'border border-primary-subtle' : 'border' ?>" style="color:var(--text);background:<?= (int) ($notification['is_read'] ?? 0) === 0 ? 'var(--primary-soft)' : 'var(--surface)' ?>;border-color:var(--border)!important">
                    <span class="notification-icon"><i class="fa-solid <?= esc(notification_icon($notification['type'] ?? null)) ?>"></i></span>
                    <span class="flex-grow-1"><span class="d-flex justify-content-between gap-3"><strong class="small"><?= esc($notification['title'] ?? '') ?></strong><small class="muted text-nowrap"><?= esc(human_time($notification['created_at'] ?? null)) ?></small></span><span class="small muted d-block mt-1"><?= esc($notification['message'] ?? '') ?></span></span>
                    <?php if ((int) ($notification['is_read'] ?? 0) === 0): ?><span class="badge rounded-pill text-bg-primary"><?= esc(lang('App.unread')) ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="mt-4"><?= $pager?->links() ?></div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
