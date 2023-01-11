<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;

class MultipleDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multipledb:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'multiple db import/export';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mysqlHostName      = env('DB_HOST_SECOND');
        $mysqlUserName      = env('DB_USERNAME_SECOND');
        $mysqlPassword      = env('DB_PASSWORD_SECOND');
        $DbName             = env('DB_DATABASE_SECOND');

        $tables = DB::connection('mysql')->select('SHOW TABLES');
        $tables2 = DB::connection('mysql_second')->select('SHOW TABLES');

        $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword",array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        
        $output_create = '';
        foreach($tables2 as $key => $table)
        {
            $existing_table_names = get_object_vars($table);
            if(isset($tables[$key]->Tables_in_relation))
            {
                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');
                Schema::connection('mysql')->dropIfExists($tables[$key]->Tables_in_relation);
            }
            if(isset($existing_table_names[key($existing_table_names)]))
            {
                $show_table_query = "SHOW CREATE TABLE " . $existing_table_names[key($existing_table_names)] . "";
                $statement = $connect->prepare($show_table_query);
                $statement->execute();
                $show_table_result = $statement->fetchAll();
                foreach($show_table_result as $show_table_row)
                {
                    $output_create = "\n\n" . $show_table_row["Create Table"] . ";\n\n";
                    DB::connection('mysql')->select(DB::connection('mysql')->raw($output_create));
                }
                $select_query = "SELECT * FROM " . $existing_table_names[key($existing_table_names)] . "";
                $statement = $connect->prepare($select_query);
                $statement->execute();
                $total_row = $statement->rowCount();
                $multiquery = $statement->fetchAll(PDO::FETCH_OBJ);
                $multiquery = json_decode(json_encode($multiquery), true);
                $mutichunk = array_chunk($multiquery,4000,true);
                if(count($mutichunk) > 0){
                    foreach($mutichunk as $keys => $val)
                    {
                        DB::connection('mysql')->table($existing_table_names[key($existing_table_names)])->insert(
                            $mutichunk[$keys]
                        );
                    }
                
                }
            }
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
    }
}
