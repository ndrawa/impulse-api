<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $room_id =  DB::table('rooms')
                    ->select('id')
                    ->where('msteam_code', 'PRPSL6')
                    ->first();
        $class_id = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-03')
                    ->first();

        $schedule = Schedule::create([
            'name' => 'Praktikum 1',
            'day' => 'tuesday',
            'time_start' => date('Y-m-d H:i:s'),
            'time_end' => date('Y-m-d H:i:s'),
            'room_id' => $room_id->id,
            'periode_start' => date('Y-m-d H:i:s'),
            'periode_end' => date('Y-m-d H:i:s'),
            'class_id' => $class_id->id,
            'module_id' => 'belum ada',
        ]);
        $schedule->save();
    }
}
