<?php

namespace Tests\Unit\Repositories\Role;

use Tests\TestCase;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Role;
use DB;

class RoleRepositoryTest extends TestCase
{
    protected $roleRepo;

    public function setUp() :void
    {
        parent::setUp();
        $this->roleRepo = new RoleRepository();
    }

    public function tearDown() : void
    {
        unset($this->roleRepo);
        parent::tearDown();
    }

    public function test_update_success()
    {
        $id = 4;
        $role = $this->roleRepo->find($id);
        dd($role);
    }
}
