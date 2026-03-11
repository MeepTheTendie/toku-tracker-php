<?php

use PHPUnit\Framework\TestCase;

class TokuTrackerTest extends TestCase
{
    public function testGetFranchises()
    {
        $franchises = TokuTracker::getFranchises();
        $this->assertNotEmpty($franchises);
        $this->assertIsArray($franchises);
    }

    public function testFranchisesHaveRequiredFields()
    {
        $franchises = TokuTracker::getFranchises();
        foreach ($franchises as $key => $franchise) {
            $this->assertArrayHasKey('name', $franchise);
            $this->assertArrayHasKey('icon', $franchise);
            $this->assertArrayHasKey('color', $franchise);
            $this->assertArrayHasKey('bg_gradient', $franchise);
        }
    }

    public function testGetStats()
    {
        $stats = TokuTracker::getStats();
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_series', $stats);
        $this->assertArrayHasKey('total_episodes', $stats);
        $this->assertArrayHasKey('watched_episodes', $stats);
    }

    public function testGetFranchiseStats()
    {
        $stats = TokuTracker::getFranchiseStats();
        $this->assertNotEmpty($stats);
        $this->assertIsArray($stats);
    }
}
