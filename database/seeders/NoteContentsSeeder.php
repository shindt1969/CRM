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
        $data =  DB::select(
            "SELECT id from users;", [1]
        );
        // Log::info($data);

        for ($index = 0; $index < 10; $index++) {
            $rand_keys = array_rand($data);
            
            Log::info(gettype($rand_keys));

            DB::table('noteContents')->insert([
                'text' => $this->getRandText(),
                'owner_id' => random_int(1, 3), 
                'type_id' => random_int(1, 3),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'create_by_id' => $data[$rand_keys]->id
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
}
 