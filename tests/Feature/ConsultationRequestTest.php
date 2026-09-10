<?php

namespace Tests\Feature;

use App\Models\ConsultationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_submit_a_consultation_request(): void
    {
        $this->post('/consultations', [
            'name' => '张先生',
            'contact' => '13800138000',
            'organization_name' => '仁心中医馆',
            'organization_type' => 'tcm_clinic',
            'request_type' => 'trial',
            'requirements' => '希望了解处方和库存功能。',
            'consent' => true,
            'website' => '',
        ])->assertRedirect()->assertSessionHas('consultation_submitted', true);

        $this->assertDatabaseHas(ConsultationRequest::class, [
            'name' => '张先生',
            'contact' => '13800138000',
            'request_type' => 'trial',
            'status' => 'pending',
        ]);
    }

    public function test_contact_and_consent_are_required(): void
    {
        $this->post('/consultations', [
            'name' => '张先生',
            'request_type' => 'consultation',
        ])->assertSessionHasErrors(['contact', 'consent']);
    }
}
