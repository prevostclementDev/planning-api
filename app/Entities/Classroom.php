<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use JetBrains\PhpStorm\ArrayShape;

class Classroom extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    #[ArrayShape(['isAvailable' => "bool", 'slots' => "mixed"])]
    public function classRoomIsAvailable(string $start_hour, string $end_hour, string $daydate,User $user, ?array $excludeSlot = null): array
    {
        $isAvailable = false;

        $planningSlotModel = $user->queryOnSchoolSpace('PlanningsSlotsModel','pm_plannings.id_school_space');
        $planningSlotModel
            ->join('pm_plannings','pm_plannings.id = pm_planning_slots.id_planning')
            ->where('pm_planning_slots.id_classrom',$this->attributes['id'])
            ->where('pm_planning_slots.daydate',$daydate)
            ->groupStart()
                ->groupStart()
                    ->where('pm_planning_slots.start_hour <=',$start_hour)
                    ->where('pm_planning_slots.end_hour >',$start_hour)
                ->groupEnd()
                ->orGroupStart()
                    ->where('pm_planning_slots.start_hour <',$end_hour)
                    ->where('pm_planning_slots.end_hour >=',$end_hour)
                ->groupEnd()
                ->orGroupStart()
                    ->where('pm_planning_slots.start_hour >',$start_hour)
                    ->where('pm_planning_slots.end_hour <',$end_hour)
                ->groupEnd()
            ->groupEnd();

        if ( !is_null($excludeSlot) ) {
            $planningSlotModel->whereNotIn('pm_planning_slots.id',$excludeSlot);
        }

        $slotsForClassroom = $planningSlotModel->findAll();

        if ( empty($slotsForClassroom) ) {
            $isAvailable = true;
        }

        return array('isAvailable' => $isAvailable, 'slots' => $slotsForClassroom);

    }

}
