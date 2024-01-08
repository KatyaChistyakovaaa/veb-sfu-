<!DOCTYPE HTML>
<html>
<head>
    <title>Таблица умножения</title>
</head>
    <style>
        table {
            border-collapse: collapse;
        }
        table td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
<body>
    <table>
        <?php
        for ($i = 0; $i <= 10; $i++) {
            echo "<tr>";
            for ($j = 0; $j <= 10; $j++) {
                echo "<td>" . $i . " * " . $j . " = " . ($i * $j) . "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

