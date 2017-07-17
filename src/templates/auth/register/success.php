<?php $this->layout('layouts/index.php') ?>

<div class="alert alert-success mt-4" role="alert">
  <h4 class="alert-heading">Well done!</h4>
  <p>
      Yay, welcome on board <?=$this->e($user->getUsername())?>!
  </p>
</div>
