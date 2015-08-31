<?php

namespace Helge\Framework;

/**
 * Class Session
 * Responsible for dealing with setting, getting and deleting of session variables
 */
class Session
{

    /**
     * Starts the session via session_start
     */
    public static function start()
    {
        session_start();
    }


    /**
     * Gets or sets the cache limiter option for the session
     *
     * Valid options:
     *      - nocache: Disallows any client/proxy from caching
     *      - public: Allows caching by proxy and client
     *      - private: Disallows caching by proxies and allows the client to cache the contents.
     *
     * Setting the cache limiter to '' will turn off automatic sending of cache headers.
     * @option string $cache_limiter the option for the cache limiter
     */
    public static function cacheLimiter($cache_limiter = false)
    {
        if ($cache_limiter === false)
            session_cache_limiter();
        else
            session_cache_limiter($cache_limiter);
    }


    /**
     * Sets a session variable with key of $key and value of $value
     * @param string $key
     * @param string $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Returns the value stored in the session variable $key or null if that key does not exist
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * @param string $key deletes the session variable with key of $key
     */
    public static function delete($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Clear all data in any session variable
     */
    public static function clear()
    {
        session_unset();
    }
}