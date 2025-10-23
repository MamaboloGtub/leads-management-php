<?php

namespace Tests\Unit;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_can_be_created_with_valid_data(): void
    {
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'lead_status' => 'new',
            'lead_source' => 'website'
        ];

        $lead = Lead::create($leadData);

        $this->assertInstanceOf(Lead::class, $lead);
        $this->assertEquals('John Doe', $lead->name);
        $this->assertEquals('john@example.com', $lead->email);
        $this->assertEquals('new', $lead->lead_status);
        $this->assertEquals('website', $lead->lead_source);
    }

    public function test_lead_has_fillable_attributes(): void
    {
        $lead = new Lead();
        $expectedFillable = ['name', 'email', 'lead_status', 'lead_source'];

        $this->assertEquals($expectedFillable, $lead->getFillable());
    }

    public function test_lead_has_timestamps(): void
    {
        $lead = Lead::factory()->create();

        $this->assertNotNull($lead->created_at);
        $this->assertNotNull($lead->updated_at);
    }

    public function test_lead_can_be_updated(): void
    {
        $lead = Lead::factory()->create([
            'name' => 'Original Name',
            'lead_status' => 'new'
        ]);

        $lead->update([
            'name' => 'Updated Name',
            'lead_status' => 'contacted'
        ]);

        $this->assertEquals('Updated Name', $lead->name);
        $this->assertEquals('contacted', $lead->lead_status);
    }

    public function test_lead_can_be_deleted(): void
    {
        $lead = Lead::factory()->create();
        $leadId = $lead->id;

        $lead->delete();

        $this->assertNull(Lead::find($leadId));
    }
}