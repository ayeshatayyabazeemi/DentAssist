<?php namespace App\Models;

use CodeIgniter\Model;

class MedicalHistoryModel extends Model
{
    protected $table = 'medical_history';
    protected $primaryKey = 'history_id';
    protected $allowedFields = [
        'appointment_id','patient_id','main_complaint','treatment_history','medicines','allergies',
        'pan_chewing','tobacco','smoking','medical_conditions','visit_date'
    ];
}