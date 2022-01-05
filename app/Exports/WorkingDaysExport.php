<?php

namespace App\Exports;

use App\Models\Trip;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WorkingDaysExport implements ShouldAutoSize, FromArray, WithHeadings
{
    use Exportable;

    public function __construct($month, $year, $giorniDelMese)
    {
        $this->month = $month;
        $this->year = $year;
        $this->giorniDelMese = $giorniDelMese;
    }

    public function array(): array
    {
        $utenti = Trip::select('user_email', 'name')->distinct('user_email')->whereMonth('date', $this->month)->whereYear('date', $this->year)->get();

        $arrayMensilita = [];

        foreach ($utenti as $utente) {

            $giorni = Trip::select('date')->distinct('date')->where('user_email', $utente->user_email)->whereMonth('date', $this->month)->whereYear('date', $this->year)->get();

            $mensilita = [$utente->name, $giorni->count()];


            for ($i = 1; $i <= $this->giorniDelMese; $i++) {
                $controllo = 0;
                foreach ($giorni as $giorno) {
                    if ($giorno->date->format('d') == $i) {
                        array_push($mensilita, 'X');
                        $controllo = 1;
                    }
                }
                if ($controllo == 0) {
                    array_push($mensilita, '');
                }
            }
            array_push($arrayMensilita, $mensilita);
            $mensilita = [];
        }
        return $arrayMensilita;
    }

    public function headings(): array
    {
        $name = __('Name');
        $work = __('Working Days');
        $risultato = [$name, $work];

        for ($i = 1; $i <= $this->giorniDelMese; $i++) {
            array_push($risultato, $i);
        }

        return $risultato;
    }
}
