<?php

require '../vendor/autoload.php';
require 'core/Middleware.php';

use Noodlehaus\Config;
use Helge\Framework\Session;
use Helge\Framework\Authentication;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Bridge\Twig\Extension\TranslationExtension;

// Start session
Session::start();
Session::cacheLimiter("nocache");

// Instantiate and setup Slim application instance
$app = new \Slim\Slim(array(
    // Views
    'view' => new \Slim\Views\Twig(),
    'templates.path' => '../app/views',

    // Cookies
    'cookies.encrypt' => true,
    'cookies.secret_key' => "pCAyyIHcGmZ9kT1yzsA8YuhNt64oNPvSOBHHUhQYuamRjYKFrQujaLMjTlhS", // TODO: Change this
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC,

    // Logging
    'log.enabled' => true,
    'log.level' => \Slim\Log::WARN,
    'log.writer' => new \Slim\Logger\DateTimeFileWriter(array(
        'path' => "../log/"
    ))
));

// Load the ini config file
$app->container->set("config", Config::load('../app/config.ini'));

// Set the default timezone from config
date_default_timezone_set($app->config->get("localization.timezone"));

// If debugging is enabled we show all errors
if ($app->config->get("development.debugging")) {
    error_reporting(E_ALL);
    ini_set("display_errors", "on");
} else {
    ini_set("display_errors", "off");
}


// Create a translator instance
$app->container->set("translator", new Translator($app->config->get("localization.language"), new MessageSelector()));
$app->translator->setFallbackLocales(['nb_NO']);
$app->translator->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());

// Include require all route files in the routes directory
$languages = glob("../app/lang/*.php");

if ($languages) {
    foreach ($languages as $language) {

        // Extract the language code from the language files
        $lastIndex = strrpos($language, DIRECTORY_SEPARATOR);
        $lang_code = trim(trim(substr($language, $lastIndex), DIRECTORY_SEPARATOR), ".php");

        // Add the language file to the translator
        $app->translator->addResource('php', $language, $lang_code);
    }
}

// Add parser extensions
$view = $app->view();
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new \Twig_Extension_Debug(),
    new TranslationExtension($app->translator)
);

// Set twig caching options
$view->parserOptions = array(
    'debug' => $app->config->get("development.debugging"),
    'cache' => '../cache/views'
);


// Inject the session super global into the views so we can access it.
$twig = $app->view->getEnvironment();
$twig->addGlobal("session", $_SESSION);


// Setup the database connection using the Database class
$app->container->set("db",
    new Helge\Framework\Database(
        $app->config->get("database.host"),
        $app->config->get("database.name"),
        $app->config->get("database.user"),
        $app->config->get("database.pass"),
        $app->config->get("database.charset")
    )
);


// Initialize our authentication class
$app->container->set("auth", function () use ($app) {
    return new Authentication($app->db);
});


// Include require all route files in the routes directory
$routes = glob("../app/routes/*.php");
if ($routes) {
    foreach ($routes as $route) {
        require $route;
    }
}
