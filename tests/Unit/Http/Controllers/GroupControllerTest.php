<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use App\Models\Course;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\EditGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Group;
use App\Models\User;
use App\Repositories\Group\GroupRepositoryInterface;
use App\Http\Controllers\GroupController;
use App\Repositories\Group\GroupRepository;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use App\Http\Requests\AddUserRequest;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GroupControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $groupMock;
    protected $userMock;
    protected $courseMock;
    protected $groupController;

    protected function setUp() : void
    {
        parent::setUp();
        $this->groupMock = Mockery::mock(GroupRepositoryInterface::class)->makePartial();
        $this->userMock = Mockery::mock(UserRepositoryInterface::class)->makePartial();
        $this->courseMock = Mockery::mock(CourseRepositoryInterface::class)->makePartial();
        $this->groupController = new GroupController($this->groupMock, $this->userMock, $this->courseMock);
    }

    public function tearDown() : void
    {
        Mockery::close();
        unset($this->groupController);
        parent::tearDown();
    }

    public function test_create_group_when_is_not_admin()
    {
        $id = 1;
        $request = new GroupRequest();
        $data = [
            'name' => $request->name_group,
            'course_id' => $id,
        ];
        $this->groupMock->shouldReceive('create')->with($data);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->store($id, $request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.courseDetail', $id), $response->headers->get('Location'));
        $this->assertEquals(trans('group.add_noti'), $response->getSession()->get('message'));
    }

    public function test_create_group_when_is_admin()
    {
        $id = 1;
        $request = new GroupRequest();
        $data = [
            'name' => $request->name_group,
            'course_id' => $id,
        ];
        $this->groupMock->shouldReceive('create')->with($data);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->store($id, $request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('courses.show', $id), $response->headers->get('Location'));
        $this->assertEquals(trans('group.add_noti'), $response->getSession()->get('message'));
    }

    public function test_get_users_has_no_group()
    {
        $idGroup = 2;
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->with($idGroup)->andReturn($group);
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->with($group)->andReturn($groupIds);
        $userIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->with($group)->andReturn($userIds);
        $this->userMock->shouldReceive('getUsersNoGroup')->with($userIds, $groupIds)->andReturn(new Collection);

        $result = $this->groupController->getUsersHasNoGroup($idGroup);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_add_users_to_group_with_admin_fail_when_add_user_is_admin()
    {
        $userIds = [
            5,
            6,
            7,
        ];
        $request = Mockery::mock(AddUserRequest::class)->makePartial();
        $request->shouldReceive('all')
            ->andReturn(['user_id' => $userIds]);
        $idGroup = 1;
        //get all user, that can add to group
        $users = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getUsersToAddGroup')->with($userIds)->once()->andReturn($users);
        //return $group by id
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        //get collection of groupId
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->andReturn($groupIds);
        //get collection of userId
        $userId = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->andReturn($userId);
        //get all user has no group
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($users);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'admin')->once()->andReturn(true);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'lecturer')->once()->andReturn(false);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->addUserToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('groups.show', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('course.permission_student'), $response->getSession()->get('errors')->getMessages()['user_id'][0]);
    }

    public function test_add_users_to_group_with_admin_fail_when_add_user_is_lecturer()
    {
        $userIds = [
            5,
            6,
            7,
        ];
        $request = Mockery::mock(AddUserRequest::class)->makePartial();
        $request->shouldReceive('all')
            ->andReturn(['user_id' => $userIds]);
        $idGroup = 1;
        //get all user, that can add to group
        $users = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getUsersToAddGroup')->with($userIds)->once()->andReturn($users);
        //return $group by id
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        //get collection of groupId
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->andReturn($groupIds);
        //get collection of userId
        $userId = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->andReturn($userId);
        //get all user has no group
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($users);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'lecturer')->andReturn(true);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'admin')->andReturn(false);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->addUserToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('groups.show', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('course.permission_student'), $response->getSession()->get('errors')->getMessages()['user_id'][0]);
    }

    public function test_add_users_to_group_with_lecturer_fail_when_add_user_is_admin()
    {
        $userIds = [
            5,
            6,
            7,
        ];
        $request = Mockery::mock(AddUserRequest::class)->makePartial();
        $request->shouldReceive('all')
            ->andReturn(['user_id' => $userIds]);
        $idGroup = 1;
        //get all user, that can add to group
        $users = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getUsersToAddGroup')->with($userIds)->once()->andReturn($users);
        //return $group by id
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        //get collection of groupId
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->andReturn($groupIds);
        //get collection of userId
        $userId = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->andReturn($userId);
        //get all user has no group
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($users);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'lecturer')->andReturn(false);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'admin')->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->addUserToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.groupDetail', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('course.permission_student'), $response->getSession()->get('errors')->getMessages()['user_id'][0]);
    }

    public function test_add_users_to_group_with_lecturer_fail_when_add_user_is_lecturer()
    {
        $userIds = [
            5,
            6,
            7,
        ];
        $request = Mockery::mock(AddUserRequest::class)->makePartial();
        $request->shouldReceive('all')
            ->andReturn(['user_id' => $userIds]);
        $idGroup = 1;
        //get all user, that can add to group
        $users = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getUsersToAddGroup')->with($userIds)->once()->andReturn($users);
        //return $group by id
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        //get collection of groupId
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->andReturn($groupIds);
        //get collection of userId
        $userId = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->andReturn($userId);
        //get all user has no group
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($users);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'lecturer')->andReturn(true);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'admin')->andReturn(false);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->addUserToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.groupDetail', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('course.permission_student'), $response->getSession()->get('errors')->getMessages()['user_id'][0]);
    }

    public function test_add_users_to_group_with_admin_fail_when_users_belong_to_orther_group()
    {
        $userIds = [
            5,
            6,
            7,
        ];
        $request = Mockery::mock(AddUserRequest::class)->makePartial();
        $request->shouldReceive('all')
            ->andReturn(['user_id' => $userIds]);
        $idGroup = 1;
        //get all user, that can add to group
        $users = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getUsersToAddGroup')->with($userIds)->once()->andReturn($users);
        //return $group by id
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        //get collection of groupId
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->andReturn($groupIds);
        //get collection of userId
        $userId = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->andReturn($userId);
        //get all user has no group
        $usernogroup = factory(User::class, 2)->make();
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($usernogroup);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'lecturer')->andReturn(false);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'admin')->andReturn(false);
        $users[0]->id = 100;
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->addUserToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('groups.show', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('course.invalid'), $response->getSession()->get('errors')->getMessages()['user_id'][0]);
    }

    public function test_add_users_to_group_with_role_lecturer_fail_when_users_belong_to_orther_group()
    {
        $userIds = [
            5,
            6,
            7,
        ];
        $request = Mockery::mock(AddUserRequest::class)->makePartial();
        $request->shouldReceive('all')
            ->andReturn(['user_id' => $userIds]);
        $idGroup = 1;
        //get all user, that can add to group
        $users = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getUsersToAddGroup')->with($userIds)->once()->andReturn($users);
        //return $group by id
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        //get collection of groupId
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->andReturn($groupIds);
        //get collection of userId
        $userId = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->andReturn($userId);
        //get all user has no group
        $usernogroup = factory(User::class, 2)->make();
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($usernogroup);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'lecturer')->andReturn(false);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'admin')->andReturn(false);
        $users[0]->id = 100;
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->addUserToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.groupDetail', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('course.invalid'), $response->getSession()->get('errors')->getMessages()['user_id'][0]);
    }

    public function test_add_users_to_group_success_with_admin()
    {
        $userIds = [
            5,
            6,
            7,
        ];
        $request = Mockery::mock(AddUserRequest::class)->makePartial();
        $request->shouldReceive('all')
            ->andReturn(['user_id' => $userIds]);
        $idGroup = 1;
        //get all user, that can add to group
        $users = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getUsersToAddGroup')->with($userIds)->once()->andReturn($users);
        //return $group by id
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        //get collection of groupId
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->andReturn($groupIds);
        //get collection of userId
        $userId = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->andReturn($userId);
        //get all user has no group
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($users);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'lecturer')->andReturn(false);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'admin')->andReturn(false);
        $this->groupMock->shouldReceive('addUsersToGroup')->with($group, $userIds)->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->addUserToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('groups.show', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('group.noti_addUser'), $response->getSession()->get('message'));
    }

    public function test_add_users_to_group_success_with_lecturer()
    {
        $userIds = [
            5,
            6,
            7,
        ];
        $request = Mockery::mock(AddUserRequest::class)->makePartial();
        $request->shouldReceive('all')
            ->andReturn(['user_id' => $userIds]);
        $idGroup = 1;
        //get all user, that can add to group
        $users = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getUsersToAddGroup')->with($userIds)->once()->andReturn($users);
        //return $group by id
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        //get collection of groupId
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->andReturn($groupIds);
        //get collection of userId
        $userId = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->andReturn($userId);
        //get all user has no group
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($users);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'lecturer')->andReturn(false);
        $this->userMock->shouldReceive('checkRoleForUser')->with($users[0], 'admin')->andReturn(false);
        $this->groupMock->shouldReceive('addUsersToGroup')->with($group, $userIds)->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->addUserToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.groupDetail', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('group.noti_addUser'), $response->getSession()->get('message'));
    }

    public function test_add_leader_to_group_with_role_admin()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $leaderId = 10;
        $request->shouldReceive('all')->andReturn(['leader' => $leaderId]);
        $idGroup = 1;
        $group = factory(Group::class, 1);
        $this->groupMock->shouldReceive('find')->with($idGroup)->andReturn($group);
        $this->userMock->shouldReceive('addLeader')->with($group, $leaderId)->once()->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->addLeaderToGroup($request, $idGroup);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('groups.show', $idGroup), $response->headers->get('Location'));
        $this->assertEquals(trans('group.noti_addLeader'), $response->getSession()->get('message'));
    }

    public function test_add_leader_to_group_with_role_lecturer()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $leaderId = 10;
        $request->shouldReceive('all')->andReturn(['leader' => $leaderId]);
        $id = 100;
        $group = factory(Group::class, 1)->make();
        $this->groupMock->shouldReceive('find')->with($id)->andReturn($group);
        $this->userMock->shouldReceive('addLeader')->with($group, $leaderId)->once()->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->addLeaderToGroup($request, $id);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.groupDetail', $id), $response->headers->get('Location'));
        $this->assertEquals(trans('group.noti_addLeader'), $response->getSession()->get('message'));
    }

    public function test_delete_user_from_group_with_role_admin()
    {
        $group = factory(Group::class, 1)->make();
        $groupId = 1;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        $user = factory(User::class, 1)->make();
        $userId = 10;
        $this->userMock->shouldReceive('find')->andReturn($user);
        $this->groupMock->shouldReceive('deleteUser')->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->deleteUserFromGroup($groupId, $userId);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('groups.show', $groupId), $response->headers->get('Location'));
        $this->assertEquals(trans('group.noti_deleteUser'), $response->getSession()->get('message'));
    }

    public function test_delete_user_from_group_with_role_lecturer()
    {
        $group = factory(Group::class, 1)->make();
        $groupId = 1;
        $this->groupMock->shouldReceive('find')->andReturn($group);
        $user = factory(User::class, 1)->make();
        $userId = 10;
        $this->userMock->shouldReceive('find')->andReturn($user);
        $this->groupMock->shouldReceive('deleteUser')->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->deleteUserFromGroup($groupId, $userId);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.groupDetail', $groupId), $response->headers->get('Location'));
        $this->assertEquals(trans('group.noti_deleteUser'), $response->getSession()->get('message'));
    }

    public function test_show_detail_group_with_role_admin_when_existed_leader()
    {
        $groupId = 1;
        $group = factory(Group::class, 1)->make();
        $this->groupMock->shouldReceive('find')->andReturn($group);
        $this->courseMock->shouldReceive('getLatestCourses')->andReturn(new Collection);
        $leader = factory(User::class, 1)->make();
        $this->userMock->shouldReceive('getLeader')->with($groupId)->once()->andReturn($leader);
        $users = factory(User::class, 3)->make();
        $this->courseMock->shouldReceive('getGroupIds')->andReturn(new Collection);
        $this->courseMock->shouldReceive('getUserIds')->andReturn(new Collection);
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($users);
        $view = $this->groupController->show($groupId);
        $this->assertEquals('users.admin.group_detail', $view->getName());
        $this->assertArrayHasKey('group', $view->getData());
        $this->assertArrayHasKey('newCourses', $view->getData());
        $this->assertArrayHasKey('users', $view->getData());
        $this->assertArrayHasKey('leader', $view->getData());
    }

    public function test_show_detail_group_with_role_admin_when_not_existed_leader()
    {
        $groupId = 1;
        $group = factory(Group::class, 1)->make();
        $this->groupMock->shouldReceive('find')->andReturn($group);
        $this->courseMock->shouldReceive('getLatestCourses')->andReturn(new Collection);
        $this->userMock->shouldReceive('getLeader')->with($groupId)->once()->andReturn(false);
        $users = factory(User::class, 3)->make();
        $this->courseMock->shouldReceive('getGroupIds')->andReturn(new Collection);
        $this->courseMock->shouldReceive('getUserIds')->andReturn(new Collection);
        $this->userMock->shouldReceive('getUsersNoGroup')->andReturn($users);
        $view = $this->groupController->show($groupId);
        $this->assertEquals('users.admin.group_detail', $view->getName());
        $this->assertArrayHasKey('group', $view->getData());
        $this->assertArrayHasKey('newCourses', $view->getData());
        $this->assertArrayHasKey('users', $view->getData());
        $this->assertArrayHasKey('leader', $view->getData());
    }

    public function test_show_edit_form_with_role_admin()
    {
        $groupId = 1;
        $group = factory(Group::class, 1)->make();
        $this->groupMock->shouldReceive('find')->andReturn($group);
        $this->courseMock->shouldReceive('getLatestCourses')->andReturn(new Collection);
        $view = $this->groupController->edit($groupId);
        $this->assertEquals('users.admin.group_edit', $view->getName());
        $this->assertArrayHasKey('group', $view->getData());
        $this->assertArrayHasKey('newCourses', $view->getData());
    }

    public function test_update_group_with_role_admin()
    {
        $request = Mockery::mock(EditGroupRequest::class)->makePartial();
        $request->shouldReceive('all')->andReturn([
            'name' => 'g1',
        ]);
        $groupId = 1;
        $group = new Group();
        $group->id = $groupId;
        $group->name = 'g1';
        $group->course_id = $groupId;
        $this->groupMock->shouldReceive('find')->with($groupId)->andReturn($group);
        $this->groupMock->shouldReceive('update')->once()->andReturn($group);
        $response = $this->groupController->update($request, $groupId);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('courses.show', $group->course_id), $response->headers->get('Location'));
        $this->assertEquals(trans('group.edit_noti'), $response->getSession()->get('message'));
    }

    public function test_destroy_group_with_role_admin()
    {
        $groupId = 1;
        $group = new Group();
        $group->id = $groupId;
        $group->name = 'g1';
        $group->course_id = $groupId;
        $this->groupMock->shouldReceive('find')->with($groupId)->andReturn($group);
        $this->groupMock->shouldReceive('delete')->with($groupId)->once()->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->destroy($groupId);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('courses.show', $group->course_id), $response->headers->get('Location'));
        $this->assertEquals(trans('group.delete_noti'), $response->getSession()->get('message'));
    }

    public function test_destroy_group_with_role_lecturer()
    {
        $groupId = 1;
        $group = new Group();
        $group->id = $groupId;
        $group->name = 'g1';
        $group->course_id = $groupId;
        $this->groupMock->shouldReceive('find')->with($groupId)->andReturn($group);
        $this->groupMock->shouldReceive('delete')->with($groupId)->once()->andReturn(true);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->destroy($groupId);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.courseDetail', $group->course_id), $response->headers->get('Location'));
        $this->assertEquals(trans('group.delete_noti'), $response->getSession()->get('message'));
    }
}
