<?php
namespace App\Controllers;

use App\Models\PatientModel;
use CodeIgniter\Controller;

class PatientProfile extends Controller
{
    public function view($patientId = null)
    {
        if ($patientId === null) {
            // invalid request
            return redirect()->to('/adminDashboard'); // or some safe place
        }

        $model = new PatientModel();
       
  $patient = $model->find($patientId);
         


        if (!$patient) {
            // no patient found
            return redirect()->to('/adminDashboard')->with('error','Patient not found');
        }

        // pass data to view or return JSON if you want
        return view('patient/patientprofile', ['patient' => $patient]);
    }
}