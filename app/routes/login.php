<?php

use Helge\Framework\Session;

$app->map('/login', function () use ($app) {
    $username = null;

    if ($app->request()->isPost()) {
        $username = $app->request->post('username');
        $password = $app->request->post('password');

        $password = hash("sha512", $password);

        $stmt = $app->db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->execute(array(
            "username" => $username,
            "password" => $password
        ));

        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount()) {

            Session::set("user", $userInfo);

            $app->flash("info", $app->translator->trans("logged_in"));
            $app->redirect("/");
        } else {
            $app->flashNow("error", $app->translator->trans("wrong_username_or_password"));
        }
    }

    $app->render('login.twig', array('username' => $username));

})->via('GET', 'POST')->name('login');


$app->get('/logout', function () use ($app) {

    // Clear session values
    Session::clear();

    $app->flash("info", $app->translator->trans("logged_out"));
    $app->redirectTo('login');

})->name("logout");


