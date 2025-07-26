<?php

namespace MohamedHekal\LaravelSchemaTrack\Commands;

use Illuminate\Console\Command;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;

class SchemaListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:list 
                            {--format=table : Output format (table, json)}
                            {--limit= : Limit number of snapshots to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available schema snapshots';

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
        $format = $this->option('format');
        $limit = $this->option('limit');

        try {
            $snapshots = $this->snapshotService->getAllSnapshots();

            if (empty($snapshots)) {
                $this->info('No snapshots found.');

                return 0;
            }

            if ($limit && is_numeric($limit)) {
                $snapshots = array_slice($snapshots, -(int) $limit);
            }

            if ($format === 'json') {
                $this->line(json_encode($snapshots, JSON_PRETTY_PRINT));
            } else {
                $this->displayTable($snapshots);
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to list snapshots: '.$e->getMessage());

            return 1;
        }
    }

    protected function displayTable(array $snapshots): void
    {
        $headers = ['Name', 'Timestamp', 'Database', 'Tables'];
        $rows = [];

        foreach ($snapshots as $snapshot) {
            $rows[] = [
                $snapshot['name'],
                date('Y-m-d H:i:s', strtotime($snapshot['timestamp'])),
                $snapshot['database'],
                count($snapshot['schema']),
            ];
        }

        $this->table($headers, $rows);
    }
}
