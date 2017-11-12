<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<?=$form->open()->action('')->addClass('mt-4 mb-3 col-6')?>

<h1 class="mt-4 mb-3">
    <?=_('Delete datapoint')?>
</h1>
<hr class="mb-3">

<?=$form->text(_('Hydrometer'), 'name')->value($datapoint->getHydrometer()->getName())->disabled()?>

<?=$form->text(_('Values'), 'name')->value($datapoint->getGravity() . ' / ' . $datapoint->getTemperature())->disabled()?>

<?php foreach ($csrf as $key => $value):?>
    <?=$form->hidden($key, $key)->value($value)?>
<?php endforeach; ?>

<?=$form->submit(_('Delete'))->addClass('btn btn-primary')?>
<?=$form->close()?>
