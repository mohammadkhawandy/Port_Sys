<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= lang('App.profile_title') ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $user = $user ?? []; ?>
<div class="page-card p-4 p-lg-5 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-auto"><div class="avatar" style="width:72px;height:72px;border-radius:20px;font-size:1.5rem"><?= esc(mb_strtoupper(mb_substr(current_user_display_name(), 0, 1))) ?></div></div>
        <div class="col"><div class="section-title"><?= esc(lang('App.nav_profile')) ?></div><h1 class="page-title h2 mb-1"><?= esc(lang('App.profile_title')) ?></h1><p class="muted mb-0"><?= esc(lang('App.profile_desc')) ?></p></div>
        <div class="col-lg-auto"><span class="badge rounded-pill text-bg-<?= ($user['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?> px-3 py-2"><?= esc(($user['status'] ?? 'active') === 'active' ? lang('App.active') : lang('App.inactive')) ?></span></div>
    </div>
</div>
<div class="row g-4">
    <div class="col-xl-7">
        <div class="form-shell p-4 p-lg-5 h-100">
            <div class="mb-4"><h2 class="panel-title h5 mb-1"><?= esc(lang('App.profile_information')) ?></h2><p class="muted small mb-0"><?= esc(lang('App.profile_information_desc')) ?></p></div>
            <form method="post" action="<?= site_url('profile') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="full_name"><?= esc(lang('App.full_name')) ?></label><input class="form-control" id="full_name" name="full_name" value="<?= esc(old('full_name', $user['full_name'] ?? '')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label" for="email"><?= esc(lang('App.email')) ?></label><input class="form-control" type="email" id="email" name="email" value="<?= esc(old('email', $user['email'] ?? '')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label"><?= esc(lang('App.role')) ?></label><input class="form-control" value="<?= esc(($user['role'] ?? 'user') === 'admin' ? lang('App.role_admin') : lang('App.role_user')) ?>" disabled></div>
                    <div class="col-md-6"><label class="form-label"><?= esc(lang('App.last_login')) ?></label><input class="form-control" value="<?= esc(format_datetime($user['last_login_at'] ?? null)) ?>" disabled></div>
                </div>
                <button class="btn btn-primary mt-4 px-4" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i><?= esc(lang('App.save_changes')) ?></button>
            </form>
        </div>
    </div>
    <div class="col-xl-5" id="password-section">
        <div class="form-shell p-4 p-lg-5 h-100">
            <div class="mb-4"><h2 class="panel-title h5 mb-1"><?= esc(lang('App.change_password')) ?></h2><p class="muted small mb-0"><?= esc(lang('App.password_security_desc')) ?></p></div>
            <form method="post" action="<?= site_url('profile/password') ?>">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label" for="current_password"><?= esc(lang('App.current_password')) ?></label><input class="form-control" type="password" id="current_password" name="current_password" required autocomplete="current-password"></div>
                <div class="mb-3"><label class="form-label" for="password"><?= esc(lang('App.new_password')) ?></label><input class="form-control" type="password" id="password" name="password" minlength="8" required autocomplete="new-password"></div>
                <div class="mb-3"><label class="form-label" for="confirm_password"><?= esc(lang('App.confirm_new_password')) ?></label><input class="form-control" type="password" id="confirm_password" name="confirm_password" minlength="8" required autocomplete="new-password"></div>
                <button class="btn btn-primary px-4" type="submit"><i class="fa-solid fa-key me-2"></i><?= esc(lang('App.change_password')) ?></button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
