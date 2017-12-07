<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>
<?php
use App\Modules\Formula\Formatter;

?>
<h1 class="mt-4 mb-3">
    <?=_('Fermentations')?>
    <a href="/ui/fermentations/add" class="btn btn-success float-md-right"><?=_('Add fermentation')?></a>
</h1>
<hr class="mb-3">

<?php if (!empty($data)) : ?>

<table class="table table-striped table-hover table-responsive">
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
                <small><?=$ferm['begin']?> &ndash; <?=(!empty($ferm['ending'])) ? $ferm['ending'] : $ferm['activity']?></small>
            </a>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['temperature'])) : ?>
                &#216;
                <?=$this->e(Formatter::format($ferm['temperature'], $ferm['metricTemperature']))?>
                <br>
                <small class="text-muted">
                    <?=$this->e(Formatter::format($ferm['min_temperature'], $ferm['metricTemperature']))?>
                    &ndash;
                    <?=$this->e(Formatter::format($ferm['max_temperature'], $ferm['metricTemperature']))?>
                </small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['max_angle'])) : ?>
                <small class="text">
                    <?=$this->e(Formatter::format($ferm['max_angle'], '°'))?>
                    &rarr;
                    <?=$this->e(Formatter::format($ferm['min_angle'], '°'))?>
                </small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['max_gravity'])) : ?>
                <small class="text">
                    <?=$this->e(Formatter::format($ferm['max_gravity'], $ferm['metricGravity']))?>
                    &rarr;
                    <?=$this->e(Formatter::format($ferm['min_gravity'], $ferm['metricGravity']))?>
                </small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['max_trubidity'])) : ?>
                <small class="text">
                    <?=$this->e(Formatter::format($ferm['min_trubidity']))?>
                    &rarr;
                    <?=$this->e(Formatter::format($ferm['max_trubidity']))?>
                </small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-right">
            <div class="btn-group">
                <a class="btn btn-secondary btn-sm" href="/ui/fermentations/edit/<?=$optimus->encode($ferm['id'])?>"><?=_('edit')?></a>
                <a class="btn btn-sm btn-primary" href="/ui/fermentations/<?=$optimus->encode((int)$ferm['id'])?>">details</a>
            </div>
            &nbsp;
            <a class="close" href="/ui/fermentations/delete/<?=$optimus->encode((int)$ferm['id'])?>"><span aria-hidden="true">&times;</span></a>
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
