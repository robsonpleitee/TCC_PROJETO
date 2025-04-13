# Sistema de Controle de Estoque

Sistema web para controle de estoque desenvolvido em PHP com diferentes níveis de acesso para usuários.

## Funcionalidades

- Controle de produtos (adicionar, editar, excluir)
- Gerenciamento de usuários
- Diferentes níveis de acesso:
  - Administrador: acesso total ao sistema
  - Usuário: pode gerenciar produtos e gerar relatórios
  - Visualizador: apenas visualiza produtos
- Geração de relatórios avançados (diários, semanais e mensais)
- Exportação em PDF e Excel
- Log de atividades dos usuários

## Requisitos

- PHP 7.0 ou superior
- MySQL 5.6 ou superior
- Servidor web (Apache, Nginx, etc.)
- Biblioteca TCPDF para geração de PDFs

## Instalação

1. Clone o repositório:
```bash
git clone [URL_DO_REPOSITORIO]
```

2. Importe o arquivo `bd.sql` para criar o banco de dados e as tabelas necessárias

3. Configure a conexão com o banco de dados no arquivo `conexao.php`

4. Acesse o sistema através do navegador

## Instalação do TCPDF

Para gerar relatórios em PDF, siga um dos métodos abaixo:

### Método 1: Usando o instalador automático (recomendado)

1. Acesse o arquivo `instalar_tcpdf.php` através do seu navegador
2. O script baixará e instalará automaticamente o TCPDF no projeto
3. Após a instalação, clique no link para testar se está funcionando

### Método 2: Download manual

Se o instalador automático não funcionar:

1. Baixe a última versão do TCPDF em: https://github.com/tecnickcom/TCPDF/releases
2. Extraia os arquivos para uma pasta chamada `tcpdf` no diretório raiz do projeto
3. Teste o funcionamento acessando o arquivo `teste_tcpdf.php`

### Método 3: Via Composer

Se você usa Composer:

```bash
composer require tecnickcom/tcpdf
```

## Configuração do Banco de Dados

O arquivo `bd.sql` contém a estrutura do banco de dados e alguns dados iniciais. Para criar um usuário administrador, execute o arquivo `criar_admin.php` ou use o seguinte comando SQL:

```sql
INSERT INTO usuarios (nome, email, senha, nivel_acesso)
VALUES ('Admin', 'admin@example.com', '$2y$10$ExemploDeHash', 'administrador');
```

## Estrutura do Projeto

- `admin_dashboard.php`: Painel do administrador
- `cadastrar_usuario.php`: Formulário para cadastro de usuários
- `conexao.php`: Configuração de conexão com o banco de dados
- `editar_produto.php`: Formulário para edição de produtos
- `editar_usuario.php`: Formulário para edição de usuários
- `excluir_produto.php`: Script para exclusão de produtos
- `excluir_usuario.php`: Script para exclusão de usuários
- `index.php`: Página inicial do sistema
- `lista_produtos.php`: Lista de produtos em estoque
- `lista_usuarios.php`: Lista de usuários cadastrados
- `login.php`: Página de login
- `logout.php`: Script para encerrar sessão

## Segurança

- Senhas são armazenadas com hash seguro
- Proteção contra SQL Injection usando prepared statements
- Validação de dados em todos os formulários
- Controle de acesso baseado em sessões
- Proteção contra XSS usando htmlspecialchars

## Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.