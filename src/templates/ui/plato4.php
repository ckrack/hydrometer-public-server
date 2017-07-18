<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<h1 class="mt-4 mb-3">
    <?=_('Extract')?>
</h1>
<hr class="mb-3">

<div class="card">
    <div class="card-header">
        <?=$this->e($name)?>
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
              dens: 'y2'
            },
            hide: ['gravity', 'angle', 'trubidity', 'battery']
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
                    text: 'Temperature (°C)',
                    position: 'outer-middle',
                    fit: true
                }
            },
            y2: {
                label: {
                    text: 'Extrakt (°P)',
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
    chart.unload(['gravity', 'angle', 'trubidity', 'battery']);
</script>
