<?php
// Database connection
$host = getenv("MYSQL_SERVICE_HOST");
$user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASSWORD");
$dbname = getenv("MYSQL_DATABASE");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle loop action and data insertion
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'insert') {
        $message = $_POST['message'] ?? 'Default Message';
        $stmt = $pdo->prepare("INSERT INTO loop_data (message) VALUES (:message)");
        $stmt->execute(['message' => $message]);
        echo "Data inserted: $message";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infinite Loop Without Session</title>
    <script>
        let loopInterval;

        // Start the loop
        function startLoop() {
            document.getElementById('status').innerText = "Loop is running...";
            loopInterval = setInterval(() => {
                const message = `Looping at ${new Date().toLocaleTimeString()}`;

                // Display output in the browser
                const output = document.getElementById('output');
                const newItem = document.createElement('div');
                newItem.innerText = message;
                output.appendChild(newItem);

                // Send data to the server for insertion into MySQL
                fetch('index.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=insert&message=${encodeURIComponent(message)}`
                })
                .then(response => response.text())
                .then(data => console.log(data))
                .catch(error => console.error('Error:', error));
            }, 1000); // Loop every 1 second
        }

        // Stop the loop
        function stopLoop() {
            clearInterval(loopInterval);
            document.getElementById('status').innerText = "Loop stopped.";
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        #output {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            height: 300px;
            overflow-y: auto;
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Infinite Loop Without Session</h1>
    <p id="status">Press "Start" to begin.</p>
    <button onclick="startLoop()">Start Loop</button>
    <button onclick="stopLoop()">Stop Loop</button>

    <div id="output"></div>
</body>
</html>
