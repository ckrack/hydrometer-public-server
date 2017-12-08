<?php

// Routes

//####### API

// this allows posting without auth, as the auth is in the token
$app->post('/api/ispindel/{token}', 'App\Controller\Api\DataPoint:post')->setName('api-post-spindle');

$app->post('/api/tilt/{token}', 'App\Controller\Api\DataPoint:post')->setName('api-post-tilt');

//####### oauth
$app->group('/auth', function () {
    $this->any('/init/{provider}', 'App\Controller\OAuth\OAuth:init');
    $this->any('/confirm/{provider}', 'App\Controller\OAuth\OAuth:confirm');
    $this->any('[/]', 'App\Controller\OAuth\Choices:display');
});

// these require a logged in user
$app->group('', function () {
    $this->any('/auth/success[/{register}]', 'App\Controller\OAuth\OAuth:success');
    $this->any('/auth/logout', 'App\Controller\OAuth\OAuth:logout');
})
// require a 'user' in $request that matches an App\Entity\User object
->add($app->getContainer()->get('App\Modules\Auth\Middleware\RequireLogin'));

//####### UI
$app->group('/ui', function () {
    $this->get('[/]', 'App\Controller\UI\Hydrometers:display');
    $this->get('/status/{hydrometer:[0-9]+}', 'App\Controller\UI\Status:display')->setName('status');
    $this->get('/plato/{hydrometer:[0-9]+}', 'App\Controller\UI\Status:plato')->setName('plato');
    $this->get('/angle/{hydrometer:[0-9]+}', 'App\Controller\UI\Status:angle')->setName('angle');
    $this->get('/battery/{hydrometer:[0-9]+}', 'App\Controller\UI\Status:battery')->setName('battery');
    $this->get('/data[/{hydrometer:[0-9]+}]', 'App\Controller\UI\DataPoints:display')->setName('datapoints');
    $this->any('/data/delete/{datapoint:[0-9]+}', 'App\Controller\UI\DataPoints:delete')->setName('data-delete');
    $this->any('/fermentations/add', 'App\Controller\UI\Fermentations:add')->setName('fermentations-add');
    $this->any('/fermentations/edit/{fermentation:[0-9]+}', 'App\Controller\UI\Fermentations:edit')->setName('fermentation-edit');
    $this->any('/fermentations/delete/{fermentation:[0-9]+}', 'App\Controller\UI\Fermentations:delete')->setName('fermentation-delete');
    $this->get('/fermentations/{fermentation:[0-9]+}', 'App\Controller\UI\Fermentations:details')->setName('fermentations-details');
    $this->get('/fermentations', 'App\Controller\UI\Fermentations:display')->setName('fermentations');
    $this->any('/hydrometers/add', 'App\Controller\UI\Hydrometers:add')->setName('hydrometer-add');
    $this->any('/hydrometers/edit/{hydrometer:[0-9]+}', 'App\Controller\UI\Hydrometers:edit')->setName('hydrometer-edit');
    $this->any('/hydrometers/help/{hydrometer:[0-9]+}', 'App\Controller\UI\Hydrometers:help')->setName('hydrometer-help');
})
// require a 'user' in $request that matches an App\Entity\User object
->add($app->getContainer()->get('App\Modules\Auth\Middleware\RequireLogin'))
// add CSRF
->add(new \Slim\Csrf\Guard);

// Pages
$app->get('/[{site}]', 'App\Controller\Index:display');

// Public fermentations
$app->get('/fermentations/public/{fermentation:[0-9]+}', 'App\Controller\UI\Fermentations:show')->setName('fermentations-show');
