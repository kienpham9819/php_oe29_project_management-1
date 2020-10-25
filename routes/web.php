<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('lang/{lang}', 'LocalizationController@translate')->name('localization');
Auth::routes(['register' => false]);
Route::get('/', 'HomeController@index')->name('home');

Route::resource('users', 'UserController');
Route::get('user-deleted', 'UserController@deleted')->name('users.deleted');
Route::get('user-restore/{id}', 'UserController@restore')->name('users.restore');
Route::delete('user-force-delete/{id}', 'UserController@forceDelete')->name('users.forceDelete');
Route::get('change-password', 'PasswordController@editPassword')->name('change_password');
Route::patch('change-password', 'PasswordController@updatePassword')->name('update_password');
Route::post('import', 'UserController@import')->name('users.import');
Route::resource('roles', 'RoleController');

Route::resource('projects', ProjectController::class)->except(['create', 'store']);

Route::resource('groups.projects', ProjectController::class)->only(['create', 'store']);

Route::resource('projects.task-lists', TaskListController::class);

Route::resource('courses', 'CourseController');
Route::post('import-course', 'CourseController@importCourse')->name('courses.importCourse');
Route::get('restore-course/{id}', 'CourseController@restoreCourse')->name('courses.restore');
Route::post('addUser/{course}', 'CourseController@addUserToCourse')->name('courses.addUser');
Route::delete('deleteUser/{course}/{user}', 'CourseController@deleteUserFromCourse')->name('courses.deleteUser');

Route::resource('courses.groups', 'GroupController')->shallow();
Route::post('add-user-to-group/{group}', 'GroupController@addUserToGroup')->name('groups.addUser');
Route::delete('delete-user-of-group/{group}/{user}', 'GroupController@deleteUserFromGroup')->name('groups.deleteUser');
Route::post('add-leader-to-group/{group}', 'GroupController@addLeaderToGroup')->name('groups.addLeader');

Route::get('course-list-student', 'StudentController@listCourse')->name('students.courseList');
Route::get('course-detail-student/{course}', 'StudentController@showDetailCourse')->name('students.courseDetail');
Route::get('group-detail-student/{group}', 'StudentController@showDetailGroup')->name('students.groupDetail');

Route::get('course-list-lecturer', 'LecturerController@listCourse')->name('lecturers.courseList');
Route::get('course-detail-lecturer/{course}', 'LecturerController@showDetailCourse')->name('lecturers.courseDetail');
Route::get('show-form-group/{group}', 'LecturerController@showFormEditGroup')->name('lecturers.showFormEditGroup');
Route::patch('update-group/{group}', 'LecturerController@updateGroup')->name('lecturers.updateGroup');
Route::get('group-detail/{group}', 'LecturerController@groupDetail')->name('lecturers.groupDetail');
Route::post('addUserToGroup/{group}', 'GroupController@addUserToGroup')->name('groups.addUser');
Route::delete('deleteUserOfGroup/{group}/{user}', 'GroupController@deleteUserFromGroup')->name('groups.deleteUser');
Route::post('addLeaderToGroup/{group}', 'GroupController@addLeaderToGroup')->name('groups.addLeader');

Route::resource('task-lists.tasks', TaskController::class)
    ->only([
        'index',
        'store',
        'destroy',
    ])
    ->shallow();
Route::patch('tasks/{task}/toggle', 'TaskController@toggle')->name('tasks.toggle');
