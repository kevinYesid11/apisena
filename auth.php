<?php

require_once 'db.php';

function authenticate($username, $password)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        return true;
    } else {
        return false;
    }
}

function registerUser($username, $password)
{
    global $pdo;

    if (empty($username) || empty($password)) {
        return json_encode(["error" => "Usuario y contraseña son campos obligatorios"]);
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['count'] > 0) {
        return json_encode(["error" => "El usuario ya existe"]);
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);

    return json_encode(["message" => "Usuario registrado: $username"]);
}
