<?php

namespace App\Http\Services\Date;

use App\Models\Configuration;
use App\Models\Employee;
use App\Models\EmployeeRequest;
use App\Models\Repositories\Configuration\ConfigurationRepositoryInterface;
use Carbon\Carbon;

class ManipulateWorkDaysService
{
    private $configurationRepository;

    public function __construct(ConfigurationRepositoryInterface $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    public function getWorkDay(Employee $employee)
    {
        $importantDate= $this->configurationRepository->getCompanyImportantDateConfigurations($employee->company);
        $cutOffDate = $importantDate->cut_off; // assuming static at day 20th every month
        $now = Carbon::now();
        $salary = $employee->salary;
        $lastCutOff = Carbon::parse(
            Carbon::now()->subMonthNoOverflow()->format('Y-m') . "-$cutOffDate"
        );

        $holidaysConfig = collect(json_decode(Configuration::select([
            'data->id as ID'
        ])->where('slug', 'holidays')->first()->ID));

        $holidays = $holidaysConfig->map(function ($holiday) {
            return Carbon::parse($holiday->date);
        });

        // Filtering sat sun and holi-fucking-days
        $workDays = $lastCutOff->addDay()->diffInDaysFiltered(function (Carbon $date) use ($holidays, &$periodPopulations) {
            return $date;
        }, $now);

        if ((int)$now->format('d') === $cutOffDate) {
            $workDays = 0;
            $lastCutOff = Carbon::now();
        }

        $advancedSalary = (int) $this->doTheMath($salary, $workDays);

        $histories = EmployeeRequest::where('employee_uuid', $employee->uuid)
            ->where('status', 1)
            ->whereBetween('created_at', [$lastCutOff, $now])
            ->sum('amount');

        return [
            'cutOffDate' => $lastCutOff->subDay()->format('d-m-Y'),
            'today' => $now->format('d-m-Y'),
            'workDays' => $workDays,
            'salary' => $salary,
            'payday' => Carbon::parse(Carbon::now()->format('Y-m') . "-" .$importantDate->payday),
            'advancedSalary' => $advancedSalary - $histories,
        ];
    }

    private function doTheMath(int $salary, int $workDays)
    {
        return ($salary / 30) * $workDays;
    }
}
