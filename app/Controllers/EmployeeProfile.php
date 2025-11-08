<?php
namespace App\Controllers;

use App\Models\EmployeeModel;
use CodeIgniter\Controller;

class PatientProfile extends Controller
{
    public function view($id = null)
    {
        if ($id === null) {
            // invalid request
            return redirect()->to('/adminDashboard'); // or some safe place
        }

        $model = new PatientModel();
        $patient = $model->find($id);

        if (!$patient) {
            // no patient found
            return redirect()->to('/adminDashboard')->with('error','Patient not found');
        }

        // pass data to view or return JSON if you want
        return view('employee/employeeprofile', ['employee' => $patient]);
    }
}