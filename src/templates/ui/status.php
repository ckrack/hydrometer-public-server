<?php $this->layout('layouts/index.php', ['user' => (isset($user) ? $user : null)]) ?>

<h1 class="mt-4 mb-3">
    <?=_('Status')?>
</h1>
<hr class="mb-3">

<div class="card">
    <div class="card-header">
        <?=$this->e($name)?>
    </div>
    <div class="card-body">
        <div id="wrapper" style="display: grid; grid-template-columns: 1fr 1fr 1fr;">
            <div id="battery"></div>
            <div id="angle"></div>
            <div id="temperature"></div>
        </div>
    </div>
</div>
<script>
    var battery = c3.generate({
        bindto: '#battery',
        data: {
            columns: [
                ['voltage', <?=$battery?>],
            ],
            type: 'gauge'
        },
        gauge: {
            min: 2.7,
            max: 4.5,
            units: ' Voltage',
            width: 39
        },
        color: {
            pattern: ['#DF5353', '#DF5353', '#DDDF0D', '#55BF3B'], // the three color levels for the percentage values.
            threshold: {
    //            unit: 'value', // percentage is default
    //            max: 200,
                values: [2.7, 3.1, 3.5, 4.5]
            }
        },
        size: {
            height: 180
        }
    });

    var angle = c3.generate({
        bindto: '#angle',
        data: {
            columns: [
                ['angle', <?=$angle?>],
            ],
            type: 'gauge'
        },
        gauge: {
            label: {
                format: function(value, ratio) {
                    return Math.round(value)+'°';
                },
                //show: false // to turn off the min/max labels.
           },
            min: 0,
            max: 90,
            units: ' Angle',
            width: 39
        },
        color: {
            pattern: ['#DF5353', '#DF5353', '#DDDF0D', '55BF3B', '#DDDF0D', '#55BF3B'], // the three color levels for the percentage values.
            threshold: {
    //            unit: 'value', // percentage is default
    //            max: 200,
                values: [0, 15, 25, 70, 80, 90]
            }
        },
        size: {
            height: 180
        }
    });

    var temperature = c3.generate({
        bindto: '#temperature',
        data: {
            columns: [
                ['temperature', <?=$temperature?>],
            ],
            type: 'gauge'
        },
        gauge: {
            label: {
                format: function(value, ratio) {
                    return value.toFixed(2)+'°C';
                },
                //show: false // to turn off the min/max labels.
           },
            min: -2.5,
            max: 35.5,
            units: ' Temperature',
            width: 39
        },
        color: {
            pattern: ['#DF5353', '#DF5353', '#DDDF0D', '#55BF3B', '#DDDF0D', '#DF5353'], // the color levels for the percentage values.
            threshold: {
    //            unit: 'value', // percentage is default
    //            max: 200,
                values: [-2.5, 8, 14, 22, 26, 35.5]
            }
        },
        size: {
            height: 180
        }
    });

</script>



