<?php $this->layout('layouts/index.php') ?>

<?=$form->open()->action('/auth/register')->addClass('mt-4 mb-3 col-6');?>
<h1 class="mt-4 mb-3"><?=_('Register')?></h1>
<?=$form->text(_('Username'), 'username')->placeholder(_('Your username'))?>
<?=$form->text(_('Email'), 'email')->placeholder(_('you@example.com'));?>
<?=$form->submit(_('Register'))->addClass('btn btn-primary')?>
<?=$form->close()?>
