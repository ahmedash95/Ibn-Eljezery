<?php

namespace Tests\Feature;

use App\Filament\Resources\StudentResource;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminStudentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_app_students_only(): void
    {
        $this->seed(RoleSeeder::class);
        Role::findOrCreate('super_admin', 'web');
        $admin = User::factory()->create()->assignRole('super_admin');
        $student = User::factory()->create(['name' => 'طالب التطبيق', 'phone' => '201011112222'])->assignRole('student');
        User::factory()->create(['name' => 'مشرف الحضور'])->assignRole('supervisor');

        $this->actingAs($admin)
            ->get(StudentResource::getUrl('index'))
            ->assertOk()
            ->assertSee('طالب التطبيق')
            ->assertDontSee('مشرف الحضور');

        $this->actingAs($admin)
            ->get(StudentResource::getUrl('view', ['record' => $student]))
            ->assertOk()
            ->assertSee('201011112222');
    }
}
