<?php

namespace MohamedHekal\LaravelSchemaTrack\Tests\Feature;

use MohamedHekal\LaravelSchemaTrack\Tests\TestCase;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaDiffInterface;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SchemaDiffTest extends TestCase
{
    protected SchemaDiffInterface $diffService;
    protected SchemaSnapshotInterface $snapshotService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->diffService = app(SchemaDiffInterface::class);
        $this->snapshotService = app(SchemaSnapshotInterface::class);
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

    public function test_can_compare_snapshots(): void
    {
        // Create initial schema
        $this->createInitialSchema();
        $this->snapshotService->takeSnapshot('snapshot_1');

        // Modify schema
        $this->modifySchema();
        $this->snapshotService->takeSnapshot('snapshot_2');

        $diff = $this->diffService->compare('snapshot_1', 'snapshot_2');

        $this->assertIsArray($diff);
        $this->assertArrayHasKey('new_tables', $diff);
        $this->assertArrayHasKey('removed_tables', $diff);
        $this->assertArrayHasKey('modified_tables', $diff);
    }

    public function test_detects_new_tables(): void
    {
        // Create initial schema
        $this->createInitialSchema();
        $this->snapshotService->takeSnapshot('snapshot_1');

        // Add new table
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->timestamps();
        });
        $this->snapshotService->takeSnapshot('snapshot_2');

        $diff = $this->diffService->compare('snapshot_1', 'snapshot_2');

        $this->assertContains('comments', $diff['new_tables']);
    }

    public function test_detects_removed_tables(): void
    {
        // Create initial schema with extra table
        $this->createInitialSchema();
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->timestamps();
        });
        $this->snapshotService->takeSnapshot('snapshot_1');

        // Remove table
        Schema::drop('comments');
        $this->snapshotService->takeSnapshot('snapshot_2');

        $diff = $this->diffService->compare('snapshot_1', 'snapshot_2');

        $this->assertContains('comments', $diff['removed_tables']);
    }

    public function test_detects_column_changes(): void
    {
        // Create initial schema
        $this->createInitialSchema();
        $this->snapshotService->takeSnapshot('snapshot_1');

        // Modify column
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 100)->change(); // Change length
        });
        $this->snapshotService->takeSnapshot('snapshot_2');

        $diff = $this->diffService->compare('snapshot_1', 'snapshot_2');

        $this->assertArrayHasKey('users', $diff['modified_tables']);
        $this->assertArrayHasKey('modified_columns', $diff['modified_tables']['users']);
    }

    public function test_can_format_diff_as_markdown(): void
    {
        // Create initial schema
        $this->createInitialSchema();
        $this->snapshotService->takeSnapshot('snapshot_1');

        // Add new table
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->timestamps();
        });
        $this->snapshotService->takeSnapshot('snapshot_2');

        $diff = $this->diffService->compare('snapshot_1', 'snapshot_2');
        $markdown = $this->diffService->getFormattedDiff($diff, 'markdown');

        $this->assertStringContainsString('# Schema Changes', $markdown);
        $this->assertStringContainsString('comments', $markdown);
    }

    public function test_can_detect_breaking_changes(): void
    {
        // Create initial schema
        $this->createInitialSchema();
        $this->snapshotService->takeSnapshot('snapshot_1');

        // Remove table (breaking change)
        Schema::drop('posts');
        $this->snapshotService->takeSnapshot('snapshot_2');

        $diff = $this->diffService->compare('snapshot_1', 'snapshot_2');
        $hasBreakingChanges = $this->diffService->hasBreakingChanges($diff);

        $this->assertTrue($hasBreakingChanges);
    }

    public function test_can_get_change_summary(): void
    {
        // Create initial schema
        $this->createInitialSchema();
        $this->snapshotService->takeSnapshot('snapshot_1');

        // Make changes
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->timestamps();
        });
        Schema::drop('posts');
        $this->snapshotService->takeSnapshot('snapshot_2');

        $diff = $this->diffService->compare('snapshot_1', 'snapshot_2');
        $summary = $this->diffService->getChangeSummary($diff);

        $this->assertArrayHasKey('total_changes', $summary);
        $this->assertArrayHasKey('new_tables', $summary);
        $this->assertArrayHasKey('removed_tables', $summary);
        $this->assertArrayHasKey('modified_tables', $summary);
        $this->assertArrayHasKey('breaking_changes', $summary);
        $this->assertTrue($summary['breaking_changes']);
    }

    protected function createInitialSchema(): void
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

    protected function modifySchema(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('post_id')->constrained();
            $table->timestamps();
        });
    }
}
