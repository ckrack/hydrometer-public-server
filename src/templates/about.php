<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-3">
        About
    </h1>
    <p class="lead">
        You own an <a href="">iSpindel</a> electronic hydrometer?<br>
        Monitor your fermentations in an easy to use online interface!
    </p>
    <hr class="my-4">
    <p>
        Best of all, it's free.
    </p>
    <a class="btn btn-primary btn-lg ml-auto mr-0" href="/auth/register" role="button">Sign up</a>
  </div>
</div>
