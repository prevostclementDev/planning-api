<?php

namespace App\Models;

use App\Entities\Slot;
use CodeIgniter\Model;

class PlanningsSlotsModel extends Model
{
    protected $table            = 'pm_planning_slots';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Slot::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'start_hour',
        'end_hour',
        'daydate',
        'name',
        'id_course',
        'id_teacher',
        'id_planning',
        'id_classrom',
        'total_time',
        'teacher_status',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = 'planningSlots';
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


}
