<?php

namespace App\Exports;

use App\Models\Trip;
use Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserWorkingDaysExport implements ShouldAutoSize, FromArray, WithHeadings
{
    use Exportable;

    public function __construct($user, $from, $to)
    {
        $this->user = $user;
        $this->from = $from;
        $this->to = $to;
    }

    public function array(): array
    {
        $trips = Trip::where('user_email', $this->user->email)
            ->where('companyId', auth('admin')->user()->companyId)
            ->where('date', '>=', $this->from)
            ->where('date', '<=', $this->to)
            ->get();

        $arrayMensilita = [];

        foreach ($trips as $n => $trip) {
            $workingday = [];
            $date = new Carbon($trip->date);

            if ($n === 0) {
                array_push($workingday, $this->user->name);
                array_push($workingday, $date->format('d-m-Y'));
                array_push($workingday, count($trips));
            } else {
                array_push($workingday, '');
                array_push($workingday, $date->format('d-m-Y'));
            }

            array_push($arrayMensilita, $workingday);
        }

        return $arrayMensilita;
    }

    public function headings(): array
    {
        $name = __('Name');
        $work = __('Working days');
        $number = __('Number of working days');
        $result = [$name, $work, $number];

        return $result;
    }
}
