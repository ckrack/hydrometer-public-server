<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<h1 class="mt-4 mb-3">
    <?=_('Setup device')?>
</h1>
<hr class="mb-3">

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#ispindle" role="tab"><?=_('iSpindle')?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#tilt" role="tab"><?=_('Tilt')?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#plaato" role="tab"><?=_('Plaato')?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#beerbug" role="tab"><?=_('Beerbug')?></a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="ispindle" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <?=_('iSpindle HTTP')?>
        </div>
        <div class="card-block">
            <ol>
                <li class="card-text"><?=_('Put iSpindle into configuration mode by pressing reset')?></li>
                <li class="card-text"><?=_('Open the configuration page and put in:')?></li>
                <li class="card-text">
                    <dl>
                        <dt><?=_('Service type')?></dt>
                        <dd><?=_('HTTP')?></dd>

                        <dt><?=_('Server address')?></dt>
                        <dd><?=$this->uriScheme().'://'.$this->uriHost()?></dd>

                        <dt><?=_('Server port')?></dt>
                        <dd>80</dd>

                        <dt><?=_('Server URL')?></dt>
                        <dd><?=$this->pathFor('api-post-spindle', ['token' => $token->getValue()])?></dd>
                    </dl>
                </li>
            </ol>
        </div>
    </div>
  </div>
  <div class="tab-pane" id="tilt" role="tabpanel">
      <div class="card">
        <div class="card-block">
              <ol>
                <li class="card-text"><?=_('Open TILT app on your phone and enter settings.')?></li>
                <li class="card-text"><?=_('Choose color of your tilt')?></li>
                <li class="card-text"><?=_('Choose color of your tilt')?></li>
                <li class="card-text">
                    <dl>
                        <dt><?=_('Logging URL')?></dt>
                        <dd><?=$this->uriScheme().'://'.$this->uriHost().$this->pathFor('api-post-tilt', ['token' => $token->getValue()])?></dd>
                    </dl>
                </li>
            </ol>
        </div>
      </div>
  </div>
  <div class="tab-pane" id="plaato" role="tabpanel">...</div>
  <div class="tab-pane" id="beerbug" role="tabpanel">...</div>
</div>


