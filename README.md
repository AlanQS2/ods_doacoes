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

`Passo 1: Clonar o Repositório`
Bash

git clone https://github.com/seu-usuario/alimentosolidario.git
cd alimentosolidario`

`Passo 2: Configurar o Banco de Dados`
Abra o phpMyAdmin (ou seu gerenciador SQL preferido).

Crie um novo banco de dados chamado ods_doacao.

`Execute o script SQL presente no repositorio`


`Passo 3: Configurar Conexão`
Verifique o arquivo db.php na raiz do projeto. Se o seu MySQL tiver senha (o padrão do XAMPP é sem senha), edite esta parte:

`PHP`

$host = 'localhost';
$db   = 'ods_doacao';
$user = 'root';     // Seu usuário MySQL
$pass = '';         // Sua senha MySQL
Passo 4: Executar
Mova a pasta do projeto para dentro do diretório do seu servidor (ex: htdocs no XAMPP).

`Acesse no navegador: http://localhost/alimentosolidario.`

🔐 Acesso Administrativo
Para acessar o painel de controle e moderação:

`URL: http://localhost/alimentosolidario/login.php`

`Email: admin@alimentosolidario.com`

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
