<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Client;
use App\Services\OdontogramService;

class OdontogramTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_have_odontogram_initialized()
    {
        $client = Client::factory()->create();

        $this->assertNotNull($client->odontogram);
        $this->assertArrayHasKey('permanent', $client->odontogram);
        $this->assertArrayHasKey('temporary', $client->odontogram);
        $this->assertArrayHasKey('metadata', $client->odontogram);
    }

    public function test_odontogram_validation_works_correctly()
    {
        $validOdontogram = [
            'permanent' => [
                '11' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->toISOString()]
            ],
            'temporary' => [
                '51' => ['status' => 'cavity', 'notes' => 'Test', 'updatedAt' => now()->toISOString()]
            ],
            'metadata' => [
                'created_at' => now()->toISOString(),
                'last_updated' => now()->toISOString(),
                'version' => '1.0'
            ]
        ];

        $errors = OdontogramService::validateOdontogram($validOdontogram);
        $this->assertEmpty($errors);
    }

    public function test_odontogram_validation_fails_with_invalid_tooth_number()
    {
        $invalidOdontogram = [
            'permanent' => [
                '99' => ['status' => 'healthy', 'notes' => '', 'updatedAt' => now()->toISOString()]
            ],
            'temporary' => [],
            'metadata' => []
        ];

        $errors = OdontogramService::validateOdontogram($invalidOdontogram);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Número de diente permanente inválido: 99', implode(', ', $errors));
    }

    public function test_odontogram_statistics_generation()
    {
        $odontogram = [
            'permanent' => [
                '11' => ['status' => 'healthy'],
                '12' => ['status' => 'cavity'],
                '13' => ['status' => 'treated'],
            ],
            'temporary' => [
                '51' => ['status' => 'healthy'],
                '52' => ['status' => 'cavity'],
            ],
            'metadata' => []
        ];

        $stats = OdontogramService::generateStatistics($odontogram);

        $this->assertEquals(3, $stats['total_permanent']);
        $this->assertEquals(2, $stats['total_temporary']);
        $this->assertEquals(2, $stats['status_counts']['healthy']);
        $this->assertEquals(2, $stats['status_counts']['cavity']);
        $this->assertEquals(1, $stats['status_counts']['treated']);
    }

    public function test_odontogram_export_json_format()
    {
        $odontogram = OdontogramService::initializeEmptyOdontogram();

        $jsonExport = OdontogramService::export($odontogram, 'json');

        $this->assertJson($jsonExport);
        $decodedData = json_decode($jsonExport, true);
        $this->assertArrayHasKey('permanent', $decodedData);
        $this->assertArrayHasKey('temporary', $decodedData);
        $this->assertArrayHasKey('metadata', $decodedData);
    }

    public function test_odontogram_export_csv_format()
    {
        $odontogram = [
            'permanent' => [
                '11' => ['status' => 'healthy', 'notes' => 'Test note', 'updatedAt' => '2025-01-01T00:00:00Z']
            ],
            'temporary' => [],
            'metadata' => []
        ];

        $csvExport = OdontogramService::export($odontogram, 'csv');

        $this->assertStringContainsString('Diente,Tipo,Estado,Notas,Fecha Actualización', $csvExport);
        $this->assertStringContainsString('11,Incisivo Central,healthy,Test note', $csvExport);
    }

    public function test_tooth_info_retrieval()
    {
        $toothInfo = OdontogramService::getToothInfo(11);

        $this->assertEquals(11, $toothInfo['number']);
        $this->assertEquals(1, $toothInfo['quadrant']);
        $this->assertEquals(1, $toothInfo['position']);
        $this->assertEquals('Superior Derecho', $toothInfo['quadrant_name']);
        $this->assertEquals('Incisivo Central', $toothInfo['type']);
        $this->assertTrue($toothInfo['is_permanent']);
        $this->assertFalse($toothInfo['is_temporary']);
    }

    public function test_temporary_tooth_info_retrieval()
    {
        $toothInfo = OdontogramService::getToothInfo(51);

        $this->assertEquals(51, $toothInfo['number']);
        $this->assertEquals(5, $toothInfo['quadrant']);
        $this->assertEquals(1, $toothInfo['position']);
        $this->assertEquals('Superior Derecho (Temporal)', $toothInfo['quadrant_name']);
        $this->assertEquals('Incisivo Central', $toothInfo['type']);
        $this->assertFalse($toothInfo['is_permanent']);
        $this->assertTrue($toothInfo['is_temporary']);
    }

    public function test_client_odontogram_update_triggers_observer()
    {
        $client = Client::factory()->create();

        $originalTimestamp = $client->odontogram['metadata']['last_updated'];

        sleep(1); // Asegurar diferencia de tiempo

        $client->update([
            'odontogram' => array_merge($client->odontogram, [
                'permanent' => [
                    '11' => ['status' => 'cavity', 'notes' => 'New cavity', 'updatedAt' => now()->toISOString()]
                ]
            ])
        ]);

        $client->refresh();

        $this->assertNotEquals($originalTimestamp, $client->odontogram['metadata']['last_updated']);
    }

    public function test_empty_odontogram_initialization()
    {
        $emptyOdontogram = OdontogramService::initializeEmptyOdontogram();

        $this->assertIsArray($emptyOdontogram);
        $this->assertEmpty($emptyOdontogram['permanent']);
        $this->assertEmpty($emptyOdontogram['temporary']);
        $this->assertNotEmpty($emptyOdontogram['metadata']);
        $this->assertArrayHasKey('created_at', $emptyOdontogram['metadata']);
        $this->assertArrayHasKey('last_updated', $emptyOdontogram['metadata']);
        $this->assertArrayHasKey('version', $emptyOdontogram['metadata']);
    }
}
