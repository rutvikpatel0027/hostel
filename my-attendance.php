<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();
?>
<!doctype html>
<html lang="en" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#1E3A8A">
    <title>My Attendance</title>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="css/style.css?v=5.2">
    <style>
        .page-title {
            color: #333;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .table-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85em;
        }

        .badge-present {
            background: #d4edda;
            color: #155724;
        }

        .badge-absent {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-leave {
            background: #fff3cd;
            color: #856404;
        }

        .badge-pending {
            background: #d1ecf1;
            color: #0c5460;
        }

        .time-text {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #555;
        }
    </style>
</head>

<body>
    <?php include("includes/header.php"); ?>
    <div class="ts-main-content">
        <?php include("includes/sidebar.php"); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title" style="margin-top: 30px;"><i class="fa fa-calendar-check-o"></i> My
                            Attendance History</h2>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table id="zctb" class="table table-striped table-bordered table-hover" cellspacing="0"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Entry Time</th>
                                            <th>Exit Time</th>
                                            <th>Duration</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Get current student REGISTRATION ID (not user ID, but we usually use user ID to find registration)
                                        // Check how registration ID is stored. In dashboard, it seems $_SESSION['id'] is used.
                                        $uid = $_SESSION['id'];
                                        // We need to find the registration id for this user.
                                        // In many simple systems the user id IS the registration id or linked.
                                        // Let's check how other files fetch data.
                                        // In 'room-details.php' -> $ret="select * from registration where id=?"; $stmt= $mysqli->prepare($ret) ; $stmt->bind_param('i',$uid);
                                        // So 'id' in 'registration' table corresponds to $_SESSION['id']?
                                        // Let's assume $_SESSION['id'] matches 'id' in registration table.
                                        
                                        $query = "SELECT * FROM student_attendance WHERE registration_id = ? ORDER BY attendance_date DESC";
                                        $stmt = $mysqli->prepare($query);
                                        $stmt->bind_param('i', $uid);
                                        $stmt->execute();
                                        $res = $stmt->get_result();

                                        $cnt = 1;
                                        if ($res->num_rows > 0) {
                                            while ($row = $res->fetch_assoc()) {
                                                $status = $row['status'];
                                                $status_badge = "badge-pending";
                                                if ($status == 'present')
                                                    $status_badge = "badge-present";
                                                elseif ($status == 'absent')
                                                    $status_badge = "badge-absent";
                                                elseif ($status == 'on_leave')
                                                    $status_badge = "badge-leave";

                                                $entry = $row['entry_time'] ? date('H:i', strtotime($row['entry_time'])) : '-';
                                                $exit = $row['exit_time'] ? date('H:i', strtotime($row['exit_time'])) : '-';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $cnt; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d-m-Y', strtotime($row['attendance_date'])); ?>
                                                    </td>
                                                    <td><span class="badge <?php echo $status_badge; ?>">
                                                            <?php echo ucfirst($status); ?>
                                                        </span></td>
                                                    <td><span class="time-text">
                                                            <?php echo $entry; ?>
                                                        </span></td>
                                                    <td><span class="time-text">
                                                            <?php echo $exit; ?>
                                                        </span></td>
                                                    <td>
                                                        <?php echo $row['stay_duration'] ? $row['stay_duration'] : '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['remarks']; ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                $cnt++;
                                            }
                                        } else {
                                            // Optional: Show a row saying no records found, or let DataTable handle empty state
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/fileinput.js"></script>
    <script src="js/chartData.js"></script>
    <script src="js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#zctb').DataTable();
        });
    </script>
</body>

</html>