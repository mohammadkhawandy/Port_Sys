<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.add_trip_title')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$ships = $ships ?? [];
$ports = $ports ?? [];
$shipSchedules = $shipSchedules ?? [];
?>
<div class="page-card p-4 p-lg-5">
    <div class="mb-4">
        <div class="section-title"><?= esc(lang('App.nav_trips')) ?></div>
        <h1 class="page-title h2 mb-2"><?= esc(lang('App.add_trip_title')) ?></h1>
        <p class="muted mb-0"><?= esc(lang('App.add_trip_desc')) ?></p>
    </div>

    <div class="alert alert-info d-flex gap-3 align-items-start rounded-4 border-0 mb-4" id="ship-availability-box" role="status">
        <i class="fa-solid fa-ship mt-1"></i>
        <div>
            <strong class="d-block mb-1"><?= esc(lang('App.ship_availability_title')) ?></strong>
            <span id="ship-availability-text"><?= esc(lang('App.update_dates_to_check_availability')) ?></span>
        </div>
    </div>

    <form action="<?= site_url('trips/create') ?>" method="post" id="trip-form" autocomplete="off">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="ship_id" class="form-label"><?= esc(lang('App.ship')) ?></label>
                <select class="form-select" id="ship_id" name="ship_id" required>
                    <option value=""><?= esc(lang('App.select_ship')) ?></option>
                    <?php foreach ($ships as $ship): ?>
                        <?php $shipLabel = trim(($ship['name'] ?? '') . ' — ' . ($ship['type'] ?? '') . ' (' . number_format((int) ($ship['capacity'] ?? 0)) . ')'); ?>
                        <option value="<?= (int) ($ship['id'] ?? 0) ?>" data-label="<?= esc($shipLabel) ?>" <?= (int) old('ship_id', 0) === (int) ($ship['id'] ?? 0) ? 'selected' : '' ?>>
                            <?= esc($shipLabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text muted"><?= esc(lang('App.ship_availability_desc')) ?></div>
            </div>

            <div class="col-md-6">
                <label for="departure_port_id" class="form-label"><?= esc(lang('App.departure_port')) ?></label>
                <select class="form-select" id="departure_port_id" name="departure_port_id" required>
                    <option value=""><?= esc(lang('App.select_departure_port')) ?></option>
                    <?php foreach ($ports as $port): ?>
                        <option value="<?= (int) ($port['id'] ?? 0) ?>" <?= (int) old('departure_port_id', 0) === (int) ($port['id'] ?? 0) ? 'selected' : '' ?>>
                            <?= esc($port['name'] ?? '') ?> — <?= esc(implode('، ', array_filter([$port['city'] ?? '', $port['country'] ?? '']))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="arrival_port_id" class="form-label"><?= esc(lang('App.arrival_port')) ?></label>
                <select class="form-select" id="arrival_port_id" name="arrival_port_id" required>
                    <option value=""><?= esc(lang('App.select_arrival_port')) ?></option>
                    <?php foreach ($ports as $port): ?>
                        <option value="<?= (int) ($port['id'] ?? 0) ?>" <?= (int) old('arrival_port_id', 0) === (int) ($port['id'] ?? 0) ? 'selected' : '' ?>>
                            <?= esc($port['name'] ?? '') ?> — <?= esc(implode('، ', array_filter([$port['city'] ?? '', $port['country'] ?? '']))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="departure_date" class="form-label"><?= esc(lang('App.departure_date')) ?></label>
                <input type="datetime-local" class="form-control" id="departure_date" name="departure_date" min="<?= date('Y-m-d\TH:i') ?>" value="<?= esc(old('departure_date', '')) ?>" required>
            </div>

            <div class="col-md-3">
                <label for="arrival_date" class="form-label"><?= esc(lang('App.arrival_date')) ?></label>
                <input type="datetime-local" class="form-control" id="arrival_date" name="arrival_date" min="<?= date('Y-m-d\TH:i') ?>" value="<?= esc(old('arrival_date', '')) ?>" required>
            </div>
        </div>

        <div id="trip-client-error" class="alert alert-danger mt-3 d-none" role="alert"></div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" id="createTripButton" class="btn btn-primary px-4">
                <i class="fa-solid fa-plus me-2"></i><span><?= esc(lang('App.create_trip')) ?></span>
            </button>
            <a href="<?= site_url('trips') ?>" class="btn btn-outline-light px-4"><?= esc(lang('App.cancel')) ?></a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('trip-form');
    const shipSelect = document.getElementById('ship_id');
    const depPort = document.getElementById('departure_port_id');
    const arrPort = document.getElementById('arrival_port_id');
    const dep = document.getElementById('departure_date');
    const arr = document.getElementById('arrival_date');
    const box = document.getElementById('trip-client-error');
    const availabilityBox = document.getElementById('ship-availability-box');
    const availabilityText = document.getElementById('ship-availability-text');
    const submitButton = document.getElementById('createTripButton');
    const schedules = <?= json_encode($shipSchedules, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const messages = {
        differentPorts: <?= json_encode(lang('App.msg_different_ports'), JSON_UNESCAPED_UNICODE) ?>,
        arrivalAfterDeparture: <?= json_encode(lang('App.msg_arrival_after_departure'), JSON_UNESCAPED_UNICODE) ?>,
        selectedShipUnavailable: <?= json_encode(lang('App.selected_ship_unavailable'), JSON_UNESCAPED_UNICODE) ?>,
        busyUntil: <?= json_encode(lang('App.ship_busy_until'), JSON_UNESCAPED_UNICODE) ?>,
        availableShipsCount: <?= json_encode(lang('App.available_ships_count'), JSON_UNESCAPED_UNICODE) ?>,
        noAvailableShips: <?= json_encode(lang('App.no_available_ships_for_period'), JSON_UNESCAPED_UNICODE) ?>,
        checkAvailability: <?= json_encode(lang('App.update_dates_to_check_availability'), JSON_UNESCAPED_UNICODE) ?>,
    };

    const locale = document.documentElement.lang && document.documentElement.lang.startsWith('en') ? 'en' : 'ar';
    const dateFormatter = new Intl.DateTimeFormat(locale, { dateStyle: 'medium', timeStyle: 'short' });
    const pad = value => String(value).padStart(2, '0');
    const toLocalInput = date => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    const parseInputDate = value => value ? new Date(value).getTime() : NaN;
    const parseDbDate = value => value ? new Date(String(value).replace(' ', 'T')).getTime() : NaN;
    const formatDate = value => {
        const time = parseDbDate(value);
        return Number.isFinite(time) ? dateFormatter.format(new Date(time)) : value;
    };
    const overlaps = (aStart, aEnd, bStart, bEnd) => aStart < bEnd && aEnd > bStart;

    const setError = message => {
        if (!box) return;
        box.textContent = message || '';
        box.classList.toggle('d-none', !message);
    };

    const availabilityConflicts = shipId => {
        const requestedStart = parseInputDate(dep?.value);
        const requestedEnd = parseInputDate(arr?.value);
        if (!Number.isFinite(requestedStart) || !Number.isFinite(requestedEnd) || requestedEnd <= requestedStart) {
            return [];
        }

        return schedules.filter(schedule => {
            if (Number(schedule.ship_id) !== Number(shipId)) return false;
            const scheduleStart = parseDbDate(schedule.departure_date);
            const scheduleEnd = parseDbDate(schedule.arrival_date);
            return Number.isFinite(scheduleStart) && Number.isFinite(scheduleEnd) && overlaps(requestedStart, requestedEnd, scheduleStart, scheduleEnd);
        });
    };

    const refreshShipAvailability = () => {
        if (!shipSelect) return true;
        delete shipSelect.dataset.availabilityError;
        const requestedStart = parseInputDate(dep?.value);
        const requestedEnd = parseInputDate(arr?.value);
        const canCheck = Number.isFinite(requestedStart) && Number.isFinite(requestedEnd) && requestedEnd > requestedStart;
        let available = 0;
        let selectedBecameUnavailable = false;

        Array.from(shipSelect.options).forEach(option => {
            if (!option.value) return;
            const baseLabel = option.dataset.label || option.textContent;
            option.dataset.label = baseLabel;
            option.disabled = false;
            option.textContent = baseLabel;

            if (!canCheck) {
                available += 1;
                return;
            }

            const conflicts = availabilityConflicts(option.value);
            if (conflicts.length > 0) {
                const latestEnd = conflicts.reduce((latest, item) => Math.max(latest, parseDbDate(item.arrival_date) || 0), 0);
                option.disabled = true;
                option.textContent = `${baseLabel} — ${messages.busyUntil} ${latestEnd ? dateFormatter.format(new Date(latestEnd)) : ''}`;
                if (option.selected) selectedBecameUnavailable = true;
            } else {
                available += 1;
            }
        });

        if (selectedBecameUnavailable) {
            shipSelect.value = '';
            shipSelect.dataset.availabilityError = '1';
            setError(messages.selectedShipUnavailable);
        }

        if (availabilityText && availabilityBox) {
            availabilityText.textContent = canCheck
                ? (available > 0 ? messages.availableShipsCount.replace('{count}', available) : messages.noAvailableShips)
                : messages.checkAvailability;
            availabilityBox.classList.toggle('alert-info', !canCheck || available > 0);
            availabilityBox.classList.toggle('alert-danger', canCheck && available === 0);
        }

        return !canCheck || available > 0;
    };

    const validate = () => {
        let message = '';
        if (shipSelect?.dataset.availabilityError === '1') {
            message = messages.selectedShipUnavailable;
        } else if (depPort?.value && depPort.value === arrPort?.value) {
            message = messages.differentPorts;
        } else if (dep?.value && arr?.value && new Date(arr.value) <= new Date(dep.value)) {
            message = messages.arrivalAfterDeparture;
        } else if (shipSelect?.value && availabilityConflicts(shipSelect.value).length > 0) {
            message = messages.selectedShipUnavailable;
        }
        setError(message);
        return !message;
    };

    const syncArrival = () => {
        if (!dep?.value || !arr) return;
        arr.min = dep.value;
        if (!arr.value || new Date(arr.value) <= new Date(dep.value)) {
            const suggested = new Date(dep.value);
            suggested.setHours(suggested.getHours() + 24);
            arr.value = toLocalInput(suggested);
        }
        refreshShipAvailability();
        validate();
    };

    const nowLocal = toLocalInput(new Date());
    if (dep) dep.min = nowLocal;
    if (arr) arr.min = dep?.value || nowLocal;
    if (dep && !dep.value) {
        const suggestedDeparture = new Date(Date.now() + (60 * 60 * 1000));
        suggestedDeparture.setMinutes(0, 0, 0);
        dep.value = toLocalInput(suggestedDeparture);
        syncArrival();
    } else {
        refreshShipAvailability();
    }

    dep?.addEventListener('change', syncArrival);
    arr?.addEventListener('change', () => { refreshShipAvailability(); validate(); });
    shipSelect?.addEventListener('change', () => { delete shipSelect.dataset.availabilityError; validate(); });
    [depPort, arrPort].forEach(element => element?.addEventListener('change', validate));

    form?.addEventListener('submit', event => {
        refreshShipAvailability();
        if (!validate()) {
            event.preventDefault();
            return;
        }
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i>' + <?= json_encode(lang('App.saving'), JSON_UNESCAPED_UNICODE) ?>;
        }
    });
});
</script>
<?= $this->endSection() ?>
