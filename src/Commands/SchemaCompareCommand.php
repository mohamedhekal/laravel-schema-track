<?php

namespace MohamedHekal\LaravelSchemaTrack\Commands;

use Illuminate\Console\Command;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaDiffInterface;

class SchemaCompareCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:compare 
                            {--env= : Environment to compare with (staging, production)}
                            {--format=text : Output format (text, markdown, json)}
                            {--breaking-only : Show only breaking changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare current schema with another environment';

    protected SchemaDiffInterface $diffService;

    public function __construct(SchemaDiffInterface $diffService)
    {
        parent::__construct();
        $this->diffService = $diffService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $environment = $this->option('env');
        $format = $this->option('format');
        $breakingOnly = $this->option('breaking-only');

        if (! $environment) {
            $this->error('Please specify an environment with --env option.');

            return 1;
        }

        $this->info("🔄 Comparing with {$environment} environment...");

        try {
            $diff = $this->diffService->compareWithEnvironment($environment);

            if ($breakingOnly && ! $this->diffService->hasBreakingChanges($diff)) {
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
            $this->error('❌ Failed to compare with environment: '.$e->getMessage());

            return 1;
        }
    }
}
