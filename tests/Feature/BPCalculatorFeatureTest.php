<?php

namespace Tests\Feature;

use Tests\TestCase;

class BPCalculatorFeatureTest extends TestCase
{
    public function test_home_page_loads_successfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Blood Pressure Category Calculator');
    }

    public function test_ideal_blood_pressure_scenario()
    {
        $response = $this->post(route('bp.calculate'), [
            'systolic'  => 110,
            'diastolic' => 70,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Reading:');
        $response->assertSee('110 / 70');
        $response->assertSee('Category:');
        $response->assertSee('Ideal blood pressure');
        $response->assertSee('Your blood pressure is in the ideal range.');
    }

    public function test_high_blood_pressure_scenario()
    {
        $response = $this->post(route('bp.calculate'), [
            'systolic'  => 150,
            'diastolic' => 95,
        ]);

        $response->assertStatus(200);
        $response->assertSee('150 / 95');
        $response->assertSee('High blood pressure');
        $response->assertSee('Your blood pressure is high.');
    }

    public function test_invalid_input_shows_validation_errors()
    {
        $response = $this->from('/')
            ->post(route('bp.calculate'), [
                'systolic'  => 110,
                'diastolic' => 110,
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');

        $this->followRedirects($response)
            ->assertSee('The systolic (upper) value must be higher than the diastolic (lower) value.');
    }

    public function test_unrealistic_difference_shows_custom_error()
    {
        $response = $this->from('/')
            ->post(route('bp.calculate'), [
                'systolic'  => 120,
                'diastolic' => 110, // difference = 10 < 20
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');

        $this->followRedirects($response)
            ->assertSee('These blood pressure values do not look realistic.');
    }
}
