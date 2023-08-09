<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\JobTitlesModel;
use Illuminate\Http\Request;
use \Illuminate\Support\Collection;

class JobTitlesController extends Controller
{
    private $jobTitlesModel;

    public function __construct(){
        $this->jobTitlesModel = new JobTitlesModel();
    }
    public function getJobTitles($type = null)
    {
        return $this->jobTitlesModel->getJobTitles($type);

    }

}
