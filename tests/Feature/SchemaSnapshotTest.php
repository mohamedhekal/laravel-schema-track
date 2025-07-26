<?php

namespace MohamedHekal\LaravelSchemaTrack\Tests\Feature;

use MohamedHekal\LaravelSchemaTrack\Tests\TestCase;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SchemaSnapshotTest extends TestCase
{
    protected SchemaSnapshotInterface $snapshotService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->snapshotService = app(SchemaSnapshotInterface::class);

        // Create test tables
        $this->createTestTables();
    }

    protected function tearDown(): void
    {
        // Clean up test storage
        $storagePath = config('schema-track.storage_path');
        if (is_dir($storagePath)) {
            $files = glob("$storagePath/*.json");
            if ($files !== false) {
                array_map('unlink', $files);
            }
            rmdir($storagePath);
        }

        parent::tearDown();
    }

    public function test_can_take_snapshot(): void
    {
        $snapshot = $this->snapshotService->takeSnapshot('test_snapshot');

        $this->assertIsArray($snapshot);
        $this->assertEquals('test_snapshot', $snapshot['name']);
        $this->assertArrayHasKey('timestamp', $snapshot);
        $this->assertArrayHasKey('database', $snapshot);
        $this->assertArrayHasKey('schema', $snapshot);
    }

    public function test_can_get_snapshot(): void
    {
        $this->snapshotService->takeSnapshot('test_snapshot');

        $snapshot = $this->snapshotService->getSnapshot('test_snapshot');

        $this->assertNotNull($snapshot);
        $this->assertEquals('test_snapshot', $snapshot['name']);
    }

    public function test_can_get_all_snapshots(): void
    {
        $this->snapshotService->takeSnapshot('snapshot_1');
        $this->snapshotService->takeSnapshot('snapshot_2');

        $snapshots = $this->snapshotService->getAllSnapshots();

        $this->assertCount(2, $snapshots);
        $this->assertEquals('snapshot_1', $snapshots[0]['name']);
        $this->assertEquals('snapshot_2', $snapshots[1]['name']);
    }

    public function test_can_get_latest_snapshot(): void
    {
        $this->snapshotService->takeSnapshot('snapshot_1');
        $this->snapshotService->takeSnapshot('snapshot_2');

        $latest = $this->snapshotService->getLatestSnapshot();

        $this->assertNotNull($latest);
        $this->assertEquals('snapshot_2', $latest['name']);
    }

    public function test_can_delete_snapshot(): void
    {
        $this->snapshotService->takeSnapshot('test_snapshot');

        $this->assertTrue($this->snapshotService->snapshotExists('test_snapshot'));

        $result = $this->snapshotService->deleteSnapshot('test_snapshot');

        $this->assertTrue($result);
        $this->assertFalse($this->snapshotService->snapshotExists('test_snapshot'));
    }

    public function test_snapshot_contains_table_information(): void
    {
        $snapshot = $this->snapshotService->takeSnapshot('test_snapshot');

        $this->assertArrayHasKey('users', $snapshot['schema']);
        $this->assertArrayHasKey('posts', $snapshot['schema']);

        $usersTable = $snapshot['schema']['users'];
        $this->assertArrayHasKey('columns', $usersTable);
        $this->assertArrayHasKey('indexes', $usersTable);
        $this->assertArrayHasKey('foreign_keys', $usersTable);
    }

    public function test_snapshot_excludes_system_tables(): void
    {
        $snapshot = $this->snapshotService->takeSnapshot('test_snapshot');

        $excludeTables = config('schema-track.exclude_tables', []);

        foreach ($excludeTables as $table) {
            $this->assertArrayNotHasKey($table, $snapshot['schema']);
        }
    }

    protected function createTestTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }
}
