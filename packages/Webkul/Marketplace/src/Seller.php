<?php

namespace Webkul\Marketplace;

class Seller
{
    /**
     * Checks if seller allowed or not for certain action
     */
    public function hasPermission(string $permission): bool
    {
        $seller = auth()->guard('seller');

        if (! $seller->check()) {
            return false;
        }

        $user = $seller->user();

        if (is_null($user->parent_id)) {
            return true;
        }

        if ($user->role->permission_type === 'all') {
            return in_array($permission, array_column(config('marketplace_acl'), 'key'));
        }

        return $user->hasPermission($permission);
    }

    /**
     * Checks if user allowed or not for certain action
     */
    public static function allow(string $permission): void
    {
        if (
            ! auth()->guard('seller')->check()
            || ! auth()->guard('seller')->user()->hasPermission($permission)
        ) {
            abort(401, 'This action is unauthorized');
        }
    }
}
