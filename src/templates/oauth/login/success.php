<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<div class="jumbotron jumbotron-fluid ">
  <div class="container">
    <div class="alert alert-success">
        <h1 class="display-3 alert-heading">
            <?=_('Welcome back!')?>
        </h1>
        <p class="lead">
            <?=_(sprintf('Nice to see you again, %s!', $this->e($user->getUsername())))?>
        </p>
    </div>
    <hr class="my-4">
    <a href="/ui/" class="btn btn-primary">
        <?=_('Go to your hydrometers')?>
    </a>
  </div>
</div>
