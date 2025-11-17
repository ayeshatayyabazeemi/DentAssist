<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Login extends BaseController
{
    public function index()
    {
        // Show login view
        // Load URL helper so base_url() works in views
        helper('url');
        return view('login/login');
    }

    public function auth()
{
    helper(['url','form']);
    $postData = $this->request->getPost();
    log_message('debug', 'Login form posted: ' . print_r($postData, true));

    $validation = \Config\Services::validation();
    $validation->setRules([
        'username' => 'required|valid_email',
        'password' => 'required',
        'role'     => 'required'  // ensure role is selected
    ]);

    if (! $validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $validation->getError('username')
                         ?? $validation->getError('password')
                         ?? $validation->getError('role')
        ]);
    }

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');
    $selectedRole = $this->request->getPost('role');

    $employeeModel = new \App\Models\EmployeeModel();
    $employee = $employeeModel->where('email', $username)->first();
    log_message('debug', 'Employee fetched for email ' . $username . ': ' . print_r($employee, true));

    if (! $employee || empty($employee['password'])) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'User not found'
        ]);
    }

    // For now plain‐text (change later to hashed)
    if ($password !== $employee['password']) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid password'
        ]);
    }

    // Determine actual role from employee record
    $role = null;
    if ((int)$employee['is_admin'] === 1) {
        $role = 'admin';
    } elseif ((int)$employee['is_doctor'] === 1) {
        $role = 'doctor';
    } elseif ((int)$employee['is_receptionist'] === 1) {
        $role = 'receptionist';
    } elseif ((int)$employee['is_staff'] === 1) {
        $role = 'staff';
    } else {
        // fallback or unknown
        $role = 'staff';
    }

    // Check that selected role from form matches actual role
    if ($role !== $selectedRole) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Assigned role mismatch'
        ]);
    }

    // Set session
    session()->set([
        'user_id'    => $employee['employee_id'],
        'username'   => $employee['name'],
        'role'       => $role,
        'isLoggedIn' => true
    ]);

    // Redirect based on role
    switch ($role) {
        case 'admin':
            $redirectURL = base_url('adminDashboard');
            break;
        case 'doctor':
            $redirectURL = base_url('doctorDashboard');
            break;
        case 'receptionist':
            $redirectURL = base_url('receptionDashboard');
            break;
       
    }

    return $this->response->setJSON([
        'success'  => true,
        'redirect' => $redirectURL
    ]);

}

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}
