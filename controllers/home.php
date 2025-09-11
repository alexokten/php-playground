<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Playground with FrankenPHP</title>
</head>
<body>
    <h1>Welcome to your PHP Playground!</h1>
    <p>FrankenPHP is running successfully.</p>
    <p>PHP Version: <?= PHP_VERSION ?></p>
    <p>Server: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></p>
    
    <h2>Quick Start Commands:</h2>
    <ul>
        <li><code>docker-compose up --build</code> - Start the development server</li>
        <li><code>docker-compose down</code> - Stop the server</li>
        <li>Visit <a href="http://localhost:8080">http://localhost:8080</a> to see your app</li>
    </ul>
</body>
</html>
