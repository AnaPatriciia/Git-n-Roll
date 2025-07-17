CREATE TABLE `administrador` (
  `id_administrador` int NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `senha_adm` varchar(8) NOT NULL,
  `id_perfil` enum('cli','adm') NOT NULL,
  PRIMARY KEY (`id_administrador`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `checkin` (
  `id_checkin` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `start_day` date NOT NULL,
  `last_day` date NOT NULL,
  `checkin_ativo` tinyint(1) DEFAULT '1',
  `sequence_day` int DEFAULT NULL,
  PRIMARY KEY (`id_checkin`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `checkin_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `checkin_diario` (
  `id_checkin` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `data_checkin` date NOT NULL,
  `dia_sequencia` int NOT NULL,
  `recompensa` int DEFAULT '1',
  PRIMARY KEY (`id_checkin`),
  UNIQUE KEY `unique_checkin` (`id_usuario`,`data_checkin`),
  CONSTRAINT `checkin_diario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `promocoes` (
  `id_promocao` int NOT NULL AUTO_INCREMENT,
  `imagem_promocao` varchar(255) DEFAULT NULL,
  `promocao_titulo` varchar(255) DEFAULT NULL,
  `promocao_subtitulo` varchar(255) DEFAULT NULL,
  `status_produto` tinyint DEFAULT NULL,
  `tipo_promocao` tinyint DEFAULT NULL,
  PRIMARY KEY (`id_promocao`),
  CONSTRAINT `promocoes_chk_1` CHECK ((`status_produto` in (0,1))),
  CONSTRAINT `promocoes_chk_2` CHECK ((`tipo_promocao` in (0,1)))
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `senha` varchar(255) NOT NULL,
  `id_perfil` enum('cli','adm') NOT NULL,
  `telefone` varchar(11) NOT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuarios` (`senha`, `id_perfil`, `telefone`, `ativo`) VALUES
('nova123', 'cli', '67999887766', 1),
('nova456', 'cli', '67988887755', 1),
('nova789', 'cli', '67977776655', 1);

INSERT INTO `checkin_diario` (`id_usuario`, `data_checkin`, `dia_sequencia`, `recompensa`) VALUES
(22, CURDATE(), 1, 1),
(23, CURDATE(), 1, 1),
(24, CURDATE(), 1, 1);

INSERT INTO usuarios (id_usuario, telefone, senha, id_perfil) values (default, '123456', '1234','adm');