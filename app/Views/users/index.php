<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.users_management')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$users = $users ?? [];
$filters = $filters ?? ['q' => '', 'role' => '', 'status' => ''];
$stats = $stats ?? ['total' => 0, 'admins' => 0, 'active' => 0, 'inactive' => 0];
$currentUserId = (int) session()->get('userId');
?>
<div class="page-card p-4 p-lg-5 mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <div class="section-title"><?= esc(lang('App.nav_users')) ?></div>
            <h1 class="page-title h2 mb-2"><?= esc(lang('App.users_management')) ?></h1>
            <p class="muted mb-0"><?= esc(lang('App.users_management_desc')) ?></p>
        </div>
        <span class="badge badge-soft rounded-pill px-3 py-2"><i class="fa-solid fa-shield-halved me-2"></i><?= esc(lang('App.last_admin_protection')) ?></span>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([
        [lang('App.users_total'), $stats['total'] ?? 0, 'fa-users'],
        [lang('App.users_admins'), $stats['admins'] ?? 0, 'fa-user-shield'],
        [lang('App.users_active'), $stats['active'] ?? 0, 'fa-user-check'],
        [lang('App.users_inactive'), $stats['inactive'] ?? 0, 'fa-user-lock'],
    ] as [$label, $value, $icon]): ?>
        <div class="col-sm-6 col-xl-3"><div class="kpi-card"><span class="kpi-icon"><i class="fa-solid <?= esc($icon) ?>"></i></span><span><small class="muted d-block"><?= esc($label) ?></small><strong class="kpi-value"><?= number_format((int) $value) ?></strong></span></div></div>
    <?php endforeach; ?>
</div>

<div class="table-shell p-3 p-lg-4">
    <form method="get" class="row g-3 align-items-end mb-4" role="search">
        <div class="col-lg-6"><label for="user-q" class="form-label"><?= esc(lang('App.search')) ?></label><input id="user-q" class="form-control" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="<?= esc(lang('App.search_users_placeholder')) ?>"></div>
        <div class="col-lg-2"><label for="user-role" class="form-label"><?= esc(lang('App.role')) ?></label><select id="user-role" class="form-select" name="role"><option value=""><?= esc(lang('App.all')) ?></option><option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>><?= esc(lang('App.role_admin')) ?></option><option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>><?= esc(lang('App.role_user')) ?></option></select></div>
        <div class="col-lg-2"><label for="user-status" class="form-label"><?= esc(lang('App.status')) ?></label><select id="user-status" class="form-select" name="status"><option value=""><?= esc(lang('App.all')) ?></option><option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>><?= esc(lang('App.user_status_active')) ?></option><option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= esc(lang('App.user_status_inactive')) ?></option></select></div>
        <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="fa-solid fa-filter me-2"></i><?= esc(lang('App.filter')) ?></button><a class="btn btn-outline-light" href="<?= site_url('users') ?>" aria-label="<?= esc(lang('App.reset')) ?>"><i class="fa-solid fa-rotate-left"></i></a></div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th><?= esc(lang('App.user')) ?></th><th><?= esc(lang('App.role')) ?></th><th><?= esc(lang('App.status')) ?></th><th><?= esc(lang('App.created_at')) ?></th><th><?= esc(lang('App.account_last_login')) ?></th><th class="text-end"><?= esc(lang('App.actions')) ?></th></tr></thead>
            <tbody>
            <?php if ($users === []): ?><tr><td colspan="6" class="text-center muted py-5"><i class="fa-solid fa-users-slash fa-2x d-block mb-3"></i><?= esc(lang('App.no_users_found')) ?></td></tr>
            <?php else: foreach ($users as $user): ?>
                <?php
                $id = (int) ($user['id'] ?? 0);
                $role = (string) ($user['role'] ?? 'user');
                $status = (string) ($user['status'] ?? 'active');
                $name = trim((string) ($user['full_name'] ?? '')) ?: (string) ($user['email'] ?? lang('App.unknown'));
                $initial = mb_strtoupper(mb_substr($name, 0, 1));
                ?>
                <tr>
                    <td><div class="d-flex align-items-center gap-3"><span class="avatar"><?= esc($initial) ?></span><span><strong class="small d-block"><?= esc($name) ?><?= $id === $currentUserId ? ' · ' . esc(lang('App.you')) : '' ?></strong><small class="muted"><?= esc($user['email'] ?? '') ?></small></span></div></td>
                    <td><span class="badge badge-soft rounded-pill"><?= esc($role === 'admin' ? lang('App.role_admin') : lang('App.role_user')) ?></span></td>
                    <td><span class="badge rounded-pill text-bg-<?= $status === 'active' ? 'success' : 'secondary' ?>"><?= esc($status === 'active' ? lang('App.user_status_active') : lang('App.user_status_inactive')) ?></span></td>
                    <td class="text-nowrap"><?= esc(format_datetime($user['created_at'] ?? null)) ?></td>
                    <td class="text-nowrap"><?= esc(($user['last_login_at'] ?? null) ? human_time($user['last_login_at']) : lang('App.never')) ?></td>
                    <td class="text-end">
                        <div class="user-actions">
                            <form method="post" action="<?= site_url('users/' . $id . '/role') ?>" class="action-form"><?= csrf_field() ?><select class="form-select form-select-sm" name="role" aria-label="<?= esc(lang('App.role')) ?>" <?= $id === $currentUserId ? 'disabled' : '' ?>><option value="user" <?= $role === 'user' ? 'selected' : '' ?>><?= esc(lang('App.role_user')) ?></option><option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>><?= esc(lang('App.role_admin')) ?></option></select><?php if ($id !== $currentUserId): ?><button class="btn btn-sm btn-outline-light" type="submit" aria-label="<?= esc(lang('App.save')) ?>" title="<?= esc(lang('App.save')) ?>"><i class="fa-solid fa-floppy-disk"></i></button><?php endif; ?></form>
                            <form method="post" action="<?= site_url('users/' . $id . '/status') ?>" onsubmit="return confirm(<?= json_encode(lang('App.confirm_user_status_change'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><input type="hidden" name="status" value="<?= $status === 'active' ? 'inactive' : 'active' ?>"><button class="btn btn-sm <?= $status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?>" type="submit" <?= $id === $currentUserId ? 'disabled' : '' ?>><?= esc($status === 'active' ? lang('App.disable') : lang('App.enable')) ?></button></form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?><div class="mt-4"><?= $pager->links() ?></div><?php endif; ?>
</div>
<?= $this->endSection() ?>
