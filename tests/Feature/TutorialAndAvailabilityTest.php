<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorialAndAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test home page returns data-has-hours="false" when user has no weekly hours.
     */
    public function test_home_page_shows_false_data_has_hours_when_availability_is_zero(): void
    {
        $user = User::factory()->create([
            'horario_semanal' => [
                'domingo' => 0,
                'segunda' => 0,
                'terca' => 0,
                'quarta' => 0,
                'quinta' => 0,
                'sexta' => 0,
                'sabado' => 0,
            ],
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-has-hours="false"', false);
    }

    /**
     * Test home page returns data-has-hours="true" when user has weekly hours.
     */
    public function test_home_page_shows_true_data_has_hours_when_availability_is_greater_than_zero(): void
    {
        $user = User::factory()->create([
            'horario_semanal' => [
                'domingo' => 0,
                'segunda' => 2, // 2 hours
                'terca' => 0,
                'quarta' => 0,
                'quinta' => 0,
                'sexta' => 0,
                'sabado' => 0,
            ],
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-has-hours="true"', false);
    }

    /**
     * Test updating profile availability.
     */
    public function test_user_can_update_availability_via_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'horario_semanal' => [
                'domingo' => 1,
                'segunda' => 2,
                'terca' => 3,
                'quarta' => 4,
                'quinta' => 5,
                'sexta' => 6,
                'sabado' => 7,
            ],
        ]);

        $response->assertRedirect();
        $user->refresh();

        $this->assertEquals(28, array_sum($user->horario_semanal));
    }
}
