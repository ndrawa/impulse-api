<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Module;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ScheduleTest;
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
        $staff_id = DB::table('staffs')
                    ->select('id')
                    ->where('code', 'AJS')
                    ->first();
        $class_id = DB::table('classes')
                    ->select('id')
                    ->where('name', 'IF-43-01')
                    ->first();
        $academic_year_id = DB::table('academic_years')
                            ->select('id')
                            ->where('semester', 'odd')
                            ->first();
        $course_id =    DB::table('courses')
                        ->select('id')
                        ->where('code', 'CSG2H3')
                        ->first();
        $class_course_id =  DB::table('class_course')
                            ->select('id')
                            ->where('class_id', $class_id->id)
                            ->where('academic_year_id', $academic_year_id->id)
                            ->where('course_id', $course_id->id)
                            ->where('staff_id', $staff_id->id)
                            ->first();

        $room_id =  DB::table('rooms')
                    ->select('id')
                    ->where('msteam_code', 'PRPSL6')
                    ->first();

        //Create test for pretest modul 1
        $pretest_1 = Test::create([
            'type' => 'essay'
        ]);

        //Create 1st question for pretest modul 1
        $question = Question::create([
            'test_id' => $pretest_1->id,
            'question' => 'Apa penyebab penangangan pandemi COVID-19 carut marut di Indonesia?',
            'weight' => 5,
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Karena terlalu banyak ahli. Misal, 10rb orang sakit,
                        100rb ahli ikut bicara.',
        ]);
        //Create 2nd question for pretest modul 1
        $question = Question::create([
            'test_id' => $pretest_1->id,
            'question' => 'Kenapa kamu tidak percaya jika omongan adalah doa?',
            'weight' => 5,
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Karena elit politik pernah bilang "Indonesia kuat karena
                        banyak sering makan nasi kucing", ternyata tahun ini Indonesia
                        keteteran juga.',
        ]);

        //Create test for posttest modul 1
        $posttest_1 = Test::create([
            'type' => 'multiple_choice'
        ]);
        //Create 1st question for posttest modul 1
        $question = Question::create([
            'test_id' => $posttest_1->id,
            'question' => 'Ban ban apa yang ilang?',
            'weight' => 5,
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Bantal',
            'is_answer' => 'false',
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Bansos',
            'is_answer' => 'true',
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Banser',
            'is_answer' => 'false',
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Banjir',
            'is_answer' => 'false',
        ]);
        //Create 2nd question for posttest modul 1
        $question = Question::create([
            'test_id' => $posttest_1->id,
            'question' => 'Dari 1-4 bagaimana kondisi ekonomi anda saat ini?',
            'weight' => 10,
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Sangat kurang (1)',
            'is_answer' => 'true',
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Kurang (2)',
            'is_answer' => 'false',
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Cukup (3)',
            'is_answer' => 'false',
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Sangat cukup (4)',
            'is_answer' => 'false',
        ]);

        $module = Module::create([
            'course_id' => $course_id->id,
            'index' => 1,
            'academic_year_id' => $academic_year_id->id,
            'pretest_id' => $pretest_1->id,
            'posttest_id' => $posttest_1->id,
        ]);
        $schedule_1 = Schedule::create([
            'name' => 'PBO PRAKTIKUM 1 (dummy)',
            'time_start' => '2021-08-12 07:30:00',
            'time_end' => '2021-08-12 10:30:00',
            'room_id' => $room_id->id,
            'class_course_id' => $class_course_id->id,
            'module_id' => $module->id,
            'academic_year_id' => $academic_year_id->id,
            'date' => '2021-08-12',
        ]);
        $schedule_1->save();
        //Create test for posttest modul 2
        $posttest_2 = Test::create([
            'type' => 'essay'
        ]);

        //Create 1st question for posttest modul 2
        $question = Question::create([
            'test_id' => $posttest_2->id,
            'question' => 'Sebutkan 3 tim sepak bola dari London ...',
            'weight' => 5,
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => '- Chelsea FC<br>- Arsenal FC<br>- Crystal Palace FC<br>
                        - Tottenham Hotspur FC<br>- Queens Park Ranger FC<br>
                        - Corinthian FC',
        ]);
        //Create 2nd question for pretest modul 2
        $question = Question::create([
            'test_id' => $posttest_2->id,
            'question' => 'Isilah dengan jawaban panjang:',
            'weight' => 5,
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non felis felis.
                        Aliquam elit elit, rhoncus suscipit nisl vel, tempor maximus neque. Nulla
                        facilisi. Integer elementum hendrerit mi a cursus. Vivamus ut purus pharetra,
                        ultrices massa id, rutrum sem. Pellentesque congue iaculis nisi ut condimentum.
                        Curabitur et aliquet neque. Vestibulum at sem ex. Sed tempus varius nisl, et
                        hendrerit eros luctus sit amet. Suspendisse sed felis magna.',
        ]);
        //Create 3rd question for pretest modul 2
        $question = Question::create([
            'test_id' => $posttest_2->id,
            'question' => 'Panjangin lagi dong?',
            'weight' => 5,
        ]);
        $answer = Answer::create([
            'question_id' => $question->id,
            'answer' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non felis felis.
                        Aliquam elit elit, rhoncus suscipit nisl vel, tempor maximus neque. Nulla
                        facilisi. Integer elementum hendrerit mi a cursus. Vivamus ut purus pharetra,
                        ultrices massa id, rutrum sem. Pellentesque congue iaculis nisi ut condimentum.
                        Curabitur et aliquet neque. Vestibulum at sem ex. Sed tempus varius nisl, et
                        hendrerit eros luctus sit amet. Suspendisse sed felis magna.<br> Donec eu efficitur
                        diam. Nunc pellentesque odio id ipsum mollis maximus. Cras in ullamcorper dolor,
                        sit amet volutpat eros. Quisque nulla sem, convallis eget mauris a, ornare fringilla orci.
                        Nulla facilisi. Aenean vitae nunc id ipsum pulvinar suscipit ac sed neque. Lorem ipsum
                        dolor sit amet, consectetur adipiscing elit. Nullam mollis porta elit tempus porttitor. Sed
                        in commodo dui. Nulla a dignissim diam. Proin sed sapien pellentesque, consectetur ipsum
                        at, interdum enim. Pellentesque vehicula tortor eget libero porttitor, ac finibus augue
                        laoreet. Aliquam ac metus pretium enim blandit sollicitudin. Nullam bibendum a ipsum vel
                        tempor. Nulla convallis mi non turpis tristique, id blandit mauris sollicitudin. ',
        ]);

        $module = Module::create([
            'course_id' => $course_id->id,
            'index' => 2,
            'academic_year_id' => $academic_year_id->id,
            'posttest_id' => $posttest_2->id,
        ]);
        $schedule_2 = Schedule::create([
            'name' => 'PBO PRAKTIKUM 2 (dummy)',
            'time_start' => '2021-08-19 07:30:00',
            'time_end' => '2021-08-19 10:30:00',
            'room_id' => $room_id->id,
            'class_course_id' => $class_course_id->id,
            'module_id' => $module->id,
            'academic_year_id' => $academic_year_id->id,
            'date' => '2021-08-19',
        ]);
        $schedule_2->save();

        $module = Module::create([
            'course_id' => $course_id->id,
            'index' => 3,
            'academic_year_id' => $academic_year_id->id,
        ]);
        $schedule_3 = Schedule::create([
            'name' => 'PBO PRAKTIKUM 3 (dummy)',
            'time_start' => '2021-08-26 07:30:00',
            'time_end' => '2021-08-26 10:30:00',
            'room_id' => $room_id->id,
            'class_course_id' => $class_course_id->id,
            'module_id' => $module->id,
            'academic_year_id' => $academic_year_id->id,
            'date' => '2021-08-26',
        ]);
        $schedule_3->save();



        //Create the 1st schedule_test
        //Create pretest schedule 1
        $schedule_test_1 = ScheduleTest::create([
            'schedule_id' => $schedule_1->id,
            'test_id' => $pretest_1->id,
            'time_start' => '2021-08-12 07:30:00',
            'time_end' => '2021-08-12 07:45:00',
            'is_active' => 'false',
            'auth' => '123456'
        ]);

        //Create posttest schedule 1
        $schedule_test_1 = ScheduleTest::create([
            'schedule_id' => $schedule_2->id,
            'test_id' => $posttest_1->id,
            'time_start' => '2021-08-12 10:00:00',
            'time_end' => '2021-08-12 10:30:00',
            'is_active' => 'false',
            'auth' => '123456'
        ]);
    }
}
