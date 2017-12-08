<?php
use Jenssegers\Date\Date;
use App\Modules\Formula\Formatter;

$this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)])
?>


<h1 class="mt-4 mb-3">
    <?=_('Available hydrometers')?>
    <a href="/ui/hydrometers/add" class="btn btn-success float-md-right"><?=_('Add hydrometer')?></a>
</h1>
<hr class="mb-3">

<?php if (!empty($hydrometers)) : ?>

<?php foreach ($hydrometers as $hydrometer) : ?>
    <div class="card mb-3">
        <h5 class="card-header">
            <?=$hydrometer['name']?>
            <div class="btn-group float-right">
                <a class="btn btn-secondary btn-sm" href="/ui/hydrometers/help/<?=$optimus->encode($hydrometer['id'])?>"><?=_('setup')?></a>
                <a class="btn btn-secondary btn-sm" href="/ui/hydrometers/edit/<?=$optimus->encode($hydrometer['id'])?>"><?=_('edit')?></a>
            </div>
        </h5>
        <div class="card-body">
        <?php if (!empty($hydrometer['activity'])) : ?>
            <div class="row">
            <?php if (!empty($hydrometer['battery'])) : ?>
                <div class="col-sm">
                    <div id="battery<?=$this->e($hydrometer['id'])?>"></div>
                </div>
            <?php endif;?>

            <?php if (!empty($hydrometer['angle'])) : ?>
                <div class="col-sm">
                    <div id="angle<?=$this->e($hydrometer['id'])?>"></div>
                </div>
            <?php endif;?>

            <?php if (!empty($hydrometer['gravity'])) : ?>
                <div class="col-sm">
                    <div id="gravity<?=$this->e($hydrometer['id'])?>"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($hydrometer['temperature'])) : ?>
                <div class="col-sm">
                    <div id="temperature<?=$this->e($hydrometer['id'])?>"></div>
                </div>
            <?php endif; ?>
            </div>

            <script>
                var battery = c3.generate({
                    bindto: '#battery'+'<?=$this->e($hydrometer['id'])?>',
                    data: {
                        columns: [
                            ['voltage', <?=$hydrometer['battery']?>],
                        ],
                        type: 'gauge'
                    },
                    gauge: {
                        min: <?=min(Formatter::treshold('V'))?>,
                        max: <?=max(Formatter::treshold('V'))?>,
                        units: '<?=_('Voltage')?>'
                    },
                    color: {
                        pattern: ['#DF5353', '#DF5353', '#DDDF0D', '#55BF3B'], // the color levels for the percentage values.
                        threshold: {
                            unit: 'value', // percentage is default
                            values: <?=json_encode(Formatter::treshold('V'))?>
                        }
                    },
                    tooltip: {
                        format: {
                            value: function(value) {
                                return d3.format(',.<?=Formatter::roundTo('V')?>f')(value)+'V';
                            }
                        }
                    },
                    size: {
                        height: 180
                    }
                });

                var angle = c3.generate({
                    bindto: '#angle'+'<?=$this->e($hydrometer['id'])?>',
                    data: {
                        columns: [
                            ['angle', <?=$hydrometer['angle']?>],
                        ],
                        type: 'gauge'
                    },
                    gauge: {
                        label: {
                            format: function(value, ratio) {
                                return Math.round(value)+'°';
                            },
                       },
                        min: 0,
                        max: 90,
                        units: '<?=_('Angle')?>'
                    },
                    color: {
                        pattern: ['#DF5353', '#DF5353', '#DDDF0D', '55BF3B', '#DDDF0D', '#55BF3B'], // the color levels for the percentage values.
                        threshold: {
                            unit: 'value', // percentage is default
                            values: [0, 15, 25, 70, 80, 90]
                        }
                    },
                    tooltip: {
                        format: {
                            value: function(value) {
                                return d3.format(',.2f')(value)+'°';
                            }
                        }
                    },
                    size: {
                        height: 180
                    }
                });

                var gravity = c3.generate({
                    bindto: '#gravity'+'<?=$this->e($hydrometer['id'])?>',
                    data: {
                        columns: [
                            ['gravity', <?=$hydrometer['gravity']?>],
                        ],
                        type: 'gauge'
                    },
                    gauge: {
                        label: {
                            format: function(value, ratio) {
                                return value.toFixed(<?=Formatter::roundTo($hydrometer['metricGravity'])?>)+'<?=$hydrometer['metricGravity']?>';
                            },
                       },
                        min: <?=min(Formatter::treshold($hydrometer['metricGravity']))?>,
                        max: <?=round($hydrometer['max_gravity'], Formatter::roundTo($hydrometer['metricGravity']))?>,
                        units: '<?=_('Gravity')?>'
                    },
                    color: {
                        pattern: ['#55BF3B', '#55BF3B', '#DDDF0D', '#DDDF0D', '#DF5353', '#DF5353'], // the color levels for the percentage values.
                        threshold: {
                            unit: 'value', // percentage is default
                            values: <?=json_encode(Formatter::treshold($hydrometer['metricGravity']))?>,
                        }
                    },
                    tooltip: {
                        format: {
                            value: function(value) {
                                return d3.format(',.<?=Formatter::roundTo($hydrometer['metricGravity'])?>f')(value)+'<?=$hydrometer['metricGravity']?>';
                            }
                        }
                    },
                    size: {
                        height: 180
                    }
                });

                var temperature = c3.generate({
                    bindto: '#temperature'+'<?=$this->e($hydrometer['id'])?>',
                    data: {
                        columns: [
                            ['temperature', <?=$hydrometer['temperature']?>],
                        ],
                        type: 'gauge'
                    },
                    gauge: {
                        label: {
                            format: function(value, ratio) {
                                return value.toFixed(<?=Formatter::roundTo($hydrometer['metricTemperature'])?>)+'<?=$hydrometer['metricTemperature']?>';
                            },
                       },
                        min: <?=min(Formatter::treshold($hydrometer['metricTemperature']))?>,
                        max: <?=max(Formatter::treshold($hydrometer['metricTemperature']))?>,
                        units: '<?=_('Temperature')?>'
                    },
                    color: {
                        pattern: ['#DF5353', '#DF5353', '#DDDF0D', '#55BF3B', '#DDDF0D', '#DF5353'], // the color levels for the percentage values.
                        threshold: {
                            unit: 'value', // percentage is default
                            values: <?=json_encode(Formatter::treshold($hydrometer['metricTemperature']))?>
                        }
                    },
                    tooltip: {
                        format: {
                            value: function(value) {
                                return d3.format(',.<?=Formatter::roundTo($hydrometer['metricTemperature'])?>f')(value)+'<?=$hydrometer['metricTemperature']?>';
                            }
                        }
                    },
                    size: {
                        height: 180
                    }
                });

            </script>
        <?php else: ?>
            <?=_('No data yet')?>
        <?php endif; ?>
        </div>
        <div class="card-footer">
            <?php if (!empty($hydrometer['activity'])) : ?>
                <small class="card-subtitle text-muted">
                    <?=_('Last activity:')?> <?=Date::parse($hydrometer['activity'])->diffForHumans()?>
                </small>
            <?php endif; ?>

            <a class="card-link float-right" href="/ui/data/<?=$optimus->encode($hydrometer['id'])?>"><?=_('Datapoints')?></a>
        </div>
    </div>
<?php endforeach; ?>
<?php else: ?>
    <div class="jumbotron jumbotron-fluid">
      <div class="container">
        <p class="lead">
            <?=_('To start collecting fermentation data, you need to add and setup your first device.')?>
        </p>
        <p>
            <?=_('Start by adding it here, then set it up with the token generated by the system.')?>
        </p>
        <hr class="my-4">
        <a class="btn btn-primary btn-lg ml-auto mr-0" href="/ui/hydrometers/add" role="button"><?=_('Add a hydrometer')?></a>
      </div>
    </div>
<?php endif; ?>
