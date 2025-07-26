<?php

namespace MohamedHekal\LaravelSchemaTrack\Contracts;

interface SchemaDiffInterface
{
    /**
     * Compare two snapshots and return differences
     */
    public function compare(string $fromSnapshot, string $toSnapshot): array;

    /**
     * Compare with another environment
     */
    public function compareWithEnvironment(string $environment): array;

    /**
     * Get formatted diff output
     */
    public function getFormattedDiff(array $diff, string $format = 'text'): string;

    /**
     * Check if there are any breaking changes
     */
    public function hasBreakingChanges(array $diff): bool;

    /**
     * Get summary of changes
     */
    public function getChangeSummary(array $diff): array;
}
