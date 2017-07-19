<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<h1 class="mt-4 mb-3">
    <?=_('Fermentations')?>
    <a href="/ui/fermentations/add" class="btn btn-success float-md-right"><?=_('Add fermentation')?></a>
</h1>
<hr class="mb-3">

<?php if (!empty($data)) : ?>

<table class="table table-striped table-hover">
  <thead class="thead-inverse">
    <tr>
      <th><?=_('Name')?></th>
      <th class="text-center"><?=_('Temp (&deg;C)')?></th>
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
                <small class="text-muted">(<?=$this->e($ferm['spindle'])?>)</small>
                <br>
                <small class="text-muted">Last activity: <?=$ferm['activity']?></small><br>
                <small><?=$ferm['begin']?> &ndash; <?=$ferm['ending']?></small>
            </a>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['temperature'])) : ?>
                &#216; <?=round($ferm['temperature'], 2)?>&deg;C<br>
                <small class="text-muted"><?=round($ferm['min_temperature'], 2)?>&deg;C &ndash; <?=round($ferm['max_temperature'], 2)?>&deg;C</small>
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
                <small class="text"><?=round($ferm['max_gravity'], 2)?>&deg;P &rarr; <?=round($ferm['min_gravity'], 2)?>&deg;P</small>
            <?php else : ?>
                &hellip;
            <?php endif ?>
        </td>
        <td class="text-center">
            <?php if (!empty($ferm['max_trubidity'])) : ?>
                <small class="text"><?=round($ferm['max_trubidity'], 2)?>&deg;P &rarr; <?=round($ferm['min_trubidity'], 2)?>&deg;P</small>
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
<?php endif; ?>
