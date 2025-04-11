<?php
include 'conexao.php';

$sql = "SELECT id, nome, quantidade, preco FROM produtos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["nome"] . "</td>";
        echo "<td>" . $row["quantidade"] . "</td>";
        echo "<td>R$ " . number_format($row["preco"], 2, ',', '.') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>Nenhum produto encontrado</td></tr>";
}

$conn->close();
?>