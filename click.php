<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click Counter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        .counter-container {
            text-align: center;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        #count {
            font-size: 2em;
            margin: 10px 0;
        }

        .button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            background: #007BFF;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        .button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="counter-container">
        <h1>Click Counter</h1>
        <p id="count">0</p>
        <button id="clickButton" class="button">Click Me!</button>
        <button id="saveButton" class="button">Save Count</button>
        <button id="resetButton" class="button">Reset Count</button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            let count = 0;
            const countDisplay = document.getElementById('count');
            const clickButton = document.getElementById('clickButton');
            const saveButton = document.getElementById('saveButton');
            const resetButton = document.getElementById('resetButton');

            clickButton.addEventListener('click', () => {
                count++;
                countDisplay.textContent = count;
            });

            saveButton.addEventListener('click', () => {
                fetch('click.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ count: count })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    if (data.status === 'success') {
                        count = 0; // 保存成功後重置計數
                        countDisplay.textContent = count;
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch((error) => console.error('Error:', error));
            });

            resetButton.addEventListener('click', () => {
                count = 0;
                countDisplay.textContent = count;
            });
        });
    </script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "3013856paul";
    $dbname = "click_counter_db";

    // 建立連接
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 檢查連接
    if ($conn->connect_error) {
        die("連接失敗: " . $conn->connect_error);
    }

    // 獲取POST數據
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $count = $request->count;

    // 將計數插入資料庫
    $sql = "INSERT INTO clicks (count) VALUES ($count)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "count" => $count]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }

    $conn->close();
}
?>
