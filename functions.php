<?php
function get_deadline_indicator($deadline_date_str) {
    $current_date_ts = strtotime(date('Y-m-d'));
    $deadline_ts = strtotime($deadline_date_str);
    $diff_days = floor(($deadline_ts - $current_date_ts) / (60 * 60 * 24));

    if ($deadline_ts < $current_date_ts) {
        return '<span class="badge bg-danger">🟥 Overdue</span>';
    } elseif ($deadline_ts == $current_date_ts) {
        return '<span class="badge bg-warning text-dark">🟧 Due Today</span>';
    } else {
        return '<span class="badge bg-success">🟩 ' . $diff_days . ' Days Left</span>';
    }
}

function get_status_badge($status) {
    switch ($status) {
        case 'Completed':
            return '<span class="badge bg-success">Completed</span>';
        case 'In Progress':
            return '<span class="badge bg-primary">In Progress</span>';
        case 'Not Started':
        default:
            return '<span class="badge bg-secondary">Not Started</span>';
    }
}
?>