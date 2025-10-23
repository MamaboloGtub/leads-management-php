<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadValidationTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser()
    {
        $user = User::factory()->create();
        return \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
    }

    public function test_lead_name_cannot_exceed_255_characters(): void
    {
        $token = $this->getAuthenticatedUser();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/leads', [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'lead_status' => 'new'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    public function test_lead_status_cannot_exceed_50_characters(): void
    {
        $token = $this->getAuthenticatedUser();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/leads', [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'lead_status' => str_repeat('a', 51)
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['lead_status']);
    }

    public function test_lead_source_cannot_exceed_100_characters(): void
    {
        $token = $this->getAuthenticatedUser();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/leads', [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'lead_status' => 'new',
            'lead_source' => str_repeat('a', 101)
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['lead_source']);
    }

    public function test_lead_source_can_be_null(): void
    {
        $token = $this->getAuthenticatedUser();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/leads', [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'lead_status' => 'new',
            'lead_source' => null
        ]);

        $response->assertStatus(201);
    }

    public function test_lead_update_validates_email_uniqueness_excluding_current_lead(): void
    {
        $token = $this->getAuthenticatedUser();
        
        // Create two leads
        $lead1 = Lead::factory()->create(['email' => 'lead1@example.com']);
        $lead2 = Lead::factory()->create(['email' => 'lead2@example.com']);

        // Try to update lead2 with lead1's email (should fail)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/leads/{$lead2->id}", [
            'email' => 'lead1@example.com'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_lead_update_allows_keeping_same_email(): void
    {
        $token = $this->getAuthenticatedUser();
        
        $lead = Lead::factory()->create(['email' => 'original@example.com']);

        // Update lead keeping the same email (should pass)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/leads/{$lead->id}", [
            'name' => 'Updated Name',
            'email' => 'original@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Lead updated successfully',
                    'data' => [
                        'name' => 'Updated Name',
                        'email' => 'original@example.com'
                    ]
                ]);
    }

    public function test_lead_update_with_partial_data(): void
    {
        $token = $this->getAuthenticatedUser();
        
        $lead = Lead::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'lead_status' => 'new',
            'lead_source' => 'website'
        ]);

        // Update only the name
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/leads/{$lead->id}", [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(200);
        
        $lead->refresh();
        $this->assertEquals('Updated Name', $lead->name);
        $this->assertEquals('original@example.com', $lead->email); // Should remain unchanged
        $this->assertEquals('new', $lead->lead_status); // Should remain unchanged
    }
}