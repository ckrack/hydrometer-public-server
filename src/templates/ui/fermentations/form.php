<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<?=$form->open()->action('')->addClass('mt-4 mb-3 col-6')?>

<h1 class="mt-4 mb-3">
    <?=_('Add fermentation')?>
</h1>
<hr class="mb-3">

<?=$form->text(_('Name'), 'name')->placeholder(_('e.g. Pilsener Batch #1, My Pale Ale #2'))?>
<?=$form->select(_('Hydrometer'), 'hydrometer_id', $hydrometers)?>
<?=$form->dateTimeLocal(_('Begin'), 'begin')->placeholder(_('YYYY-MM-DD HH:MM'))->defaultValue(\DateTime::createFromFormat('U', time()))?>
<?=$form->dateTimeLocal(_('End'), 'end')->placeholder(_('YYYY-MM-DD HH:MM'))->defaultValue(\DateTime::createFromFormat('U', time()))?>
<p class="form-text text-warning">
    <?=_('All datapoints of the selected hydrometer in the defined timeframe, that are not yet part of a fermentation, will be added to the new fermentation.')?>
</p>
<?=$form->checkbox(_('Public'), 'public')?>

<?php foreach ($csrf as $key => $value):?>
    <?=$form->hidden($key, $key)->value($value)?>
<?php endforeach; ?>

<?=$form->submit(_('Add'))->addClass('btn btn-primary')?>
<?=$form->close()?>
