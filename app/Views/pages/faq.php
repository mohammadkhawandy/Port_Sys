<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?><?= esc(portsys_text('faq')) ?> - <?= esc(portsys_text('brand')) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="inner-hero"><div class="container"><h1><?= esc(portsys_text('faq')) ?></h1><div class="breadcrumb-mini"><?= esc(portsys_text('breadcrumb_home')) ?> / <?= esc(portsys_text('faq')) ?></div></div></section>
<section class="section-pad">
    <div class="container" style="max-width:900px">
        <div class="section-title"><h2><?= esc(portsys_text('faq')) ?></h2><p><?= esc(lang('App.faq_intro')) ?></p></div>
        <div class="accordion" id="faqAccordion">
            <?php for ($i = 1; $i <= 6; $i++): $headingId = 'faq-heading-' . $i; $collapseId = 'faq-collapse-' . $i; ?>
                <div class="accordion-item faq-item overflow-hidden">
                    <h3 class="accordion-header" id="<?= esc($headingId) ?>">
                        <button class="accordion-button <?= $i === 1 ? '' : 'collapsed' ?> fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc($collapseId) ?>" aria-expanded="<?= $i === 1 ? 'true' : 'false' ?>" aria-controls="<?= esc($collapseId) ?>">
                            <?= esc(lang('App.faq_q' . $i)) ?>
                        </button>
                    </h3>
                    <div id="<?= esc($collapseId) ?>" class="accordion-collapse collapse <?= $i === 1 ? 'show' : '' ?>" aria-labelledby="<?= esc($headingId) ?>" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted"><?= esc(lang('App.faq_a' . $i)) ?></div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
