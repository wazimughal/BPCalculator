<?php

namespace Tests\Unit;

use App\Services\BloodPressureService;
use Tests\TestCase;

class BloodPressureServiceTest extends TestCase
{
    private BloodPressureService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BloodPressureService();
    }

    public function test_low_blood_pressure_classification()
    {
        $result = $this->service->classify(85, 55);

        $this->assertEquals('Low blood pressure', $result['label']);
        $this->assertEquals('#800080', $result['color']);
    }

    public function test_ideal_blood_pressure_classification()
    {
        $result = $this->service->classify(110, 70);

        $this->assertEquals('Ideal blood pressure', $result['label']);
        $this->assertEquals('#008000', $result['color']);
    }

    public function test_pre_high_blood_pressure_classification()
    {
        $result = $this->service->classify(130, 85);

        $this->assertEquals('Pre-high blood pressure', $result['label']);
        $this->assertEquals('#c7ca0bff', $result['color']);
    }

    public function test_high_blood_pressure_by_systolic()
    {
        $result = $this->service->classify(150, 80);

        $this->assertEquals('High blood pressure', $result['label']);
        $this->assertEquals('#FF0000', $result['color']);
    }

    public function test_high_blood_pressure_by_diastolic()
    {
        $result = $this->service->classify(130, 95);

        $this->assertEquals('High blood pressure', $result['label']);
        $this->assertEquals('#FF0000', $result['color']);
    }

    public function test_realistic_combination_accepts_190_over_95()
    {
        $this->assertTrue(
            $this->service->isRealisticCombination(190, 95)
        );
    }

    public function test_unrealistic_when_difference_too_small()
    {
        // 120 / 110 -> diff = 10, should be unrealistic
        $this->assertFalse(
            $this->service->isRealisticCombination(120, 110)
        );
    }

    public function test_unrealistic_when_difference_too_large()
    {
        // 170 / 45 -> diff = 125, should be unrealistic
        $this->assertFalse(
            $this->service->isRealisticCombination(170, 45)
        );
    }
}
