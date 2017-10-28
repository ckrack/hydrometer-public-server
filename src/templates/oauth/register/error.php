<?php $this->layout('layouts/index.php') ?>

<div class="alert alert-danger mt-4" role="alert">
  <h4 class="alert-heading">There was a problem</h4>
  <?php if (isset($msg)) : ?>
      <p>
          <?=$this->e($msg)?>
      </p>
  <?php else : ?>
      <p>
          We could not complete your registration.
      </p>
  <?php endif; ?>
</div>
