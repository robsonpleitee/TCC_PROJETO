-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS estoque_tcc;
USE estoque_tcc;

-- Criação da tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    quantidade INT NOT NULL,
    preco DECIMAL(10, 2) NOT NULL
);

-- Inserção de alguns produtos de exemplo
INSERT INTO produtos (nome, quantidade, preco) VALUES 
('Notebook Dell', 5, 3500.00),
('Mouse Wireless', 10, 89.90),
('Teclado Mecânico', 8, 299.90),
('Monitor 24"', 3, 1299.90),
('Impressora HP', 2, 899.90);

-- Criação da tabela de fornecedores
CREATE TABLE IF NOT EXISTS fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cnpj VARCHAR(14) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    rua VARCHAR(100) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    uf CHAR(2) NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel_acesso ENUM('administrador', 'usuario', 'visualizador') NOT NULL
);

-- Inserção de usuários de exemplo
-- Senha para todos os usuários: senha123
INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES 
('Administrador', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador'),
('Usuário Padrão', 'usuario@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario'),
('Visualizador', 'visualizador@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'visualizador');



