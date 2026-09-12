<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.trip_details')) ?><?= $this->endSection() ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .request-choice-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
    .request-choice{position:relative;height:100%}
    .request-choice input{position:absolute;opacity:0;pointer-events:none}
    .request-choice-card{height:100%;border:1px solid var(--border);background:var(--surface);border-radius:18px;padding:1.1rem;cursor:pointer;transition:.2s;display:flex;gap:.85rem;align-items:flex-start}
    .request-choice-card:hover{border-color:var(--primary);background:var(--surface-2);transform:translateY(-2px)}
    .request-choice input:checked + .request-choice-card{border-color:var(--primary);background:color-mix(in srgb,var(--primary-soft) 72%,var(--surface));box-shadow:0 0 0 4px rgba(13,99,199,.10)}
    .request-choice-icon{width:44px;height:44px;border-radius:14px;background:var(--primary-soft);color:var(--primary);display:grid;place-items:center;flex:0 0 auto}
    .request-choice-title{font-weight:900;color:var(--text);display:block;margin-bottom:.25rem}
    .request-choice-desc{font-size:.8rem;line-height:1.65;color:var(--muted);margin:0}
    .request-note{border:1px dashed color-mix(in srgb,var(--primary) 30%,var(--border));background:color-mix(in srgb,var(--primary-soft) 50%,var(--surface));border-radius:16px;padding:1rem}
    .contact-mini-card{background:var(--surface-2);border:1px solid var(--border);border-radius:16px;padding:1rem}
    @media(max-width:767px){.request-choice-grid{grid-template-columns:1fr}}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$trip = $trip ?? [];
$existingRequest = $existingRequest ?? null;
$overlappingApprovedTrip = $overlappingApprovedTrip ?? null;
$hasOverlappingApprovedTrip = is_array($overlappingApprovedTrip) && $overlappingApprovedTrip !== [];
$capacityStatus = $capacityStatus ?? ['capacity' => (int) ($trip['ship_capacity'] ?? 0), 'approved' => 0, 'remaining' => (int) ($trip['ship_capacity'] ?? 0), 'is_full' => false];
$tripCapacity = max(0, (int) ($capacityStatus['capacity'] ?? 0));
$approvedPeopleCount = max(0, (int) ($capacityStatus['approved'] ?? 0));
$remainingCapacity = max(0, (int) ($capacityStatus['remaining'] ?? 0));
$isTripFull = (bool) ($capacityStatus['is_full'] ?? false);
$capacityPercent = $tripCapacity > 0 ? min(100, (int) round(($approvedPeopleCount / $tripCapacity) * 100)) : 0;
$departureTs = strtotime((string) ($trip['departure_date'] ?? '')) ?: 0;
$canRequest = $departureTs > time() && ! $isTripFull && ! $hasOverlappingApprovedTrip;
$defaultPeopleCount = max(1, (int) old('people_count', 1));
$depLat = filter_var($trip['dep_lat'] ?? null, FILTER_VALIDATE_FLOAT);
$depLng = filter_var($trip['dep_lng'] ?? null, FILTER_VALIDATE_FLOAT);
$arrLat = filter_var($trip['arr_lat'] ?? null, FILTER_VALIDATE_FLOAT);
$arrLng = filter_var($trip['arr_lng'] ?? null, FILTER_VALIDATE_FLOAT);
$hasMap = $depLat !== false && $depLng !== false && $arrLat !== false && $arrLng !== false;
$defaultContactName = old('contact_name', current_user_display_name());
$defaultContactEmail = old('contact_email', session('userEmail'));
$selectedRequestType = old('request_type', 'booking');
?>
<div class="page-card p-4 p-lg-5 mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div>
        <div class="section-title"><?= esc(lang('App.nav_trips')) ?></div>
        <h1 class="page-title h2 mb-2"><?= esc(lang('App.trip_details')) ?> #<?= (int) ($trip['id'] ?? 0) ?></h1>
        <p class="muted mb-0"><?= esc(lang('App.trip_details_desc')) ?></p>
    </div>
    <span class="badge rounded-pill text-bg-<?= esc($trip['status_class'] ?? 'secondary') ?> fs-6 px-3 py-2"><?= esc($trip['status_label'] ?? '') ?></span>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="form-shell p-4 h-100">
            <h2 class="h5 mb-4"><?= esc(lang('App.trip_information')) ?></h2>
            <div class="vstack gap-3">
                <div class="d-flex justify-content-between gap-3 border-bottom pb-3"><span class="muted"><?= esc(lang('App.ship')) ?></span><strong><?= esc($trip['ship_name'] ?? '') ?></strong></div>
                <div class="d-flex justify-content-between gap-3 border-bottom pb-3"><span class="muted"><?= esc(lang('App.type')) ?></span><strong><?= esc($trip['ship_type'] ?? '—') ?></strong></div>
                <div class="border-bottom pb-3">
                    <div class="d-flex justify-content-between gap-3 mb-2"><span class="muted"><?= esc(lang('App.capacity')) ?></span><strong><?= number_format($approvedPeopleCount) ?> / <?= number_format($tripCapacity) ?></strong></div>
                    <div class="progress" style="height:9px"><div class="progress-bar <?= $isTripFull ? 'bg-danger' : '' ?>" role="progressbar" style="width:<?= $capacityPercent ?>%" aria-valuenow="<?= $capacityPercent ?>" aria-valuemin="0" aria-valuemax="100"></div></div>
                    <div class="d-flex justify-content-between small muted mt-2"><span><?= esc(lang('App.approved_people_count')) ?>: <?= number_format($approvedPeopleCount) ?></span><span><?= esc(lang('App.remaining_capacity')) ?>: <?= number_format($remainingCapacity) ?></span></div>
                    <?php if ($isTripFull): ?><span class="badge rounded-pill text-bg-danger mt-2"><?= esc(lang('App.trip_full_badge')) ?></span><?php endif; ?>
                </div>
                <div class="border-bottom pb-3"><div class="muted small mb-1"><?= esc(lang('App.departure_port')) ?></div><strong><?= esc($trip['departure_port_name'] ?? '') ?></strong><div class="small muted"><?= esc(implode('، ', array_filter([$trip['departure_port_city'] ?? '', $trip['departure_port_country'] ?? '']))) ?></div></div>
                <div class="border-bottom pb-3"><div class="muted small mb-1"><?= esc(lang('App.arrival_port')) ?></div><strong><?= esc($trip['arrival_port_name'] ?? '') ?></strong><div class="small muted"><?= esc(implode('، ', array_filter([$trip['arrival_port_city'] ?? '', $trip['arrival_port_country'] ?? '']))) ?></div></div>
                <div class="d-flex justify-content-between gap-3"><span class="muted"><?= esc(lang('App.departure_date')) ?></span><strong><?= esc(format_datetime($trip['departure_date'] ?? null)) ?></strong></div>
                <div class="d-flex justify-content-between gap-3"><span class="muted"><?= esc(lang('App.arrival_date')) ?></span><strong><?= esc(format_datetime($trip['arrival_date'] ?? null)) ?></strong></div>
                <div class="d-flex justify-content-between gap-3"><span class="muted"><?= esc(lang('App.duration_hours')) ?></span><strong><?= esc((string) ($trip['duration_hours'] ?? 0)) ?></strong></div>
            </div>

            <?php if (! is_admin_user()): ?>
                <hr class="my-4">
                <?php if ($existingRequest === null && $canRequest): ?>
                    <div class="mb-4">
                        <div class="section-title"><?= esc(lang('App.trip_request_action_title')) ?></div>
                        <h2 class="h5 mb-2"><?= esc(lang('App.trip_request_action_heading')) ?></h2>
                        <p class="muted mb-3"><?= esc(lang('App.trip_request_action_desc')) ?></p>
                        <div class="request-note small">
                            <strong class="d-block mb-1"><?= esc(lang('App.request_type_difference_title')) ?></strong>
                            <div class="mb-1"><strong><?= esc(lang('App.request_booking')) ?>:</strong> <?= esc(lang('App.request_booking_help')) ?></div>
                            <div><strong><?= esc(lang('App.request_participation')) ?>:</strong> <?= esc(lang('App.request_participation_help')) ?></div>
                        </div>
                    </div>

                    <form method="post" action="<?= site_url('trip-requests') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="trip_id" value="<?= (int) ($trip['id'] ?? 0) ?>">

                        <div class="mb-4">
                            <label class="form-label"><?= esc(lang('App.request_type')) ?></label>
                            <div class="request-choice-grid">
                                <label class="request-choice">
                                    <input type="radio" name="request_type" value="booking" <?= $selectedRequestType === 'booking' ? 'checked' : '' ?> required>
                                    <span class="request-choice-card">
                                        <span class="request-choice-icon"><i class="fa-solid fa-calendar-check"></i></span>
                                        <span><span class="request-choice-title"><?= esc(lang('App.request_booking')) ?></span><p class="request-choice-desc"><?= esc(lang('App.request_booking_desc')) ?></p></span>
                                    </span>
                                </label>
                                <label class="request-choice">
                                    <input type="radio" name="request_type" value="participation" <?= $selectedRequestType === 'participation' ? 'checked' : '' ?> required>
                                    <span class="request-choice-card">
                                        <span class="request-choice-icon"><i class="fa-solid fa-people-arrows"></i></span>
                                        <span><span class="request-choice-title"><?= esc(lang('App.request_participation')) ?></span><p class="request-choice-desc"><?= esc(lang('App.request_participation_desc')) ?></p></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="people-count"><?= esc(lang('App.people_count')) ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-users"></i></span>
                                <input id="people-count" name="people_count" type="number" min="1" <?= $tripCapacity > 0 ? 'max="' . (int) $remainingCapacity . '"' : '' ?> class="form-control" value="<?= esc((string) min($defaultPeopleCount, max(1, $remainingCapacity))) ?>" required>
                            </div>
                            <div class="form-text muted"><?= esc(sprintf(lang('App.people_count_help'), $remainingCapacity)) ?></div>
                        </div>

                        <div class="contact-mini-card mb-4">
                            <h3 class="h6 mb-2"><?= esc(lang('App.request_contact_title')) ?></h3>
                            <p class="small muted mb-3"><?= esc(lang('App.request_contact_desc')) ?></p>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label" for="contact-name"><?= esc(lang('App.request_contact_name')) ?></label><input id="contact-name" name="contact_name" class="form-control" value="<?= esc($defaultContactName) ?>" maxlength="150" required></div>
                                <div class="col-md-6"><label class="form-label" for="contact-phone"><?= esc(lang('App.request_contact_phone')) ?></label><input id="contact-phone" name="contact_phone" class="form-control" value="<?= esc(old('contact_phone')) ?>" dir="ltr" maxlength="50" required placeholder="+963 9xx xxx xxx"></div>
                                <div class="col-md-6"><label class="form-label" for="contact-email"><?= esc(lang('App.request_contact_email')) ?></label><input id="contact-email" name="contact_email" type="email" class="form-control" value="<?= esc($defaultContactEmail) ?>" maxlength="255" required></div>
                                <div class="col-md-6"><label class="form-label" for="organization"><?= esc(lang('App.organization_optional')) ?></label><input id="organization" name="organization" class="form-control" value="<?= esc(old('organization')) ?>" maxlength="150"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="request-message"><?= esc(lang('App.request_details')) ?></label>
                            <textarea id="request-message" name="message" class="form-control" rows="4" maxlength="1000" placeholder="<?= esc(lang('App.request_message_hint')) ?>"><?= esc(old('message')) ?></textarea>
                        </div>
                        <div class="alert alert-info small mb-3"><i class="fa-solid fa-circle-info me-2"></i><?= esc(lang('App.request_admin_followup_notice')) ?></div>
                        <button type="submit" class="btn btn-primary"><i class="fa-regular fa-paper-plane me-2"></i><?= esc(lang('App.submit_request')) ?></button>
                    </form>
                <?php elseif ($existingRequest === null && $hasOverlappingApprovedTrip): ?>
                    <div class="alert alert-warning mb-0">
                        <i class="fa-solid fa-calendar-xmark me-2"></i>
                        <?= esc(lang('App.msg_user_has_overlapping_approved_trip')) ?>
                        <div class="small mt-2"><?= esc(lang('App.overlapping_trip')) ?>:
                            <strong><?= esc($overlappingApprovedTrip['ship_name'] ?? '') ?></strong>
                            · <?= esc(format_datetime($overlappingApprovedTrip['departure_date'] ?? null)) ?>
                            → <?= esc(format_datetime($overlappingApprovedTrip['arrival_date'] ?? null)) ?>
                        </div>
                    </div>
                <?php elseif ($existingRequest === null && $isTripFull): ?>
                    <div class="alert alert-warning mb-0"><i class="fa-solid fa-circle-exclamation me-2"></i><?= esc(lang('App.trip_full_public_message')) ?></div>
                <?php elseif ($existingRequest === null): ?>
                    <div class="alert alert-secondary mb-0"><?= esc(lang('App.requests_closed')) ?></div>
                <?php else: $status = $existingRequest['status'] ?? 'pending'; ?>
                    <div class="rounded-4 border p-3">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-3"><strong><?= esc(lang('App.request_status')) ?></strong><span class="badge rounded-pill text-bg-<?= esc(request_status_class($status)) ?>"><?= esc(request_status_label($status)) ?></span></div>
                        <div class="small muted mb-2"><?= esc(lang('App.request_type')) ?>: <strong><?= esc(request_type_label($existingRequest['request_type'] ?? null)) ?></strong></div>
                        <div class="small muted mb-2"><?= esc(lang('App.people_count')) ?>: <strong><?= number_format(max(1, (int) ($existingRequest['people_count'] ?? 1))) ?></strong></div>
                        <div class="small muted mb-2"><?= esc(lang('App.request_contact_information')) ?>: <?= esc(trim(($existingRequest['contact_name'] ?? '') . ' · ' . ($existingRequest['contact_phone'] ?? '') . ' · ' . ($existingRequest['contact_email'] ?? ''), ' ·')) ?></div>
                        <?php if (! empty($existingRequest['message'])): ?><p class="small muted mt-3 mb-2"><?= nl2br(esc($existingRequest['message'])) ?></p><?php endif; ?>
                        <div class="small muted"><?= esc(lang('App.request_submitted_on')) ?>: <?= esc(format_datetime($existingRequest['created_at'] ?? null)) ?></div>
                        <?php if ($status === 'pending'): ?><form method="post" action="<?= site_url('trip-requests/' . (int) $existingRequest['id'] . '/cancel') ?>" class="mt-3" onsubmit="return confirm(<?= json_encode(lang('App.confirm_cancel_request'), JSON_UNESCAPED_UNICODE) ?>)"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit"><?= esc(lang('App.cancel_request')) ?></button></form><?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <hr class="my-4">
                <a href="<?= site_url('trips/' . (int) ($trip['id'] ?? 0) . '/accepted') ?>" class="btn btn-outline-light"><i class="fa-solid fa-users me-2"></i><?= esc(lang('App.view_accepted')) ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-shell p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                <h2 class="h5 mb-0"><?= esc(lang('App.route_map')) ?></h2>
                <?php if ($hasMap): ?><code dir="ltr"><?= esc((string) $depLat) ?>, <?= esc((string) $depLng) ?> → <?= esc((string) $arrLat) ?>, <?= esc((string) $arrLng) ?></code><?php endif; ?>
            </div>
            <?php if ($hasMap): ?>
                <div id="map" style="height:460px;border-radius:18px;overflow:hidden"></div>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center text-center p-5 rounded-4 border h-75"><div><i class="fa-solid fa-map-location-dot fa-3x muted mb-3"></i><p class="muted mb-0"><?= esc(lang('App.map_coordinates_unavailable')) ?></p></div></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<a href="<?= site_url('trips') ?>" class="btn btn-outline-light mt-4"><i class="fa-solid fa-arrow-left me-2"></i><?= esc(lang('App.back_to_trips')) ?></a>
<?= $this->endSection() ?>
<?php if ($hasMap): ?>
<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;
    if (typeof window.L === 'undefined') {
        mapElement.innerHTML = `<div class="h-100 d-flex align-items-center justify-content-center text-center p-4"><div><i class="fa-solid fa-map-location-dot fa-3x muted mb-3"></i><p class="muted mb-0"><?= esc(lang('App.map_service_unavailable')) ?></p></div></div>`;
        return;
    }
    const dep = [<?= json_encode((float) $depLat) ?>, <?= json_encode((float) $depLng) ?>];
    const arr = [<?= json_encode((float) $arrLat) ?>, <?= json_encode((float) $arrLng) ?>];
    const map = L.map('map', {scrollWheelZoom: false});
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '© OpenStreetMap'}).addTo(map);
    L.marker(dep).addTo(map).bindPopup(<?= json_encode(lang('App.departure_port') . ': ' . ($trip['departure_port_name'] ?? ''), JSON_UNESCAPED_UNICODE) ?>);
    L.marker(arr).addTo(map).bindPopup(<?= json_encode(lang('App.arrival_port') . ': ' . ($trip['arrival_port_name'] ?? ''), JSON_UNESCAPED_UNICODE) ?>);
    L.polyline([dep, arr], {weight: 4, opacity: .8}).addTo(map);
    map.fitBounds([dep, arr], {padding: [35, 35]});
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>
