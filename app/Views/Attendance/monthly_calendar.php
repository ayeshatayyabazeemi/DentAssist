<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Monthly Attendance - <?= date('F Y', mktime(0,0,0,$month,1)) ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
.calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}
.weekday-header {
    font-weight: bold;
    text-align: center;
    padding: 10px 0;
    background: #343a40;
    color: #fff;
    border-radius: 6px;
}
.day {
    min-height: 100px;
    border-radius: 6px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s;
    color: #000;
}
.day:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.day-header {
    font-weight: bold;
    margin-bottom: 5px;
}
.today {
    border: 2px solid #0d6efd;
}
.full-attendance {
    color: gold;
    font-size: 1rem;
    margin-left: 5px;
}
.present { color: green; font-weight: 600; }
.absent { color: red; font-weight: 600; }
.bg-all-present { background-color: #d4edda; } /* green */
.bg-partial { background-color: #fff3cd; } /* yellow */
.bg-none { background-color: #f8d7da; } /* red */
</style>
</head>
<body>
<div class="container mt-5">
<h3 class="mb-4">Monthly Attendance - <?= date('F Y', mktime(0,0,0,$month,1)) ?></h3>

<form id="monthYearForm" method="get" action="<?= base_url('attendance/monthlyCalendar') ?>" class="mb-3 row g-2">
    <div class="col-auto">
        <select name="month" class="form-select form-select-sm" onchange="document.getElementById('monthYearForm').submit()">
            <?php for($m=1;$m<=12;$m++): ?>
                <option value="<?= $m ?>" <?= $m==$month?'selected':'' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-auto">
        <select name="year" class="form-select form-select-sm" onchange="document.getElementById('monthYearForm').submit()">
            <?php for($y=date('Y'); $y>=date('Y')-5; $y--): ?>
                <option value="<?= $y ?>" <?= $y==$year?'selected':'' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>
</form>

<div class="calendar">
<?php
$weekdays = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
foreach($weekdays as $wd) echo "<div class='weekday-header'>$wd</div>";

$first_day = date('w', strtotime("$year-$month-01"));
for($i=0;$i<$first_day;$i++) echo "<div class='day'></div>";

$today_date = date('Y-m-d');

function hasFullAttendance($empId, $year, $month, $daysInMonth, $attendanceData) {
    for($d=1;$d<=$daysInMonth;$d++){
        $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
        $status = $attendanceData[$empId][$date]['status'] ?? 'Absent';
        if($status != 'Present') return false;
    }
    return true;
}

for($day=1;$day<=$daysInMonth;$day++) {
    $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
    $dayClass = ($date == $today_date) ? 'day today' : 'day';

    // Determine attendance summary for color
    $presentCount = 0;
    foreach($employees as $emp){
        $status = $attendanceData[$emp['employee_id']][$date]['status'] ?? '';
        if($status=='Present') $presentCount++;
    }

    if($presentCount == count($employees) && count($employees) > 0) $dayClass .= ' bg-all-present';
    elseif($presentCount > 0) $dayClass .= ' bg-partial';
    else $dayClass .= ' bg-none';

    echo "<div class='$dayClass' data-bs-toggle='modal' data-bs-target='#modal_$date'>";
    echo "<div class='day-header'>$day</div>";
    echo "<div><small>$presentCount / ".count($employees)." present</small></div>";
    echo "</div>";

    // Modal for that day
    echo "<div class='modal fade' id='modal_$date' tabindex='-1' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-centered'>
    <div class='modal-content'>
        <div class='modal-header'>
            <h5 class='modal-title'>Attendance - $date</h5>
            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
        </div>
        <div class='modal-body'>";
    
    foreach($employees as $emp){
        $empId = $emp['employee_id'];
        $att = $attendanceData[$empId][$date] ?? null;
        $status = $att['status'] ?? '-';
        $check_in = $att['check_in'] ?? '-';
        $check_out = $att['check_out'] ?? '-';
        $fullAttendance = hasFullAttendance($empId, $year, $month, $daysInMonth, $attendanceData);
        echo "<div class='d-flex justify-content-between align-items-center mb-2'>";
        echo "<div><strong>".esc($emp['name'])."</strong> : ";
        echo $status=='Present' ? "<span class='present'>P</span>" : ($status=='Absent' ? "<span class='absent'>A</span>" : "-");
        echo " ($check_in - $check_out)</div>";
        if($fullAttendance) echo "<i class='fas fa-trophy full-attendance' title='Full Attendance'></i>";
        echo "</div>";
    }

    echo "</div>
        <div class='modal-footer'>
            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
        </div>
    </div></div></div>";
}

$last_day_w = date('w', strtotime("$year-$month-$daysInMonth"));
for($i=$last_day_w+1;$i<=6;$i++) echo "<div class='day'></div>";
?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>