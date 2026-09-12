<?php
/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
<nav aria-label="<?= esc(lang('App.pagination')) ?>">
    <ul class="pagination pagination-sm flex-wrap justify-content-center gap-1 mb-0">
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link rounded-3" href="<?= esc($pager->getFirst()) ?>" aria-label="<?= esc(lang('App.pagination_first')) ?>">
                    <span aria-hidden="true">«</span><span class="d-none d-sm-inline ms-1"><?= esc(lang('App.pagination_first')) ?></span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-3" href="<?= esc($pager->getPrevious()) ?>" aria-label="<?= esc(lang('App.pagination_previous')) ?>">
                    <span aria-hidden="true">‹</span><span class="d-none d-sm-inline ms-1"><?= esc(lang('App.pagination_previous')) ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link rounded-3" href="<?= esc($link['uri']) ?>" <?= $link['active'] ? 'aria-current="page"' : '' ?>><?= esc($link['title']) ?></a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link rounded-3" href="<?= esc($pager->getNext()) ?>" aria-label="<?= esc(lang('App.pagination_next')) ?>">
                    <span class="d-none d-sm-inline me-1"><?= esc(lang('App.pagination_next')) ?></span><span aria-hidden="true">›</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-3" href="<?= esc($pager->getLast()) ?>" aria-label="<?= esc(lang('App.pagination_last')) ?>">
                    <span class="d-none d-sm-inline me-1"><?= esc(lang('App.pagination_last')) ?></span><span aria-hidden="true">»</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
