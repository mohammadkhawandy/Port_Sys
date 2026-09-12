<?php
$currentLocale = (string) service('request')->getLocale();
$isRtl = $currentLocale === 'ar';
$currentPath = portsys_current_path();
$notificationUnreadCount = (int) ($notificationUnreadCount ?? 0);
$unreadMessagesCount = (int) ($unreadMessagesCount ?? 0);
$pendingTripRequestsCount = (int) ($pendingTripRequestsCount ?? 0);
$notificationItems = $notificationItems ?? [];
$displayName = current_user_display_name();
$displayEmail = (string) (session('userEmail') ?? '');
$roleLabel = is_admin_user() ? lang('App.role_admin') : lang('App.role_user');
$initial = mb_strtoupper(mb_substr($displayName, 0, 1));
$pageTitle = trim($this->renderSection('title')) ?: lang('App.dashboard');
$faviconVersion = '20260821-1';

$menuItems = [
    ['label' => lang('App.nav_dashboard'), 'icon' => 'fa-gauge-high', 'url' => site_url('dashboard'), 'match' => 'dashboard'],
    ['label' => lang('App.nav_ports'), 'icon' => 'fa-anchor', 'url' => site_url('ports'), 'match' => 'ports'],
    ['label' => lang('App.nav_ships'), 'icon' => 'fa-ship', 'url' => site_url('ships'), 'match' => 'ships'],
    ['label' => lang('App.nav_trips'), 'icon' => 'fa-route', 'url' => site_url('trips'), 'match' => 'trips'],
];
if (is_admin_user()) {
    $menuItems[] = ['label' => lang('App.nav_trip_requests'), 'icon' => 'fa-clipboard-list', 'url' => site_url('trip-requests'), 'match' => 'trip-requests', 'badge' => $pendingTripRequestsCount];
    $menuItems[] = ['label' => lang('App.nav_places'), 'icon' => 'fa-location-dot', 'url' => site_url('places'), 'match' => 'places'];
    $menuItems[] = ['label' => lang('App.nav_users'), 'icon' => 'fa-users-gear', 'url' => site_url('users'), 'match' => 'users'];
    $menuItems[] = ['label' => lang('App.nav_messages'), 'icon' => 'fa-envelope', 'url' => site_url('messages'), 'match' => 'messages', 'badge' => $unreadMessagesCount];
    $menuItems[] = ['label' => lang('App.nav_activity_log'), 'icon' => 'fa-clock-rotate-left', 'url' => site_url('activity-log'), 'match' => 'activity-log'];
} else {
    $menuItems[] = ['label' => lang('App.nav_my_requests'), 'icon' => 'fa-clipboard-check', 'url' => site_url('trip-requests/my'), 'match' => 'trip-requests/my'];
    $menuItems[] = ['label' => lang('App.nav_my_trips'), 'icon' => 'fa-ticket', 'url' => site_url('my-trips'), 'match' => 'my-trips'];
}

$isActive = static function (string $match) use ($currentPath): bool {
    if ($match === 'trips') {
        return $currentPath === 'trips' || str_starts_with($currentPath, 'trips/');
    }
    return $currentPath === $match || str_starts_with($currentPath, $match . '/');
};
?>
<!DOCTYPE html>
<html lang="<?= esc($currentLocale) ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= esc(csrf_hash()) ?>">
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

        :root{
            --navy:#082b5c;--navy-deep:#061d3d;--primary:#0d63c7;--primary-soft:#eaf3ff;--surface:#fff;--surface-2:#f7f9fc;--surface-3:#edf3fb;--page:#f2f5f9;--text:#132b4d;--muted:#6f7f94;--border:#e1e8f0;--danger:#dc3545;--success:#198754;--shadow:0 10px 32px rgba(12,42,79,.07);--sidebar:272px;--radius:16px;--focus-ring:0 0 0 .2rem rgba(13,99,199,.14);--close-filter:none;--hero-overlay-start:rgba(4,23,50,.96);--hero-overlay-end:rgba(8,43,92,.70);--hero-sheen:rgba(255,255,255,.06);--hero-chip-bg:rgba(232,255,242,.92);--hero-chip-text:#0b5b37;--hero-chip-border:rgba(18,122,74,.18)
        }
        [data-theme="dark"]{
            color-scheme:dark;--surface:#101d2d;--surface-2:#142338;--surface-3:#1a2c44;--page:#0b1522;--text:#e8f0fa;--muted:#9cafc6;--border:#24364b;--primary-soft:#17355b;--shadow:0 12px 34px rgba(0,0,0,.22);--focus-ring:0 0 0 .2rem rgba(59,130,246,.22);--close-filter:invert(1) grayscale(100%) brightness(210%);--hero-overlay-start:rgba(4,16,34,.93);--hero-overlay-end:rgba(7,34,71,.82);--hero-sheen:rgba(255,255,255,.04);--hero-chip-bg:rgba(15,73,44,.42);--hero-chip-text:#c9f8dc;--hero-chip-border:rgba(102,220,151,.24)
        }
        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{margin:0;background:var(--page);color:var(--text);font-family:<?= $isRtl ? "'Tajawal','Noto Kufi Arabic','Tahoma'" : "'Inter','Segoe UI'" ?>,sans-serif;min-height:100vh}
        a{color:var(--primary)}
        .admin-shell{min-height:100vh}
        .admin-sidebar{position:fixed;inset-block:0;inset-inline-start:0;width:var(--sidebar);z-index:1040;background:linear-gradient(180deg,var(--navy-deep),var(--navy));color:#fff;padding:1.35rem 1rem;display:flex;flex-direction:column;box-shadow:0 0 34px rgba(4,26,56,.2);transition:transform .25s ease}
        .admin-brand{display:flex;align-items:center;gap:.8rem;padding:.35rem .45rem 1.35rem;border-bottom:1px solid rgba(255,255,255,.1);text-decoration:none;color:#fff}
        .brand-mark{width:48px;height:48px;border-radius:15px;background:rgba(255,255,255,.12);display:grid;place-items:center;font-size:1.35rem;overflow:hidden}
        .brand-mark img{width:100%;height:100%;display:block}
        .admin-brand strong{font-size:1.15rem;display:block}
        .admin-brand small{font-size:.72rem;color:#b7c9df}
        .menu-label{font-size:.68rem;text-transform:uppercase;letter-spacing:.09em;color:#829dbd;margin:1.4rem .75rem .55rem}
        .admin-menu{list-style:none;padding:0;margin:0}
        .admin-menu a{display:flex;align-items:center;gap:.8rem;color:#d7e4f5;text-decoration:none;padding:.76rem .85rem;border-radius:11px;margin:.18rem 0;font-size:.88rem;font-weight:700;transition:.18s}
        .admin-menu a i{width:22px;text-align:center}
        .admin-menu a:hover,.admin-menu a.active{background:rgba(35,126,225,.9);color:#fff;transform:translateX(<?= $isRtl ? '-2px' : '2px' ?>)}
        .menu-badge{margin-inline-start:auto;background:#ef4444;color:#fff;min-width:21px;height:21px;padding:0 6px;border-radius:20px;display:grid;place-items:center;font-size:.68rem}
        .sidebar-footer{margin-top:auto;border-top:1px solid rgba(255,255,255,.1);padding-top:1rem}
        .sidebar-user{display:flex;align-items:center;gap:.7rem;padding:.65rem}
        .avatar{width:39px;height:39px;border-radius:12px;background:linear-gradient(135deg,#1f7de1,#60a5fa);display:grid;place-items:center;color:#fff;font-weight:800;flex:0 0 auto}
        .sidebar-user strong{font-size:.8rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px}
        .sidebar-user small{font-size:.67rem;color:#a9bdd6}
        .admin-main{min-height:100vh;margin-inline-start:var(--sidebar)}
        .admin-topbar{height:76px;background:color-mix(in srgb,var(--surface) 94%,transparent);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:1030;display:flex;align-items:center;padding:0 1.4rem;gap:1rem}
        .topbar-search{position:relative;width:min(560px,48vw)}
        .topbar-search .search-icon{position:absolute;inset-inline-start:1rem;top:50%;transform:translateY(-50%);color:var(--muted);z-index:2}
        .topbar-search input{width:100%;height:44px;border:1px solid var(--border);border-radius:12px;background:var(--surface-2);color:var(--text);padding-inline-start:2.8rem;padding-inline-end:3.2rem;outline:none;transition:.2s}
        .topbar-search input::placeholder{color:var(--muted);opacity:.9}
        .topbar-search input:focus{border-color:var(--primary);background:var(--surface);box-shadow:var(--focus-ring)}
        .search-shortcut{position:absolute;inset-inline-end:.75rem;top:50%;transform:translateY(-50%);font-size:.7rem;border:1px solid var(--border);background:var(--surface);color:var(--muted);border-radius:6px;padding:.15rem .42rem}
        .search-results{position:absolute;top:calc(100% + 8px);inset-inline:0;background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:0 18px 45px rgba(10,37,74,.16);overflow:hidden;display:none;max-height:430px;overflow-y:auto}
        .search-results.show{display:block}
        .search-result{display:flex;gap:.8rem;align-items:center;padding:.8rem 1rem;text-decoration:none;color:var(--text);border-bottom:1px solid var(--border)}
        .search-result:last-child{border-bottom:0}
        .search-result:hover{background:var(--surface-2)}
        .search-result .result-icon{width:35px;height:35px;border-radius:10px;background:var(--primary-soft);color:var(--primary);display:grid;place-items:center}
        .search-result strong{display:block;font-size:.82rem}
        .search-result small{display:block;color:var(--muted);font-size:.7rem}
        .topbar-actions{margin-inline-start:auto;display:flex;align-items:center;gap:.5rem}
        .top-icon{width:42px;height:42px;border:1px solid var(--border);background:var(--surface);color:var(--text);border-radius:12px;display:grid;place-items:center;position:relative;text-decoration:none;transition:.18s}
        .top-icon:hover{color:var(--primary);background:var(--primary-soft);border-color:#bcd8f8}
        .language-button{width:auto;min-width:68px;padding:0 .72rem;display:flex;gap:.45rem;font-weight:800}
        .language-button .language-code{font-size:.72rem;letter-spacing:.06em}
        .language-menu{min-width:210px}
        .language-option{display:flex;align-items:center;justify-content:space-between;gap:1rem}
        .language-option.active{background:var(--primary-soft);color:var(--primary);font-weight:800}
        .notification-dot{position:absolute;top:-5px;inset-inline-end:-5px;background:#ef4444;color:#fff;border:2px solid var(--surface);border-radius:20px;min-width:21px;height:21px;padding:0 5px;font-size:.62rem;display:grid;place-items:center}
        .profile-button{height:44px;border:1px solid var(--border);background:var(--surface);border-radius:12px;padding:.3rem .55rem;display:flex;align-items:center;gap:.6rem;color:var(--text)}
        .profile-button .avatar{width:33px;height:33px;border-radius:9px;font-size:.78rem}
        .profile-copy{text-align:start;line-height:1.15}
        .profile-copy strong{display:block;font-size:.74rem;max-width:130px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .profile-copy small{font-size:.62rem;color:var(--muted)}
        .dropdown-menu,.modal-content,.offcanvas,.list-group-item,.accordion-item,.breadcrumb,.card,.input-group-text{background:var(--surface);color:var(--text);border-color:var(--border)}
        .dropdown-menu{box-shadow:0 16px 42px rgba(8,43,92,.15);border-radius:14px;padding:.55rem}
        .dropdown-item{color:var(--text);border-radius:9px;font-size:.82rem;padding:.62rem .75rem}
        .dropdown-item:hover{background:var(--surface-2);color:var(--primary)}
        .dropdown-item.active,.dropdown-item:active{background:var(--primary);color:#fff}
        .notifications-menu{width:min(390px,calc(100vw - 24px));padding:0;overflow:hidden}
        .notifications-head{padding:1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
        .notifications-list{max-height:390px;overflow-y:auto}
        .notification-item{display:flex;gap:.75rem;padding:.85rem 1rem;text-decoration:none;color:var(--text);border-bottom:1px solid var(--border);position:relative}
        .notification-item:hover{background:var(--surface-2)}
        .notification-item.unread{background:color-mix(in srgb,var(--primary-soft) 60%,var(--surface))}
        .notification-item.unread:after{content:"";position:absolute;inset-inline-end:.65rem;top:1rem;width:7px;height:7px;border-radius:50%;background:var(--primary)}
        .notification-icon{width:38px;height:38px;border-radius:11px;background:var(--primary-soft);color:var(--primary);display:grid;place-items:center;flex:0 0 auto}
        .notification-copy strong{display:block;font-size:.78rem;margin-bottom:.18rem}
        .notification-copy p{font-size:.7rem;color:var(--muted);margin:0 0 .2rem;line-height:1.55}
        .notification-copy time{font-size:.62rem;color:var(--muted)}
        .notifications-foot{padding:.72rem;text-align:center;background:var(--surface-2)}
        .content-wrap{padding:1.5rem;max-width:1700px;margin:0 auto}
        .page-card,.table-shell,.form-shell,.stat-card,.panel-card,.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow)}
        .page-title{font-weight:800;color:var(--text)}
        .muted,.text-muted,.text-body-secondary{color:var(--muted)!important}
        .text-dark,.text-body,.text-body-emphasis{color:var(--text)!important}
        .bg-light,.bg-white,.bg-body,.bg-body-tertiary,.bg-body-secondary,.bg-body-secondary-subtle,.bg-body-tertiary-subtle{background:var(--surface)!important;color:var(--text)!important}
        .section-title{font-size:.73rem;color:var(--primary);font-weight:800;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem}
        .panel-title{font-weight:800;color:var(--text)}
        .btn{border-radius:10px;font-weight:700}
        .btn-primary{background:var(--primary);border-color:var(--primary)}
        .btn-outline-light{border-color:var(--border);color:var(--text);background:var(--surface)}
        .btn-outline-light:hover{background:var(--surface-2);color:var(--primary);border-color:var(--primary)}
        .btn-outline-success{color:#137a4a;border-color:rgba(19,122,74,.36);background:transparent}
        .btn-outline-success:hover{background:rgba(19,122,74,.10);color:#0f6b43;border-color:#137a4a}
        .btn-outline-danger{color:#ba3249;border-color:rgba(186,50,73,.36);background:transparent}
        .btn-outline-danger:hover{background:rgba(186,50,73,.10);color:#9f2338;border-color:#ba3249}
        .btn-outline-warning{color:#a36a00;border-color:rgba(163,106,0,.35);background:transparent}
        .btn-outline-warning:hover{background:rgba(245,158,11,.12);color:#8c5a00;border-color:#c88500}
        [data-theme="dark"] .btn-outline-success{color:#bff1d4;border-color:rgba(110,228,160,.32)}
        [data-theme="dark"] .btn-outline-success:hover{background:rgba(54,179,107,.18);color:#d7f8e3;border-color:#63d197}
        [data-theme="dark"] .btn-outline-danger{color:#ffc8d0;border-color:rgba(255,125,145,.30)}
        [data-theme="dark"] .btn-outline-danger:hover{background:rgba(220,53,69,.18);color:#ffdbe0;border-color:#ff90a1}
        [data-theme="dark"] .btn-outline-warning{color:#ffe2a1;border-color:rgba(255,196,82,.30)}
        [data-theme="dark"] .btn-outline-warning:hover{background:rgba(245,158,11,.18);color:#ffebb9;border-color:#ffc355}
        .btn-link{color:var(--primary)}
        .btn:disabled,.btn.disabled{opacity:.62;box-shadow:none}
        .btn-close{filter:var(--close-filter);opacity:.9}
        .table{--bs-table-bg:transparent;--bs-table-color:var(--text);--bs-table-striped-bg:color-mix(in srgb,var(--surface-2) 84%,transparent);--bs-table-striped-color:var(--text);--bs-table-hover-bg:color-mix(in srgb,var(--primary-soft) 55%,transparent);--bs-table-hover-color:var(--text);color:var(--text);margin-bottom:0}
        .table thead th{color:var(--muted);font-size:.72rem;font-weight:700;border-bottom-color:var(--border);white-space:nowrap}
        .table td,.table th{vertical-align:middle;border-color:var(--border);padding:.88rem}
        .form-control,.form-select{background:var(--surface);color:var(--text);border-color:var(--border);border-radius:10px;min-height:44px}
        .form-control:focus,.form-select:focus{background:var(--surface);color:var(--text);border-color:var(--primary);box-shadow:var(--focus-ring)}
        .form-control::placeholder{color:var(--muted);opacity:.9}
        .form-control:disabled,.form-control[readonly],.form-select:disabled{background:var(--surface-3)!important;color:var(--text)!important;border-color:var(--border)!important;opacity:1;-webkit-text-fill-color:var(--text);cursor:not-allowed}
        .form-select.form-select-sm{min-width:124px}
        .form-check-input{background-color:var(--surface);border-color:var(--border)}
        .form-check-input:checked{background-color:var(--primary);border-color:var(--primary)}
        .form-check-input:focus{box-shadow:var(--focus-ring)}
        .form-label{font-size:.8rem;font-weight:700}
        .badge-soft{background:var(--primary-soft);color:var(--primary);border:1px solid color-mix(in srgb,var(--primary) 18%,var(--border))}
        .dashboard-hero{position:relative;overflow:hidden;isolation:isolate;background:linear-gradient(90deg,var(--hero-overlay-start),var(--hero-overlay-end)),url('<?= portsys_asset('banner-port-night.jpg') ?>') center 42%/cover no-repeat;color:#fff;border-radius:24px;padding:2.25rem 2.4rem;min-height:210px;display:flex;align-items:center;box-shadow:0 18px 42px rgba(6,29,61,.16)}
        .dashboard-hero::before{content:"";position:absolute;inset:0;background:radial-gradient(circle at <?= $isRtl ? '18% 30%' : '82% 26%' ?>,var(--hero-sheen),transparent 34%),linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,0));pointer-events:none}
        .dashboard-hero:after{content:"";position:absolute;width:260px;height:260px;border:52px solid rgba(255,255,255,.05);border-radius:50%;inset-inline-start:<?= $isRtl ? '-28px' : 'auto' ?>;inset-inline-end:<?= $isRtl ? 'auto' : '-40px' ?>;top:-80px}
        .dashboard-hero>*{position:relative;z-index:1}
        .dashboard-hero .section-title{color:#cfe4ff;margin-bottom:.6rem}
        .dashboard-hero h1{color:#fff;line-height:1.15;text-shadow:0 5px 22px rgba(0,0,0,.18)}
        .dashboard-hero p{font-size:1.02rem;line-height:1.8;color:rgba(255,255,255,.84)!important}
        .dashboard-status-pill{display:inline-flex;align-items:center;gap:.45rem;padding:.7rem 1rem;border-radius:999px;background:var(--hero-chip-bg)!important;color:var(--hero-chip-text)!important;border:1px solid var(--hero-chip-border)!important;box-shadow:0 10px 22px rgba(0,0,0,.12);font-weight:800;backdrop-filter:blur(8px)}
        .dashboard-status-pill i{font-size:.95rem}
        .kpi-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);padding:1.15rem;display:flex;align-items:center;gap:.9rem;height:100%;transition:.2s}
        .kpi-card:hover{transform:translateY(-3px);box-shadow:0 16px 38px rgba(8,43,92,.11)}
        .kpi-icon{width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:var(--primary-soft);color:var(--primary);font-size:1.15rem}
        .kpi-value{font-size:1.45rem;font-weight:800;color:var(--text)}
        .user-actions{display:flex;gap:.5rem;flex-wrap:wrap;justify-content:flex-end;align-items:center}
        .user-actions .action-form{display:flex;gap:.35rem;align-items:center;margin:0}
        .user-actions .form-select{min-width:132px}
        .user-actions .btn{white-space:nowrap}
        .alert{border-radius:13px;border:1px solid var(--border);box-shadow:var(--shadow)}
        [data-theme="dark"] .alert-success{background:rgba(25,135,84,.14);color:#d0f1df}
        [data-theme="dark"] .alert-danger{background:rgba(220,53,69,.16);color:#ffd4d9}
        .mobile-toggle{display:none}
        .sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(3,15,31,.55);z-index:1035}
        .pagination{--bs-pagination-bg:var(--surface);--bs-pagination-color:var(--text);--bs-pagination-border-color:var(--border);--bs-pagination-hover-bg:var(--primary-soft);--bs-pagination-hover-color:var(--primary);--bs-pagination-active-bg:var(--primary);--bs-pagination-active-border-color:var(--primary)}
        .pagination .page-link{background:var(--surface);border-color:var(--border);color:var(--text);box-shadow:none}
        .pagination .page-link:hover{background:var(--surface-2);border-color:var(--primary);color:var(--primary)}
        .pagination .page-item.active .page-link{background:var(--primary);border-color:var(--primary);color:#fff}
        .list-group-item:hover,.accordion-button:not(.collapsed),.nav-tabs .nav-link.active{background:var(--surface-2);color:var(--text)}
        .accordion-button{background:var(--surface);color:var(--text)}
        .accordion-button:not(.collapsed){box-shadow:none}
        .accordion-button:focus{box-shadow:var(--focus-ring)}
        .nav-tabs,.nav-pills,.border,.border-top,.border-end,.border-bottom,.border-start,.hr,hr{border-color:var(--border)!important}
        .nav-tabs .nav-link{color:var(--muted)}
        .nav-tabs .nav-link:hover{border-color:var(--border);color:var(--text)}
        .nav-tabs .nav-link.active{border-color:var(--border) var(--border) var(--surface)}
        .modal-header,.modal-footer,.offcanvas-header,.offcanvas-footer,.list-group-item,.breadcrumb-item+.breadcrumb-item::before{border-color:var(--border)}
        .breadcrumb-item,.breadcrumb-item a{color:var(--muted)}
        .text-bg-light,.text-bg-secondary,.text-bg-secondary-subtle{background:var(--surface-2)!important;color:var(--text)!important}
        .text-bg-success-subtle{background:rgba(25,135,84,.14)!important;color:#0f6b43!important}
        .text-bg-warning-subtle{background:rgba(245,158,11,.16)!important;color:#9a6700!important}
        .text-bg-danger-subtle{background:rgba(220,53,69,.14)!important;color:#b42333!important}
        .text-bg-info-subtle,.text-bg-primary-subtle{background:rgba(13,99,199,.14)!important;color:#0d63c7!important}
        .text-success-emphasis{color:#0f6b43!important}.text-warning-emphasis{color:#9a6700!important}.text-danger-emphasis{color:#b42333!important}.text-info-emphasis,.text-primary-emphasis{color:#0d63c7!important}
        [data-theme="dark"] .text-bg-success-subtle{background:rgba(25,135,84,.22)!important;color:#c7f1da!important}
        [data-theme="dark"] .text-bg-warning-subtle{background:rgba(245,158,11,.20)!important;color:#ffe1a3!important}
        [data-theme="dark"] .text-bg-danger-subtle{background:rgba(220,53,69,.22)!important;color:#ffd0d5!important}
        [data-theme="dark"] .text-bg-info-subtle,[data-theme="dark"] .text-bg-primary-subtle{background:rgba(59,130,246,.20)!important;color:#d8e9ff!important}
        [data-theme="dark"] .text-success-emphasis{color:#c7f1da!important}[data-theme="dark"] .text-warning-emphasis{color:#ffe1a3!important}[data-theme="dark"] .text-danger-emphasis{color:#ffd0d5!important}[data-theme="dark"] .text-info-emphasis,[data-theme="dark"] .text-primary-emphasis{color:#d8e9ff!important}
        @media(max-width:1100px){.profile-copy{display:none}.topbar-search{width:min(460px,55vw)}}
        @media(max-width:991.98px){.admin-sidebar{transform:translateX(<?= $isRtl ? '105%' : '-105%' ?>)}.admin-sidebar.show{transform:translateX(0)}.admin-main{margin-inline-start:0}.mobile-toggle{display:grid}.sidebar-backdrop.show{display:block}.content-wrap{padding:1rem}.admin-topbar{padding:0 .9rem}.topbar-search{width:auto;flex:1}.dashboard-hero{padding:1.7rem 1.5rem;background-position:center 36%}}
        @media(max-width:640px){.topbar-search{display:none}.admin-topbar{justify-content:space-between}.dashboard-hero{padding:1.35rem 1.15rem;min-height:168px;border-radius:18px}.dashboard-hero h1{font-size:clamp(1.8rem,5vw,2.2rem)}.dashboard-status-pill{padding:.55rem .85rem}.content-wrap{padding:.8rem}.top-icon.hide-mobile{display:none}.profile-button{padding:.3rem}.notifications-menu{width:calc(100vw - 16px)}}
        .admin-brand-image{justify-content:center;align-items:center;padding:1rem .8rem;margin:.1rem .15rem 1.05rem;background:rgba(255,255,255,.96);border:1px solid rgba(255,255,255,.42);border-radius:22px;box-shadow:0 18px 38px rgba(0,0,0,.20);border-bottom:0}.admin-full-logo{height:158px;width:auto;max-width:150px;object-fit:contain;display:block;filter:none}.sidebar-footer{padding-top:1.15rem}@media(max-width:991.98px){.admin-full-logo{height:142px}}
        @media print{.admin-sidebar,.admin-topbar{display:none!important}.admin-main{margin:0}.content-wrap{padding:0}.page-card,.table-shell,.panel-card{box-shadow:none}}
        
    </style>
    <?= $this->renderSection('head') ?>
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar" aria-label="<?= esc(lang('App.dashboard_aria_sidebar_navigation')) ?>">
        <a class="admin-brand admin-brand-image" href="<?= site_url('dashboard') ?>" aria-label="<?= esc(lang('App.logo_alt')) ?>">
            <img class="admin-full-logo" src="<?= portsys_asset('portsys-logo.png') . '?v=' . $faviconVersion ?>" alt="<?= esc(lang('App.logo_alt')) ?>">
        </a>
        <div class="menu-label"><?= esc(lang('App.dashboard_management_summary')) ?></div>
        <ul class="admin-menu">
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <a class="<?= $isActive($item['match']) ? 'active' : '' ?>" href="<?= esc($item['url']) ?>">
                        <i class="fa-solid <?= esc($item['icon']) ?>"></i>
                        <span><?= esc($item['label']) ?></span>
                        <?php if ((int) ($item['badge'] ?? 0) > 0): ?><span class="menu-badge"><?= (int) $item['badge'] ?></span><?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="sidebar-footer">
            <a class="admin-menu d-block text-decoration-none" href="<?= site_url('/') ?>">
                <span style="display:flex;align-items:center;gap:.8rem;color:#d7e4f5;padding:.72rem .85rem"><i class="fa-solid fa-arrow-up-right-from-square"></i><?= esc(lang('App.public_site')) ?></span>
            </a>
            <div class="sidebar-user">
                <span class="avatar"><?= esc($initial) ?></span>
                <span class="min-w-0"><strong><?= esc($displayName) ?></strong><small><?= esc($roleLabel) ?></small></span>
            </div>
        </div>
    </aside>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <main class="admin-main">
        <header class="admin-topbar">
            <button class="top-icon mobile-toggle" id="sidebarToggle" type="button" aria-label="<?= esc(lang('App.dashboard_toggle_sidebar')) ?>"><i class="fa-solid fa-bars"></i></button>
            <div class="topbar-search" id="globalSearchBox">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input id="globalSearch" type="search" autocomplete="off" aria-label="<?= esc(lang('App.dashboard_global_search')) ?>" placeholder="<?= esc(lang('App.dashboard_global_search_placeholder')) ?>">
                <span class="search-shortcut">/</span>
                <div class="search-results" id="globalSearchResults" aria-live="polite"></div>
            </div>
            <div class="topbar-actions">
                <?php if (is_admin_user()): ?>
                    <div class="dropdown hide-mobile">
                        <button class="top-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="<?= esc(lang('App.quick_actions')) ?>"><i class="fa-solid fa-plus"></i></button>
                        <div class="dropdown-menu dropdown-menu-end mt-2" style="min-width:220px">
                            <div class="px-3 py-2 small fw-bold muted"><?= esc(lang('App.quick_actions')) ?></div>
                            <a class="dropdown-item" href="<?= site_url('ports/create') ?>"><i class="fa-solid fa-anchor me-2"></i><?= esc(lang('App.create_port')) ?></a>
                            <a class="dropdown-item" href="<?= site_url('ships/create') ?>"><i class="fa-solid fa-ship me-2"></i><?= esc(lang('App.create_ship')) ?></a>
                            <a class="dropdown-item" href="<?= site_url('trips/create') ?>"><i class="fa-solid fa-route me-2"></i><?= esc(lang('App.create_trip')) ?></a>
                        </div>
                    </div>
                <?php endif; ?>
                <button class="top-icon hide-mobile" id="fullscreenToggle" type="button" title="<?= esc(lang('App.dashboard_enter_fullscreen')) ?>" aria-label="<?= esc(lang('App.dashboard_enter_fullscreen')) ?>"><i class="fa-solid fa-expand"></i></button>
                <button class="top-icon" id="themeToggle" type="button" title="<?= esc(lang('App.dashboard_toggle_theme')) ?>" aria-label="<?= esc(lang('App.dashboard_toggle_theme')) ?>"><i class="fa-regular fa-moon"></i></button>
                <div class="dropdown">
                    <button class="top-icon" id="notificationsToggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="<?= esc(lang('App.aria_open_notifications')) ?>" title="<?= esc(lang('App.dashboard_notifications')) ?>">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notification-dot <?= $notificationUnreadCount > 0 ? '' : 'd-none' ?>" id="notificationBadge"><?= $notificationUnreadCount > 99 ? '99+' : $notificationUnreadCount ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notifications-menu" id="notificationsMenu">
                        <div class="notifications-head">
                            <div><strong><?= esc(lang('App.nav_notifications')) ?></strong><div class="small muted"><span id="notificationUnreadText"><?= $notificationUnreadCount ?></span> <?= esc(lang('App.unread')) ?> · <?= esc(lang('App.notification_live_updates')) ?></div></div>
                            <form id="markAllNotificationsForm" class="<?= $notificationUnreadCount > 0 ? '' : 'd-none' ?>" method="post" action="<?= site_url('notifications/read-all') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-link text-decoration-none" type="submit"><?= esc(lang('App.mark_all_read')) ?></button></form>
                        </div>
                        <div class="notifications-list" id="notificationsList">
                            <?php if ($notificationItems === []): ?>
                                <div class="text-center p-4"><div class="notification-icon mx-auto mb-2"><i class="fa-regular fa-bell-slash"></i></div><div class="small fw-bold"><?= esc(lang('App.no_notifications')) ?></div><div class="small muted mt-1"><?= esc(lang('App.no_notifications_desc')) ?></div></div>
                            <?php else: foreach ($notificationItems as $notification): ?>
                                <a class="notification-item <?= (int) ($notification['is_read'] ?? 0) === 0 ? 'unread' : '' ?>" href="<?= site_url('notifications/read/' . (int) $notification['id']) ?>">
                                    <span class="notification-icon"><i class="fa-solid <?= esc(notification_icon($notification['type'] ?? null)) ?>"></i></span>
                                    <span class="notification-copy"><strong><?= esc((string) ($notification['title'] ?? '')) ?></strong><p><?= esc((string) ($notification['message'] ?? '')) ?></p><time><?= esc(human_time($notification['created_at'] ?? null)) ?></time></span>
                                </a>
                            <?php endforeach; endif; ?>
                        </div>
                        <div class="notifications-foot"><a class="small fw-bold text-decoration-none" href="<?= site_url('notifications') ?>"><?= esc(lang('App.view_all_notifications')) ?></a></div>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="top-icon language-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="<?= esc(lang('App.switch_language')) ?>" aria-label="<?= esc(lang('App.switch_language')) ?>">
                        <i class="fa-solid fa-globe"></i><span class="language-code"><?= $isRtl ? 'AR' : 'EN' ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end language-menu mt-2">
                        <div class="px-3 py-2 small fw-bold muted"><?= esc(lang('App.current_language')) ?></div>
                        <a class="dropdown-item language-option <?= $isRtl ? 'active' : '' ?>" href="<?= esc(language_switch_url('ar')) ?>"><span><?= esc(lang('App.arabic_language')) ?></span><?php if ($isRtl): ?><i class="fa-solid fa-check"></i><?php endif; ?></a>
                        <a class="dropdown-item language-option <?= ! $isRtl ? 'active' : '' ?>" href="<?= esc(language_switch_url('en')) ?>"><span><?= esc(lang('App.english_language')) ?></span><?php if (! $isRtl): ?><i class="fa-solid fa-check"></i><?php endif; ?></a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="profile-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="<?= esc(lang('App.aria_open_profile')) ?>">
                        <span class="avatar"><?= esc($initial) ?></span>
                        <span class="profile-copy"><strong><?= esc($displayName) ?></strong><small><?= esc($roleLabel) ?></small></span>
                        <i class="fa-solid fa-chevron-down small muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-2" style="min-width:220px">
                        <div class="px-3 py-2 border-bottom"><strong class="d-block small text-truncate"><?= esc($displayName) ?></strong><span class="small muted text-truncate d-block"><?= esc($displayEmail) ?></span></div>
                        <a class="dropdown-item mt-1" href="<?= site_url('profile') ?>"><i class="fa-regular fa-user me-2"></i><?= esc(lang('App.nav_profile')) ?></a>
                        <a class="dropdown-item" href="<?= site_url('notifications') ?>"><i class="fa-regular fa-bell me-2"></i><?= esc(lang('App.nav_notifications')) ?></a>
                        <a class="dropdown-item" href="<?= site_url('/') ?>"><i class="fa-solid fa-globe me-2"></i><?= esc(lang('App.public_site')) ?></a>
                        <div class="dropdown-divider"></div>
                        <form method="post" action="<?= site_url('logout') ?>"><?= csrf_field() ?><button class="dropdown-item text-danger" type="submit"><i class="fa-solid fa-right-from-bracket me-2"></i><?= esc(lang('App.nav_logout')) ?></button></form>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrap">
            <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa-solid fa-circle-check me-2"></i><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa-solid fa-circle-exclamation me-2"></i><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php $errors = session()->getFlashdata('errors'); if (is_array($errors) && $errors !== []): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><div class="fw-bold mb-1"><?= esc(lang('App.msg_validation_failed')) ?></div><ul class="mb-0 small"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?= $this->renderSection('content') ?>
        </div>
    </main>
</div>
<script src="<?= portsys_asset('bootstrap-5.3.6.bundle.min.js') ?>"></script>
<script>
(() => {
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggleSidebar = (open) => {
        const show = typeof open === 'boolean' ? open : !sidebar.classList.contains('show');
        sidebar.classList.toggle('show', show); backdrop.classList.toggle('show', show);
    };
    document.getElementById('sidebarToggle')?.addEventListener('click', () => toggleSidebar());
    backdrop?.addEventListener('click', () => toggleSidebar(false));

    const root = document.documentElement;
    const themeButton = document.getElementById('themeToggle');
    const themeMeta = document.querySelector('meta[name="theme-color"]');
    let savedTheme = null;
    try { savedTheme = localStorage.getItem('portsys-theme'); } catch (error) {}
    root.dataset.theme = savedTheme || (window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    const syncThemeIcon = () => {
        const isDark = root.dataset.theme === 'dark';
        if (themeMeta) themeMeta.setAttribute('content', isDark ? '#081423' : '#063879');
        if (!themeButton) return;
        themeButton.innerHTML = isDark ? '<i class="fa-regular fa-sun"></i>' : '<i class="fa-regular fa-moon"></i>';
        themeButton.setAttribute('aria-pressed', isDark ? 'true' : 'false');
    };
    syncThemeIcon();
    themeButton?.addEventListener('click', () => {
        root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        try { localStorage.setItem('portsys-theme', root.dataset.theme); } catch (error) {}
        syncThemeIcon();
    });

    const fullscreenButton = document.getElementById('fullscreenToggle');
    const syncFullscreenButton = () => {
        if (!fullscreenButton) return;
        const active = Boolean(document.fullscreenElement);
        const label = active
            ? <?= json_encode(lang('App.dashboard_exit_fullscreen'), JSON_UNESCAPED_UNICODE) ?>
            : <?= json_encode(lang('App.dashboard_enter_fullscreen'), JSON_UNESCAPED_UNICODE) ?>;
        fullscreenButton.innerHTML = active ? '<i class="fa-solid fa-compress"></i>' : '<i class="fa-solid fa-expand"></i>';
        fullscreenButton.title = label;
        fullscreenButton.setAttribute('aria-label', label);
    };
    fullscreenButton?.addEventListener('click', async () => {
        try {
            if (!document.fullscreenElement) await document.documentElement.requestFullscreen();
            else await document.exitFullscreen();
        } catch (error) {}
    });
    document.addEventListener('fullscreenchange', syncFullscreenButton);
    syncFullscreenButton();

    const input = document.getElementById('globalSearch');
    const results = document.getElementById('globalSearchResults');
    let timer;
    const renderMessage = (message) => { results.innerHTML = `<div class="p-3 small muted text-center">${message}</div>`; results.classList.add('show'); };
    input?.addEventListener('input', () => {
        clearTimeout(timer); const q = input.value.trim();
        if (q.length < 2) { results.classList.remove('show'); results.innerHTML = ''; return; }
        renderMessage(<?= json_encode(lang('App.search_loading'), JSON_UNESCAPED_UNICODE) ?>);
        timer = setTimeout(async () => {
            try {
                const response = await fetch(<?= json_encode(site_url('search'), JSON_UNESCAPED_SLASHES) ?> + '?q=' + encodeURIComponent(q), {headers:{'X-Requested-With':'XMLHttpRequest'}});
                if (!response.ok) throw new Error('search-unavailable');
                const data = await response.json();
                if (!data.items?.length) { renderMessage(<?= json_encode(lang('App.search_no_results'), JSON_UNESCAPED_UNICODE) ?>); return; }
                results.innerHTML = data.items.map(item => `<a class="search-result" href="${escapeHtml(item.url)}"><span class="result-icon"><i class="fa-solid ${escapeHtml(item.icon)}"></i></span><span><strong>${escapeHtml(item.label)}</strong><small>${escapeHtml(item.meta || '')}</small></span></a>`).join('');
                results.classList.add('show');
            } catch (e) { renderMessage(<?= json_encode(lang('App.search_no_results'), JSON_UNESCAPED_UNICODE) ?>); }
        }, 260);
    });
    document.addEventListener('click', e => { if (!document.getElementById('globalSearchBox')?.contains(e.target)) results?.classList.remove('show'); });
    document.addEventListener('keydown', e => { if (e.key === '/' && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) { e.preventDefault(); input?.focus(); } if (e.key === 'Escape') results?.classList.remove('show'); });
    function escapeHtml(value){ const div=document.createElement('div'); div.textContent=value ?? ''; return div.innerHTML; }

    const notificationToggle = document.getElementById('notificationsToggle');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationUnreadText = document.getElementById('notificationUnreadText');
    const notificationsList = document.getElementById('notificationsList');
    const markAllForm = document.getElementById('markAllNotificationsForm');
    const emptyNotifications = <?= json_encode(
        '<div class="text-center p-4"><div class="notification-icon mx-auto mb-2"><i class="fa-regular fa-bell-slash"></i></div><div class="small fw-bold">' . esc(lang('App.no_notifications')) . '</div><div class="small muted mt-1">' . esc(lang('App.no_notifications_desc')) . '</div></div>',
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ) ?>;
    let notificationRefreshRunning = false;

    const renderNotifications = (payload) => {
        const unread = Number(payload.unread || 0);
        if (notificationUnreadText) notificationUnreadText.textContent = String(unread);
        if (notificationBadge) {
            notificationBadge.textContent = unread > 99 ? '99+' : String(unread);
            notificationBadge.classList.toggle('d-none', unread < 1);
        }
        markAllForm?.classList.toggle('d-none', unread < 1);
        if (!notificationsList) return;
        const items = Array.isArray(payload.items) ? payload.items : [];
        if (!items.length) { notificationsList.innerHTML = emptyNotifications; return; }
        notificationsList.innerHTML = items.map(item => `
            <a class="notification-item ${Number(item.is_read) === 0 ? 'unread' : ''}" href="${escapeHtml(item.url)}">
                <span class="notification-icon"><i class="fa-solid ${escapeHtml(item.icon)}"></i></span>
                <span class="notification-copy"><strong>${escapeHtml(item.title)}</strong><p>${escapeHtml(item.message)}</p><time>${escapeHtml(item.time)}</time></span>
            </a>`).join('');
    };

    const refreshNotifications = async () => {
        if (notificationRefreshRunning || !notificationToggle) return;
        notificationRefreshRunning = true;
        try {
            const response = await fetch(<?= json_encode(site_url('notifications/feed'), JSON_UNESCAPED_SLASHES) ?>, {
                headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
                cache: 'no-store',
            });
            if (!response.ok) throw new Error('notification-feed');
            renderNotifications(await response.json());
        } catch (error) {
            const message = <?= json_encode(lang('App.notification_connection_error'), JSON_UNESCAPED_UNICODE) ?>;
            console.warn(message);
            if (notificationsList && notificationsList.children.length === 0) {
                notificationsList.innerHTML = `<div class="p-4 text-center"><div class="notification-icon mx-auto mb-2"><i class="fa-solid fa-triangle-exclamation"></i></div><div class="small muted">${escapeHtml(message)}</div></div>`;
            }
        } finally {
            notificationRefreshRunning = false;
        }
    };

    const notificationDropdown = notificationToggle?.closest('.dropdown');
    notificationDropdown?.addEventListener('show.bs.dropdown', refreshNotifications);
    notificationToggle?.addEventListener('click', () => window.setTimeout(refreshNotifications, 0));
    window.setInterval(refreshNotifications, 60000);
})();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
