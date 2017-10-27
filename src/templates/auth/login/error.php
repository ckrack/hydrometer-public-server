<?php $this->layout('layouts/index.php') ?>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <div class="alert alert-danger" role="alert">
        <h1 class="display-3 alert-heading">
            <?=_('There was a problem')?>
        </h1>
    <?php if (isset($msg)) : ?>
        <p class="lead">
            <?=$this->e($msg)?>
        </p>
    <?php else : ?>
        <p class="lead">
            <?=_('We could not complete your login.')?>
        </p>
    <?php endif; ?>
    </div>
  </div>
</div>
