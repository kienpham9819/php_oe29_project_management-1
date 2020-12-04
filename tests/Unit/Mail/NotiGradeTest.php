<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotiGrade;
use Illuminate\Mail\Markdown;
use ReflectionClass;

class NotiGradeTest extends TestCase
{
    protected $data, $notiGrade;

    public function setUp() : void
    {
        parent::setUp();
        $this->data = [
            'grade' => 10,
            'review' => 'good',
        ];
        $this->notiGrade = new NotiGrade($this->data);
    }

    public function tearDown() : void
    {
        unset($this->data);
        unset($this->notiGrade);
        parent::tearDown();
    }

    public function test_build_function()
    {
        Mail::fake();
        Mail::send($this->notiGrade);
        Mail::assertSent(NotiGrade::class, function () {
            $this->notiGrade->build();
            $reflectionClass = new ReflectionClass($this->notiGrade);
            $reflectionProperty = $reflectionClass->getProperty('markdown');
            $reflectionProperty->setAccessible(true);
            $view = $reflectionProperty->getValue($this->notiGrade);
            $markdown = $this->app->make(Markdown::class);
            $body = $markdown->renderText($view, $this->notiGrade->buildViewData())->toHtml();
            $this->assertEquals($this->data, $this->notiGrade->buildViewData()['data']);

            return true;
        });
    }
}
