<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<div class="alert alert-success mt-4" role="alert">
  <h4 class="alert-heading">Well done!</h4>
  <p>
      Yay, welcome on board <?=$this->e($user->getUsername())?>!
  </p>
</div>
<a href="/ui/" class="btn btn-primary btn-lg">Go to your spindles</a>
