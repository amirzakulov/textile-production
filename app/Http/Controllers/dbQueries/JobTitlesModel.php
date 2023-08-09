<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobTitlesModel extends Controller
{
    public function getJobTitles($type = null)
    {
        if(is_null($type)) {
            return DB::table("job_titles")
                ->orderBy("name")
                ->get();
        } else {
            return DB::table("job_titles")
                ->where("type", $type)
                ->orderBy("name")
                ->get();
        }

    }

}
