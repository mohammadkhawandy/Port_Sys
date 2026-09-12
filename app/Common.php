<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('current_user_role')) {
	function current_user_role(): string
	{
		return (string) (session()->get('userRole') ?? 'user');
	}
}

if (! function_exists('is_admin_user')) {
	function is_admin_user(): bool
	{
		return current_user_role() === 'admin';
	}
}
