<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

// try to translate for user
$app->add($app->getContainer()->get('App\Modules\Lang\UserLangMiddleware'));

// all requests check for session login (this imports user into request)
$app->add($app->getContainer()->get('App\Modules\Auth\Middleware\Session'));
