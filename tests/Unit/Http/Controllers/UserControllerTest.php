<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\EditUserRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\UserController;
use Symfony\Component\HttpFoundation\ParameterBag;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class UserControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $userMock;
    protected $roleMock;
    protected $courseMock;
    protected $controller;
    protected $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->userMock = Mockery::mock(UserRepositoryInterface::class)->makePartial();
        $this->roleMock = Mockery::mock(RoleRepositoryInterface::class)->makePartial();
        $this->courseMock = Mockery::mock(CourseRepositoryInterface::class)->makePartial();
        $this->controller = new UserController($this->userMock, $this->roleMock, $this->courseMock);
        $this->user = Mockery::mock(User::class)->makePartial();
    }

    public function tearDown() : void
    {
        Mockery::close();
        unset($this->controller);
        parent::tearDown();
    }

    public function test_index_returns_view()
    {
        $this->userMock->shouldReceive('getAll');
        $this->roleMock->shouldReceive('getAll');
        $this->courseMock->shouldReceive('getLatestCourses');
        $view = $this->controller->index();
        $this->assertEquals('users.admin.list', $view->getName());
        $this->assertArrayHasKey('users', $view->getData());
        $this->assertArrayHasKey('roles', $view->getData());
        $this->assertArrayHasKey('newCourses', $view->getData());
    }

    public function test_deleted_returns_view()
    {
        $this->userMock->shouldReceive('getDeletedUser');
        $this->roleMock->shouldReceive('getAll');
        $this->courseMock->shouldReceive('getLatestCourses');
        $view = $this->controller->deleted();
        $this->assertEquals('users.admin.restore', $view->getName());
        $this->assertArrayHasKey('users', $view->getData());
        $this->assertArrayHasKey('roles', $view->getData());
        $this->assertArrayHasKey('newCourses', $view->getData());
    }

    public function test_restore_user()
    {
        $userId = 10;
        $this->userMock->shouldReceive('restoreUser')->with($userId);
        $response = $this->controller->restore($userId);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('users.deleted'), $response->headers->get('Location'));
    }

    public function test_forcedelete_user()
    {
        $userId = 10;
        $this->userMock->shouldReceive('forceDeleteUser')->with($userId);
        $response = $this->controller->forceDelete($userId);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('users.deleted'), $response->headers->get('Location'));
        $this->assertEquals(trans('user.noti_delete'), $response->getSession()->get('message'));
    }

    public function test_edit_returns_view()
    {
        $user = factory(User::class, 1)->make();
        $id = 10;
        $this->roleMock->shouldReceive('getAll')->andReturn(new Collection);
        $this->courseMock->shouldReceive('getLatestCourses')->andReturn(new Collection);
        $this->userMock->shouldReceive('find')->with($id)->andReturn($user[0]);
        $view = $this->controller->edit($id);
        $this->assertEquals('users.admin.edit', $view->getName());
        $this->assertArrayHasKey('user', $view->getData());
        $this->assertArrayHasKey('roles', $view->getData());
        $this->assertArrayHasKey('newCourses', $view->getData());
    }

    public function test_create_user()
    {
        $data = [
            'name' => 'New name',
            'email' => 'e1@gmail.com',
            'password' => '12345678',
            'roles' => 4,
        ];
        $request  = new UserRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag($data));
        $this->userMock->shouldReceive('create')->withAnyArgs($data)->once()->andReturn(true);
        $response = $this->controller->store($request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('users.index'), $response->headers->get('Location'));
        $this->assertEquals(trans('user.noti_add'), $response->getSession()->get('message'));
    }

    public function test_destroy_user()
    {
        $user = factory(User::class, 1)->make();
        $id = 10;
        $this->userMock->shouldReceive('delete')->with($id)->andReturn(true);
        $response = $this->controller->destroy($id);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('users.index'), $response->headers->get('Location'));
        $this->assertEquals(trans('user.noti_delete'), $response->getSession()->get('message'));
    }

    public function test_update_user_when_change_password()
    {
        $dataUpdate = [
            'name' => 'New name',
            'email' => 'e1@gmail.com',
            'password' => '12345678',
            'roles' => [
                3,
                4,
            ],
        ];
        $request = new EditUserRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag($dataUpdate));
        $user = factory(User::class, 1)->make();
        $id = 10;
        $this->userMock->shouldReceive('find')->with($id)->andReturn($user[0]);
        $this->userMock->shouldReceive('updateUser');
        $response = $this->controller->update($request, $id);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('users.index'), $response->headers->get('Location'));
        $this->assertEquals(trans('user.noti_edit'), $response->getSession()->get('message'));
    }

    public function test_update_user_when_not_change_password()
    {
        $dataUpdate = [
            'name' => 'New name',
            'email' => 'e1@gmail.com',
            'roles' => [
                3,
                4,
            ],
        ];
        $request = new EditUserRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag($dataUpdate));
        $user = factory(User::class, 1)->make();
        $id = 10;
        $this->userMock->shouldReceive('find')->with($id)->andReturn($user[0]);
        $this->userMock->shouldReceive('updateUser');
        $response = $this->controller->update($request, $id);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('users.index'), $response->headers->get('Location'));
        $this->assertEquals(trans('user.noti_edit'), $response->getSession()->get('message'));
    }

    public function test_import_users()
    {
        $request = new Request();
        $request['file'] = new UploadedFile(
            '/',
            'document',
            'xlsx',
            100,
            TRUE,
        );
        $this->userMock->shouldReceive('import')->with($request);
        $response = $this->controller->import($request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('users.index'), $response->headers->get('Location'));
        $this->assertEquals(trans('user.noti_import'), $response->getSession()->get('message'));
    }
}
