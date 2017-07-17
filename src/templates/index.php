<?php $this->layout('layouts/index.php') ?>
<?php
use Jenssegers\Date\Date;
?>

<?php if (!empty($spindles)) : ?>

<h1 class="mt-4 mb-3">
    <?=_('Available spindles')?>
</h1>

<?php foreach ($spindles as $spindle) : ?>
    <div class="card mb-3">
        <h5 class="card-header"><?=$spindle['name']?></h5>
        <div class="card-block">
        <?php if (!empty($spindle['activity'])) : ?>
            <p class="card-text">
                <?=_('Last activity:')?> <?=Date::parse($spindle['activity'])->diffForHumans()?>
            </p>
        <?php endif; ?>

            <a class="card-link" href="/ui/status/<?=$optimus->encode($spindle['id'])?>"><?=_('Status')?></a>
            <a class="card-link" href="/ui/plato4/<?=$optimus->encode($spindle['id'])?>"><?=_('Plato (old)')?></a>
            <a class="card-link" href="/ui/plato/<?=$optimus->encode($spindle['id'])?>"><?=_('Plato')?></a>
            <a class="card-link" href="/ui/angle/<?=$optimus->encode($spindle['id'])?>"><?=_('Angle')?></a>
            <a class="card-link" href="/ui/battery/<?=$optimus->encode($spindle['id'])?>"><?=_('Battery')?></a>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>
