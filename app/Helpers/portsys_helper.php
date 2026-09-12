<?php

if (! function_exists('portsys_asset')) {
    function portsys_asset(string $name): string
    {
        return site_url('portsys-assets/' . rawurlencode($name));
    }
}

if (! function_exists('portsys_is_rtl')) {
    function portsys_is_rtl(): bool
    {
        return (string) service('request')->getLocale() === 'ar';
    }
}

if (! function_exists('portsys_text')) {
    /**
     * Backward-compatible public translation helper.
     *
     * Public interface strings live in the standard language files so every
     * visible label can be translated from one source of truth.
     */
    function portsys_text(string $key): string
    {
        $translationKey = 'App.public_' . $key;
        $translated = lang($translationKey);

        return $translated === $translationKey ? $key : $translated;
    }
}

if (! function_exists('format_datetime')) {
    function format_datetime(?string $value, string $fallback = '—'): string
    {
        if (! $value) {
            return $fallback;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return $value;
        }

        return date('Y-m-d H:i', $timestamp);
    }
}

if (! function_exists('human_time')) {
    function human_time(?string $value): string
    {
        if (! $value || strtotime($value) === false) {
            return lang('App.just_now');
        }

        $seconds = max(0, time() - strtotime($value));
        if ($seconds < 60) {
            return lang('App.just_now');
        }
        if ($seconds < 3600) {
            return sprintf(lang('App.minutes_ago'), (int) floor($seconds / 60));
        }
        if ($seconds < 86400) {
            return sprintf(lang('App.hours_ago'), (int) floor($seconds / 3600));
        }
        if ($seconds < 604800) {
            return sprintf(lang('App.days_ago'), (int) floor($seconds / 86400));
        }

        return format_datetime($value);
    }
}

if (! function_exists('request_status_label')) {
    function request_status_label(?string $status): string
    {
        $key = match ($status) {
            'approved' => 'request_status_approved',
            'rejected' => 'request_status_rejected',
            'cancelled' => 'request_status_cancelled',
            default => 'request_status_pending',
        };

        return lang('App.' . $key);
    }
}

if (! function_exists('request_status_class')) {
    function request_status_class(?string $status): string
    {
        return match ($status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            default => 'warning',
        };
    }
}

if (! function_exists('request_type_label')) {
    function request_type_label(?string $type): string
    {
        return $type === 'participation'
            ? lang('App.request_participation')
            : lang('App.request_booking');
    }
}

if (! function_exists('notification_target_url')) {
    function notification_target_url(array $notification): string
    {
        $type = (string) ($notification['related_type'] ?? '');
        $id = (int) ($notification['related_id'] ?? 0);

        if ($type === 'trip_request' && $id > 0) {
            return is_admin_user()
                ? site_url('trip-requests') . '#req-' . $id
                : site_url('trip-requests/my') . '#req-' . $id;
        }

        if ($type === 'trip' && $id > 0) {
            return site_url('trips/show/' . $id);
        }

        if ($type === 'contact_message' && $id > 0 && is_admin_user()) {
            return site_url('messages/' . $id);
        }

        return site_url('notifications');
    }
}

if (! function_exists('notification_icon')) {
    function notification_icon(?string $type): string
    {
        return match ($type) {
            'trip_request' => 'fa-clipboard-list',
            'request_status' => 'fa-circle-check',
            'security' => 'fa-shield-halved',
            'contact_message' => 'fa-envelope',
            default => 'fa-bell',
        };
    }
}

if (! function_exists('localized_month_year')) {
    function localized_month_year(int $timestamp): string
    {
        $month = (int) date('n', $timestamp);
        $monthNames = [
            1 => lang('App.month_january'),
            2 => lang('App.month_february'),
            3 => lang('App.month_march'),
            4 => lang('App.month_april'),
            5 => lang('App.month_may'),
            6 => lang('App.month_june'),
            7 => lang('App.month_july'),
            8 => lang('App.month_august'),
            9 => lang('App.month_september'),
            10 => lang('App.month_october'),
            11 => lang('App.month_november'),
            12 => lang('App.month_december'),
        ];

        return trim(($monthNames[$month] ?? date('F', $timestamp)) . ' ' . date('Y', $timestamp));
    }
}

if (! function_exists('activity_action_label')) {
    function activity_action_label(?string $action): string
    {
        $key = match ((string) $action) {
            'auth.registered' => 'activity_action_auth_registered',
            'auth.login' => 'activity_action_auth_login',
            'auth.logout' => 'activity_action_auth_logout',
            'profile.updated' => 'activity_action_profile_updated',
            'profile.password_changed' => 'activity_action_password_changed',
            'port.created' => 'activity_action_port_created',
            'port.updated' => 'activity_action_port_updated',
            'port.deleted' => 'activity_action_port_deleted',
            'ship.created' => 'activity_action_ship_created',
            'ship.updated' => 'activity_action_ship_updated',
            'ship.deleted' => 'activity_action_ship_deleted',
            'trip.created' => 'activity_action_trip_created',
            'trip.updated' => 'activity_action_trip_updated',
            'trip.deleted' => 'activity_action_trip_deleted',
            'place.created' => 'activity_action_place_created',
            'place.updated' => 'activity_action_place_updated',
            'place.deleted' => 'activity_action_place_deleted',
            'place.catalog_imported' => 'activity_action_place_catalog_imported',
            'trip_request.created' => 'activity_action_request_created',
            'trip_request.cancelled' => 'activity_action_request_cancelled',
            'trip_request.approved' => 'activity_action_request_approved',
            'trip_request.rejected' => 'activity_action_request_rejected',
            'message.received' => 'activity_action_message_received',
            'message.deleted' => 'activity_action_message_deleted',
            'user.role_updated' => 'activity_action_user_role_updated',
            'user.status_updated' => 'activity_action_user_status_updated',
            default => '',
        };

        if ($key !== '') {
            return lang('App.' . $key);
        }

        $fallback = trim(str_replace(['.', '_'], ' ', (string) $action));
        return $fallback !== '' ? mb_convert_case($fallback, MB_CASE_TITLE, 'UTF-8') : lang('App.unknown');
    }
}

if (! function_exists('activity_action_icon')) {
    function activity_action_icon(?string $action): string
    {
        return match (true) {
            str_starts_with((string) $action, 'auth.') => 'fa-right-to-bracket',
            str_starts_with((string) $action, 'profile.') => 'fa-user-shield',
            str_starts_with((string) $action, 'user.') => 'fa-users-gear',
            str_starts_with((string) $action, 'port.') => 'fa-anchor',
            str_starts_with((string) $action, 'ship.') => 'fa-ship',
            str_starts_with((string) $action, 'trip_request.') => 'fa-clipboard-check',
            str_starts_with((string) $action, 'trip.') => 'fa-route',
            str_starts_with((string) $action, 'place.') => 'fa-location-dot',
            str_starts_with((string) $action, 'message.') => 'fa-envelope',
            default => 'fa-clock-rotate-left',
        };
    }
}

if (! function_exists('entity_type_label')) {
    function entity_type_label(?string $type): string
    {
        $key = match ((string) $type) {
            'user' => 'entity_user',
            'port' => 'entity_port',
            'ship' => 'entity_ship',
            'trip' => 'entity_trip',
            'place' => 'entity_place',
            'trip_request' => 'entity_trip_request',
            'contact_message' => 'entity_contact_message',
            default => '',
        };

        return $key !== '' ? lang('App.' . $key) : lang('App.unknown');
    }
}

if (! function_exists('current_user_display_name')) {
    function current_user_display_name(): string
    {
        $name = trim((string) session()->get('userName'));
        if ($name !== '') {
            return $name;
        }

        $email = trim((string) session()->get('userEmail'));
        return $email !== '' ? $email : lang('App.user');
    }
}

if (! function_exists('portsys_current_path')) {
    /**
     * Returns the current application route without the base folder or
     * configured index page. This keeps navigation active states stable on
     * both clean URLs and XAMPP-style /index.php/... installations.
     */
    function portsys_current_path(): string
    {
        $appConfig = config('App');
        $path = trim((string) service('uri')->getPath(), '/');
        $basePath = trim((string) parse_url(site_url('/'), PHP_URL_PATH), '/');
        if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
            $path = ltrim(substr($path, strlen($basePath)), '/');
        }

        $indexPage = trim((string) $appConfig->indexPage, '/');
        if ($indexPage !== '' && ($path === $indexPage || str_starts_with($path, $indexPage . '/'))) {
            $path = ltrim(substr($path, strlen($indexPage)), '/');
        }

        return trim($path, '/');
    }
}

if (! function_exists('language_switch_url')) {
    /**
     * Builds a deterministic language-switch URL and preserves the current
     * local page without duplicating the configured base path or index.php.
     */
    function language_switch_url(string $locale): string
    {
        $appConfig = config('App');
        $supported = (array) $appConfig->supportedLocales;
        if (! in_array($locale, $supported, true)) {
            $locale = (string) $appConfig->defaultLocale;
        }

        $path = portsys_current_path();
        parse_str((string) ($_SERVER['QUERY_STRING'] ?? ''), $queryParams);
        unset($queryParams['return']);
        $query = http_build_query($queryParams);
        $return = ($path === '' ? '/' : $path) . ($query !== '' ? '?' . $query : '');

        return site_url('lang/' . rawurlencode($locale)) . '?return=' . rawurlencode($return);
    }
}

if (! function_exists('place_display_name')) {
    function place_display_name(array $place): string
    {
        $rtl = portsys_is_rtl();
        $preferred = trim((string) ($place[$rtl ? 'name_ar' : 'name_en'] ?? ''));
        if ($preferred !== '') {
            return $preferred;
        }

        return trim((string) ($place['name'] ?? ''));
    }
}

if (! function_exists('place_display_country')) {
    function place_display_country(array $place): string
    {
        $rtl = portsys_is_rtl();
        $preferred = trim((string) ($place[$rtl ? 'country_ar' : 'country_en'] ?? ''));
        if ($preferred !== '') {
            return $preferred;
        }

        return trim((string) ($place['country'] ?? ''));
    }
}

if (! function_exists('place_display_city')) {
    function place_display_city(array $place): string
    {
        $rtl = portsys_is_rtl();
        return trim((string) ($place[$rtl ? 'city_ar' : 'city_en'] ?? ''));
    }
}

if (! function_exists('place_type_label')) {
    function place_type_label(?string $type): string
    {
        $key = match ((string) $type) {
            'terminal' => 'location_type_terminal',
            'anchorage' => 'location_type_anchorage',
            'city' => 'location_type_city',
            'other' => 'location_type_other',
            default => 'location_type_port',
        };

        return lang('App.' . $key);
    }
}

if (! function_exists('place_status_label')) {
    function place_status_label(?string $status): string
    {
        return (string) $status === 'inactive' ? lang('App.inactive') : lang('App.active');
    }
}

if (! function_exists('place_map_url')) {
    function place_map_url(array $place): string
    {
        $lat = (float) ($place['latitude'] ?? 0);
        $lng = (float) ($place['longitude'] ?? 0);

        return 'https://www.openstreetmap.org/?mlat=' . rawurlencode((string) $lat)
            . '&mlon=' . rawurlencode((string) $lng)
            . '#map=11/' . rawurlencode((string) $lat) . '/' . rawurlencode((string) $lng);
    }
}
