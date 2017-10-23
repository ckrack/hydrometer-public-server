<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<?=$form->open()->action('')->addClass('mt-4 mb-3 col-6')?>

<h1 class="mt-4 mb-3">
    <?=_('Add hydrometer')?>
</h1>
<hr class="mb-3">

<?=$form->text(_('Name'), 'name')->placeholder(_('e.g. color of hydrometer or any name'))?>
<?=$form->select(_('Temperature metric'), 'metric_temp', ['°C' => _('Celsius'), '°F' => _('Fahrenheit')])?>
<?=$form->select(_('Gravity metric'), 'metric_gravity', ['°P' => _('Plato'), 'SG' => _('Specific gravity (SG)'), '%' => 'Brix'])?>
<?=$form->submit(_('Add'))->addClass('btn btn-primary')?>
<?=$form->close()?>