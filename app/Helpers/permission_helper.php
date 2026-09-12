<?php

if (! function_exists('permission_matrix')) {
    function permission_matrix(): array
    {
        return [
            'admin' => [
                'admin_full_access',
                'view_trips',
                'view_trip_details',
                'create_trip_request',
                'view_ports',
                'view_ships',
                'download_my_trips',
            ],
            'user' => [
                'view_trips',
                'view_trip_details',
                'create_trip_request',
                'view_ports',
                'view_ships',
                'download_my_trips',
            ],
        ];
    }
}

if (! function_exists('user_permissions')) {
    function user_permissions(): array
    {
        $role = current_user_role();
        $matrix = permission_matrix();

        return $matrix[$role] ?? [];
    }
}

if (! function_exists('can')) {
    function can(string $permission): bool
    {
        $permission = trim($permission);

        if ($permission === '') {
            return false;
        }

        if (is_admin_user()) {
            return true;
        }

        return in_array($permission, user_permissions(), true);
    }
}
