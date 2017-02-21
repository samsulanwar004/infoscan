<?php

use Illuminate\Database\Seeder;

class PointLevelSeeder extends Seeder
{

    protected $tasks = [
        'Take picture of modern receipt',
        'Handwritten receipt',
        'Arrange and tag items',
        'Arrange item but doesn\'t tag an item',
        'Arrange items and snap and record'
    ];

    protected $levels = [
        'level 1', 'level 2', 'level 3',
        'level 4', 'level 5', 'level 6',
        'level 7', 'level 8', 'level 9', 'level 10',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create tasks data
        DB::beginTransaction();
        try {
            foreach ($this->tasks as $value) {
                $t = new \App\Task;
                $t->code = str_random(10);
                $t->name = $value;
                $t->save();
            }

            // create levels
            foreach ($this->levels as $value) {
                $l = new \App\TaskLevelPoint;
                $l->name = $value;
                $l->save();
            }

            // create related data;
            $tasks = \App\Task::all();
            foreach ($tasks as $task) {
                for ($i=0; $i < 10; $i++) {
                    \DB::insert('insert into tasks_level_points (task_id, level_id, point, created_at) values (?, ?, ?, ?)', [
                        $task->id, $i+1, rand(0, 100), date('Y-m-d H:i:s')
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            dd($e->getMessage());
            DB::rollback();
        }
    }
}
