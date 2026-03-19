<?php namespace App\Models;

use CodeIgniter\Model;

class DentalFormModel extends Model
{
    protected $table = 'dental_forms';
    protected $primaryKey = 'form_id';
    protected $allowedFields = [
        'appointment_id','patient_id','main_complaint','treatment_history','medicines','allergies',
        'pan_chewing','tobacco','smoking','medical_conditions'
    ];
}