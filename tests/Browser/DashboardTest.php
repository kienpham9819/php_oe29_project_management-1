<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class DashboardTest extends DuskTestCase
{
    public function test_dashboard_view_in_english()
    {
        $user = User::find(1);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->assertSee('Language')
                ->clickLink('Language')
                ->assertSee('English')
                ->assertSee('Tiếng Việt')
                ->clickLink('English')
                ->assertSee('Dashboard')
                ->assertSee('Courses management')
                ->assertSee('Project management')
                ->assertSee('APPROVED PROJECT')
                ->assertSee('PENDING PROJECT')
                ->assertSee('UNFINISHED TASKS')
                ->assertSee('COMPLETED TASKS')
                ->assertSee('STATISTIC')
                ->assertSee('RECENT PROJECT')
                ->assertSee($user->name)
                ->clickLink($user->name)
                ->assertSee('Logout')
                ->assertSee('Account setting');
        });
    }

    public function test_dashboard_view_in_vietnamese()
    {
        $user = User::find(1);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                ->assertSee('Language')
                ->clickLink('Language')
                ->assertSee('English')
                ->assertSee('Tiếng Việt')
                ->clickLink('Tiếng Việt')
                ->assertSee('Bảng điều khiển')
                ->assertSee('Quản lý khóa học')
                ->assertSee('Quản lý dự án')
                ->assertSee('Thêm')
                ->assertPathIs('/')
                ->assertSee('THÔNG SỐ')
                ->assertSee('DỰ ÁN ĐANG CHỜ DUYỆT')
                ->assertSee('DỰ ÁN ĐƯỢC CHẤP NHẬN')
                ->assertSee('NHIỆM VỤ CHƯA HOÀN THÀNH')
                ->assertSee('NHIỆM VỤ ĐÃ HOÀN THÀNH')
                ->assertSee('DỰ ÁN GẦN ĐÂY')
                ->assertSee($user->name)
                ->clickLink($user->name)
                ->assertSee('Đăng xuất')
                ->assertSee('Cài đặt tài khoản')
                ->assertSee('Ngôn ngữ')
                ->clickLink('Ngôn ngữ')
                ->assertSee('English')
                ->clickLink('English');
        });
    }

    public function test_redirect_to_course_list()
    {
        $user = User::find(1);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                ->assertSee('Language')
                ->clickLink('Language')
                ->assertSee('English')
                ->clickLink('English')
                ->assertSee('Courses management')
                ->clickLink('Courses management')
                ->assertPathIs('/course-list-student');
        });
    }

    public function test_redirect_to_project_list()
    {
        $user = User::find(1);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                ->assertSee('Language')
                ->clickLink('Language')
                ->assertSee('English')
                ->clickLink('English')
                ->assertSee('Project management')
                ->clickLink('Project management')
                ->assertPathIs('/projects');
        });
    }

    public function test_redirect_to_account_setting()
    {
        $user = User::find(1);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                ->assertSee($user->name)
                ->clickLink($user->name)
                ->assertSee('Account setting')
                ->clickLink('Account setting')
                ->assertPathIs('/change-password');
        });
    }

    public function test_logout()
    {
        $user = User::find(1);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                ->assertSee($user->name)
                ->clickLink($user->name)
                ->assertSee('Logout')
                ->click('#logout-form')
                ->assertPathIs('/login');
        });
    }
}
