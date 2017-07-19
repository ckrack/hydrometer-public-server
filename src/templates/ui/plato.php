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
            gravity: 'y2'
        },
        colors: {
            temperature: '#d9534f',
            trubidity: '#f0ad4e',
            gravity: '#0275d8',
            battery: '#5bc0de',
            angle: '#5cb85c'
        },
        hide: ['angle', 'battery', 'trubidity']
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
                text: '<?=_('Extract (°)')?>',
                position: 'outer-middle',
                fit: false
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
    chart.unload(['angle', 'battery', 'trubidity']);
</script>
