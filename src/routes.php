<?php
// Routes

$app->get('[/]', 'App\Controller\Index:display');

//####### API
$app->post('/api', 'App\Controller\Api\DataPoint:post');
$app->get('/api/spindles/{spindle:[0-9]+}', 'App\Controller\Api\Spindle:details');
$app->get('/api/spindles', 'App\Controller\Api\Spindle:get');

$app->get('/api/data/{spindle:[0-9]+}', 'App\Controller\Api\DataPoint:get');
$app->get('/api/data', 'App\Controller\Api\DataPoint:get');

$app->get('/api/fermentations/{fermentation:[0-9]+}', 'App\Controller\Api\Fermentations:details');
$app->get('/api/fermentations', 'App\Controller\Api\Fermentations:get');
$app->post('/api/fermentations', 'App\Controller\Api\Fermentations:post');

$app->get('/api/calibrations/{calibration:[0-9]+}', 'App\Controller\Api\Calibrations:details');
$app->get('/api/calibrations', 'App\Controller\Api\Calibrations:get');
$app->post('/api/calibrations', 'App\Controller\Api\Calibrations:post');


//####### UI
$app->get('/status/{spindle:[0-9]+}', 'App\Controller\Status:display')->setName('status');
$app->get('/plato4/{spindle:[0-9]+}', 'App\Controller\Status:plato4')->setName('plato4');
$app->get('/plato/{spindle:[0-9]+}', 'App\Controller\Status:plato')->setName('plato');
$app->get('/angle/{spindle:[0-9]+}', 'App\Controller\Status:angle')->setName('angle');
$app->get('/battery/{spindle:[0-9]+}', 'App\Controller\Status:battery')->setName('battery');
