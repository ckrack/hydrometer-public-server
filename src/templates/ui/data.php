<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>
<?php
use App\Modules\Stats\Anomaly;
use App\Modules\Stats\AnomalyFixed;

$tempAnomaly = new Anomaly(3.5, $logger);
$angleAnomaly = new Anomaly(2, $logger);

?>

<?php if (!empty($data)) : ?>

<h1 class="mt-4 mb-3">
    <?=_('Datapoints')?>
</h1>
<hr class="mb-3">

<?php if (!empty($hydrometer)) : ?>
<h2 class="mt-2 mb-3"><?=$hydrometer->getName()?></h2>
<?php endif ?>

<table class="table table-striped table-hover table-sm">
    <thead class="thead-inverse">
        <tr>
            <th><?=_('Date')?></th>
            <?php if(empty($hydrometer)) : ?>
            <th><?=_('Hydrometer')?></th>
            <?php endif ?>
            <th class="text-right"><?=_('Temperature')?></th>
            <th class="text-right"><?=_('Angle')?></th>
            <th class="text-right"><?=_('Battery')?></th>
            <th class="text-right"><?=_('Gravity')?></th>
            <th class="text-right"><?=_('Trubidity')?></th>
            <th class="text-right"><?=_('Actions')?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data as $point) : ?>
        <tr class="">
            <td>
                <?=$point['time']?>
            </td>
            <?php if(empty($hydrometer)) : ?>
            <td>
                <?=$point['hydrometer']?>
            </td>
            <?php endif ?>
            <td class="text-right <?php if ($tempAnomaly->is($point['temperature'])) echo 'table-warning' ?>">
                <?=number_format($point['temperature'], 2)?> &deg;C
            </td>
            <td class="text-right <?php if ($angleAnomaly->is($point['angle'])) echo 'table-warning' ?>">
                <?=number_format($point['angle'], 2)?>&deg;
            </td>
            <td class="text-right">
                <?=number_format($point['battery'], 2)?> V
            </td>
            <td class="text-right">
                <?=number_format($point['gravity'], 2)?> &deg;P
            </td>
            <td class="text-right">
                <?=number_format($point['trubidity'], 0)?>
            </td>
            <td class="text-right">
                <a href="/ui/data/delete/<?=$optimus->encode((int)$point['id'])?>" class="close"><span aria-hidden="true">&times;</span></a>
            </td>
        </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
