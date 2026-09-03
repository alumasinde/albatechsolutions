<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Modules\Growth\Repository\GrowthAnalyticsRepository;
use PHPUnit\Framework\TestCase;

/**
 * Read-only integration coverage for the growth repository.
 *
 * This specifically executes servicePerformance(), which previously contained
 * a repeated :days placeholder and failed with PDO HY093 when native prepares
 * were enabled.
 *
 * Requires a configured test/local database with V3/V4 migrations applied.
 */
final class GrowthAnalyticsRepositoryTest extends TestCase
{
    public function testServicePerformanceExecutesWithNativePdoPrepares(): void
    {
        $repository = new GrowthAnalyticsRepository();

        $rows = $repository->servicePerformance(30);

        self::assertIsArray($rows);
    }

    public function testOtherGrowthReadQueriesExecute(): void
    {
        $repository = new GrowthAnalyticsRepository();

        self::assertIsArray($repository->summary(30));
        self::assertIsArray($repository->topPages(30, 5));
        self::assertIsArray($repository->sources(30, 5));
        self::assertIsArray($repository->eventCounts(30));
        self::assertIsArray($repository->assistantIntentInsights(30, 5));
        self::assertIsArray($repository->contentGaps(30, 5));
        self::assertIsArray($repository->openNotes(5));
    }
}
