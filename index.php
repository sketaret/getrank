<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البحث عن ترتيب الطالب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #495057;
            color: #ffffff;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .result-card {
            margin-top: 20px;
        }
        footer {
            margin-top: 50px;
            padding: 10px 0;
            background-color: #212529;
            text-align: center;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2 class="text-center">البحث عن ترتيب الطالب</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="seat_number" class="form-label">رقم الجلوس</label>
                <input type="text" class="form-control" id="seat_number" name="seat_number" required>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">القسم</label>
                <select id="department" name="department" class="form-select" required>
                    <option value="network">الشبكات</option>
                    <option value="programming">البرمجة</option>
                    <option value="communication">الإتصالات</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">ابحث عن الترتيب</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $seat_number = $_POST['seat_number'];
        $department = $_POST['department'];

        $department_file = $department . ".csv";
        $all_students_file = "allstudent.csv";

        function find_rank($seat_number, $file) {
            if (($handle = fopen($file, "r")) !== FALSE) {
                $data = [];
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($row[4] == 'primary') {
                        $data[] = $row;
                    }
                }
                fclose($handle);

                usort($data, function($a, $b) {
                    return floatval($b[2]) - floatval($a[2]);
                });

                foreach ($data as $index => $row) {
                    if ($row[0] == $seat_number) {
                        return ['rank' => $index + 1, 'student_name' => $row[1]];
                    }
                }
            }
            return ['rank' => -1, 'student_name' => ''];
        }

        function check_source($seat_number, $file) {
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($row[0] == $seat_number) {
                        return $row[4];
                    }
                }
                fclose($handle);
            }
            return 'not_found';
        }

        $source = check_source($seat_number, $all_students_file);
        echo '<div class="card result-card">';
        if ($source == 'primary') {
            $result = find_rank($seat_number, $department_file);
            $overall_result = find_rank($seat_number, $all_students_file);

            if ($result['rank'] > 0 && $overall_result['rank'] > 0) {
                echo "<h3>ترتيب الطالب</h3>";
                echo "اسم الطالب: " . $result['student_name'] . "<br>";
                echo "ترتيبه في القسم: " . $result['rank'] . "<br>";
                echo "ترتيبه على مستوى المدرسة: " . $overall_result['rank'] . "<br>";
            } else {
                echo "لم يتم العثور على رقم الجلوس في القسم المحدد أو في قائمة المدرسة الكاملة.";
            }
        } elseif ($source == 'second') {
            echo "أتفهم موقفك وأنا أثق بك أنك ستقدم أفضل ما لديك في الدور الثاني.";
        } elseif ($source == 'fail') {
            echo "من فضلك اعمل بجد أكبر المرة القادمة.";
        } else {
            echo "لم يتم العثور على رقم الجلوس.";
        }
        echo '</div>';
    }
    ?>

</div>

<footer>
   By Gomaa For WE student
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
