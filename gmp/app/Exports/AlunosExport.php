<?php

namespace App\Exports;

use App\Aluno;
use Maatwebsite\Excel\Concerns\FromCollection;

class AlunosExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Aluno::all();
    }
}
