<?php

namespace App\Models\Repositories\Configuration;

use App\Models\Company;

interface ConfigurationRepositoryInterface
{
    public function getCompanyImportantDateConfigurations(Company $company);
}