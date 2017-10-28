<?php $this->layout('layouts/index.php') ?>

<h1 class="mt-4 mb-3"><?=_('Sign in')?></h1>
<hr class="mb-3">

<p class="lead">
    <?=_('You can use your existing account at one the the below services to handle authentication.')?>
</p>

<div class="container">

            <div class="row">
                <?php foreach($choices as $key => $choice): ?>
                <div class="card col1">
                  <div class="card-header">
                    <?=ucfirst($key)?>
                  </div>
                  <div class="card-block">
                    <p>
                        <?=$this->e($choice['description'])?>
                    </p>
                    <a href="/auth/init/<?=$key?>" class="btn btn-primary"><?=_('Sign in with')?> <?=$key?></a>
                  </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
