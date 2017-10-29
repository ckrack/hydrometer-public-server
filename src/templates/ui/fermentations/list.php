<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<h1 class="mt-4 mb-3">
    <?=_('Fermentations')?>
    <a href="/ui/fermentations/add" class="btn btn-success float-md-right"><?=_('Add fermentation')?></a>
</h1>
<hr class="mb-3">

<?php if (!empty($data)) : ?>

<table class="table table-striped table-hover">
  <thead class="thead-dark">
    <tr>
      <th><?=_('Name')?></th>
      <th class="text-center"><?=_('Temperature')?></th>
      <th class="text-center"><?=_('Angle')?></th>
      <th class="text-center"><?=_('Gravity')?></th>
      <th class="text-center"><?=_('Trubidity')?></th>
      <th class="text-right"><?=_('Actions')?></th>
    </tr>
  </thead>
  <tbody>

<?php foreach ($data as $ferm) : ?>
    <tr class="">
        <td>
            <a href="/ui/fermentations/<?=$optimus->encode((int)$ferm['id'])?>">
                <?=$ferm['name']?>
                <small class="text-muted">(<?=$this->e($ferm['hydrometer'])?>)</small>
                <br>
                <small class="text-muted"><?=_('Last activity')?>: <?=$ferm['activity']?></small><br>
                <small><?=$ferm['begin']?> &ndash; <?=$ferm['ending']?></small>
            </a>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['temperature'])) : ?>
                &#216; <?=round($ferm['temperature'], 2)?> <?=$this->e($ferm['metricTemperature'])?><br>
                <small class="text-muted">
                    <?=round($ferm['min_temperature'], 2)?><?=$this->e($ferm['metricTemperature'])?>
                    &ndash;
                    <?=round($ferm['max_temperature'], 2)?><?=$this->e($ferm['metricTemperature'])?>
                </small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['max_angle'])) : ?>
                <small class="text"><?=round($ferm['max_angle'], 2)?>&deg; &rarr; <?=round($ferm['min_angle'], 2)?>&deg;</small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['max_gravity'])) : ?>
                <small class="text">
                    <?=round($ferm['max_gravity'], 2)?><?=$this->e($ferm['metricGravity'])?>
                    &rarr;
                    <?=round($ferm['min_gravity'], 2)?><?=$this->e($ferm['metricGravity'])?>
                </small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['max_trubidity'])) : ?>
                <small class="text">
                    <?=round($ferm['max_trubidity'], 2)?><?=$this->e($ferm['metricGravity'])?>
                    &rarr;
                    <?=round($ferm['min_trubidity'], 2)?><?=$this->e($ferm['metricGravity'])?>
                </small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-right">
            <a href="/ui/fermentations/<?=$optimus->encode((int)$ferm['id'])?>" class="btn btn-sm btn-primary float-sm-left">details</a>
            <a href="/ui/fermentations/delete/<?=$optimus->encode((int)$ferm['id'])?>" class="close"><span aria-hidden="true">&times;</span></a>
        </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
    <div class="jumbotron jumbotron-fluid">
      <div class="container">
        <p class="lead">
            <?=_('Group your data into fermentations. This allows to archive past beers in order to keep a detailled log.')?>
        </p>
        <hr class="my-4">
        <a class="btn btn-primary btn-lg ml-auto mr-0" href="/ui/fermentations/add" role="button"><?=_('Add your first fermentation')?></a>
      </div>
    </div>
<?php endif; ?>
