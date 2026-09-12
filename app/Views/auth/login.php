<?= $this->extend('layouts/auth') ?>
<?= $this->section('title') ?><?= esc(lang('App.auth_login')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="mb-4"><div class="auth-eyebrow"><?= esc(lang('App.auth_login_secure')) ?></div><h1 class="auth-title"><?= esc(lang('App.auth_welcome_back')) ?></h1><p class="auth-subtitle"><?= esc(lang('App.auth_signin_subtitle')) ?></p></div>
<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
<?php $errors = session()->getFlashdata('errors'); if (is_array($errors)): ?><div class="alert alert-danger"><ul class="mb-0 small"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<?php $lockRemaining = (int) ($lockRemaining ?? 0); if ($lockRemaining > 0): ?><div class="alert alert-warning" id="loginLockAlert" data-seconds="<?= $lockRemaining ?>"><i class="fa-solid fa-clock me-2"></i><span><?= esc(sprintf(lang('App.msg_login_locked'), max(1, (int) ceil($lockRemaining / 60)))) ?></span></div><?php endif; ?>
<form method="post" action="<?= site_url('login') ?>">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label fw-bold" for="email"><?= esc(lang('App.auth_email')) ?></label><input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" class="form-control" required autocomplete="email" autofocus></div>
    <div class="mb-4"><label class="form-label fw-bold" for="password"><?= esc(lang('App.auth_password')) ?></label><div class="input-wrap"><input type="password" id="password" name="password" class="form-control" required autocomplete="current-password"><button class="password-toggle" type="button" data-password-toggle="password" aria-label="<?= esc(lang('App.show_password')) ?>"><i class="fa-regular fa-eye"></i></button></div></div>
    <button class="btn btn-primary w-100" id="loginSubmit" type="submit" <?= $lockRemaining > 0 ? 'disabled' : '' ?>><i class="fa-solid fa-right-to-bracket me-2"></i><?= esc(lang('App.auth_login')) ?></button>
</form>
<div class="text-center mt-4 small"><span class="text-secondary"><?= esc(lang('App.auth_new_here')) ?></span> <a class="fw-bold text-decoration-none" href="<?= site_url('register') ?>"><?= esc(lang('App.auth_create_new')) ?></a></div>
<div class="text-center mt-3"><a class="small public-link text-decoration-none" href="<?= site_url('/') ?>"><i class="fa-solid fa-arrow-left me-1"></i><?= esc(lang('App.public_site')) ?></a></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
const lockAlert = document.getElementById('loginLockAlert');
const loginSubmit = document.getElementById('loginSubmit');
if (lockAlert && loginSubmit) {
    let remaining = Number(lockAlert.dataset.seconds || 0);
    const timer = window.setInterval(() => {
        remaining -= 1;
        if (remaining <= 0) { lockAlert.remove(); loginSubmit.disabled = false; window.clearInterval(timer); }
    }, 1000);
}
</script>
<?= $this->endSection() ?>
