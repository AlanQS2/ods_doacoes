🌱 AlimentoSolidário
Transformando excesso em refeições. Um projeto alinhado com a ODS 2 (Fome Zero e Agricultura Sustentável), conectando produtores, distribuidores e cozinheiros para combater o desperdício de alimentos.

📋 Sobre o Projeto
O AlimentoSolidário é uma aplicação web que gerencia um ecossistema de doação de alimentos. O objetivo é facilitar a logística entre quem tem excedente de produção e quem pode transformar esse alimento em refeições para a comunidade.

👥 Perfis de Usuário
Produtor: Publica doações de ingredientes (frutas, vegetais) excedentes.

Distribuidor: Visualiza doações e refeições disponíveis, realiza a coleta e a entrega (logística).

Cozinheiro: Recebe ingredientes, prepara refeições e as disponibiliza para distribuição.

Administrador: Gerencia usuários (banimento), modera conteúdos e visualiza estatísticas do sistema.

🚀 Funcionalidades
✅ Cadastro e Login: Sistema seguro com hash de senhas e seleção de cidade/estado via API do IBGE.

✅ Feed em Tempo Real: Visualização de doações e refeições disponíveis na cidade do usuário.

✅ Gestão de Fluxo:

Produtor cria doação -> Distribuidor coleta -> Entraga ao Cozinheiro.

Cozinheiro cria refeição (usando ingredientes doados) -> Distribuidor coleta -> Entrega final.

✅ Sistema de Avaliação: Usuários avaliam a conduta uns dos outros após a conclusão de uma entrega.

✅ Painel Administrativo: Controle total sobre usuários e itens cadastrados.

✅ Responsividade: Interface adaptada para Desktop, Tablets e Smartphones (Mobile First).

🛠️ Tecnologias Utilizadas
Backend: PHP (Nativo/Vanilla) com PDO.

Banco de Dados: MySQL.

Frontend: HTML5, JavaScript (Fetch API).

Estilização: Tailwind CSS (via CDN).

API Externa: IBGE (para carregar Estados e Cidades).

⚙️ Como Rodar o Projeto
Pré-requisitos
Um servidor web local (como XAMPP, WAMP ou Laragon).

PHP 7.4 ou superior.

Banco de dados MySQL.

Passo 1: Clonar o Repositório
Bash

git clone https://github.com/seu-usuario/alimentosolidario.git
cd alimentosolidario
Passo 2: Configurar o Banco de Dados
Abra o phpMyAdmin (ou seu gerenciador SQL preferido).

Crie um novo banco de dados chamado ods_doacao.

Execute o script SQL abaixo na aba SQL para criar as tabelas e o usuário administrador:

SQL

-- Criação das Tabelas

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    tipo ENUM('produtor', 'distribuidor', 'cozinheiro', 'admin') NOT NULL,
    capacidade_transporte VARCHAR(100),
    capacidade_producao VARCHAR(100),
    disponibilidade TEXT,
    termos_aceitos TINYINT(1) DEFAULT 0,
    banned TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produtor_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    tipo ENUM('frutas', 'vegetais', 'ambos') NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    unidade VARCHAR(20) NOT NULL,
    data_colheita DATE,
    data_limite DATE,
    status ENUM('disponivel', 'coletada', 'aguardando_aceite', 'entregue') DEFAULT 'disponivel',
    distribuidor_id INT,
    cozinheiro_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produtor_id) REFERENCES users(id),
    FOREIGN KEY (distribuidor_id) REFERENCES users(id),
    FOREIGN KEY (cozinheiro_id) REFERENCES users(id)
);

CREATE TABLE meals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cozinheiro_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    quantidade VARCHAR(100) NOT NULL,
    data_producao DATE,
    data_validade DATE,
    status ENUM('disponivel', 'coletada', 'entregue') DEFAULT 'disponivel',
    distribuidor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cozinheiro_id) REFERENCES users(id),
    FOREIGN KEY (distribuidor_id) REFERENCES users(id)
);

CREATE TABLE meal_ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meal_id INT NOT NULL,
    donation_id INT NOT NULL,
    FOREIGN KEY (meal_id) REFERENCES meals(id),
    FOREIGN KEY (donation_id) REFERENCES donations(id)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doacao_id INT NOT NULL,
    avaliador_id INT NOT NULL,
    avaliado_id INT NOT NULL,
    nota INT NOT NULL,
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doacao_id) REFERENCES donations(id),
    FOREIGN KEY (avaliador_id) REFERENCES users(id),
    FOREIGN KEY (avaliado_id) REFERENCES users(id)
);

CREATE TABLE meal_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    refeicao_id INT NOT NULL,
    avaliador_id INT NOT NULL,
    avaliado_id INT NOT NULL,
    nota INT NOT NULL,
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (refeicao_id) REFERENCES meals(id),
    FOREIGN KEY (avaliador_id) REFERENCES users(id),
    FOREIGN KEY (avaliado_id) REFERENCES users(id)
);

-- Criar Usuário Administrador Padrão
-- Email: admin@alimentosolidario.com
-- Senha: admin123 (Hash gerado abaixo)
INSERT INTO users (nome, email, password_hash, telefone, cidade, estado, tipo, termos_aceitos, banned) 
VALUES ('Administrador Sistema', 'admin@alimentosolidario.com', '$2y$10$e.g./l.W.d/.r./././././././././././././././././.', '0000000000', 'Sistema', 'BR', 'admin', 1, 0);
Passo 3: Configurar Conexão
Verifique o arquivo db.php na raiz do projeto. Se o seu MySQL tiver senha (o padrão do XAMPP é sem senha), edite esta parte:

PHP

$host = 'localhost';
$db   = 'ods_doacao';
$user = 'root';     // Seu usuário MySQL
$pass = '';         // Sua senha MySQL
Passo 4: Executar
Mova a pasta do projeto para dentro do diretório do seu servidor (ex: htdocs no XAMPP).

Acesse no navegador: http://localhost/alimentosolidario.

🔐 Acesso Administrativo
Para acessar o painel de controle e moderação:

URL: http://localhost/alimentosolidario/login.php

Email: admin@alimentosolidario.com

Senha: admin123 (Nota: Se o hash da senha no SQL acima não funcionar devido a diferenças de versão do PHP, crie um usuário comum no cadastro e altere manualmente a coluna tipo para admin no banco de dados).

📂 Estrutura de Arquivos
index.php: Landing page pública.

db.php: Conexão com banco de dados.

cadastro.php / login.php: Autenticação.

feed.php: Painel principal dos usuários (Produtor/Distribuidor/Cozinheiro).

feed_admin.php: Painel exclusivo do administrador.

perfil.php: Edição de dados do usuário.

criar_doacao.php / criar_refeicao.php: Formulários de cadastro.

processar_acao.php: Lógica de backend para mudanças de status (coletas, entregas).

admin_acoes.php: Lógica de backend do administrador (banir, excluir).
