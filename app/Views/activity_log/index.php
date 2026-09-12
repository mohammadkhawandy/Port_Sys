<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.activity_log_title')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$logs = $logs ?? [];
$filters = $filters ?? ['q' => '', 'action' => ''];
$actions = $actions ?? [];
?>
<div class="page-card p-4 p-lg-5 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="section-title"><?= esc(lang('App.nav_activity_log')) ?></div>
            <h1 class="page-title h2 mb-2"><?= esc(lang('App.activity_log_title')) ?></h1>
            <p class="muted mb-0"><?= esc(lang('App.activity_log_desc')) ?></p>
        </div>
        <span class="badge badge-soft rounded-pill px-3 py-2">
            <i class="fa-solid fa-shield-halved me-2"></i><?= esc(lang('App.security_center')) ?>
        </span>
    </div>
</div>

<div class="table-shell p-3 p-lg-4">
    <form method="get" class="row g-3 align-items-end mb-4" role="search">
        <div class="col-lg-7">
            <label class="form-label" for="activity-search"><?= esc(lang('App.search')) ?></label>
            <input id="activity-search" class="form-control" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="<?= esc(lang('App.search_placeholder')) ?>">
        </div>
        <div class="col-lg-3">
            <label class="form-label" for="activity-action"><?= esc(lang('App.activity_event')) ?></label>
            <select id="activity-action" class="form-select" name="action">
                <option value=""><?= esc(lang('App.all')) ?></option>
                <?php foreach ($actions as $action): ?>
                    <option value="<?= esc($action) ?>" <?= ($filters['action'] ?? '') === $action ? 'selected' : '' ?>><?= esc(activity_action_label($action)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1" type="submit"><i class="fa-solid fa-filter me-2"></i><?= esc(lang('App.filter')) ?></button>
            <a class="btn btn-outline-light" href="<?= site_url('activity-log') ?>" aria-label="<?= esc(lang('App.reset')) ?>"><i class="fa-solid fa-rotate-left"></i></a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th><?= esc(lang('App.activity_event')) ?></th>
                <th><?= esc(lang('App.activity_description')) ?></th>
                <th><?= esc(lang('App.performed_by')) ?></th>
                <th><?= esc(lang('App.activity_entity')) ?></th>
                <th><?= esc(lang('App.ip_address')) ?></th>
                <th><?= esc(lang('App.date')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($logs === []): ?>
                <tr><td colspan="6" class="text-center muted py-5"><i class="fa-solid fa-clock-rotate-left fa-2x d-block mb-3"></i><?= esc(lang('App.no_activity')) ?></td></tr>
            <?php else: foreach ($logs as $log): ?>
                <?php
                $action = (string) ($log['action'] ?? '');
                $actor = trim((string) ($log['user_name'] ?? '')) ?: trim((string) ($log['user_email'] ?? '')) ?: lang('App.system_user');
                $entity = entity_type_label($log['entity_type'] ?? null);
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="notification-icon" style="width:36px;height:36px"><i class="fa-solid <?= esc(activity_action_icon($action)) ?>"></i></span>
                            <strong class="small"><?= esc(activity_action_label($action)) ?></strong>
                        </div>
                    </td>
                    <td><span class="small"><?= esc($log['description'] ?? '') ?></span></td>
                    <td><strong class="small"><?= esc($actor) ?></strong><small class="muted d-block"><?= esc($log['user_email'] ?? '') ?></small></td>
                    <td><span class="badge badge-soft rounded-pill"><?= esc($entity) ?><?= ! empty($log['entity_id']) ? ' #' . (int) $log['entity_id'] : '' ?></span></td>
                    <td><code><?= esc($log['ip_address'] ?? '—') ?></code></td>
                    <td class="text-nowrap"><span class="small"><?= esc(format_datetime($log['created_at'] ?? null)) ?></span><small class="muted d-block"><?= esc(human_time($log['created_at'] ?? null)) ?></small></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?><div class="mt-4"><?= $pager->links() ?></div><?php endif; ?>
</div>
<?= $this->endSection() ?>
