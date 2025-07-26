<?php

namespace MohamedHekal\LaravelSchemaTrack\Services;

use Doctrine\DBAL\Schema\Table;
use Illuminate\Support\Facades\DB;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;

class SchemaSnapshotService implements SchemaSnapshotInterface
{
    protected string $storagePath;

    public function __construct()
    {
        $this->storagePath = config('schema-track.storage_path', storage_path('schema-track'));

        if (! file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    public function takeSnapshot(?string $name = null): array
    {
        $name = $name ?: 'snapshot_'.date('Y_m_d_His');
        $filename = $name.'.json';

        $schema = $this->extractSchema();
        $snapshot = [
            'name' => $name,
            'timestamp' => date('c'),
            'database' => config('database.default'),
            'schema' => $schema,
        ];

        $this->saveSnapshot($filename, $snapshot);

        return $snapshot;
    }

    public function getSnapshot(string $name): ?array
    {
        $filename = $name.'.json';
        $path = $this->storagePath.'/'.$filename;

        if (! file_exists($path)) {
            return null;
        }

        return json_decode(file_get_contents($path), true);
    }

    public function getAllSnapshots(): array
    {
        $snapshots = [];
        $files = glob($this->storagePath.'/*.json');

        foreach ($files as $file) {
            $content = json_decode(file_get_contents($file), true);
            if ($content) {
                $snapshots[] = $content;
            }
        }

        // Sort by timestamp
        usort($snapshots, function ($a, $b) {
            return strtotime($a['timestamp']) <=> strtotime($b['timestamp']);
        });

        return $snapshots;
    }

    public function getLatestSnapshot(): ?array
    {
        $snapshots = $this->getAllSnapshots();

        return end($snapshots) ?: null;
    }

    public function deleteSnapshot(string $name): bool
    {
        $filename = $name.'.json';
        $path = $this->storagePath.'/'.$filename;

        if (file_exists($path)) {
            return unlink($path);
        }

        return false;
    }

    public function snapshotExists(string $name): bool
    {
        $filename = $name.'.json';
        $path = $this->storagePath.'/'.$filename;

        return file_exists($path);
    }

    protected function extractSchema(): array
    {
        $connection = DB::connection();
        $schemaManager = $connection->getDoctrineSchemaManager();
        $schema = $schemaManager->createSchema();

        $tables = [];
        $excludeTables = config('schema-track.exclude_tables', ['migrations', 'failed_jobs']);

        foreach ($schema->getTables() as $table) {
            $tableName = $table->getName();

            if (in_array($tableName, $excludeTables)) {
                continue;
            }

            $tables[$tableName] = [
                'columns' => $this->extractColumns($table),
                'indexes' => $this->extractIndexes($table),
                'foreign_keys' => $this->extractForeignKeys($table),
            ];
        }

        return $tables;
    }

    protected function extractColumns(Table $table): array
    {
        $columns = [];

        foreach ($table->getColumns() as $column) {
            $columns[$column->getName()] = [
                'type' => $column->getType()->getName(),
                'length' => $column->getLength(),
                'precision' => $column->getPrecision(),
                'scale' => $column->getScale(),
                'nullable' => ! $column->getNotnull(),
                'default' => $column->getDefault(),
                'auto_increment' => $column->getAutoincrement(),
                'unsigned' => $column->getUnsigned(),
                'fixed' => $column->getFixed(),
            ];
        }

        return $columns;
    }

    protected function extractIndexes(Table $table): array
    {
        $indexes = [];

        foreach ($table->getIndexes() as $index) {
            $indexes[$index->getName()] = [
                'columns' => $index->getColumns(),
                'is_unique' => $index->isUnique(),
                'is_primary' => $index->isPrimary(),
            ];
        }

        return $indexes;
    }

    protected function extractForeignKeys(Table $table): array
    {
        $foreignKeys = [];

        foreach ($table->getForeignKeys() as $foreignKey) {
            $foreignKeys[$foreignKey->getName()] = [
                'local_columns' => $foreignKey->getLocalColumns(),
                'foreign_table' => $foreignKey->getForeignTableName(),
                'foreign_columns' => $foreignKey->getForeignColumns(),
                'on_delete' => $foreignKey->onDelete(),
                'on_update' => $foreignKey->onUpdate(),
            ];
        }

        return $foreignKeys;
    }

    protected function saveSnapshot(string $filename, array $snapshot): void
    {
        $path = $this->storagePath.'/'.$filename;
        file_put_contents($path, json_encode($snapshot, JSON_PRETTY_PRINT));
    }
}
