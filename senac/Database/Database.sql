-- Criação do banco de dados
CREATE DATABASE sistema_usuarios;
USE sistema_usuarios;

-- Tabela: usuários
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    telefone VARCHAR(20) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);


alter table usuarios
add column id_perfil enum("cli", "adm") not null;

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

alter table usuarios
add column telefone varchar(11) not null;

describe usuarios;
create table administrador( 
	id_administrador int not null auto_increment, 
    id_usuario int not null,
    constraint primary key(id_administrador),
    
    foreign key(id_usuario) references usuarios(id_usuario) 
); 

INSERT INTO usuarios (id_usuario, telefone, senha, id_perfil) values (default, '123456', '1234','adm');

select * from usuarios;

describe usuarios;

INSERT INTO administrador (id_administrador, email, senha_adm) values (default, 'buyathomecgr@gmail.com', '18072025');

drop table che;

CREATE TABLE administrador (
  id_administrador INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(50) NOT NULL,
  senha_adm VARCHAR(8) NOT NULL,
  id_perfil ENUM('cli','adm') NOT NULL
);

INSERT INTO  administrador (id_administrador, email, senha_adm, id_perfil) values (default, 'buyathomecgr@gmail.com', '18072025', 'adm');

CREATE TABLE checkin_diario (
    id_checkin INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    data_checkin DATE NOT NULL,
    dia_sequencia INT NOT NULL,
    recompensa INT DEFAULT 1,
    UNIQUE KEY unique_checkin (id_usuario, data_checkin),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

UPDATE checkin_diario
SET data_checkin = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
WHERE id_usuario = 1
ORDER BY data_checkin DESC
LIMIT 1;

ALTER TABLE usuarios ADD COLUMN ativo TINYINT(1) DEFAULT 1;

select * from checkin_diario;

UPDATE usuarios
SET ativo = 1
WHERE id_usuario = 14;


CREATE TABLE promocoes (
    id_promocao INT AUTO_INCREMENT PRIMARY KEY,
    imagem_promocao varchar(255),
    promocao_titulo VARCHAR(255),
    promocao_subtitulo VARCHAR(255),
    status_produto TINYINT CHECK (status_produto IN(0,1)),
    tipo_promocao TINYINT CHECK (tipo_promocao IN(0,1))
);
select * from promocoes;
