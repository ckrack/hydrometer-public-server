<?php
// Application middleware

// try to translate for user
$app->add($app->getContainer()->get('App\Modules\Lang\UserLangMiddleware'));

// all requests check for session login (this imports user into request)
$app->add($app->getContainer()->get('App\Modules\Auth\Middleware\Session'));
