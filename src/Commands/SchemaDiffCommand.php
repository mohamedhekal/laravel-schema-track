<?php

namespace MohamedHekal\LaravelSchemaTrack\Commands;

use Illuminate\Console\Command;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaDiffInterface;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;

class SchemaDiffCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:diff 
                            {--from= : From snapshot name or "latest"}
                            {--to= : To snapshot name or "latest"}
                            {--format=text : Output format (text, markdown, json)}
                            {--breaking-only : Show only breaking changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare two schema snapshots and show differences';

    protected SchemaDiffInterface $diffService;
    protected SchemaSnapshotInterface $snapshotService;

    public function __construct(
        SchemaDiffInterface $diffService,
        SchemaSnapshotInterface $snapshotService
    ) {
        parent::__construct();
        $this->diffService = $diffService;
        $this->snapshotService = $snapshotService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $from = $this->option('from') ?: 'latest';
        $to = $this->option('to') ?: 'latest';
        $format = $this->option('format');
        $breakingOnly = $this->option('breaking-only');

        // Resolve snapshot names
        $fromSnapshot = $this->resolveSnapshotName($from);
        $toSnapshot = $this->resolveSnapshotName($to);

        if (!$fromSnapshot || !$toSnapshot) {
            $this->error('Could not resolve snapshot names. Use --from and --to options.');
            return 1;
        }

        try {
            $diff = $this->diffService->compare($fromSnapshot, $toSnapshot);

            if ($breakingOnly && !$this->diffService->hasBreakingChanges($diff)) {
                $this->info('✅ No breaking changes detected.');
                return 0;
            }

            $output = $this->diffService->getFormattedDiff($diff, $format);
            $this->line($output);

            // Show summary
            $summary = $this->diffService->getChangeSummary($diff);
            $this->newLine();
            $this->info("📊 Summary: {$summary['total_changes']} total changes");

            if ($summary['breaking_changes']) {
                $this->warn('⚠️ Breaking changes detected!');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to compare snapshots: " . $e->getMessage());
            return 1;
        }
    }

    protected function resolveSnapshotName(string $name): ?string
    {
        if ($name === 'latest') {
            $latest = $this->snapshotService->getLatestSnapshot();
            return $latest ? $latest['name'] : null;
        }

        if ($this->snapshotService->snapshotExists($name)) {
            return $name;
        }

        return null;
    }
}
