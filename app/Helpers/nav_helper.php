<?php

if (! function_exists('get_nav_class')) {
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
    function is_nav_active(string $path): bool
    {
        $linkUrl = rtrim(base_url($path), '/');
        $currUrl = rtrim(str_replace('/index.php', '', current_url()), '/');

        if ($path === '' || $path === '/') {
            return $linkUrl === $currUrl;
        }

        if ($path === 'dashboard') {
            return str_contains($currUrl, '/dashboard') && !str_contains($currUrl, '/gudang/dashboard');
        }

        if ($path === 'gudang/dashboard') {
            $baseGudang = rtrim(base_url('gudang'), '/');
            return str_contains($currUrl, '/gudang/dashboard') || $currUrl === $baseGudang;
        }

        if ($path === 'gudang/permintaan') {
            $isDeviasiGudang = str_contains($currUrl, '/permintaan/deviasi') && isset($_GET['source']) && $_GET['source'] === 'gudang';
            return str_contains($currUrl, '/gudang/permintaan') || $isDeviasiGudang;
        }

        if ($path === 'menu-rap') {
            $baseProyek = rtrim(base_url('proyek'), '/');
            return (str_starts_with($currUrl . '/', $linkUrl . '/')
                || str_starts_with($currUrl . '/', $baseProyek . '/')
                || str_contains($currUrl, '/proyek/menu/'))
                && !str_contains($currUrl, '/schedule')
                && !str_contains($currUrl, '/realisasi')
                && !str_contains($currUrl, '/permintaan');
        }

        return $linkUrl === $currUrl
            || str_starts_with($currUrl . '/', $linkUrl . '/');
    }
}
