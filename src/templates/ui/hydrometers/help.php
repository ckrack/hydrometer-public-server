<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<h1 class="mt-4 mb-3">
    <?=_('Setup device')?>
</h1>
<hr class="mb-3">

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#intro" role="tab"><?=_('Intro')?></a>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?=_('iSpindle')?></a>
        <div class="dropdown-menu">
          <a class="dropdown-item" data-toggle="tab" role="tab" href="#ispindel-tcp" aria-expanded="true" aria-controls="#ispindel-tcp"><?=_('TCP')?></a>
          <a class="dropdown-item" data-toggle="tab" role="tab" href="#ispindel-http" aria-expanded="true" aria-controls="#ispindel-http"><?=_('HTTP')?></a>
        </div>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#tilt" role="tab"><?=_('TILT')?></a>
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
  <div class="tab-pane active" id="intro" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <?=_('Setup')?>
        </div>
        <div class="card-body">
            <p>
                <?=_('Your generated token is')?>:<br>
                <strong><?=$token->getValue()?></strong>
            </p>
            <p>
                <?=_('Please select your device-type to get detailled setup instructions.')?>
            </p>
        </div>
    </div>
  </div>
  <div class="tab-pane" id="ispindel-tcp" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <?=_('iSpindle TCP')?>
        </div>
        <div class="card-body">
            <ol>
                <li class="card-text"><?=_('Put iSpindle into configuration mode by pressing reset')?></li>
                <li class="card-text"><?=_('Open the configuration page and put in:')?></li>
                <li class="card-text">
                    <dl>
                        <dt><?=_('Service type')?></dt>
                        <dd><?=_('TCP')?></dd>

                        <dt><?=_('Token')?></dt>
                        <dd><?=$token->getValue()?></dd>

                        <dt><?=_('Server address')?></dt>
                        <dd><?=getenv('TCP_API_HOST')?></dd>

                        <dt><?=_('Server port')?></dt>
                        <dd><?=getenv('TCP_API_PORT')?></dd>
                    </dl>
                </li>
            </ol>
        </div>
    </div>
  </div>
  <div class="tab-pane" id="ispindel-http" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <?=_('iSpindle HTTP')?>
        </div>
        <div class="card-body">
            <ol>
                <li class="card-text"><?=_('Put iSpindle into configuration mode by pressing reset')?></li>
                <li class="card-text"><?=_('Open the configuration page and put in:')?></li>
                <li class="card-text">
                    <dl>
                        <dt><?=_('Service type')?></dt>
                        <dd><?=_('HTTP')?></dd>

                        <dt><?=_('Server URL')?></dt>
                        <dd><?=$this->pathFor('api-post-spindle', ['token' => $token->getValue()])?></dd>

                        <dt><?=_('Server address')?></dt>
                        <dd><?=$this->uriScheme().'://'.$this->uriHost()?></dd>

                        <dt><?=_('Server port')?></dt>
                        <dd>80</dd>
                    </dl>
                </li>
            </ol>
        </div>
    </div>
  </div>
  <div class="tab-pane" id="tilt" role="tabpanel">
      <div class="card">
        <div class="card-body">
              <ol>
                <li class="card-text"><?=_('Open TILT app on your phone and enter settings.')?></li>
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
  <div class="tab-pane" id="plaato" role="tabpanel">
      <div class="card">
          <div class="card-header">
              <?=_('Plaato')?>
          </div>
          <div class="card-body">
              <?=_('Apparently the Plaato device is not yet supported.')?>
          </div>
      </div>
  </div>
  <div class="tab-pane" id="beerbug" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <?=_('Beerbug')?>
        </div>
        <div class="card-body">
            <?=_('Apparently the Beerbug device is not yet supported.')?>
        </div>
    </div>
  </div>
</div>


