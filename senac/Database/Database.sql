-- Criação do banco de dados
CREATE DATABASE sistema_usuarios;
USE sistema_usuarios;

-- Tabela: usuários
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    telefone VARCHAR(20) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Tabela: administradores
CREATE TABLE adm (
    id_adm INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Tabela: promoções
CREATE TABLE promocoes (
    id_promocao INT AUTO_INCREMENT PRIMARY KEY,
    promocao_semanal VARCHAR(255),
    promocao_sazonal VARCHAR(255),
    status_produto TINYINT CHECK (status_produto IN(0,1))
);

-- Tabela: checkin
CREATE TABLE checkin (
    id_checkin INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    start_day DATE NOT NULL,
    last_day DATE NOT NULL,
    checkin_ativo BOOLEAN DEFAULT TRUE,
    sequence_day INT,
    
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) 
);
