<?= $this->extend('layouts/auth') ?>
<?= $this->section('title') ?><?= esc(lang('App.auth_create_account')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="mb-4"><div class="auth-eyebrow"><?= esc(lang('App.auth_register_secure')) ?></div><h1 class="auth-title"><?= esc(lang('App.auth_create_account_title')) ?></h1><p class="auth-subtitle"><?= esc(lang('App.auth_create_account_subtitle')) ?></p></div>
<?php $errors = session()->getFlashdata('errors'); if (is_array($errors)): ?><div class="alert alert-danger"><ul class="mb-0 small"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= site_url('register') ?>">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label fw-bold" for="full_name"><?= esc(lang('App.auth_full_name')) ?></label><input id="full_name" name="full_name" value="<?= esc(old('full_name')) ?>" class="form-control" required autocomplete="name" autofocus></div>
    <div class="mb-3"><label class="form-label fw-bold" for="email"><?= esc(lang('App.auth_email')) ?></label><input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" class="form-control" required autocomplete="email"></div>
    <div class="mb-3"><label class="form-label fw-bold" for="password"><?= esc(lang('App.auth_password')) ?></label><div class="input-wrap"><input type="password" id="password" name="password" class="form-control" required minlength="8" autocomplete="new-password"><button class="password-toggle" type="button" data-password-toggle="password" aria-label="<?= esc(lang('App.show_password')) ?>"><i class="fa-regular fa-eye"></i></button></div></div>
    <div class="mb-4"><label class="form-label fw-bold" for="confirm_password"><?= esc(lang('App.auth_confirm_password')) ?></label><div class="input-wrap"><input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8" autocomplete="new-password"><button class="password-toggle" type="button" data-password-toggle="confirm_password" aria-label="<?= esc(lang('App.show_password')) ?>"><i class="fa-regular fa-eye"></i></button></div></div>
    <button class="btn btn-primary w-100" type="submit"><i class="fa-solid fa-user-plus me-2"></i><?= esc(lang('App.auth_create_account')) ?></button>
</form>
<div class="text-center mt-4 small"><span class="text-secondary"><?= esc(lang('App.auth_already_have')) ?></span> <a class="fw-bold text-decoration-none" href="<?= site_url('login') ?>"><?= esc(lang('App.auth_back_to_login')) ?></a></div>
<?= $this->endSection() ?>
