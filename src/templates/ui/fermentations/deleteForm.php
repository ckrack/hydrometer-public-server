<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<?=$form->open()->action('')->addClass('mt-4 mb-3 col-6')?>

<h1 class="mt-4 mb-3">
    <?=_('Delete fermentation')?>
</h1>
<hr class="mb-3">

<?=$form->text(_('Name'), 'name')->value($fermentation->getName())->disabled()?>
<p class="form-text text-warning">
    <?=_('Datapoints will not be deleted, but released from the fermentation.')?>
</p>

<?php foreach ($csrf as $key => $value):?>
    <?=$form->hidden($key, $key)->value($value)?>
<?php endforeach; ?>

<?=$form->submit(_('Delete'))->addClass('btn btn-primary')?>
<?=$form->close()?>
