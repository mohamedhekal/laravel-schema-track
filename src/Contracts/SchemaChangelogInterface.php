<?php

namespace MohamedHekal\LaravelSchemaTrack\Contracts;

interface SchemaChangelogInterface
{
    /**
     * Generate changelog between two snapshots
     */
    public function generate(string $fromSnapshot, string $toSnapshot, string $format = 'markdown'): string;

    /**
     * Generate changelog for all snapshots
     */
    public function generateFull(string $format = 'markdown'): string;

    /**
     * Save changelog to file
     */
    public function saveToFile(string $content, string $filename): bool;

    /**
     * Get changelog for specific date range
     */
    public function generateForDateRange(string $fromDate, string $toDate, string $format = 'markdown'): string;
}
