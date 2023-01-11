<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Country;
use App\Models\User;
use DataTables;
use Illuminate\Support\Facades\File as Files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;

class UserController extends Controller
{

    public function index(Request $request)
    {
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
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('user.company.edit',$row->id).'" class="btn btn-warning btn-sm">Edit</a>';
                    return $btn;
                })
                ->rawColumns(['company','action'])
                ->make(true);
        }
        return view('users.index');
    }

    public function create()
    {
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
        $companies = Company::all();
        $users = User::where('id',$user->id)->with('company')->first();
        return view('users.edit',compact('users','companies'));
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
        return redirect()->route('user.company.index')->with('message', 'Company updated successfully!');
    }


    public function multiDB()
    {
        $mysqlHostName      = env('DB_HOST_SECOND');
        $mysqlUserName      = env('DB_USERNAME_SECOND');
        $mysqlPassword      = env('DB_PASSWORD_SECOND');
        $DbName             = env('DB_DATABASE_SECOND');
        $backup_name        = "mybackup.sql";

        $tables = DB::connection('mysql')->select('SHOW TABLES');
        // dd($tables);
        $tables2 = DB::connection('mysql_second')->select('SHOW TABLES');
        //print_r(DB::connection('mysql')->select('SHOW TABLES')); exit;
        
        // foreach(DB::connection('mysql')->select('SHOW TABLES') as $table) {
            
        //     $all_table_names = get_object_vars($table);
        //     // Schema::connection('mysql')->drop($all_table_names[key($all_table_names)]);
        //     // DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');
        //     Schema::connection('mysql')->dropIfExists($all_table_names[key($all_table_names)]);
        // }
        // $tables = array("billing_cycles"); //here your tables...

         $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword",array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        
        // $get_all_table_query = "SHOW TABLES";
        // $statement = $connect->prepare($get_all_table_query);
        // $statement->execute();
        // $result = $statement->fetchAll();
        

        $output_create = '';
        $output_insert = '';
        foreach($tables2 as $key => $table)
        {
            // print_r($table); exit;
            $existing_table_names = get_object_vars($table);
            // print_r($existing_table_names[key($existing_table_names)]); exit;
            if(isset($tables[$key]->Tables_in_relation))
            {
                // print_r($tables[$key]->Tables_in_relation); exit;
                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');
                Schema::connection('mysql')->dropIfExists($tables[$key]->Tables_in_relation);
                // print_r("success"); exit;
            }

            if(isset($existing_table_names[key($existing_table_names)]))
            {
                // DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');
                // Schema::connection('mysql')->dropIfExists($tables2[$key]->Tables_in_billing_new);

                // Schema::connection('mysql')->create($tables2[$key]->Tables_in_billing_new, function($table)
                // {
                    
                // });
            
            
                $show_table_query = "SHOW CREATE TABLE " . $existing_table_names[key($existing_table_names)] . "";
                $statement = $connect->prepare($show_table_query);
                $statement->execute();
                $show_table_result = $statement->fetchAll();
                // DB::connection('mysql')->query($show_table_result);

                // echo "<pre>";
                //     print_r($show_table_result); exit;
                foreach($show_table_result as $show_table_row)
                {
                    // echo "<pre>";
                    // print_r($show_table_row['Create Table']); exit;
                    $output_create = "\n\n" . $show_table_row["Create Table"] . ";\n\n";
                    // DB::connection('mysql')->query($output);
                    DB::connection('mysql')->select(DB::connection('mysql')->raw($output_create));
                     //echo "<pre>";
                     //print_r($output_create); //exit;
                }
                $select_query = "SELECT * FROM " . $existing_table_names[key($existing_table_names)] . "";
                $statement = $connect->prepare($select_query);
                $statement->execute();
                $total_row = $statement->rowCount();
                $multiquery = $statement->fetchAll(PDO::FETCH_OBJ);
                $multiquery = json_decode(json_encode($multiquery), true);
                $mutichunk = array_chunk($multiquery,4000,true);
                // print_r($mutichunk[1]); exit;
                if(count($mutichunk) > 0){
                    foreach($mutichunk as $keys => $val)
                    {
                        DB::connection('mysql')->table($existing_table_names[key($existing_table_names)])->insert(
                            $mutichunk[$keys]
                        );
                    }
                
                }
               
                // $multiquery = array_map(function ($short, $long) {
                //     return array(
                //         'short' => $short,
                //         'long'  => $long
                //     );
                // }, array_keys($multiquery), $multiquery);
                // echo "<pre>";
                // print_r($multiquery); exit;

                // foreach($multiquery as $mulque)
                // {
                   
                //     $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                //     $table_column_array = array_keys($single_result);
                //     $table_value_array = array_values($single_result);
                //     echo "<pre>";
                //     print_r($table_value_array); exit;
                //     $output_insert = "\nINSERT INTO $table->Tables_in_billing_new (";
                //     $output_insert .= "" . implode(", ", $table_column_array) . ") VALUES (";
                //     $output_insert .= "'" . implode("','", $table_value_array) . "');\n";
                // }

                // for($count=0; $cot_r($output_create);unt<$total_row; $count++)
                // {
                //     $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                //     echo "<pre>";
                //     print_r($single_result); exit;
                //     // foreach($single_results as $key => $single_result)
                //     // {
                //         $table_column_array = array_keys($single_result);
                //         $table_value_array = array_values($single_result);
                //         // echo "<pre>";
                //         // print_r($table_value_array); exit;
                //         $output_insert = "\nINSERT INTO $table->Tables_in_billing_new (";
                //         $output_insert .= "" . implode(", ", $table_column_array) . ") VALUES (";
                //         $output_insert .= "'" . implode("','", $table_value_array) . "');\n";
                //     // }
                //     // DB::connection('mysql')->select(DB::connection('mysql')->raw($output_insert));
                //     echo "<pre>";
                //     print_r($output_insert); exit;
                // }
            }
            sleep(2);
        }

        $mysql_users = DB::connection('mysql')->table('users')->select('id','email')->whereNotIn('email',['admin@admin.com'])->get();
        // dd($mysql_users);
        foreach($mysql_users as $user)
        {
            $data = $user->email;
            $whatIWant = substr($data, strpos($data, "@") + 1);
            $replce_email = str_replace($whatIWant, 'yopmail.com', $data);
            DB::connection('mysql')->table('users')->where('id',$user->id)->update(['email'=>$replce_email]);
            // dd($replce_str);
            // echo $whatIWant;
        }
        // dd($mysql_users);
        // $file_name = 'database_backup_on_' . date('y-m-d') . '.sql';
        // // DB::unprepared(Files::get('/home/acquaint/Downloads/'.$file_name));
        // $file_handle = fopen($file_name, 'w+');
        // fwrite($file_handle, $output);
        // fclose($file_handle);
        // header('Content-Description: File Transfer');
        // header('Content-Type: application/octet-stream');
        // header('Content-Disposition: attachment; filename=' . basename($file_name));
        // header('Content-Transfer-Encoding: binary');
        // header('Expires: 0');
        // header('Cache-Control: must-revalidate');
        //    header('Pragma: public');
        //    header('Content-Length: ' . filesize($file_name));
        //    ob_clean();
        //    flush();
        //    readfile($file_name);
        //    unlink($file_name);

        // // $exitCode = Artisan::call('migrate:reset', [
        // // '--force' => true,
        
        // // ]);
        // // Schema::connection('mysql')->dropIfExists('migrations');
        // DB::connection('mysql')->unprepared(Files::get('/home/acquaint/Downloads/'.$file_name));
    }


    public function multidb_2()
    {
        $tables = DB::connection('mysql_second')->select('SHOW TABLES');
        foreach($tables->table('subscriptions')->get() as $data){

            dd($data);
            $existing_table_names = get_object_vars($data);
            // $existing_table_names[key($existing_table_names)];
            // Save data to staging database - default db connection
            DB::table($existing_table_names[key($existing_table_names)])->insert((array) $data);
        }
    }
}