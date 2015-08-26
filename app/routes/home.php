<?php


$app->get("/", $authenticate(1), function () use ($app) {
    $app->render("home.twig");
})->name("home");