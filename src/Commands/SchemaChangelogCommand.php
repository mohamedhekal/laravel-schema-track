<?php

namespace MohamedHekal\LaravelSchemaTrack\Commands;

use Illuminate\Console\Command;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaChangelogInterface;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;

class SchemaChangelogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:changelog 
                            {--from= : From snapshot name or "latest"}
                            {--to= : To snapshot name or "latest"}
                            {--format=markdown : Output format (markdown, json, text)}
                            {--output= : Output file path (optional)}
                            {--full : Generate full changelog for all snapshots}
                            {--date-from= : Start date for changelog (YYYY-MM-DD)}
                            {--date-to= : End date for changelog (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate changelog from schema snapshots';

    protected SchemaChangelogInterface $changelogService;
    protected SchemaSnapshotInterface $snapshotService;

    public function __construct(
        SchemaChangelogInterface $changelogService,
        SchemaSnapshotInterface $snapshotService
    ) {
        parent::__construct();
        $this->changelogService = $changelogService;
        $this->snapshotService = $snapshotService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $format = (string) $this->option('format');
        $output = (string) $this->option('output');
        $full = (bool) $this->option('full');
        $dateFrom = (string) $this->option('date-from');
        $dateTo = (string) $this->option('date-to');

        try {
            if ($full) {
                $content = $this->changelogService->generateFull($format);
            } elseif ($dateFrom && $dateTo) {
                $content = $this->changelogService->generateForDateRange($dateFrom, $dateTo, $format);
            } else {
                $from = (string) ($this->option('from') ?: 'latest');
                $to = (string) ($this->option('to') ?: 'latest');

                $fromSnapshot = $this->resolveSnapshotName($from);
                $toSnapshot = $this->resolveSnapshotName($to);

                if (!$fromSnapshot || !$toSnapshot) {
                    $this->error('Could not resolve snapshot names. Use --from and --to options.');
                    return 1;
                }

                $content = $this->changelogService->generate($fromSnapshot, $toSnapshot, $format);
            }

            if ($output) {
                if ($this->changelogService->saveToFile($content, $output)) {
                    $this->info("✅ Changelog saved to: {$output}");
                } else {
                    $this->error("❌ Failed to save changelog to: {$output}");
                    return 1;
                }
            } else {
                $this->line($content);
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to generate changelog: " . $e->getMessage());
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
