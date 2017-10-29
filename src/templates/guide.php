<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-3">
        <?=_('Guide')?>
    </h1>
    <p class="lead">
        <?=_('During setup, installation instructions will be displayed, along with your API tokens.')?>
    </p>
    <hr class="my-4">
    <a class="btn btn-primary btn-lg ml-auto mr-0" href="/auth" role="button"><?=_('Sign in')?></a>
  </div>
</div>
