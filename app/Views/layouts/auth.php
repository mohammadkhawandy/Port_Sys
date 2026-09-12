<?php
$currentLocale = (string) service('request')->getLocale();
$isRtl = $currentLocale === 'ar';
$pageTitle = trim($this->renderSection('title')) ?: lang('App.auth_login');
$faviconVersion = '20260821-1';
?>
<!DOCTYPE html>
<html lang="<?= esc($currentLocale) ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PortSys | <?= esc($pageTitle) ?></title>
    <meta name="theme-color" content="#063879">
    <meta name="application-name" content="PortSys">
    <meta name="apple-mobile-web-app-title" content="PortSys">
    <meta name="color-scheme" content="light dark">
    <link rel="icon" type="image/svg+xml" href="<?= esc(portsys_asset('favicon.svg') . '?v=' . $faviconVersion) ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= esc(portsys_asset('portsys-icon-192.png') . '?v=' . $faviconVersion) ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= esc(portsys_asset('favicon.ico') . '?v=' . $faviconVersion) ?>">
    <link rel="apple-touch-icon" href="<?= esc(portsys_asset('apple-touch-icon.png') . '?v=' . $faviconVersion) ?>">
    <link rel="manifest" href="<?= esc(portsys_asset('site.webmanifest') . '?v=' . $faviconVersion) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;family=Tajawal:wght@400;500;700;800&amp;display=swap" rel="stylesheet">
    <link href="<?= portsys_asset('bootstrap-5.3.6.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root{--navy:#061d3d;--blue:#0d63c7;--border:#dfe8f2;--muted:#73859a}*{box-sizing:border-box}body{min-height:100vh;margin:0;font-family:<?= $isRtl ? "'Tajawal','Noto Kufi Arabic','Tahoma'" : "'Inter','Segoe UI'" ?>,sans-serif;background:linear-gradient(90deg,rgba(6,29,61,.94),rgba(6,29,61,.72)),url('<?= portsys_asset('auth-bg.jpg') ?>') center/cover fixed;display:grid;place-items:center;padding:24px}.auth-shell{width:min(1080px,100%);display:grid;grid-template-columns:1fr 1fr;border-radius:26px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.3);background:#fff}.auth-visual{padding:3.5rem;color:#fff;background:linear-gradient(145deg,#061d3d,#0b4c94);position:relative;overflow:hidden;min-height:650px}.auth-visual:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 20% 15%,rgba(255,255,255,.15),transparent 30%)}.auth-visual:after{content:"";position:absolute;width:390px;height:390px;border:72px solid rgba(255,255,255,.05);border-radius:50%;bottom:-170px;inset-inline-end:-120px}.brand{display:flex;align-items:center;gap:.85rem;font-size:1.4rem;font-weight:800;position:relative;z-index:1}.brand-mark{width:54px;height:54px;border-radius:17px;overflow:hidden;box-shadow:0 12px 30px rgba(0,0,0,.18)}.brand-mark img{width:100%;height:100%;display:block}.visual-copy{position:relative;z-index:1;margin-top:7rem;max-width:480px}.visual-copy h2{font-size:2.35rem;font-weight:800;line-height:1.45}.visual-copy p{color:#c4d6eb;line-height:1.9;font-size:1rem}.visual-features{display:grid;grid-template-columns:repeat(2,1fr);gap:.8rem;margin-top:2rem}.visual-feature{display:flex;align-items:center;gap:.65rem;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);border-radius:13px;padding:.85rem;font-size:.78rem;font-weight:700}.auth-form{padding:3.5rem;background:#fff;display:flex;flex-direction:column;justify-content:center}.form-control{min-height:49px;border-radius:12px;border-color:var(--border)}.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 .2rem rgba(13,99,199,.1)}.input-wrap{position:relative}.password-toggle{position:absolute;inset-inline-end:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:#74869b;width:38px;height:38px;border-radius:9px}.password-toggle:hover{background:#f1f6fc;color:var(--blue)}.btn-primary{background:var(--blue);border-color:var(--blue);border-radius:12px;min-height:49px;font-weight:800}.alert{border-radius:12px}.language-switch{position:fixed;top:18px;inset-inline-end:18px;z-index:10}.language-switch .btn{background:#fff;color:#12335d;border:0;border-radius:12px;padding:.6rem .85rem;box-shadow:0 10px 30px rgba(0,0,0,.16);font-weight:800;display:flex;align-items:center;gap:.45rem}.dropdown-menu{border:0;border-radius:13px;padding:.5rem;box-shadow:0 16px 40px rgba(0,0,0,.18)}.dropdown-item{border-radius:9px;padding:.6rem .75rem}.dropdown-item.active{background:#eaf3ff;color:var(--blue);font-weight:800}.auth-eyebrow{color:var(--blue);font-weight:800;font-size:.8rem;margin-bottom:.65rem}.auth-title{font-size:2rem;font-weight:800;margin-bottom:.55rem}.auth-subtitle{color:var(--muted);margin-bottom:0}.public-link{color:var(--muted)}@media(max-width:850px){body{padding:16px}.auth-shell{grid-template-columns:1fr}.auth-visual{display:none}.auth-form{padding:2.2rem;min-height:620px}.language-switch{top:12px;inset-inline-end:12px}}@media(max-width:480px){.auth-form{padding:1.5rem}.auth-title{font-size:1.7rem}.auth-full-logo{height:150px;max-width:140px}}
.auth-brand-image{align-items:center;justify-content:center;background:rgba(255,255,255,.96);border-radius:24px;padding:1rem 1.2rem;width:max-content;max-width:100%;box-shadow:0 18px 42px rgba(0,0,0,.22)}.auth-full-logo{height:190px;max-width:170px;width:auto;object-fit:contain;display:block;filter:none}.auth-visual .brand{font-size:0}.auth-form{position:relative}@media(max-width:850px){.auth-full-logo{height:76px}}
    </style>
    <?= $this->renderSection('head') ?>
</head>
<body>
<div class="dropdown language-switch">
    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="<?= esc(lang('App.switch_language')) ?>"><i class="fa-solid fa-globe"></i><span><?= $isRtl ? 'AR' : 'EN' ?></span></button>
    <div class="dropdown-menu dropdown-menu-end mt-2">
        <a class="dropdown-item <?= $isRtl ? 'active' : '' ?>" href="<?= esc(language_switch_url('ar')) ?>"><?= esc(lang('App.arabic_language')) ?></a>
        <a class="dropdown-item <?= ! $isRtl ? 'active' : '' ?>" href="<?= esc(language_switch_url('en')) ?>"><?= esc(lang('App.english_language')) ?></a>
    </div>
</div>
<main class="auth-shell">
    <section class="auth-visual">
        <a class="brand auth-brand-image text-decoration-none text-white" href="<?= site_url('/') ?>" aria-label="<?= esc(lang('App.logo_alt')) ?>"><img class="auth-full-logo" src="<?= portsys_asset('portsys-logo.png') . '?v=' . $faviconVersion ?>" alt="<?= esc(lang('App.logo_alt')) ?>"></a>
        <div class="visual-copy"><h2><?= esc(lang('App.home_title')) ?></h2><p><?= esc(lang('App.home_copy')) ?></p><div class="visual-features"><div class="visual-feature"><i class="fa-solid fa-anchor"></i><?= esc(lang('App.service_port_management')) ?></div><div class="visual-feature"><i class="fa-solid fa-ship"></i><?= esc(lang('App.service_ship_management')) ?></div><div class="visual-feature"><i class="fa-solid fa-route"></i><?= esc(lang('App.service_trip_title')) ?></div><div class="visual-feature"><i class="fa-solid fa-shield-halved"></i><?= esc(lang('App.service_security_title')) ?></div></div></div>
    </section>
    <section class="auth-form"><?= $this->renderSection('content') ?></section>
</main>
<script src="<?= portsys_asset('bootstrap-5.3.6.bundle.min.js') ?>"></script>
<script>
document.querySelectorAll('[data-password-toggle]').forEach(button => button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.passwordToggle);
    if (!input) return;
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    button.innerHTML = show ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
    button.setAttribute('aria-label', show ? <?= json_encode(lang('App.hide_password'), JSON_UNESCAPED_UNICODE) ?> : <?= json_encode(lang('App.show_password'), JSON_UNESCAPED_UNICODE) ?>);
}));
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
