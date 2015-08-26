<?php

/**
 * ABAX WORKER STATUS PANEL
 *
 * This application interacts with the ABAX Worker API to make the life of the
 * portfolio employees easier, it gives an overview of inactive users, the amount
 * of users a customer has ordered and how many they have gotten delivered, a customer
 * list that the users can search and find a customer's URL quickly and easily.
 *
 * @author Helge Sverre <hehe@abax.no>
 * @version 1.0.0
 */

define("APP_VERSION", "1.0.0");

// Check for version compatibility with PHP
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    die("PHP Version 5.3.0 or later is required, your version is " . PHP_VERSION);
}

// If it doesn't exist, we have to install the application
if (!file_exists("../app/config.ini")) {

    // First we clear all sessions so we don't accidentally let someone with old session data login
    session_unset();

    // Then redirect to the install page
    header("Location: /install.php");
    die;
}


// Load the initialization script, setup routes and dependencies
require "../app/init.php";

// Run the application.
$app->run();