<?php

/**
 * nav_helper.php
 *
 * CodeIgniter 4 helper for navbar active-state detection.
 * Loaded automatically via app/Config/Autoload.php $helpers array.
 */

if (! function_exists('get_nav_class')) {
    /**
     * Returns the full Tailwind CSS class string for a navigation link,
     * applying active styling when the current URL matches the given path.
     *
     * @param  string $path  The URL segment(s) relative to base_url (e.g. 'dashboard', 'menu-rap')
     * @return string
     */
    function get_nav_class(string $path): string
    {
        $base     = 'p-2 md:w-28 md:justify-center flex items-center text-sm'
                  . ' focus:outline-hidden md:rounded-none transition-colors duration-200';
        $active   = 'bg-white text-gray-900 font-semibold';
        $inactive = 'text-navbar-foreground hover:bg-navbar-hover focus:bg-navbar-focus';

        return $base . ' ' . (is_nav_active($path) ? $active : $inactive);
    }
}

if (! function_exists('is_nav_active')) {
    /**
     * Returns true when the current request URL matches the given path.
     * Performs an exact match for the root ('/') and a prefix match for all other paths.
     *
     * @param  string $path  The URL segment(s) relative to base_url (e.g. 'dashboard', 'menu-rap')
     * @return bool
     */
    function is_nav_active(string $path): bool
    {
        $linkUrl = rtrim(base_url($path), '/');
        $currUrl = rtrim(str_replace('/index.php', '', current_url()), '/');

        if ($path === '' || $path === '/') {
            return $linkUrl === $currUrl;
        }

        return $linkUrl === $currUrl
            || str_starts_with($currUrl . '/', $linkUrl . '/');
    }
}
