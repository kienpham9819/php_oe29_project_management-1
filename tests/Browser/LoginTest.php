<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function test_login_form_when_language_is_changed()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(env('APP_URL') . '/login')
                ->assertSee('Language')
                ->assertSee('LOGIN TO YOUR ACCOUNT')
                ->assertSeeIn('form', 'Remember me')
                ->assertSeeIn('form', 'LOGIN')
                ->assertSee('FORGOT YOUR PASSWORD ?')
                ->assertSee('Login')
                ->click('.lang')
                ->assertSee('English')
                ->assertSee('Tiếng Việt')
                ->click('#vi')
                ->assertPathIs('/login')
                ->assertSee('ĐĂNG NHẬP VÀO TÀI KHOẢN')
                ->assertSeeIn('form', 'Ghi nhớ mật khẩu')
                ->assertSeeIn('form', 'ĐĂNG NHẬP')
                ->assertSee('QUÊN MẬT KHẨU')
                ->assertSee('Ngôn ngữ')
                ->assertSee('Đăng nhập')
                ->click('.lang')
                ->assertSee('English')
                ->assertSee('Tiếng Việt')
                ->click('#en')
                ->assertPathIs('/login');
        });
    }

    public function test_login_fail()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(env('APP_URL') . '/login')
                ->type('email', 'taylor@gmail.com')
                ->type('password', '12345678')
                ->press('.login-btn')
                ->assertPathIs('/login');
        });
    }

    public function test_login_for_all_user()
    {
        $this->browse(function ($admin, $lecturer, $student) {
            $admin->visit(env('APP_URL') . '/login')
                ->type('email', 'admin@gmail.com')
                ->type('password', '12345678')
                ->press('.login-btn')
                ->assertPathIs('/users');
            $lecturer->visit(env('APP_URL') . '/login')
                ->type('email', 'giangvien@gmail.com')
                ->type('password', '12345678')
                ->press('.login-btn')
                ->assertPathIs('/course-list-lecturer');
            $student->visit(env('APP_URL') . '/login')
                ->type('email', 'kienphp86@gmail.com')
                ->type('password', '12345678')
                ->press('.login-btn')
                ->assertPathIs('/course-list-student');
        });
    }

}
