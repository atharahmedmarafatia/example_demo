<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Country;
use App\Models\User;
use DataTables;
use Validator;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Company::get();
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('user_id',function($row) {
                    $btn_user = '<a href="" data-id="'.$row->id.'" id="show" class="btn btn-primary btn-sm">show</a>';
                    return $btn_user;
                })
                ->addColumn('country_id',function($row) {
                    return $row->country->name;
                })
                ->addColumn('created_at',function($row) {
                    return $row->created_at->format('D M d, Y');
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('company.edit',$row->id).'" class="btn btn-warning btn-sm">Edit</a>';
                    return $btn;
                })
                ->rawColumns(['user_id','action'])
                ->make(true);
        }
    }

    public function create()
    {
        $users = User::all();
        $country = Country::all();
        return view('company.create',compact('users','country'));
    }

    public function getUserData(Request $request)
    {
        $company_id = Company::find($request->id);
        $users = $company_id->users;
        $html = [];
        $html[] = "<tr>
        <th width='30%'><b>Name:</b></td>
        </tr>";
        foreach($users as $user)
        {
            if(!empty($user)){
                $html[] ="<tr>
                    <td width='70%'> ".$user->name."</td>
                </tr>";
            }
        }
        $response['html'] = $html;
        return response()->json($response);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|alpha',
            'country_id' => 'required',
            'user_ids' => 'required',
        ],[
            'user_ids.required' => "User name is required",
        ]);
        $name = $request->name;
        $user_ids = $request->user_ids;
        $country = $request->country_id;
        $company = Company::create([
            'name' => $name,
            'country_id' => $country,
        ]);
        if($company)
        {
            foreach($user_ids as $user)
            {
                CompanyUser::create([
                    'user_id' => $user,
                    'company_id' => $company->id
                ]);
            }
        }
        return redirect()->route('dashboard')->with('message', 'Company created successfully!');
    }
    
    public function edit($id)
    {
        $company = Company::where('id',$id)->with('users')->first();
        $users = User::all();
        $country = Country::all();
        return view('company.edit',compact('company','users','country'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::find($id);
        $validated = $request->validate([
            'name' => 'required|max:255|alpha',
            'country_id' => 'required',
            'user_ids' => 'required',
        ],[
            'user_ids.required' => "User name is required",
        ]);
        if(isset($request->name))
        {
            $name = $request->name;
        }
        if(isset($request->country_id))
        {
            $country = $request->country_id;
        }
        if(isset($request->user_ids))
        {
            $user_ids = $request->user_ids;
        }

        Company::where('id',$company->id)->update([
            'name' => $name,
            'country_id' => $country,
        ]);

        $company = Company::where('id',$company->id)->first();
        if($company)
        {
            CompanyUser::where('company_id',$company->id)->delete();
            foreach($user_ids as $user)
            {
                CompanyUser::create([
                    'user_id' => $user,
                    'company_id' => $company->id
                ]);
            }
        }
        return redirect()->route('dashboard')->with('message', 'Company updated successfully!');
    }
    
    public function getAllDetails(Request $request)
    {
        $user_id = $request->user_id;
        $users = User::where('id',$user_id)->with('company')->get();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                "success" => false,
                'code' => 400,
                'message' => $validator->errors(),
            ],400);
        }
        foreach($users as $user)
        {
            $response['users'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('D M d, Y'),
                'company' => $user->company,
    
            ];
            foreach($user->company as $company)
            {
                unset($company->pivot);
            }
        }
        return response()->json([
            "success" => true,
            'code' => 200,
            "message" => "User Company Details fetch successfully.",
            "data" => $response
        ]);
    }
}
