<?php
include 'conexao.php';

// Dados do administrador
$nome = "Admin";
$email = "admin@example.com"; // Substitua pelo e-mail desejado
$senha = password_hash("senha123", PASSWORD_DEFAULT); // Substitua "senha123" pela senha desejada
$nivel_acesso = "administrador";

// Verifica se o administrador já existe
$sql = "SELECT id FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Administrador já cadastrado.";
} else {
    // Insere o administrador no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $senha, $nivel_acesso);

    if ($stmt->execute()) {
        echo "Administrador cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar administrador: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>