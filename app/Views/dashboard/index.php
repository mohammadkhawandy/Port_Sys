<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= lang('App.dashboard') ?><?= $this->endSection() ?>
<?= $this->section('head') ?>
<style>
    .native-bar-chart{height:300px;display:flex;align-items:stretch;gap:12px;padding:18px 8px 0;border-bottom:1px solid var(--border);position:relative}
    .native-bar-chart:before,.native-bar-chart:after{content:"";position:absolute;inset-inline:0;border-top:1px dashed var(--border);pointer-events:none}.native-bar-chart:before{top:33%}.native-bar-chart:after{top:66%}
    .native-bar-item{flex:1;min-width:54px;display:grid;grid-template-rows:26px 1fr 40px;align-items:end;text-align:center;position:relative;z-index:1}
    .native-bar-value{font-size:.72rem;font-weight:800;color:var(--text);opacity:0;transform:translateY(5px);transition:.2s}.native-bar-item:hover .native-bar-value{opacity:1;transform:none}
    .native-bar-track{height:100%;display:flex;align-items:end;justify-content:center}.native-bar-fill{width:min(46px,70%);min-height:5px;border-radius:10px 10px 4px 4px;background:linear-gradient(180deg,#2d8bea,var(--primary));box-shadow:0 8px 22px rgba(13,99,199,.2);transition:height .45s ease,transform .2s}.native-bar-item:hover .native-bar-fill{transform:translateY(-3px)}
    .native-bar-label{font-size:.68rem;color:var(--muted);line-height:1.25;align-self:center}
    .native-doughnut{width:190px;aspect-ratio:1;border-radius:50%;display:grid;place-items:center;margin:10px auto 20px;position:relative;box-shadow:inset 0 0 0 1px var(--border)}
    .native-doughnut:after{content:"";width:66%;aspect-ratio:1;background:var(--surface);border-radius:50%;box-shadow:0 0 0 1px var(--border);position:absolute}
    .native-doughnut-center{position:relative;z-index:1;text-align:center}.native-doughnut-center strong{font-size:1.55rem;display:block}.native-doughnut-center small{color:var(--muted)}
    .chart-legend-item{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.48rem .1rem;font-size:.78rem}.chart-legend-dot{width:9px;height:9px;border-radius:50%;display:inline-block;margin-inline-end:.45rem}
    @media(max-width:767px){.native-bar-chart{overflow-x:auto;justify-content:flex-start}.native-bar-item{flex:0 0 72px}}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$isAdminDashboard = (bool) ($isAdminDashboard ?? false);
$metrics = $metrics ?? [];
$userDashboard = $userDashboard ?? null;
$upcomingDepartures = $upcomingDepartures ?? [];
$topRoutes = $topRoutes ?? [];
$latestRequests = $latestRequests ?? [];
$recentActivity = $recentActivity ?? [];
$chartTripsByMonth = $chartTripsByMonth ?? ['labels' => [], 'values' => []];
$chartTripsByStatus = $chartTripsByStatus ?? ['labels' => [], 'values' => []];
$insights = $insights ?? [];
$name = current_user_display_name();
?>
<section class="dashboard-hero mb-4" aria-label="<?= esc(lang('App.dashboard_aria_hero')) ?>">
    <div class="row align-items-center w-100 g-4">
        <div class="col-lg-8">
            <div class="section-title text-white-50"><?= esc(lang('App.dashboard_hero_label')) ?></div>
            <h1 class="display-6 fw-bold mb-2"><?= esc(sprintf(lang('App.dashboard_welcome'), $name)) ?></h1>
            <p class="mb-0 text-white-50" style="max-width:720px"><?= esc($isAdminDashboard ? lang('App.dashboard_admin_copy') : lang('App.dashboard_user_copy')) ?></p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge rounded-pill dashboard-status-pill"><i class="fa-solid fa-circle-check"></i><?= esc(lang('App.dashboard_online')) ?></span>
        </div>
    </div>
</section>

<?php if ($isAdminDashboard): ?>
    <?php
    $kpis = [
        [lang('App.dashboard_kpi_total_vessels'), (int) ($metrics['totalShips'] ?? 0), 'fa-ship', site_url('ships')],
        [lang('App.dashboard_kpi_total_ports'), (int) ($metrics['totalPorts'] ?? 0), 'fa-anchor', site_url('ports')],
        [lang('App.dashboard_total_trips'), (int) ($metrics['totalTrips'] ?? 0), 'fa-route', site_url('trips')],
        [lang('App.dashboard_active_trips'), (int) ($metrics['activeTrips'] ?? 0), 'fa-water', site_url('trips?status=active')],
        [lang('App.dashboard_pending_requests'), (int) ($metrics['pendingRequests'] ?? 0), 'fa-clipboard-list', site_url('trip-requests?status=pending')],
        [lang('App.dashboard_unread_messages'), (int) ($metrics['unreadMessages'] ?? 0), 'fa-envelope', site_url('messages?status=unread')],
    ];
    ?>
    <div class="row g-3 mb-4" aria-label="<?= esc(lang('App.dashboard_aria_kpi_metrics')) ?>">
        <?php foreach ($kpis as [$label, $value, $icon, $url]): ?>
            <div class="col-sm-6 col-xl-4 col-xxl-2">
                <a href="<?= esc($url) ?>" class="kpi-card text-decoration-none">
                    <span class="kpi-icon"><i class="fa-solid <?= esc($icon) ?>"></i></span>
                    <span><span class="muted small d-block"><?= esc($label) ?></span><span class="kpi-value"><?= number_format((int) $value) ?></span></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="panel-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <div><div class="section-title"><?= esc(lang('App.dashboard_operational_overview')) ?></div><h2 class="panel-title h5 mb-0"><?= esc(lang('App.dashboard_monthly_trips')) ?></h2></div>
                    <span class="badge badge-soft rounded-pill px-3 py-2"><?= esc(lang('App.dashboard_last_6_months')) ?></span>
                </div>
                <?php
                $monthLabels = (array) ($chartTripsByMonth['labels'] ?? []);
                $monthValues = array_map('intval', (array) ($chartTripsByMonth['values'] ?? []));
                $monthMax = max(1, ...($monthValues !== [] ? $monthValues : [1]));
                ?>
                <?php if ($monthValues === []): ?>
                    <div class="d-flex align-items-center justify-content-center text-center muted" style="height:300px"><div><i class="fa-solid fa-chart-line fa-2x mb-3 d-block"></i><?= esc(lang('App.dashboard_chart_no_data')) ?></div></div>
                <?php else: ?>
                    <div class="native-bar-chart" role="img" aria-label="<?= esc(lang('App.dashboard_monthly_trips')) ?>">
                        <?php foreach ($monthValues as $index => $value): ?>
                            <?php $height = max(3, (int) round(($value / $monthMax) * 100)); ?>
                            <div class="native-bar-item" title="<?= esc(($monthLabels[$index] ?? '') . ': ' . $value) ?>">
                                <span class="native-bar-value"><?= number_format($value) ?></span>
                                <span class="native-bar-track"><span class="native-bar-fill" style="height:<?= $height ?>%"></span></span>
                                <span class="native-bar-label"><?= esc($monthLabels[$index] ?? '') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel-card p-4 h-100">
                <div class="section-title"><?= esc(lang('App.dashboard_operational_overview')) ?></div>
                <h2 class="panel-title h5 mb-3"><?= esc(lang('App.dashboard_status_distribution')) ?></h2>
                <?php
                $statusLabels = (array) ($chartTripsByStatus['labels'] ?? []);
                $statusValues = array_map('intval', (array) ($chartTripsByStatus['values'] ?? []));
                $statusTotal = array_sum($statusValues);
                $upcomingPercent = $statusTotal > 0 ? round((($statusValues[0] ?? 0) / $statusTotal) * 100, 2) : 0;
                $activePercent = $statusTotal > 0 ? round((($statusValues[1] ?? 0) / $statusTotal) * 100, 2) : 0;
                $completedPercent = max(0, 100 - $upcomingPercent - $activePercent);
                $firstStop = $upcomingPercent;
                $secondStop = $upcomingPercent + $activePercent;
                ?>
                <div class="native-doughnut" style="background:conic-gradient(#3b82f6 0 <?= $firstStop ?>%,#f59e0b <?= $firstStop ?>% <?= $secondStop ?>%,#22c55e <?= $secondStop ?>% 100%)">
                    <div class="native-doughnut-center"><strong><?= number_format($statusTotal) ?></strong><small><?= esc(lang('App.nav_trips')) ?></small></div>
                </div>
                <div class="mb-3">
                    <?php foreach ($statusLabels as $index => $label): $legendColor = ['#3b82f6', '#f59e0b', '#22c55e'][$index] ?? '#94a3b8'; ?>
                        <div class="chart-legend-item"><span><span class="chart-legend-dot" style="background:<?= esc($legendColor) ?>"></span><?= esc($label) ?></span><strong><?= number_format((int) ($statusValues[$index] ?? 0)) ?></strong></div>
                    <?php endforeach; ?>
                </div>
                <div class="row g-2 mt-3 text-center">
                    <div class="col-4"><div class="p-2 rounded-3" style="background:var(--surface-2)"><strong class="d-block"><?= esc((string) ($insights['fleetUtilization'] ?? 0)) ?>%</strong><small class="muted"><?= esc(lang('App.dashboard_utilization_rate')) ?></small></div></div>
                    <div class="col-4"><div class="p-2 rounded-3" style="background:var(--surface-2)"><strong class="d-block"><?= esc((string) ($insights['completionRate'] ?? 0)) ?>%</strong><small class="muted"><?= esc(lang('App.dashboard_completion_rate')) ?></small></div></div>
                    <div class="col-4"><div class="p-2 rounded-3" style="background:var(--surface-2)"><strong class="d-block"><?= esc((string) ($insights['avgDurationHours'] ?? 0)) ?></strong><small class="muted"><?= esc(lang('App.hours_short')) ?></small></div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="table-shell p-3 p-lg-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div><div class="section-title"><?= esc(lang('App.dashboard_forecast')) ?></div><h2 class="panel-title h5 mb-0"><?= esc(lang('App.dashboard_upcoming_departures')) ?></h2></div>
                    <a href="<?= site_url('trips') ?>" class="btn btn-sm btn-outline-light"><?= esc(lang('App.view_all')) ?></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th><?= esc(lang('App.ship')) ?></th><th><?= esc(lang('App.route')) ?></th><th><?= esc(lang('App.departure_date')) ?></th><th></th></tr></thead>
                        <tbody>
                        <?php if ($upcomingDepartures === []): ?><tr><td colspan="4" class="text-center muted py-5"><?= esc(lang('App.no_upcoming_departures')) ?></td></tr>
                        <?php else: foreach ($upcomingDepartures as $departure): ?>
                            <tr>
                                <td><div class="fw-bold"><?= esc($departure['ship_name'] ?? '') ?></div><small class="muted">#<?= (int) ($departure['id'] ?? 0) ?></small></td>
                                <td><span><?= esc($departure['departure_port_name'] ?? '') ?></span><i class="fa-solid fa-arrow-<?= service('request')->getLocale() === 'ar' ? 'left' : 'right' ?> mx-2 text-primary"></i><span><?= esc($departure['arrival_port_name'] ?? '') ?></span></td>
                                <td><?= esc(format_datetime($departure['departure_date'] ?? null)) ?></td>
                                <td><a class="btn btn-sm btn-outline-light" href="<?= site_url('trips/show/' . (int) $departure['id']) ?>" aria-label="<?= esc(lang('App.view')) ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel-card p-4 h-100">
                <div class="section-title"><?= esc(lang('App.dashboard_operational_overview')) ?></div>
                <h2 class="panel-title h5 mb-3"><?= esc(lang('App.dashboard_top_routes')) ?></h2>
                <?php if ($topRoutes === []): ?><div class="text-center muted py-5"><?= esc(lang('App.no_routes_yet')) ?></div>
                <?php else: foreach ($topRoutes as $index => $route): ?>
                    <div class="d-flex align-items-center gap-3 py-3 <?= $index + 1 < count($topRoutes) ? 'border-bottom' : '' ?>" style="border-color:var(--border)!important">
                        <span class="kpi-icon" style="width:38px;height:38px;font-size:.85rem"><?= $index + 1 ?></span>
                        <div class="flex-grow-1 min-w-0"><div class="small fw-bold text-truncate"><?= esc(($route['departure_port_name'] ?? '') . ' → ' . ($route['arrival_port_name'] ?? '')) ?></div><small class="muted"><?= (int) ($route['total_trips'] ?? 0) ?> <?= esc(lang('App.nav_trips')) ?></small></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="table-shell p-3 p-lg-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3"><h2 class="panel-title h5 mb-0"><?= esc(lang('App.my_recent_requests')) ?></h2><a href="<?= site_url('trip-requests') ?>" class="btn btn-sm btn-outline-light"><?= esc(lang('App.view_all')) ?></a></div>
                <div class="table-responsive"><table class="table table-hover"><thead><tr><th><?= esc(lang('App.user')) ?></th><th><?= esc(lang('App.ship')) ?></th><th><?= esc(lang('App.status')) ?></th><th><?= esc(lang('App.created_at')) ?></th></tr></thead><tbody>
                <?php if ($latestRequests === []): ?><tr><td colspan="4" class="text-center muted py-4"><?= esc(lang('App.no_requests_yet')) ?></td></tr>
                <?php else: foreach ($latestRequests as $request): ?><tr><td><div class="fw-bold small"><?= esc(trim((string) ($request['user_name'] ?? '')) ?: ($request['user_email'] ?? lang('App.unknown'))) ?></div><small class="muted"><?= esc($request['user_email'] ?? '') ?></small></td><td><?= esc($request['ship_name'] ?? '') ?></td><td><span class="badge rounded-pill text-bg-<?= esc(request_status_class($request['status'] ?? null)) ?>"><?= esc(request_status_label($request['status'] ?? null)) ?></span></td><td><?= esc(human_time($request['created_at'] ?? null)) ?></td></tr><?php endforeach; endif; ?>
                </tbody></table></div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="panel-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3"><h2 class="panel-title h5 mb-0"><?= esc(lang('App.dashboard_recent_activity')) ?></h2><a href="<?= site_url('activity-log') ?>" class="small fw-bold text-decoration-none"><?= esc(lang('App.view_all')) ?></a></div>
                <?php if ($recentActivity === []): ?><div class="text-center muted py-5"><?= esc(lang('App.dashboard_no_activity')) ?></div>
                <?php else: foreach ($recentActivity as $activity): ?>
                    <div class="d-flex gap-3 py-2">
                        <span class="notification-icon" style="width:36px;height:36px"><i class="fa-solid <?= esc(activity_action_icon($activity['action'] ?? null)) ?>"></i></span>
                        <div class="flex-grow-1"><div class="small fw-bold"><?= esc(activity_action_label($activity['action'] ?? null)) ?></div><small class="muted d-block"><?= esc($activity['description'] ?? '') ?></small><small class="muted"><?= esc(trim((string) ($activity['user_name'] ?? '')) ?: trim((string) ($activity['user_email'] ?? '')) ?: lang('App.system_user')) ?> · <?= esc(human_time($activity['created_at'] ?? null)) ?></small></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php
    $availableTrips = $userDashboard['availableTrips'] ?? [];
    $myRequests = $userDashboard['myRequests'] ?? [];
    $filters = $userDashboard['filters'] ?? ['ship_name' => '', 'port_name' => '', 'date' => '', 'status' => ''];
    $userKpis = [
        [lang('App.dashboard_total_trips'), (int) ($metrics['totalTrips'] ?? 0), 'fa-route'],
        [lang('App.dashboard_active_trips'), (int) ($metrics['activeTrips'] ?? 0), 'fa-water'],
        [lang('App.dashboard_upcoming_trips'), (int) ($metrics['upcomingTrips'] ?? 0), 'fa-calendar-days'],
        [lang('App.dashboard_pending_requests'), (int) ($metrics['pendingRequests'] ?? 0), 'fa-hourglass-half'],
        [lang('App.dashboard_approved_requests'), (int) ($metrics['approvedRequests'] ?? 0), 'fa-circle-check'],
    ];
    ?>
    <div class="row g-3 mb-4"><?php foreach ($userKpis as [$label, $value, $icon]): ?><div class="col-sm-6 col-xl"><div class="kpi-card"><span class="kpi-icon"><i class="fa-solid <?= esc($icon) ?>"></i></span><span><span class="muted small d-block"><?= esc($label) ?></span><span class="kpi-value"><?= number_format($value) ?></span></span></div></div><?php endforeach; ?></div>

    <div class="panel-card p-3 p-lg-4 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3"><div><div class="section-title"><?= esc(lang('App.dashboard_forecast')) ?></div><h2 class="panel-title h5 mb-0"><?= esc(lang('App.dashboard_available_trips')) ?></h2></div><a class="btn btn-sm btn-primary" href="<?= site_url('trips') ?>"><?= esc(lang('App.view_all')) ?></a></div>
        <form method="get" class="row g-3 align-items-end mb-4">
            <div class="col-md-3"><label class="form-label"><?= esc(lang('App.search_ship_name')) ?></label><input class="form-control" name="ship_name" value="<?= esc($filters['ship_name']) ?>"></div>
            <div class="col-md-3"><label class="form-label"><?= esc(lang('App.search_port_name')) ?></label><input class="form-control" name="port_name" value="<?= esc($filters['port_name']) ?>"></div>
            <div class="col-md-2"><label class="form-label"><?= esc(lang('App.date')) ?></label><input type="date" class="form-control" name="date" value="<?= esc($filters['date']) ?>"></div>
            <div class="col-md-2"><label class="form-label"><?= esc(lang('App.status')) ?></label><select class="form-select" name="status"><option value="upcoming"><?= esc(lang('App.trip_status_upcoming')) ?></option><option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>><?= esc(lang('App.trip_status_active')) ?></option><option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>><?= esc(lang('App.trip_status_completed')) ?></option></select></div>
            <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><?= esc(lang('App.search')) ?></button><a class="btn btn-outline-light" href="<?= site_url('dashboard') ?>" aria-label="<?= esc(lang('App.reset')) ?>"><i class="fa-solid fa-rotate-left"></i></a></div>
        </form>
        <div class="table-responsive"><table class="table table-hover"><thead><tr><th><?= esc(lang('App.ship')) ?></th><th><?= esc(lang('App.route')) ?></th><th><?= esc(lang('App.departure_date')) ?></th><th><?= esc(lang('App.status')) ?></th><th></th></tr></thead><tbody>
        <?php if ($availableTrips === []): ?><tr><td colspan="5" class="text-center muted py-5"><?= esc(lang('App.no_results')) ?></td></tr>
        <?php else: foreach ($availableTrips as $trip): ?><tr><td><strong><?= esc($trip['ship_name']) ?></strong><small class="d-block muted"><?= esc($trip['ship_type']) ?></small></td><td><?= esc($trip['departure_port_name']) ?> → <?= esc($trip['arrival_port_name']) ?></td><td><?= esc(format_datetime($trip['departure_date'])) ?></td><td><span class="badge rounded-pill text-bg-<?= esc($trip['status_class']) ?>"><?= esc($trip['status_label']) ?></span></td><td><a class="btn btn-sm btn-outline-light" href="<?= site_url('trips/show/' . (int) $trip['id']) ?>"><?= esc(lang('App.view')) ?></a></td></tr><?php endforeach; endif; ?>
        </tbody></table></div>
    </div>

    <div class="table-shell p-3 p-lg-4">
        <div class="d-flex align-items-center justify-content-between mb-3"><h2 class="panel-title h5 mb-0"><?= esc(lang('App.dashboard_my_requests')) ?></h2><a href="<?= site_url('trip-requests/my') ?>" class="btn btn-sm btn-outline-light"><?= esc(lang('App.view_all')) ?></a></div>
        <div class="table-responsive"><table class="table table-hover"><thead><tr><th><?= esc(lang('App.ship')) ?></th><th><?= esc(lang('App.route')) ?></th><th><?= esc(lang('App.request_type')) ?></th><th><?= esc(lang('App.status')) ?></th><th><?= esc(lang('App.created_at')) ?></th></tr></thead><tbody>
        <?php if ($myRequests === []): ?><tr><td colspan="5" class="text-center muted py-5"><?= esc(lang('App.no_requests_yet')) ?></td></tr>
        <?php else: foreach ($myRequests as $request): ?><tr><td><?= esc($request['ship_name'] ?? '') ?></td><td><?= esc($request['departure_port_name']) ?> → <?= esc($request['arrival_port_name']) ?></td><td><?= esc(request_type_label($request['request_type'])) ?></td><td><span class="badge rounded-pill text-bg-<?= esc(request_status_class($request['status'] ?? null)) ?>"><?= esc(request_status_label($request['status'] ?? null)) ?></span></td><td><?= esc(human_time($request['created_at'] ?? null)) ?></td></tr><?php endforeach; endif; ?>
        </tbody></table></div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
