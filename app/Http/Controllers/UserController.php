<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Country;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index(Request $request)
    {
        // $user = Auth::user();
        if ($request->ajax()) {
            $user = Auth::user();
            $data = User::where('id',$user->id)->first()->company;
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('company',function($row) {
                    return $row->name;
                })
                ->addColumn('created_at',function($row) {
                    return $row->created_at->format('D M d, Y');
                })
                ->rawColumns(['company','action'])
                ->make(true);
        }
        return view('users.index');
    }

    public function create()
    {
        // $users = User::all();
        $companies = Company::all();
        return view('users.create',compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required',
        ],[
            'company_id.required' => "Company is required",
        ]);
        $company_id = $request->company_id;
        $user = Auth::user();
       
        if($company_id)
        {
            CompanyUser::where('user_id',$user->id)->delete();
            foreach($company_id as $company)
            {
                CompanyUser::create([
                    'user_id' => $user->id,
                    'company_id' => $company
                ]);
            }
        }
        return redirect()->route('dashboard')->with('message', 'Company added successfully!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $users = User::where('id',$user->id)->with('company')->first();

        return view('users.edit',compact('users'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'company_id' => 'required',
        ],[
            'company_id.required' => "Company is required",
        ]);
        $company_id = $request->company_id;
        $user = Auth::user();
       
        if($company_id)
        {
            CompanyUser::where('user_id',$user->id)->delete();
            foreach($company_id as $company)
            {
                CompanyUser::create([
                    'user_id' => $user->id,
                    'company_id' => $company
                ]);
            }
        }
        return redirect()->route('dashboard')->with('message', 'Company updated successfully!');
    }
}
