<?php
namespace App\Controllers;

class AdminDashboard extends BaseController
{
    public function index()
    {
       
        // Only allow if logged in
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        helper('url');
       


        // load your dashboard view
        return view('admin/dashboard');
    }
}
