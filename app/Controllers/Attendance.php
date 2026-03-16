<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;

class Attendance extends BaseController
{
    public function index()
    {
        // Use ONLY Asia/Karachi timezone
        date_default_timezone_set('Asia/Karachi');

        $today = date('Y-m-d'); // Always current Pakistan date

        $employeeModel = new EmployeeModel();
        $attendanceModel = new AttendanceModel();

        $employees = $employeeModel->where('is_admin', 0)->findAll();

        $attendanceData = [];
        foreach ($employees as $emp) {
            $att = $attendanceModel
                ->where('employee_id', $emp['employee_id'])
                ->where('date', $today)
                ->first();
            $attendanceData[$emp['employee_id']] = $att ?? null;
        }

        return view('attendance/daily', [
            'employees' => $employees,
            'attendanceData' => $attendanceData,
            'today' => $today
        ]);
    }

    public function save()
    {
        date_default_timezone_set('Asia/Karachi');

        $attendanceModel = new AttendanceModel();
        $employee_ids = $this->request->getPost('employee_id');

        if (!$employee_ids || !is_array($employee_ids)) {
            return redirect()->back()->with('error', 'No employees found.');
        }

        $status = $this->request->getPost('status') ?? [];
        $check_in = $this->request->getPost('check_in') ?? [];
        $check_out = $this->request->getPost('check_out') ?? [];

        // Take date only from hidden input (current Pakistan date)
        $date = $this->request->getPost('date') ?? date('Y-m-d');

        foreach ($employee_ids as $id) {
            $data = [
                'employee_id' => $id,
                'date'        => $date,
                'status'      => $status[$id][$date] ?? 'Absent',
                'check_in'    => $check_in[$id][$date] ?? null,
                'check_out'   => $check_out[$id][$date] ?? null
            ];

            $existing = $attendanceModel
                ->where('employee_id', $id)
                ->where('date', $date)
                ->first();

            if ($existing) {
                $attendanceModel->update($existing['id'], $data);
            } else {
                $attendanceModel->insert($data);
            }
        }

        return redirect()->back()->with('success', 'Attendance saved successfully.');
    }

    public function monthlyCalendar()
    {
        date_default_timezone_set('Asia/Karachi');

        $employeeModel = new EmployeeModel();
        $attendanceModel = new AttendanceModel();

        $month = $this->request->getGet('month') ?? date('m');
        $year  = $this->request->getGet('year') ?? date('Y');

        $employees = $employeeModel->where('is_admin', 0)->findAll();
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $attendanceData = [];
        foreach ($employees as $emp) {
            $records = $attendanceModel
                ->where('employee_id', $emp['employee_id'])
                ->where('date >=', "$year-$month-01")
                ->where('date <=', "$year-$month-$daysInMonth")
                ->findAll();

            foreach ($records as $att) {
                $attendanceData[$emp['employee_id']][$att['date']] = $att;
            }
        }

        return view('attendance/monthly_calendar', [
            'employees' => $employees,
            'attendanceData' => $attendanceData,
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $daysInMonth
        ]);
    }
}
