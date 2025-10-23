<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser()
    {
        $user = User::factory()->create();
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
        return $token;
    }

    public function test_authenticated_user_can_get_leads(): void
    {
        $token = $this->getAuthenticatedUser();
        Lead::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/leads');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'lead_status',
                            'lead_source',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]);
    }

    public function test_unauthenticated_user_cannot_get_leads(): void
    {
        $response = $this->getJson('/api/leads');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_lead(): void
    {
        $token = $this->getAuthenticatedUser();
        
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'lead_status' => 'new',
            'lead_source' => 'website'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/leads', $leadData);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Lead created successfully',
                    'data' => $leadData
                ]);

        $this->assertDatabaseHas('leads', $leadData);
    }

    public function test_lead_creation_requires_authentication(): void
    {
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'lead_status' => 'new'
        ];

        $response = $this->postJson('/api/leads', $leadData);

        $response->assertStatus(401);
    }

    public function test_lead_creation_validates_required_fields(): void
    {
        $token = $this->getAuthenticatedUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/leads', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'lead_status']);
    }

    public function test_lead_creation_validates_email_format(): void
    {
        $token = $this->getAuthenticatedUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/leads', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'lead_status' => 'new'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_lead_creation_validates_unique_email(): void
    {
        $token = $this->getAuthenticatedUser();
        Lead::factory()->create(['email' => 'existing@example.com']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/leads', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'lead_status' => 'new'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_view_specific_lead(): void
    {
        $token = $this->getAuthenticatedUser();
        $lead = Lead::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/leads/{$lead->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'lead_status' => $lead->lead_status
                ]);
    }

    public function test_authenticated_user_can_update_lead(): void
    {
        $token = $this->getAuthenticatedUser();
        $lead = Lead::factory()->create([
            'name' => 'Original Name',
            'lead_status' => 'new'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'lead_status' => 'contacted'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/leads/{$lead->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Lead updated successfully',
                    'data' => $updateData
                ]);

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'name' => 'Updated Name',
            'lead_status' => 'contacted'
        ]);
    }

    public function test_authenticated_user_can_delete_lead(): void
    {
        $token = $this->getAuthenticatedUser();
        $lead = Lead::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/leads/{$lead->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Lead deleted successfully'
                ]);

        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_leads_can_be_filtered_by_date_range(): void
    {
        $token = $this->getAuthenticatedUser();
        
        // Create leads with different dates
        Lead::factory()->create(['created_at' => now()->subDays(10)]);
        Lead::factory()->create(['created_at' => now()]);

        $fromDate = now()->subDays(1)->format('Y-m-d H:i:s');
        $toDate = now()->addDay()->format('Y-m-d H:i:s');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/leads?from={$fromDate}&to={$toDate}");

        $response->assertStatus(200);
        
        // Should only return leads from the date range (1 lead)
        $this->assertEquals(1, count($response->json('data')));
    }

    public function test_viewing_nonexistent_lead_returns_404(): void
    {
        $token = $this->getAuthenticatedUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/leads/999');

        $response->assertStatus(404);
    }
}