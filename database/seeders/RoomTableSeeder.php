<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $room = Room::create([
            'name' => 'LABIF-01 (dummy)',
            'desc' => 'Gedung A Lt. 2',
            'msteam_code' => 'PRPSL6',
            'msteam_link' => 'https://teams.microsoft.com/example'
        ]);
        $room->save();
    }
}
