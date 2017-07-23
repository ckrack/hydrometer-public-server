<?php

// Routes

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

//####### auth
$app->group('/auth', function () {
    $this->get('/register/{ids}/{token}', 'App\Controller\Auth\Register:token')->setName('auth-register-token');
    $this->get('/register', 'App\Controller\Auth\Register:form');
    $this->post('/register', 'App\Controller\Auth\Register:post');

    $this->any('/login/{ids}/{token}', 'App\Controller\Auth\Login:token')->setName('auth-login-token');
    $this->get('/login', 'App\Controller\Auth\Login:form');
    $this->post('/login', 'App\Controller\Auth\Login:post');
});

// these require a logged in user
$app->group('', function () {
    $this->get('/auth/register/success', 'App\Controller\Auth\Register:success');
    $this->any('/auth/success', 'App\Controller\Auth\Login:success');
    $this->any('/auth/logout', 'App\Controller\Auth\Login:logout');
})
// require a 'user' in $request that matches an App\Entity\User object
->add($app->getContainer()->get('App\Modules\Auth\Middleware\RequireLogin'))
// look for userId in session
#->add($app->getContainer()->get('App\Modules\Auth\Middleware\Session'))
;

//####### UI
$app->group('/ui', function () {
    $this->get('[/]', 'App\Controller\UI\Index:display');
    $this->get('/status/{spindle:[0-9]+}', 'App\Controller\UI\Status:display')->setName('status');
    $this->get('/plato/{spindle:[0-9]+}', 'App\Controller\UI\Status:plato')->setName('plato');
    $this->get('/angle/{spindle:[0-9]+}', 'App\Controller\UI\Status:angle')->setName('angle');
    $this->get('/battery/{spindle:[0-9]+}', 'App\Controller\UI\Status:battery')->setName('battery');
    $this->get('/data[/{spindle:[0-9]+}]', 'App\Controller\UI\DataPoints:display')->setName('datapoints');
    $this->any('/fermentations/add', 'App\Controller\UI\Fermentations:add')->setName('fermentations-add');
    $this->get('/fermentations/{fermentation:[0-9]+}', 'App\Controller\UI\Fermentations:details')->setName('fermentations-details');
    $this->get('/fermentations', 'App\Controller\UI\Fermentations:display')->setName('fermentations');
    $this->get('/spindles/add', 'App\Controller\UI\Spindles:token')->setName('spindle-add-token');
})
// require a 'user' in $request that matches an App\Entity\User object
->add($app->getContainer()->get('App\Modules\Auth\Middleware\RequireLogin'))
// look for a cookie token
->add($app->getContainer()->get('App\Modules\Auth\Middleware\Cookie'))
// look for userId in session
#->add($app->getContainer()->get('App\Modules\Auth\Middleware\Session'))
;

$app->get('/[{site}]', 'App\Controller\Index:display');


// all requests check for session login
$app->add($app->getContainer()->get('App\Modules\Auth\Middleware\Session'));
