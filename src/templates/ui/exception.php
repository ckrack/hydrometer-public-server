<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <p class="lead">
        <?=_("Sorry, an error occured during your request.")?>
    </p>
  </div>
</div>
