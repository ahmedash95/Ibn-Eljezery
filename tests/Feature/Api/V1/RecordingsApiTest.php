<?php

namespace Tests\Feature\Api\V1;

use App\Enum\Recording\RecordingTypeEnum;
use App\Filament\Resources\RecordingResource;
use App\Models\Recording;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RecordingsApiTest extends TestCase
{
    use RefreshDatabase;

    private function recording(array $attributes = []): Recording
    {
        return Recording::create($attributes + [
            'title_ar' => 'شرح التجويد',
            'type' => RecordingTypeEnum::VIDEO->value,
            'url' => 'https://youtu.be/abc',
            'is_active' => true,
        ]);
    }

    public function test_lists_active_recordings_for_everyone(): void
    {
        Storage::fake('public');
        $teacher = Teacher::create(['name_ar' => 'الشيخ أحمد', 'is_active' => true]);
        $this->recording(['title_ar' => 'فيديو', 'teacher_id' => $teacher->id]);
        $this->recording(['title_ar' => 'ملف', 'type' => 'pdf', 'url' => null, 'file_path' => 'recordings/a.pdf']);
        $this->recording(['title_ar' => 'مخفي', 'is_active' => false]);

        $response = $this->withHeader('Accept-Language', 'ar')->getJson('/api/v1/academy/recordings')->assertOk();

        $response->assertJsonCount(2, 'data')
            ->assertJsonMissing(['title' => 'مخفي'])
            ->assertJsonFragment(['title' => 'فيديو', 'type' => 'video', 'url' => 'https://youtu.be/abc'])
            ->assertJsonFragment(['name' => 'الشيخ أحمد']);
        $pdf = collect($response->json('data'))->firstWhere('type', 'pdf');
        $this->assertStringEndsWith('/storage/recordings/a.pdf', $pdf['url']);
    }

    public function test_filters_by_type_and_rejects_unknown_type(): void
    {
        $this->recording(['title_ar' => 'فيديو']);
        $this->recording(['title_ar' => 'رابط', 'type' => 'link']);

        $this->withHeader('Accept-Language', 'ar')->getJson('/api/v1/academy/recordings?type=link')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonFragment(['title' => 'رابط']);
        $this->withHeader('Accept-Language', 'ar')->getJson('/api/v1/academy/recordings?type=nope')->assertStatus(422);
    }

    public function test_file_and_link_are_exclusive_and_old_file_is_deleted(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('recordings/old.mp3', 'x');
        $rec = $this->recording(['type' => 'audio', 'url' => 'https://ignored', 'file_path' => 'recordings/old.mp3']);
        $this->assertNull($rec->url);

        $rec->update(['type' => 'link', 'url' => 'https://drive.google.com/x']);
        $this->assertNull($rec->fresh()->file_path);
        Storage::disk('public')->assertMissing('recordings/old.mp3');
    }

    public function test_teacher_sees_only_own_recordings(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create()->assignRole('teacher');
        $mine = Teacher::create(['name_ar' => 'أنا', 'is_active' => true, 'user_id' => $user->id]);
        $other = Teacher::create(['name_ar' => 'غيري', 'is_active' => true]);
        $this->recording(['title_ar' => 'مادتي', 'teacher_id' => $mine->id]);
        $this->recording(['title_ar' => 'مادة غيري', 'teacher_id' => $other->id]);

        $this->actingAs($user)
            ->get(\App\Filament\Teacher\Resources\RecordingResource::getUrl('index', panel: 'teacher'))
            ->assertOk()->assertSee('مادتي')->assertDontSee('مادة غيري');
        $this->actingAs($user)
            ->get(\App\Filament\Teacher\Resources\RecordingResource::getUrl('create', panel: 'teacher'))
            ->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $this->seed(RoleSeeder::class);
        Role::findOrCreate('super_admin', 'web');
        $admin = User::factory()->create()->assignRole('super_admin');
        $rec = $this->recording(['title_ar' => 'مادة للأدمن']);

        $this->actingAs($admin)->get(RecordingResource::getUrl('index'))->assertOk()->assertSee('مادة للأدمن');
        $this->actingAs($admin)->get(RecordingResource::getUrl('create'))->assertOk();
        $this->actingAs($admin)->get(RecordingResource::getUrl('edit', ['record' => $rec]))->assertOk();
    }
}
