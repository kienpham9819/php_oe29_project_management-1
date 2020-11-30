<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Models\User as LaravelUser;
use App\Repositories\User\UserRepositoryInterface;
use App\Http\Controllers\SocialLoginController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider as SocialiteProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SocialLoginControllerTest extends TestCase
{
    protected $mockUserRepository, $controller, $provider, $abstractUser;

    public function setUp() : void
    {
        parent::setUp();
        $this->mockUserRepository = Mockery::mock(UserRepositoryInterface::class)->makePartial();
        $this->controller = new SocialLoginController($this->mockUserRepository);
        $this->abstractUser = Mockery::mock(SocialiteUser::class);
        $this->abstractUser
           ->shouldReceive('getId')
           ->andReturn(rand())
           ->shouldReceive('getName')
           ->andReturn(Str::random(10))
           ->shouldReceive('getEmail')
           ->andReturn(Str::random(10))
           ->shouldReceive('getAvatar')
           ->andReturn(Str::random(10));
        $this->provider = Mockery::mock(SocialiteProvider::class)->makePartial();
    }

    public function tearDown() : void
    {
        Mockery::close();
        unset($this->controller);
        parent::tearDown();
    }

    public function test_redirect_to_non_existed_provider()
    {
        $this->provider->shouldReceive('scopes')
            ->andReturn($this->provider);
        $this->provider->shouldReceive('redirect')
            ->andReturn(new RedirectResponse('https://github.com/login/oauth/authorize'));

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($this->provider);
        $this->expectException(NotFoundHttpException::class);
        $response = $this->controller->redirectToProvider('facebook');
    }

    public function test_redirect_to_existed_provider()
    {
        $this->provider = Mockery::mock(SocialiteProvider::class)->makePartial();
        $this->provider->shouldReceive('scopes')
            ->andReturn($this->provider);
        $this->provider->shouldReceive('redirect')
            ->andReturn(new RedirectResponse('https://github.com/login/oauth/authorize'));

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($this->provider);
        $response = $this->controller->redirectToProvider('github');
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_handle_provider_callback_success()
    {
        $this->provider->shouldReceive('user')->andReturn($this->abstractUser);

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($this->provider);
        $user = new LaravelUser;
        $user->id = rand();
        $this->mockUserRepository
            ->shouldReceive('storeGithubToken')
            ->andReturn(true);
        $response = $this->actingAs($user)->controller->handleProviderCallback('github');
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_handle_provider_callback_fail()
    {
        $this->provider->shouldReceive('user')->andReturn($this->abstractUser);

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($this->provider);
        $user = new LaravelUser;
        $user->id = rand();
        $this->mockUserRepository
            ->shouldReceive('storeGithubToken')
            ->andReturn(true);
        $this->expectException(NotFoundHttpException::class);
        $response = $this->actingAs($user)->controller->handleProviderCallback('tumblr');
    }
}
