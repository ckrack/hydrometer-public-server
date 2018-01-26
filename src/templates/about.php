<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-3">
        <?=_('About')?>
    </h1>
    <p class="lead">
        <?=_('You own an electronic hydrometer, such as TILT or the open-source iSpindle?')?><br>
        <?=_('Monitor your fermentations in an easy to use online interface!')?>
    </p>
    <hr class="my-4">
    <a class="btn btn-primary btn-lg ml-auto mr-0" href="/auth" role="button"><?=_('Sign in')?></a>
  </div>
</div>
