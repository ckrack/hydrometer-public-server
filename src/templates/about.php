<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-3">
        About
    </h1>
    <p class="lead">
        You own an electronic hydrometer, such as Tilt or the open-source iSpindle?<br>
        Monitor your fermentations in an easy to use online interface!
    </p>
    <hr class="my-4">
    <a class="btn btn-primary btn-lg ml-auto mr-0" href="/auth" role="button">Sign in</a>
  </div>
</div>
