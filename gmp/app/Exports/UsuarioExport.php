<?php 
namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UsuarioExport implements FromView
{
    public function view(): View
    {
        return view('admin.users.index', [
            'users' => User::all()
        ]);
    }
}