<?php

namespace App\Models;

use App\Entities\Course;
use CodeIgniter\Model;

class CoursesModel extends Model
{
    protected $table            = 'pm_courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Course::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'hours_required',
        'id_school_space',
        'color',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = 'courses';
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // search with string on all field
    public function searchCourses(string $search): CoursesModel
    {
        return $this
            ->where('deleted_at', null)
            ->groupStart()
            ->like('name',$search)
            ->orLike('color',$search)
            ->groupEnd();
    }

}
