<?php

namespace App\Entities;

use App\Libraries\ResponseFormat;
use App\Models\PlanningsSlotsModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Entity\Entity;

class Planning extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    // ###############################################################################################
    //                                            SLOTS
    // ###############################################################################################

    // Add slot to this planning
    public function addSlot(array $data, User $user){

        $responseFormat = new ResponseFormat();

        // NAME OR ID_COURSE REQUIRE
        if ( ! isset($data['name']) && ! isset($data['id_course']) ) {
            return $responseFormat->setError(400,'Il faut obligatoirement choisir un cours ou un nom.');
        }

        // Check if course exist if isset
        $courseIsDefined = false;
        if ( isset($data['id_course']) ) {

            $courseModel = $user->queryOnSchoolSpace('CoursesModel');
            $course = $courseModel->find($data['id_course']);

            // if course not and exist and name to
            if ( is_null($course) && ! isset($data['name']) ) {
                return $responseFormat->setError(400,'Le cours à associer est introuvable.');
            } else if ( ! is_null($course) ) {
                $courseIsDefined = true;
            }

        }

        // If id course remove name
        if ( $courseIsDefined && isset($data['name']) ) unset($data['name']);

        // if course not define and id_course exist remove him
        if ( ! $courseIsDefined && isset($data['id_course']) ) unset($data['id_course']);

        // If isset and exist
        if ( isset($data['id_teacher']) ) {
            $UserModel = $user->queryOnSchoolSpace('UserModel');
            $teacher = $UserModel->where('roles','3')->find($data['id_teacher']);

            if ( is_null($teacher) ) {
                return $responseFormat->setError(400,'Argument invalide, professeur introuvable.');
            }

            $available = $teacher->userIsAvailable(
                $data['start_hour'],
                $data['end_hour'],
                $data['daydate']
            );

            if ( ! $available['isAvailable'] ) {
                return $responseFormat
                    ->setError(400,'Professeur indisponible sur ce créneau')
                    ->addData($available['slots'],'slots');
            }

        }

        // If isset and exist
        if ( isset($data['id_classrom']) ) {
            $ClassroomModel = $user->queryOnSchoolSpace('ClassroomModel');
            $classroom = $ClassroomModel->find($data['id_classrom']);

            if ( is_null($classroom) ) {
                return $responseFormat->setError(400,'Argument invalide, salle de classe introuvable.');
            }

            $available = $classroom->classRoomIsAvailable(
                $data['start_hour'],
                $data['end_hour'],
                $data['daydate'],
                CURRENT_USER
            );

            if ( ! $available['isAvailable'] ) {
                return $responseFormat
                    ->setError(400,'Salle de classe indisponible sur ce créneau')
                    ->addData($available['slots'],'slots');
            }

        }

        $data['id_planning'] = $this->attributes['id'];

        $insert = insertNewData('PlanningsSlotsModel',$data);

        return $insert['response'];

    }

    // Update slot
    public function updateSlot(int $id, array $data, User $user): ResponseFormat
    {

        $responseFormat = new ResponseFormat();

        $planningSlotModel = new PlanningsSlotsModel();
        $slot = $planningSlotModel->where('id_planning',$this->attributes['id'])->find($id);

        if ( is_null($slot) ) {
            return $responseFormat->setError(404,'Le slot est inexistant');
        }

        // Check if course exist if isset
        $courseIsDefined = false;
        if ( isset($data['id_course']) ) {

            $courseModel = $user->queryOnSchoolSpace('CoursesModel');
            $course = $courseModel->find($data['id_course']);

            // if course not and exist and name to
            if ( is_null($course) && ! isset($data['name']) ) {
                return $responseFormat->setError(400,'Le cours à associer est introuvable.');
            } else if ( ! is_null($course) ) {
                $courseIsDefined = true;
            }

        }

        // If id course remove name
        if ( $courseIsDefined && isset($data['name']) ) unset($data['name']);

        // if course not define and id_course exist remove him
        if ( ! $courseIsDefined && isset($data['id_course']) ) unset($data['id_course']);

        $slot->fill($data);

        // If isset and exist
        if ( isset($slot->id_teacher) ) {

            $UserModel = $user->queryOnSchoolSpace('UserModel');
            $teacher = $UserModel->where('roles','3')->find($slot->id_teacher);

            // On update check if exist
            if ( isset($data['id_teacher']) && is_null($teacher) ) {

                return $responseFormat->setError(400,'Argument invalide, professeur introuvable.');

            }

            // on update check availability
            $available = $teacher->userIsAvailable(
                $slot->start_hour,
                $slot->end_hour,
                $slot->daydate,
                [$slot->id]
            );

            if ( ! $available['isAvailable'] ) {
                return $responseFormat
                    ->setError(400,'Professeur indisponible sur ce créneau')
                    ->addData($available['slots'],'slots');
            }

        }

        // If isset and exist
        if ( isset($slot->id_classrom) ) {

            $ClassroomModel = $user->queryOnSchoolSpace('ClassroomModel');
            $classroom = $ClassroomModel->find($slot->id_classrom);

            // On update check if exist
            if ( isset($data['id_classrom']) && is_null($classroom) ) {
                return $responseFormat->setError(400,'Argument invalide, salle de classe introuvable.');
            }

            // On update check availability
            $available = $classroom->classRoomIsAvailable(
                $slot->start_hour,
                $slot->end_hour,
                $slot->daydate,
                CURRENT_USER,
                [$slot->id]
            );

            if ( ! $available['isAvailable'] ) {
                return $responseFormat
                    ->setError(400,'Salle de classe indisponible sur ce créneau')
                    ->addData($available['slots'],'slots');
            }

        }

        return updateData($slot,'PlanningsSlotsModel');

    }

    // Remove slot
    public function removeSlot(int $idSlot): ResponseFormat {

        $responseFormat = new ResponseFormat();
        $planningSlotModel = new PlanningsSlotsModel();
        $slot = $planningSlotModel->where('id_planning',$this->attributes['id'])->find($idSlot);

        if ( is_null($slot) ) {
            return $responseFormat->setError(404,'Le slot est inexistant.');
        }

        try {
            $planningSlotModel->delete($idSlot);
        } catch (DatabaseException $databaseException) {
            $responseFormat->setError();
        }

        return $responseFormat;

    }

    // get slot from date
    public function getSlot(string $start_date,string $end_date, User $user): array {

        $slotModel = $user->queryOnSchoolSpace('PlanningsSlotsModel','pm_plannings.id_school_space');

        return $slotModel
            ->select('
                pm_planning_slots.id,
                pm_planning_slots.start_hour,
                pm_planning_slots.end_hour,
                pm_planning_slots.daydate,
                pm_planning_slots.name,
                pm_courses.name as nameCourse,
                pm_courses.color as colorCourse,
                pm_courses.id as idCourse,
                pm_users.first_name as teacherFirstname,
                pm_users.last_name as teacherLastname,
                pm_users.mail as teacherMail,
                pm_users.id  as teacherId,
                pm_classrooms.name as classRoomName,
                pm_classrooms.id as classRoomId
            ')
            ->join(
                'pm_courses',
                'pm_courses.id = pm_planning_slots.id_course', 'LEFT'
            )
            ->join(
                'pm_users',
                'pm_users.id = pm_planning_slots.id_teacher', 'LEFT'
            )
            ->join(
                'pm_classrooms',
                'pm_classrooms.id = pm_planning_slots.id_classrom', 'LEFT'
            )
            ->join(
                'pm_plannings',
                'pm_plannings.id = pm_planning_slots.id_planning', 'LEFT'
            )
            ->where("pm_planning_slots.daydate >=",$start_date)
            ->where("pm_planning_slots.daydate <=",$end_date)
            ->where("pm_planning_slots.id_planning <=",$this->attributes['id'])
            ->findAll();

    }

    // get program with hours completed
    public function getPrograms(): ResponseFormat {

        $responseFormat = new ResponseFormat();
        return $responseFormat;

    }

}
