<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoteContentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->insertUserData();
        $this->insertPersonData();
        $this->insertOrganizationData();

        $data =  DB::select(
            "SELECT id from users;", [1]
        );

        for ($index = 0; $index < 10; $index++) {
            $rand_keys = array_rand($data);
            
            DB::table('notes')->insert([
                'text' => $this->getRandText(),
                'target_id' => random_int(1, 3), 
                'target_type_id' => random_int(1, 3),
                'create_by_id' => $data[$rand_keys]->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ]);
        }
    }

    public function getRandText(){
        $retstr = "";
        for ($x = 0; $x <= 10; $x++) {
            $retstr .= Str::random(10)." ";
            // $retstr .= ""." ";
          }

        return $retstr;
    }


    public function insertRoleData(){
        $default_role_data = [
            [
                'name' => 'dddaa',
                'description' => "aa@aa.aa",
                'permission_type' => 'tester',
            ],
        ];
        DB::table('roles')->insert($default_role_data);
    }

    public function insertUserData(){

        $this->insertRoleData();

        $default_users_data = [
            [
                'name' => 'persons',
                'email' => "acca@aa.aa",
                'status' => 'on',
                'password' => '123456',
                'role_id' => '1'

            ],
            [
                'name' => 'persons2',
                'email' => "aaca@bb.aa",
                'status' => 'on',
                'password' => '123456',
                'role_id' => '1'

            ],
            [
                'name' => 'persons3',
                'email' => "aqqa@cc.aa",
                'status' => 'on',
                'password' => '123456',
                'role_id' => '1'

            ],
        ];

        DB::table('users')->insert($default_users_data);
    }

    public function insertPersonData(){


        $default_person_data = [
            [
                'name' => 'cus1',
                'emails' => json_encode([
                    "value" => "aa@aa.aa",
                    "label" => "work"
                ])
            ],
            [
                'name' => 'cus2',
                'emails' => json_encode([
                    "value" => "bb@bb.bb",
                    "label" => "work"
                ])
            ],
            [
                'name' => 'cus3',
                'emails' => json_encode([
                    "value" => "bb@cc.bb",
                    "label" => "work"
                ])
            ]
        ];

        DB::table('persons')->insert($default_person_data);
    }

    public function insertOrganizationData(){
        $default_Organization_data = [
            ['name' => 'dddaa'],
            ['name' => 'dddasdaa'],
            ['name' => 'ddasdsdaa']
        ];

        DB::table('organizations')->insert($default_Organization_data);

    }

}
 