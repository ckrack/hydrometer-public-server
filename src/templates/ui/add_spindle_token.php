<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<h1 class="mt-4 mb-3">
    <?=_('Add device')?>
</h1>
<hr class="mb-3">

<div class="card">
    <div class="card-header">
        <?=_('Generated device-token')?>
    </div>
    <div class="card-block">
        <h2 class="card-title">We generated the following device-user-token for you:</h2>
        <hr class="myby">
        <p class="card-text text-success"><?=$this->e($token->getValue())?></p>
        <hr class="myby">
        Please copy this into the settings panel in your device configuration.
        <hr class="myby">
        Alternatively, use this URL to post your data to:
        <p class="card-text text-success"><?=$this->uriScheme().'://'.$this->uriHost().$this->pathFor('api-post-token', ['token' => $token->getValue()])?></p>
    </div>
</div>

<script>
</script>



