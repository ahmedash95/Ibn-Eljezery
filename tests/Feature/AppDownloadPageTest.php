<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppDownloadPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_download_page_renders(): void
    {
        $this->get('/app')
            ->assertOk()
            ->assertSee('تطبيق ابن الجزري')
            ->assertSee('releases/latest/download/IbnEljezery-demo.apk', false);
    }

    public function test_privacy_page_mentions_account_deletion(): void
    {
        $this->get('/privacy')->assertOk()->assertSee('مسح حسابك');
    }
}
