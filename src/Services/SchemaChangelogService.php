<?php

namespace MohamedHekal\LaravelSchemaTrack\Services;

use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaChangelogInterface;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaDiffInterface;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;

class SchemaChangelogService implements SchemaChangelogInterface
{
    protected SchemaDiffInterface $diffService;
    protected SchemaSnapshotInterface $snapshotService;

    public function __construct(
        SchemaDiffInterface $diffService,
        SchemaSnapshotInterface $snapshotService
    ) {
        $this->diffService = $diffService;
        $this->snapshotService = $snapshotService;
    }

    public function generate(string $fromSnapshot, string $toSnapshot, string $format = 'markdown'): string
    {
        $diff = $this->diffService->compare($fromSnapshot, $toSnapshot);

        switch ($format) {
            case 'json':
                return json_encode($diff, JSON_PRETTY_PRINT);
            case 'markdown':
                return $this->generateMarkdownChangelog($diff, $fromSnapshot, $toSnapshot);
            default:
                return $this->generateTextChangelog($diff, $fromSnapshot, $toSnapshot);
        }
    }

    public function generateFull(string $format = 'markdown'): string
    {
        $snapshots = $this->snapshotService->getAllSnapshots();

        if (count($snapshots) < 2) {
            return "No snapshots available for changelog generation.\n";
        }

        $output = "# Complete Schema Changelog\n\n";
        $output .= 'Generated on: '.date('Y-m-d H:i:s')."\n\n";

        for ($i = 1; $i < count($snapshots); $i++) {
            $from = $snapshots[$i - 1];
            $to = $snapshots[$i];

            $diff = $this->diffService->compare($from['name'], $to['name']);
            $output .= $this->generateMarkdownChangelog($diff, $from['name'], $to['name']);
            $output .= "\n---\n\n";
        }

        return $output;
    }

    public function saveToFile(string $content, string $filename): bool
    {
        try {
            file_put_contents($filename, $content);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateForDateRange(string $fromDate, string $toDate, string $format = 'markdown'): string
    {
        $snapshots = $this->snapshotService->getAllSnapshots();
        $filteredSnapshots = [];

        foreach ($snapshots as $snapshot) {
            $timestamp = strtotime($snapshot['timestamp']);
            if ($timestamp >= strtotime($fromDate) && $timestamp <= strtotime($toDate)) {
                $filteredSnapshots[] = $snapshot;
            }
        }

        if (count($filteredSnapshots) < 2) {
            return "No snapshots found in the specified date range.\n";
        }

        $output = "# Schema Changelog ({$fromDate} to {$toDate})\n\n";

        for ($i = 1; $i < count($filteredSnapshots); $i++) {
            $from = $filteredSnapshots[$i - 1];
            $to = $filteredSnapshots[$i];

            $diff = $this->diffService->compare($from['name'], $to['name']);
            $output .= $this->generateMarkdownChangelog($diff, $from['name'], $to['name']);
            $output .= "\n---\n\n";
        }

        return $output;
    }

    protected function generateMarkdownChangelog(array $diff, string $fromSnapshot, string $toSnapshot): string
    {
        $from = $this->snapshotService->getSnapshot($fromSnapshot);
        $to = $this->snapshotService->getSnapshot($toSnapshot);

        $fromDate = $from ? date('Y-m-d H:i:s', strtotime($from['timestamp'])) : 'Unknown';
        $toDate = $to ? date('Y-m-d H:i:s', strtotime($to['timestamp'])) : 'Unknown';

        $output = "## Schema Changes ({$fromDate} → {$toDate})\n\n";

        if (! empty($diff['new_tables'])) {
            $output .= "### 🆕 New Tables\n\n";
            foreach ($diff['new_tables'] as $table) {
                $output .= "- **`{$table}`**\n";
            }
            $output .= "\n";
        }

        if (! empty($diff['removed_tables'])) {
            $output .= "### 🗑️ Removed Tables\n\n";
            foreach ($diff['removed_tables'] as $table) {
                $output .= "- **`{$table}`**\n";
            }
            $output .= "\n";
        }

        if (! empty($diff['modified_tables'])) {
            $output .= "### 📝 Modified Tables\n\n";
            foreach ($diff['modified_tables'] as $table => $changes) {
                $output .= "#### `{$table}`\n\n";

                if (! empty($changes['new_columns'])) {
                    $output .= "**Added Columns:**\n";
                    foreach ($changes['new_columns'] as $column) {
                        $output .= "- `{$column}`\n";
                    }
                    $output .= "\n";
                }

                if (! empty($changes['removed_columns'])) {
                    $output .= "**Removed Columns:**\n";
                    foreach ($changes['removed_columns'] as $column) {
                        $output .= "- `{$column}`\n";
                    }
                    $output .= "\n";
                }

                if (! empty($changes['modified_columns'])) {
                    $output .= "**Modified Columns:**\n";
                    foreach ($changes['modified_columns'] as $column => $modifications) {
                        $output .= "- `{$column}`:\n";
                        foreach ($modifications as $modification => $change) {
                            $modificationName = str_replace('_changed', '', $modification);
                            $output .= "  - **{$modificationName}**: `{$change['from']}` → `{$change['to']}`\n";
                        }
                    }
                    $output .= "\n";
                }
            }
        }

        // Add summary
        $summary = $this->diffService->getChangeSummary($diff);
        $output .= "### 📊 Summary\n\n";
        $output .= "- **Total Changes**: {$summary['total_changes']}\n";
        $output .= "- **New Tables**: {$summary['new_tables']}\n";
        $output .= "- **Removed Tables**: {$summary['removed_tables']}\n";
        $output .= "- **Modified Tables**: {$summary['modified_tables']}\n";
        $output .= '- **Breaking Changes**: '.($summary['breaking_changes'] ? 'Yes' : 'No')."\n\n";

        return $output;
    }

    protected function generateTextChangelog(array $diff, string $fromSnapshot, string $toSnapshot): string
    {
        $from = $this->snapshotService->getSnapshot($fromSnapshot);
        $to = $this->snapshotService->getSnapshot($toSnapshot);

        $fromDate = $from ? date('Y-m-d H:i:s', strtotime($from['timestamp'])) : 'Unknown';
        $toDate = $to ? date('Y-m-d H:i:s', strtotime($to['timestamp'])) : 'Unknown';

        $output = "Schema Changes ({$fromDate} → {$toDate})\n";
        $output .= str_repeat('=', strlen($output))."\n\n";

        if (! empty($diff['new_tables'])) {
            $output .= "New Tables:\n";
            foreach ($diff['new_tables'] as $table) {
                $output .= "  + {$table}\n";
            }
            $output .= "\n";
        }

        if (! empty($diff['removed_tables'])) {
            $output .= "Removed Tables:\n";
            foreach ($diff['removed_tables'] as $table) {
                $output .= "  - {$table}\n";
            }
            $output .= "\n";
        }

        if (! empty($diff['modified_tables'])) {
            $output .= "Modified Tables:\n";
            foreach ($diff['modified_tables'] as $table => $changes) {
                $output .= "  {$table}:\n";

                if (! empty($changes['new_columns'])) {
                    foreach ($changes['new_columns'] as $column) {
                        $output .= "    + Added: {$column}\n";
                    }
                }

                if (! empty($changes['removed_columns'])) {
                    foreach ($changes['removed_columns'] as $column) {
                        $output .= "    - Removed: {$column}\n";
                    }
                }

                if (! empty($changes['modified_columns'])) {
                    foreach ($changes['modified_columns'] as $column => $modifications) {
                        foreach ($modifications as $modification => $change) {
                            $modificationName = str_replace('_changed', '', $modification);
                            $output .= "    ~ Changed: {$column} ({$modificationName})\n";
                        }
                    }
                }
            }
        }

        return $output;
    }
}
