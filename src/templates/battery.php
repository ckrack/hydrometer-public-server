<?php $this->layout('layouts/index.php') ?>

<h1 class="mt-4 mb-3">
    <?=_('Extract')?>
</h1>

<div class="card">
    <div class="card-header">
        <?=$this->e($name)?>
    </div>
    <div class="card-block">
        <div id="battery"></div>
    </div>
</div>

<script>
    var chart = c3.generate({
        bindto: '#battery',
        data: {
            columns: [
                ['voltage', <?=$battery?>],
            ],
            type: 'gauge'
        },
        gauge: {
            label: {
                // format: function(value, ratio) {
                //     return Math.round(value, 2, 2);
                // },
                //show: false // to turn off the min/max labels.
           },
            min: 2.7,
            max: 4.5,
            units: ' Voltage',
            width: 39
        },
        color: {
            pattern: ['#DF5353', '#DF5353', '#DDDF0D', '#55BF3B'], // the color levels for the percentage values.
            threshold: {
                values: [2.7, 3.1, 3.5, 4.5]
            }
        },
        size: {
            height: 180
        }
    });

</script>



