<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<h1 class="mt-4 mb-3"><?=_('Angle')?></h1>
<hr class="mb-3">

<div class="card">
    <div class="card-header">
    <?=$name?>
    </div>
    <div class="card-block">
        <div id="chart"></div>
    </div>
</div>

<script>
    var chart = c3.generate({
        bindto: '#chart',
        data: {
          x: 'time',
          xFormat: '%Y-%m-%d %H:%M',
          columns: [
<?php foreach ($data as $key => $value):
array_unshift($value, $key);
echo json_encode($value).',';
endforeach;?>
          ],
            axes: {
              temperature: 'y',
              angle: 'y2'
            },
            hide: ['gravity', 'trubidity', 'battery']
        },
        axis: {
            x: {
                label: {
                    text: 'Date',
                    position: 'inner-center'
                },
                type : 'timeseries',
                tick: {
                    format: '%Y-%m-%d %H:%M',
                    count: 100,
                    fit: true
                }
            },
            y: {
                label: {
                    text: '<?=_('Temperature (°C)')?>',
                    position: 'outer-middle',
                    fit: true
                }
            },
            y2: {
                label: {
                    text: '<?=_('Angle (°)')?>',
                    position: 'outer-middle',
                    fit: true
                },
                show: true
            }
        },
        grid: {
            y: {
                show: true
            }
        },
        zoom: {
            enabled: true
        }
    });
    chart.unload(['gravity', 'trubidity', 'battery']);
</script>
