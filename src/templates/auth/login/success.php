<?php $this->layout('layouts/index.php') ?>

<div class="alert alert-success mt-4" role="alert">
  <h4 class="alert-heading">Welcome back!</h4>
  <p>
      Nice to see you again, <?=$this->e($user->getUsername())?>!
  </p>
</div>
