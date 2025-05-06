<?php

namespace App\Http\Controllers;

use App\Imports\VotersImport;
use App\Models\Voters;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VotersController extends Controller
{
    public function index()
    {
        return view('voters.index');
    }

    public function importExcelData(Request $request)
    {
        $request->validate([
            'import_file' => [
                'required',
                'file'
            ],
        ]);

        Excel::import(new VotersImport, $request->file('import_file'));

        return redirect()->back()->with('status', 'Imported Successfully');
    }

    public function manualAddVoter(Request $request)
    {
        $request->validate([
            'voter_id'  => 'required|unique:voters,voter_id',
            'name'      => 'required',
            'gender'    => 'required',
            'role'      => 'required',
            'programme' => 'required',
            'class'     => 'required',
            'email'     => [
                'required',
                'email',
                'unique:voters,email',
                'regex:/^[a-zA-Z0-9._%+-]+\.jnec@rub\.edu\.bt$/'
            ],  
        ], [
                'email.regex' => 'The email must end with ".jnec@rub.edu.bt".',
    
                'voter_id.unique' => 'The Voter ID is already in use.',
                'email.unique'    => 'The Email address is already registered.',
        ]);

        Voters::create([
            'voter_id'  => $request->voter_id,
            'name'      => $request->name,
            'gender'    => $request->gender,
            'role'      => $request->role,
            'programme' => $request->programme,
            'class'     => $request->class,
            'email'     => $request->email,
        ]);

        return redirect()->back()->with('success', 'Voter added successfully.');
    }
}
