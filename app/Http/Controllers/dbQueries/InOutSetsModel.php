<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\InoutSet;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\DB;

class InOutSetsModel extends Controller
{
    //Xomashyo bulimidagi barcha partiyalar
    public function getRawMaterialSets($department_id, $isRawMaterial){
        return DB::table("inout_sets as s")
            ->select("s.*", "u.fullName", "df.name as department_from", "dt.name as department_to")
            ->leftJoin("users as u", "u.id", "=", "s.user_id")
            ->leftJoin("departments as df", "df.id", "=", "s.from_department_id")
            ->leftJoin("departments as dt", "dt.id", "=", "s.to_department_id")
            ->where("s.department_id", $department_id)
            ->where("s.isRawMaterial", $isRawMaterial)
            ->orderBy("s.created_date", 'desc')
            ->get();
    }

    //bitta partiya
    public function getSet($id){
        return DB::table("inout_sets as s")
            ->select("s.*", "u.fullName", "df.name as department_from", "dt.name as department_to")
            ->leftJoin("users as u", "u.id", "=", "s.user_id")
            ->leftJoin("departments as df", "df.id", "=", "s.from_department_id")
            ->leftJoin("departments as dt", "dt.id", "=", "s.to_department_id")
            ->where("s.id", $id)
            ->orderBy("s.created_date", 'desc')
            ->first();
    }

    public function add($arr)
    {
        $inoutSet = InoutSet::create($arr);

        return $this->getSet($inoutSet->id);
    }

    public function update($id, $arr)
    {
        InoutSet::where("id", $id)->update($arr);

        return $this->getSet($id);
    }

    public function delete($id)
    {
        return InoutSet::where('id', $id)->delete();
    }



}
