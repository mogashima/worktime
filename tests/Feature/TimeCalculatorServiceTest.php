<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\TimeCalculatorService;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TimeCalculatorServiceTest extends TestCase
{

    #[Test]
    public function execute()
    {
        $result = TimeCalculatorService::getDiffMinute('10:00', '10:05');
        $this->assertSame(5, $result);

        $result = TimeCalculatorService::getDiffMinute('23:55', '00:05');
        $this->assertSame(10, $result);

        $result = TimeCalculatorService::getDiffMinute('00:00', '00:01');
        $this->assertSame(1, $result);

        $result = TimeCalculatorService::getDiffMinute('23:59', '00:00');
        $this->assertSame(1, $result);

        $result = TimeCalculatorService::getDiffMinute('', '00:05');
        $this->assertSame(0, $result);

        $result = TimeCalculatorService::getDiffMinute('00:05', '');
        $this->assertSame(0, $result);

        $result = TimeCalculatorService::getDiffMinute('00:05', '01:35');
        $this->assertSame(90, $result);

        $result = TimeCalculatorService::getDiffMinute('00:05', '02:35');
        $this->assertSame(150, $result);

    }
}
