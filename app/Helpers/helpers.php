<?php

if (! function_exists('is_tenant')) {
    /**
     * Check if the current context is a tenant.
     */
    function is_tenant(): bool
    {
        return tenant() !== null;
    }
}

if (! function_exists('is_central')) {
    /**
     * Check if the current context is the central app.
     */
    function is_central(): bool
    {
        return tenant() === null;
    }
}
