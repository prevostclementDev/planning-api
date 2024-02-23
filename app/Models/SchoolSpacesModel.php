<?php

namespace App\Models;

use App\Entities\Schoolspace;
use CodeIgniter\Model;

class SchoolSpacesModel extends Model
{
    protected $table            = 'pm_schools_spaces';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Schoolspace::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'profile_picture'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|max_length[255]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Le nom de votre espace est obligatoire',
            'max_length' => 'Le nom de votre espace est trop long 255 caractères maximum',
        ]
    ];
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
