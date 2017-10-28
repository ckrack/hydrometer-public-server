<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

<?php if (($user ?: null) instanceof \App\Entity\User) : ?>
    <?php $this->insert('partials/nav/logged_in.php', ['user' => $user]) ?>
<?php else : ?>
    <?php $this->insert('partials/nav/logged_out.php') ?>
<?php endif ?>
</nav>
