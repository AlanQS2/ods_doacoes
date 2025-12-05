-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/12/2025 às 04:21
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ods_doacao`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `produtor_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `quantidade` varchar(100) DEFAULT NULL,
  `unidade` varchar(20) DEFAULT NULL,
  `data_colheita` date DEFAULT NULL,
  `data_limite` date DEFAULT NULL,
  `status` enum('disponivel','coletada','aguardando_aceite','entregue','finalizada') DEFAULT 'disponivel',
  `distribuidor_id` int(11) DEFAULT NULL,
  `cozinheiro_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `donations`
--

INSERT INTO `donations` (`id`, `produtor_id`, `titulo`, `descricao`, `tipo`, `quantidade`, `unidade`, `data_colheita`, `data_limite`, `status`, `distribuidor_id`, `cozinheiro_id`, `created_at`) VALUES
(1, 1, 'Tomates', 'a', 'vegetais', '1', 'kg', '2025-12-04', '2025-12-06', 'entregue', 2, 3, '2025-12-05 01:32:51'),
(2, 1, 'Cenouras', 'a', 'vegetais', '2', 'kg', '2025-12-05', '2025-12-06', 'entregue', 2, 3, '2025-12-05 02:02:36'),
(3, 1, 'Batata', 'daora', 'vegetais', '100', 'caixas', '2025-12-07', '2025-12-13', 'entregue', 2, 3, '2025-12-05 03:04:57'),
(4, 1, 'Café', 'aa', 'vegetais', '100', 'unidades', '2025-12-06', '2025-12-09', 'disponivel', NULL, NULL, '2025-12-05 03:06:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `meals`
--

CREATE TABLE `meals` (
  `id` int(11) NOT NULL,
  `cozinheiro_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `quantidade` varchar(100) DEFAULT NULL,
  `data_producao` date NOT NULL,
  `data_validade` date NOT NULL,
  `status` enum('disponivel','coletada','entregue') DEFAULT 'disponivel',
  `distribuidor_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `meals`
--

INSERT INTO `meals` (`id`, `cozinheiro_id`, `titulo`, `quantidade`, `data_producao`, `data_validade`, `status`, `distribuidor_id`, `created_at`) VALUES
(1, 3, 'Sopão', '100', '2025-12-05', '2025-12-08', 'entregue', 2, '2025-12-05 02:58:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `meal_ingredients`
--

CREATE TABLE `meal_ingredients` (
  `id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `meal_ingredients`
--

INSERT INTO `meal_ingredients` (`id`, `meal_id`, `donation_id`) VALUES
(1, 1, 1),
(2, 1, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `meal_reviews`
--

CREATE TABLE `meal_reviews` (
  `id` int(11) NOT NULL,
  `refeicao_id` int(11) NOT NULL,
  `avaliador_id` int(11) NOT NULL,
  `avaliado_id` int(11) NOT NULL,
  `nota` int(11) NOT NULL CHECK (`nota` >= 1 and `nota` <= 5),
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `meal_reviews`
--

INSERT INTO `meal_reviews` (`id`, `refeicao_id`, `avaliador_id`, `avaliado_id`, `nota`, `comentario`, `created_at`) VALUES
(1, 1, 2, 3, 4, '', '2025-12-05 03:00:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `doacao_id` int(11) NOT NULL,
  `avaliador_id` int(11) NOT NULL,
  `avaliado_id` int(11) NOT NULL,
  `nota` int(11) NOT NULL CHECK (`nota` >= 1 and `nota` <= 5),
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reviews`
--

INSERT INTO `reviews` (`id`, `doacao_id`, `avaliador_id`, `avaliado_id`, `nota`, `comentario`, `created_at`) VALUES
(5, 1, 2, 3, 5, '', '2025-12-05 01:56:06'),
(6, 1, 2, 1, 5, '', '2025-12-05 01:56:09'),
(7, 1, 1, 2, 5, '', '2025-12-05 01:56:47'),
(8, 1, 1, 3, 4, '', '2025-12-05 01:56:49'),
(9, 1, 3, 2, 2, '', '2025-12-05 02:00:11'),
(10, 1, 3, 1, 2, '', '2025-12-05 02:00:13'),
(11, 2, 3, 2, 4, '', '2025-12-05 03:04:06'),
(12, 2, 3, 1, 4, '', '2025-12-05 03:04:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `tipo` enum('produtor','distribuidor','cozinheiro','administrador') NOT NULL,
  `termos_aceitos` tinyint(1) NOT NULL DEFAULT 0,
  `capacidade_transporte` varchar(100) DEFAULT NULL,
  `capacidade_producao` varchar(100) DEFAULT NULL,
  `disponibilidade` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `password_hash`, `telefone`, `cidade`, `estado`, `tipo`, `termos_aceitos`, `capacidade_transporte`, `capacidade_producao`, `disponibilidade`, `created_at`) VALUES
(1, 'Alan de Queiroz', 'alanqueiroz00@hotmail.com', '$2y$10$9st1u32kjwLh0Ltf57UGq.1zQUwMvohc3ZP5akVJ.Y3DOo4oWnPty', '71988211995', 'Salvador', 'BA', 'produtor', 1, NULL, NULL, NULL, '2025-12-05 01:31:55'),
(2, 'Alan de Queiroz', 'aaa@aaa', '$2y$10$C5je2stWXYoM1bxMfa./M./hA1LEL0nMbgGAgIGaRbMk9Xus5Fet6', '71988211995', 'Salvador', 'BA', 'distribuidor', 1, NULL, NULL, NULL, '2025-12-05 01:32:10'),
(3, 'Alan de Queiroz', 'ccc@ccc', '$2y$10$D4rePPDKf.J1jTVyI9SbPOkcgOKds6XWKK.yIcyAcgNiBre4bpNBq', '71988211995', 'Salvador', 'BA', 'cozinheiro', 1, NULL, '10', '', '2025-12-05 01:32:30');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produtor_id` (`produtor_id`),
  ADD KEY `distribuidor_id` (`distribuidor_id`),
  ADD KEY `cozinheiro_id` (`cozinheiro_id`);

--
-- Índices de tabela `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cozinheiro_id` (`cozinheiro_id`),
  ADD KEY `distribuidor_id` (`distribuidor_id`);

--
-- Índices de tabela `meal_ingredients`
--
ALTER TABLE `meal_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meal_id` (`meal_id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Índices de tabela `meal_reviews`
--
ALTER TABLE `meal_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `refeicao_id` (`refeicao_id`),
  ADD KEY `avaliador_id` (`avaliador_id`),
  ADD KEY `avaliado_id` (`avaliado_id`);

--
-- Índices de tabela `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doacao_id` (`doacao_id`),
  ADD KEY `avaliador_id` (`avaliador_id`),
  ADD KEY `avaliado_id` (`avaliado_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `meals`
--
ALTER TABLE `meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `meal_ingredients`
--
ALTER TABLE `meal_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `meal_reviews`
--
ALTER TABLE `meal_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`produtor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`distribuidor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `donations_ibfk_3` FOREIGN KEY (`cozinheiro_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `meals`
--
ALTER TABLE `meals`
  ADD CONSTRAINT `meals_ibfk_1` FOREIGN KEY (`cozinheiro_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meals_ibfk_2` FOREIGN KEY (`distribuidor_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `meal_ingredients`
--
ALTER TABLE `meal_ingredients`
  ADD CONSTRAINT `meal_ingredients_ibfk_1` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`id`),
  ADD CONSTRAINT `meal_ingredients_ibfk_2` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Restrições para tabelas `meal_reviews`
--
ALTER TABLE `meal_reviews`
  ADD CONSTRAINT `meal_reviews_ibfk_1` FOREIGN KEY (`refeicao_id`) REFERENCES `meals` (`id`),
  ADD CONSTRAINT `meal_reviews_ibfk_2` FOREIGN KEY (`avaliador_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meal_reviews_ibfk_3` FOREIGN KEY (`avaliado_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`doacao_id`) REFERENCES `donations` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`avaliador_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`avaliado_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
