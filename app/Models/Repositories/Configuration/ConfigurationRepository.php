<?php

namespace App\Models\Repositories\Configuration;

use App\Models\Company;
use App\Models\Configuration;

class ConfigurationRepository implements ConfigurationRepositoryInterface
{
    private $model;

    public function __construct(Configuration $model)
    {
        $this->model = $model;
    }

    public function getCompanyImportantDateConfigurations(Company $company)
    {
        $importantDates = $this->model->select('data')->where('slug', 'important_date')->first();

        $importantDates->data = collect(json_decode($importantDates->data));

        return $importantDates->data->where('company_id', 1)->first();
    }
}