<?php
function getDBConnection($role = 'customer') {
    $config = [
        'staff' => [
            'server' => '127.0.0.1',
            'username' => 'student2',
            'password' => 'Dlsu1234!',
            'name' => 'AnimoBowl',
            'port' => 3308
        ],
        'admin' => [
            'server' => '127.0.0.1',
            'username' => 'student1',
            'password' => 'Dlsu1234!',
            'name' => 'AnimoBowl',
            'port' => 3308
        ],
        'customer' => [
            'server' => '127.0.0.1',
            'username' => 'student3',
            'password' => 'Dlsu1234!',
            'name' => 'AnimoBowl',
            'port' => 3308
        ]
    ];

    $role = strtolower($role);
    if (!isset($config[$role])) {
        $role = 'customer';
    }

    try {
        $conn = new mysqli(
            $config[$role]['server'],
            $config[$role]['username'],
            $config[$role]['password'],
            $config[$role]['name'],
            $config[$role]['port']
        );

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    } catch (Exception $e) {
        die("ERROR: Could not connect to the database. " . $e->getMessage());
    }
}

// Default connection (for login page, etc.)
$conn = getDBConnection('customer');
?>