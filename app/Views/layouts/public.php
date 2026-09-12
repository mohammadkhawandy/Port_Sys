<?php
$currentLocale = (string) service('request')->getLocale();
$isRtl = $currentLocale === 'ar';
$isLoggedIn = (bool) session()->get('isLoggedIn');
$currentPath = portsys_current_path();
$faviconVersion = '20260821-1';
$navItems = [
    ['label' => portsys_text('home'), 'url' => site_url('/'), 'paths' => ['']],
    ['label' => portsys_text('about'), 'url' => site_url('about'), 'paths' => ['about']],
    ['label' => portsys_text('services'), 'url' => site_url('services'), 'paths' => ['services']],
    ['label' => portsys_text('ports'), 'url' => site_url('ports-directory'), 'paths' => ['ports-directory']],
    ['label' => portsys_text('ships'), 'url' => site_url('ships-directory'), 'paths' => ['ships-directory']],
    ['label' => portsys_text('news'), 'url' => site_url('news'), 'paths' => ['news']],
    ['label' => portsys_text('contact'), 'url' => site_url('contact'), 'paths' => ['contact']],
];
?>
<!DOCTYPE html>
<html lang="<?= esc($currentLocale) ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PortSys | <?= esc($this->renderSection('title') ?: portsys_text('brand_subtitle')) ?></title>
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
        :root{--ps-blue:#063879;--ps-dark:#052a5d;--ps-deeper:#041f48;--ps-accent:#0f5bb8;--ps-light:#f4f8fd;--ps-soft:#eaf2fb;--ps-text:#071f43;--ps-muted:#6e7d90;--ps-border:#dfe8f3;--shadow:0 12px 34px rgba(5,42,93,.12);}
        *{box-sizing:border-box} body{margin:0;font-family:<?= $isRtl ? "'Tajawal','Noto Kufi Arabic','Tahoma'" : "'Inter','Segoe UI'" ?>,sans-serif;background:#fff;color:var(--ps-text);line-height:1.75}.site-header{background:#fff;box-shadow:0 2px 15px rgba(5,42,93,.08);position:sticky;top:0;z-index:1020}.navbar{min-height:70px}.brand-logo{display:flex;align-items:center;gap:.6rem;text-decoration:none;color:var(--ps-text);font-weight:900;line-height:1.1}.brand-logo .mark{width:42px;height:42px;border-radius:14px;display:grid;place-items:center;background:#eef6ff;color:var(--ps-blue);font-size:1.55rem;overflow:hidden}.brand-logo .mark img{width:100%;height:100%;display:block}.language-public{min-width:74px;display:flex;align-items:center;justify-content:center;gap:.45rem}.brand-logo strong{font-size:1.28rem}.brand-logo small{display:block;color:var(--ps-muted);font-weight:700;font-size:.74rem}.nav-main .nav-link{font-weight:800;color:#13294b;margin:0 .45rem;padding:1.45rem .2rem;position:relative}.nav-main .nav-link.active,.nav-main .nav-link:hover{color:var(--ps-blue)}.nav-main .nav-link:after{content:"";height:3px;background:var(--ps-accent);border-radius:4px;position:absolute;left:.15rem;right:.15rem;bottom:1rem;transform:scaleX(0);transition:.22s}.nav-main .nav-link.active:after,.nav-main .nav-link:hover:after{transform:scaleX(1)}.btn-ps{border-radius:7px;padding:.55rem 1.05rem;font-weight:800;box-shadow:none}.btn-ps-primary{background:linear-gradient(135deg,var(--ps-accent),var(--ps-blue));border:0;color:#fff}.btn-ps-primary:hover{color:#fff;filter:brightness(1.04)}.btn-ps-outline{background:#fff;border:1px solid #b9cbe1;color:var(--ps-blue)}.btn-ps-outline:hover{background:#f4f8fd;color:var(--ps-blue)}
        .hero-home{position:relative;min-height:315px;display:flex;align-items:center;overflow:visible;background:linear-gradient(90deg,rgba(244,249,255,.98) 0%,rgba(244,249,255,.93) 42%,rgba(244,249,255,.35) 65%),url('<?= portsys_asset('hero-ship.jpg') ?>') center/cover no-repeat}.hero-home:before{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.12),rgba(255,255,255,.45));pointer-events:none}.hero-content{position:relative;z-index:1;padding:3rem 0 4rem}.kicker{font-size:1.25rem;font-weight:900;color:#112c55;margin-bottom:.2rem}.hero-home h1{font-size:clamp(2.1rem,4vw,4.1rem);font-weight:900;letter-spacing:-.04em;color:#091e40;margin:0 0 .75rem}.hero-home p{max-width:560px;color:#465a73;font-size:1.05rem}.hero-actions{display:flex;gap:.8rem;flex-wrap:wrap;margin-top:1.3rem}.stats-float{position:relative;margin-top:-32px;z-index:2}.stats-panel{max-width:900px;margin:auto;background:#fff;border-radius:12px;box-shadow:var(--shadow);border:1px solid var(--ps-border);display:grid;grid-template-columns:repeat(4,1fr);overflow:hidden}.stat-item{display:flex;align-items:center;justify-content:center;gap:.8rem;padding:1.05rem 1.1rem;border-inline-end:1px solid var(--ps-border)}.stat-item:last-child{border:0}.stat-icon,.circle-icon{width:44px;height:44px;border-radius:50%;display:grid;place-items:center;background:#edf6ff;color:var(--ps-accent);font-size:1.2rem}.stat-number{font-weight:900;color:var(--ps-accent);font-size:1.35rem;line-height:1}.stat-label{font-size:.78rem;color:#52657b;font-weight:700}.section-pad{padding:3.2rem 0}.section-title{text-align:center;margin-bottom:1.8rem}.section-title h2{font-size:1.75rem;font-weight:900;margin:0;color:#10294c}.section-title p{margin:.35rem auto 0;color:#6b7d91;max-width:650px}.feature-card,.service-card,.news-card,.port-card,.value-card,.team-card{background:#fff;border:1px solid var(--ps-border);border-radius:10px;box-shadow:0 8px 20px rgba(5,42,93,.06);transition:.22s;overflow:hidden}.feature-card:hover,.service-card:hover,.news-card:hover,.port-card:hover{transform:translateY(-4px);box-shadow:var(--shadow)}.feature-card{padding:1.35rem;text-align:center;height:100%}.feature-card .circle-icon{margin:0 auto .85rem}.feature-card h3{font-size:1rem;font-weight:900}.feature-card p{font-size:.84rem;color:#64758b;margin:0}.news-card img{width:100%;height:130px;object-fit:cover}.news-card .body{padding:1rem}.news-card h3{font-size:1rem;font-weight:900}.news-card p{font-size:.82rem;color:#64758b;margin:0}.inner-hero{background:linear-gradient(90deg,rgba(5,42,93,.96),rgba(5,42,93,.78)),url('<?= portsys_asset('banner-port-night.jpg') ?>') center/cover no-repeat;color:#fff;padding:3rem 0}.inner-hero h1{font-weight:900;font-size:2.35rem}.breadcrumb-mini{font-size:.9rem;color:#d5e7ff}.about-img,.content-img{border-radius:10px;box-shadow:var(--shadow);width:100%;object-fit:cover}.value-card{padding:1.45rem;height:100%;display:flex;gap:1rem;align-items:center}.value-card .big-icon{width:64px;height:64px;min-width:64px;border-radius:50%;display:grid;place-items:center;background:#0c59b5;color:#fff;font-size:1.8rem}.value-card h3{font-size:1.15rem;font-weight:900;margin-bottom:.25rem}.value-card p{font-size:.88rem;color:#61758d;margin:0}.service-card{height:100%;padding:1.4rem;display:flex;gap:1rem;align-items:flex-start}.service-card .circle-icon{min-width:56px;width:56px;height:56px;font-size:1.35rem}.service-card h3{font-size:1.04rem;font-weight:900;margin-bottom:.35rem}.service-card p{color:#60758e;font-size:.88rem;margin-bottom:.55rem}.service-card a{font-size:.84rem;font-weight:800;color:var(--ps-blue);text-decoration:none}.service-strip{background:#f2f7fd;border-top:1px solid var(--ps-border);border-bottom:1px solid var(--ps-border)}.cta-band{background:linear-gradient(90deg,#084aa0,#073878);color:#fff;padding:1.8rem 0}.cta-band .input-group{max-width:520px}.site-footer{background:#052a5d;color:#dbe9fb;padding:2.2rem 0 .8rem}.site-footer h4{color:#fff;font-weight:900;font-size:1rem;margin-bottom:.9rem}.site-footer a{color:#dbe9fb;text-decoration:none;display:block;margin:.25rem 0}.site-footer a:hover{color:#fff}.footer-brand .mark{background:rgba(255,255,255,.1);color:#fff}.footer-bottom{border-top:1px solid rgba(255,255,255,.12);margin-top:1.5rem;padding-top:.8rem;text-align:center;font-size:.85rem}.port-card img{width:100%;height:118px;object-fit:cover}.port-card .port-icon{width:58px;height:58px;margin:-29px auto .25rem;border:5px solid #fff;border-radius:50%;background:#eef6ff;color:var(--ps-blue);display:grid;place-items:center;font-size:1.35rem;position:relative}.port-card .body{padding:0 1rem 1rem;text-align:center}.port-card h3{font-size:1rem;font-weight:900}.mini-facts{display:grid;grid-template-columns:repeat(3,1fr);gap:.4rem;margin:.8rem 0;border-top:1px solid var(--ps-border);padding-top:.65rem}.mini-facts span{font-size:.75rem;color:#5d718a}.filter-box{background:#fff;border:1px solid var(--ps-border);border-radius:9px;box-shadow:0 8px 22px rgba(5,42,93,.06);padding:1rem}.team-avatar{width:72px;height:72px;border-radius:50%;background:#e6eef8;display:grid;place-items:center;color:#0c59b5;margin:0 auto .7rem;font-size:2rem}.team-card{text-align:center;padding:1rem}.faq-item{border:1px solid var(--ps-border);border-radius:8px;margin-bottom:.8rem;background:#fff}.faq-q{padding:1rem;font-weight:900}.faq-a{padding:0 1rem 1rem;color:#60758e}.form-box{background:#fff;border:1px solid var(--ps-border);box-shadow:var(--shadow);border-radius:10px;padding:1.4rem}.form-control,.form-select{border-color:#dce7f3;padding:.75rem}.form-control:focus,.form-select:focus{border-color:var(--ps-accent);box-shadow:0 0 0 .16rem rgba(15,91,184,.12)}@media(max-width:991px){.nav-main .nav-link{padding:.6rem 0}.stats-panel{grid-template-columns:repeat(2,1fr)}.hero-home{min-height:auto}.hero-content{padding:2.3rem 0 4rem}}@media(max-width:575px){.stats-panel{grid-template-columns:1fr}.stat-item{border-inline-end:0;border-bottom:1px solid var(--ps-border)}.stat-item:last-child{border-bottom:0}.section-pad{padding:2.2rem 0}.inner-hero{padding:2.2rem 0}}

        .brand-logo-image{gap:0;align-items:center}.brand-full-logo{height:108px;width:auto;max-width:150px;object-fit:contain;display:block}.site-header .navbar{min-height:120px;padding-block:.3rem}.site-header .brand-full-logo{margin:0}.footer-logo-block{align-items:center;background:rgba(255,255,255,.96);border-radius:20px;padding:.85rem 1rem;width:max-content;max-width:100%;box-shadow:0 14px 34px rgba(0,0,0,.18)}.footer-logo{height:170px;max-width:150px;filter:none}@media(max-width:991px){.site-header .navbar{min-height:auto}.brand-full-logo{height:92px;max-width:130px}}@media(max-width:575px){.brand-full-logo{height:78px;max-width:110px}.footer-logo{height:132px;max-width:120px}.footer-logo-block{padding:.65rem .75rem}}
        .pagination .page-link{color:#0f5bb8;border-color:#dbe5f2;box-shadow:none}.pagination .page-link:hover{background:#f4f8fd;border-color:#0f5bb8;color:#063879}.pagination .page-item.active .page-link{background:#0f5bb8;border-color:#0f5bb8;color:#fff}
    </style>
    <?= $this->renderSection('head') ?>
</head>
<body>
<header class="site-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="brand-logo brand-logo-image" href="<?= site_url('/') ?>" aria-label="<?= esc(lang('App.logo_alt')) ?>">
                <img class="brand-full-logo" src="<?= portsys_asset('portsys-logo.png') . '?v=' . $faviconVersion ?>" alt="<?= esc(lang('App.logo_alt')) ?>">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="<?= esc(lang('App.toggle_navigation')) ?>"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav nav-main mx-auto mb-2 mb-lg-0">
                    <?php foreach ($navItems as $item): $active = in_array($currentPath, $item['paths'], true); ?>
                        <li class="nav-item"><a class="nav-link <?= $active ? 'active' : '' ?>" href="<?= esc($item['url']) ?>"><?= esc($item['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div class="dropdown">
                        <button class="btn btn-ps btn-ps-outline language-public" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="<?= esc(lang('App.switch_language')) ?>"><i class="fa-solid fa-globe"></i><strong><?= $isRtl ? 'AR' : 'EN' ?></strong></button>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <a class="dropdown-item <?= $isRtl ? 'active' : '' ?>" href="<?= esc(language_switch_url('ar')) ?>"><?= esc(lang('App.arabic_language')) ?></a>
                            <a class="dropdown-item <?= ! $isRtl ? 'active' : '' ?>" href="<?= esc(language_switch_url('en')) ?>"><?= esc(lang('App.english_language')) ?></a>
                        </div>
                    </div>
                    <?php if ($isLoggedIn): ?>
                        <a class="btn btn-ps btn-ps-primary" href="<?= site_url('dashboard') ?>"><i class="fa-solid fa-gauge-high me-1"></i><?= portsys_text('dashboard') ?></a>
                    <?php else: ?>
                        <a class="btn btn-ps btn-ps-outline" href="<?= site_url('register') ?>"><i class="fa-solid fa-user-plus me-1"></i><?= portsys_text('register') ?></a>
                        <a class="btn btn-ps btn-ps-primary" href="<?= site_url('login') ?>"><i class="fa-solid fa-user-lock me-1"></i><?= portsys_text('login') ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
<?php if (session()->getFlashdata('success') || session()->getFlashdata('error') || is_array(session()->getFlashdata('errors'))): ?><div class="container pt-3"><?php if (session()->getFlashdata('success')): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa-solid fa-circle-check me-2"></i><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?><?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa-solid fa-circle-exclamation me-2"></i><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?><?php $publicErrors=session()->getFlashdata('errors'); if(is_array($publicErrors)&&$publicErrors!==[]): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="mb-0"><?php foreach($publicErrors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?></div><?php endif; ?>
<?= $this->renderSection('content') ?>
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="brand-logo footer-brand footer-logo-block mb-3"><img class="brand-full-logo footer-logo" src="<?= portsys_asset('portsys-logo.png') . '?v=' . $faviconVersion ?>" alt="<?= esc(lang('App.logo_alt')) ?>"></div>
                <p class="mb-3"><?= esc(lang('App.footer_description')) ?></p>
                <div class="d-flex gap-3 align-items-center"><a href="mailto:info@portsys.com" aria-label="<?= esc(lang('App.email')) ?>"><i class="fa-solid fa-envelope"></i></a><a href="tel:+963123456789" aria-label="<?= esc(lang('App.phone')) ?>"><i class="fa-solid fa-phone"></i></a><a href="<?= site_url('contact') ?>" aria-label="<?= esc(portsys_text('contact')) ?>"><i class="fa-solid fa-paper-plane"></i></a></div>
            </div>
            <div class="col-6 col-lg-2"><h4><?= portsys_text('quick_links') ?></h4><a href="<?= site_url('/') ?>"><?= portsys_text('home') ?></a><a href="<?= site_url('about') ?>"><?= portsys_text('about') ?></a><a href="<?= site_url('services') ?>"><?= portsys_text('services') ?></a><a href="<?= site_url('contact') ?>"><?= portsys_text('contact') ?></a></div>
            <div class="col-6 col-lg-2"><h4><?= portsys_text('our_services') ?></h4><a href="<?= site_url('services') ?>"><?= esc(lang('App.service_port_management')) ?></a><a href="<?= site_url('services') ?>"><?= esc(lang('App.service_ship_management')) ?></a><a href="<?= site_url('services') ?>"><?= esc(lang('App.service_loading_operations')) ?></a><a href="<?= site_url('services') ?>"><?= esc(lang('App.service_reports_analytics')) ?></a></div>
            <div class="col-6 col-lg-2"><h4><?= portsys_text('legal') ?></h4><a href="<?= site_url('privacy') ?>"><?= portsys_text('privacy') ?></a><a href="<?= site_url('terms') ?>"><?= portsys_text('terms') ?></a><a href="<?= site_url('faq') ?>"><?= portsys_text('faq') ?></a></div>
            <div class="col-6 col-lg-2"><h4><?= portsys_text('contact_us') ?></h4><div dir="ltr">+963 12 345 6789</div><div>info@portsys.com</div><div><?= esc(lang('App.contact_address_value')) ?></div></div>
        </div>
        <div class="footer-bottom"><?= date('Y') ?> <?= portsys_text('brand') ?> © <?= portsys_text('all_rights') ?></div>
    </div>
</footer>
<script src="<?= portsys_asset('bootstrap-5.3.6.bundle.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
