<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('news')) ?> - <?= esc(portsys_text('brand')) ?><?= $this->endSection() ?>
<?= $this->section('head') ?>
<style>
    .news-page .news-card img{height:220px;object-fit:cover;background:#eaf2fb}.news-page .news-card .body{padding:1.15rem 1.2rem 1.25rem}.news-page .news-card h3{font-size:1.08rem;line-height:1.55}.news-page .news-card p{font-size:.9rem}.news-badge{display:inline-flex;align-items:center;gap:.45rem;background:#eef6ff;color:#0f5bb8;border-radius:999px;padding:.25rem .7rem;font-size:.78rem;font-weight:900;margin-bottom:.7rem}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$newsItems = [
    ['image' => 'news-1.jpg', 'icon' => 'fa-solid fa-route', 'title' => lang('App.news_item_1_title'), 'desc' => lang('App.news_item_1_desc')],
    ['image' => 'news-2.jpg', 'icon' => 'fa-solid fa-shield-halved', 'title' => lang('App.news_item_2_title'), 'desc' => lang('App.news_item_2_desc')],
    ['image' => 'news-3.jpg', 'icon' => 'fa-solid fa-bell', 'title' => lang('App.news_item_3_title'), 'desc' => lang('App.news_item_3_desc')],
    ['image' => 'news-4.jpg', 'icon' => 'fa-solid fa-chart-line', 'title' => lang('App.news_item_4_title'), 'desc' => lang('App.news_item_4_desc')],
];
?>
<section class="inner-hero">
    <div class="container">
        <h1><?= esc(portsys_text('news')) ?></h1>
        <div class="breadcrumb-mini"><?= esc(portsys_text('breadcrumb_home')) ?> / <?= esc(portsys_text('news')) ?></div>
    </div>
</section>
<section class="section-pad news-page">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($newsItems as $item): ?>
                <div class="col-md-6 col-xl-3">
                    <article class="news-card h-100">
                        <img src="<?= portsys_asset($item['image']) ?>" alt="<?= esc($item['title']) ?>" loading="lazy">
                        <div class="body">
                            <div class="news-badge"><i class="<?= esc($item['icon']) ?>"></i><?= esc(portsys_text('news')) ?></div>
                            <h3><?= esc($item['title']) ?></h3>
                            <p><?= esc($item['desc']) ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
