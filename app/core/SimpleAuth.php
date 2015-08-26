<?php

// TODO(26 aug 2015) ~ Helge: refactor

use Helge\Framework\Session;

$authenticate = function ($role = 1) {
    return function () use ($role) {

        $app = \Slim\Slim::getInstance();

        if (Session::get("user")) {

            $stmt = $app->db->prepare("SELECT role FROM users WHERE id = :userid");
            $stmt->execute(array("userid" => Session::get("user")));

            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ((int)$userInfo["role"] >= (int)$role) {
                return true;
            }
        }

        $app->flash("error", "Du er ikkje logget inn eller har ikkje nok rettigheter");
        $app->redirect("/login");
    };
};
