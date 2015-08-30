<?php
/**
 * This file contains slim middleware (http://docs.slimframework.com/routing/middleware/)
 * They are executed before a slim route is fully executed, meaning we can use middleware
 * for authentication and checking permissions and allow/deny users on the fly.
 */

namespace Helge\Framework;

$authenticate = function ($role = 1) {
    return function () use ($role) {

        // Get the slim instance
        $app = \Slim\Slim::getInstance();

        // Fetch the userId
        $user = Session::get("user");

        // If we're not logged in
        if (!$user) {
            $app->flash("error", "Du er ikkje logget inn");
            $app->redirectTo("login");
        }


        // If we are not authenticated, redirect with an error
        if (!$app->auth->authForRole($user, $role)) {
            $app->flash("error", "Du har ikkje nok rettigheter");
            $app->redirectTo("home");
        }

        // else, do nothing and give the user access
    };
};