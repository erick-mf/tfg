<?php

namespace App\Repositories\ZReport;

interface ZReportRepositoryInterface
{
    public function findById(int $id);

    public function findLast();

    public function getPaginateHistory(int $perPage = 10);

    public function create(array $data);

    public function getSummaryForNewReport($startDate);
}
