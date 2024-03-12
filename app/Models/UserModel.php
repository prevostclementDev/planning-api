<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'pm_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = User::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'first_name',
        'last_name',
        'mail',
        'profile_picture',
        'roles',
        'password',
        'id_school_space',
        'last_connection'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = 'users';
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassorwd'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassorwd'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // search with string on all field
    public function searchUser(string $search): UserModel
    {
        return $this
            ->where('deleted_at', null)
            ->groupStart()
                ->like('first_name',$search)
                ->orLike('last_name',$search)
                ->orLike('mail',$search)
            ->groupEnd();
    }

    // hash password before update/insert
    protected function hashPassorwd(array $data): array
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

        return $data;
    }

}
