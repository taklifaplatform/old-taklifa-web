<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\Role;

class RoleRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Role::class;
    }
}
