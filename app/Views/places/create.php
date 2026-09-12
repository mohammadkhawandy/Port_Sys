<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc(lang('App.add_place_title')) ?><?= $this->endSection() ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= $this->include('places/_form', [
    'pageHeading' => lang('App.add_place_title'),
    'pageDescription' => lang('App.add_place_desc'),
    'action' => site_url('places'),
    'submitLabel' => lang('App.create_place'),
    'submitIcon' => 'fa-plus',
]) ?>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?= $this->include('places/_map_script') ?>
<?= $this->endSection() ?>
