<?php

namespace MohamedHekal\LaravelSchemaTrack\Services;

use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaDiffInterface;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;

class SchemaDiffService implements SchemaDiffInterface
{
    protected SchemaSnapshotInterface $snapshotService;

    public function __construct(SchemaSnapshotInterface $snapshotService)
    {
        $this->snapshotService = $snapshotService;
    }

    public function compare(string $fromSnapshot, string $toSnapshot): array
    {
        $from = $this->snapshotService->getSnapshot($fromSnapshot);
        $to = $this->snapshotService->getSnapshot($toSnapshot);

        if (! $from || ! $to) {
            throw new \InvalidArgumentException('One or both snapshots not found');
        }

        return $this->calculateDiff($from['schema'], $to['schema']);
    }

    public function compareWithEnvironment(string $environment): array
    {
        // This would require connecting to another environment
        // For now, we'll return an empty diff
        return [
            'new_tables' => [],
            'removed_tables' => [],
            'modified_tables' => [],
        ];
    }

    public function getFormattedDiff(array $diff, string $format = 'text'): string
    {
        switch ($format) {
            case 'json':
                return json_encode($diff, JSON_PRETTY_PRINT);
            case 'markdown':
                return $this->formatAsMarkdown($diff);
            default:
                return $this->formatAsText($diff);
        }
    }

    public function hasBreakingChanges(array $diff): bool
    {
        // Check for breaking changes
        if (! empty($diff['removed_tables'])) {
            return true;
        }

        foreach ($diff['modified_tables'] as $table => $changes) {
            if (! empty($changes['removed_columns'])) {
                return true;
            }

            foreach ($changes['modified_columns'] as $column => $modifications) {
                if (isset($modifications['type_changed']) || isset($modifications['nullable_changed'])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getChangeSummary(array $diff): array
    {
        $summary = [
            'total_changes' => 0,
            'new_tables' => count($diff['new_tables'] ?? []),
            'removed_tables' => count($diff['removed_tables'] ?? []),
            'modified_tables' => count($diff['modified_tables'] ?? []),
            'breaking_changes' => $this->hasBreakingChanges($diff),
        ];

        foreach ($diff['modified_tables'] ?? [] as $table => $changes) {
            $summary['total_changes'] += count($changes['new_columns'] ?? []);
            $summary['total_changes'] += count($changes['removed_columns'] ?? []);
            $summary['total_changes'] += count($changes['modified_columns'] ?? []);
        }

        return $summary;
    }

    protected function calculateDiff(array $fromSchema, array $toSchema): array
    {
        $diff = [
            'new_tables' => [],
            'removed_tables' => [],
            'modified_tables' => [],
        ];

        // Find new and removed tables
        $fromTables = array_keys($fromSchema);
        $toTables = array_keys($toSchema);

        $diff['new_tables'] = array_diff($toTables, $fromTables);
        $diff['removed_tables'] = array_diff($fromTables, $toTables);

        // Find modified tables
        $commonTables = array_intersect($fromTables, $toTables);

        foreach ($commonTables as $table) {
            $tableDiff = $this->compareTable($fromSchema[$table], $toSchema[$table]);
            if (! empty($tableDiff)) {
                $diff['modified_tables'][$table] = $tableDiff;
            }
        }

        return $diff;
    }

    protected function compareTable(array $fromTable, array $toTable): array
    {
        $diff = [
            'new_columns' => [],
            'removed_columns' => [],
            'modified_columns' => [],
            'new_indexes' => [],
            'removed_indexes' => [],
            'modified_indexes' => [],
        ];

        // Compare columns
        $fromColumns = array_keys($fromTable['columns'] ?? []);
        $toColumns = array_keys($toTable['columns'] ?? []);

        $diff['new_columns'] = array_diff($toColumns, $fromColumns);
        $diff['removed_columns'] = array_diff($fromColumns, $toColumns);

        $commonColumns = array_intersect($fromColumns, $toColumns);
        foreach ($commonColumns as $column) {
            $columnDiff = $this->compareColumn(
                $fromTable['columns'][$column],
                $toTable['columns'][$column]
            );
            if (! empty($columnDiff)) {
                $diff['modified_columns'][$column] = $columnDiff;
            }
        }

        return $diff;
    }

    protected function compareColumn(array $fromColumn, array $toColumn): array
    {
        $diff = [];

        $fields = ['type', 'length', 'precision', 'scale', 'nullable', 'default', 'unsigned'];

        foreach ($fields as $field) {
            if (($fromColumn[$field] ?? null) !== ($toColumn[$field] ?? null)) {
                $diff[$field.'_changed'] = [
                    'from' => $fromColumn[$field] ?? null,
                    'to' => $toColumn[$field] ?? null,
                ];
            }
        }

        return $diff;
    }

    protected function formatAsText(array $diff): string
    {
        $output = "📊 Schema Diff Report\n";
        $output .= "====================\n\n";

        if (! empty($diff['new_tables'])) {
            $output .= "🆕 New Tables:\n";
            foreach ($diff['new_tables'] as $table) {
                $output .= "  - {$table}\n";
            }
            $output .= "\n";
        }

        if (! empty($diff['removed_tables'])) {
            $output .= "🗑️ Removed Tables:\n";
            foreach ($diff['removed_tables'] as $table) {
                $output .= "  - {$table}\n";
            }
            $output .= "\n";
        }

        if (! empty($diff['modified_tables'])) {
            $output .= "📝 Modified Tables:\n";
            foreach ($diff['modified_tables'] as $table => $changes) {
                $output .= "  - {$table}:\n";

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
                            $output .= "    ~ Changed: {$column} ({$modification})\n";
                        }
                    }
                }
            }
        }

        return $output;
    }

    protected function formatAsMarkdown(array $diff): string
    {
        $output = "# Schema Changes\n\n";

        if (! empty($diff['new_tables'])) {
            $output .= "## New Tables\n\n";
            foreach ($diff['new_tables'] as $table) {
                $output .= "- `{$table}`\n";
            }
            $output .= "\n";
        }

        if (! empty($diff['removed_tables'])) {
            $output .= "## Removed Tables\n\n";
            foreach ($diff['removed_tables'] as $table) {
                $output .= "- `{$table}`\n";
            }
            $output .= "\n";
        }

        if (! empty($diff['modified_tables'])) {
            $output .= "## Modified Tables\n\n";
            foreach ($diff['modified_tables'] as $table => $changes) {
                $output .= "### `{$table}`\n\n";

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
                            $output .= "  - {$modification}: `{$change['from']}` → `{$change['to']}`\n";
                        }
                    }
                    $output .= "\n";
                }
            }
        }

        return $output;
    }
}
