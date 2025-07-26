<?php

namespace MohamedHekal\LaravelSchemaTrack\Commands;

use Illuminate\Console\Command;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;

class SchemaTrackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:track 
                            {name? : Name for the snapshot (optional)}
                            {--force : Force overwrite if snapshot exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take a snapshot of the current database schema';

    protected SchemaSnapshotInterface $snapshotService;

    public function __construct(SchemaSnapshotInterface $snapshotService)
    {
        parent::__construct();
        $this->snapshotService = $snapshotService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $force = $this->option('force');

        if ($name && $this->snapshotService->snapshotExists($name) && ! $force) {
            $this->error("Snapshot '{$name}' already exists. Use --force to overwrite.");

            return 1;
        }

        $this->info('📸 Taking schema snapshot...');

        try {
            $snapshot = $this->snapshotService->takeSnapshot($name);

            $this->info('✅ Snapshot created successfully!');
            $this->line("📁 Name: {$snapshot['name']}");
            $this->line("🕐 Timestamp: {$snapshot['timestamp']}");
            $this->line("🗄️ Database: {$snapshot['database']}");
            $this->line('📊 Tables: '.count($snapshot['schema']));

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to create snapshot: '.$e->getMessage());

            return 1;
        }
    }
}
