<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Module;

class ModuleTransformer extends TransformerAbstract
{
    public function transform(Module $module)
    {
        return [
            'id' => $module->id,
            'course_id' => $module->course_id,
            'pretest_id' => $module->pretest_id,
            'posttest_id' => $module->posttest_id,
            'journal_id' => $module->jorunal_id,
            'index' => $module->index,
            'academic_year_id' => $module->academic_year_id,
            'created_at' => $module->created_at,
            'updated_at' => $module->updated_at,
        ];
    }
}
