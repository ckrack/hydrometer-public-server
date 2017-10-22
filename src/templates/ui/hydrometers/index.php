<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>
<?php
use Jenssegers\Date\Date;
?>

<h1 class="mt-4 mb-3">
    <?=_('Available hydrometers')?>
    <a href="/ui/hydrometers/add" class="btn btn-success float-md-right"><?=_('Add hydrometer')?></a>
</h1>
<hr class="mb-3">

<?php if (!empty($hydrometers)) : ?>

<?php foreach ($hydrometers as $hydrometer) : ?>
    <div class="card mb-3">
        <h5 class="card-header">
            <?=$hydrometer['name']?>
            <a class="float-right" href="/ui/hydrometers/help/<?=$optimus->encode($hydrometer['id'])?>"><?=_('?')?></a>
            <a class="float-right" href="/ui/hydrometers/edit/<?=$optimus->encode($hydrometer['id'])?>"><?=_('edit')?></a>
        </h5>
        <div class="card-block">
        <?php if (!empty($hydrometer['activity'])) : ?>
            <p class="card-text">
                <?=_('Last activity:')?> <?=Date::parse($hydrometer['activity'])->diffForHumans()?>
            </p>
        <?php endif; ?>

            <a class="card-link" href="/ui/status/<?=$optimus->encode($hydrometer['id'])?>"><?=_('Status')?></a>
            <a class="card-link" href="/ui/plato/<?=$optimus->encode($hydrometer['id'])?>"><?=_('Plato')?></a>
            <a class="card-link" href="/ui/angle/<?=$optimus->encode($hydrometer['id'])?>"><?=_('Angle')?></a>
            <a class="card-link" href="/ui/battery/<?=$optimus->encode($hydrometer['id'])?>"><?=_('Battery')?></a>
            <a class="card-link" href="/ui/data/<?=$optimus->encode($hydrometer['id'])?>"><?=_('Datapoints')?></a>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>
