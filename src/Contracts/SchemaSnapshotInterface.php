<?php

namespace MohamedHekal\LaravelSchemaTrack\Contracts;

interface SchemaSnapshotInterface
{
    /**
     * Take a snapshot of the current database schema
     */
    public function takeSnapshot(string $name = null): array;

    /**
     * Get a specific snapshot by name
     */
    public function getSnapshot(string $name): ?array;

    /**
     * Get all available snapshots
     */
    public function getAllSnapshots(): array;

    /**
     * Get the latest snapshot
     */
    public function getLatestSnapshot(): ?array;

    /**
     * Delete a snapshot
     */
    public function deleteSnapshot(string $name): bool;

    /**
     * Check if a snapshot exists
     */
    public function snapshotExists(string $name): bool;
}
