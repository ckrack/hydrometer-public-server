<?php $this->layout('layouts/index.php') ?>

<?=$form->open()->action('/auth/login')->addClass('mt-4 mb-3 col-6');?>
<h1 class="mt-4 mb-3"><?=_('Login')?></h1>
<?=$form->text(_('Email'), 'email')->defaultValue($email)?>
<?=$form->checkbox(_('Stay logged in'), 'cookies')?>
<?=$form->submit(_('Login'))->addClass('btn btn-primary')?>
<?=$form->close()?>
