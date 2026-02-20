-- Table structure for table `tbl_roles`

CREATE TABLE tbl_roles (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  description text,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

_____________________________________________________________________

-- Table structure for table `tbl_users`

CREATE TABLE tbl_users (
  id int(11) NOT NULL AUTO_INCREMENT,
  firstname varchar(100) NOT NULL,
  lastname varchar(100) NOT NULL,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  role_id int(11) NOT NULL,
  is_active tinyint(1) NOT NULL DEFAULT '1',
  last_login timestamp DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY `email` (email),
  KEY `idx_role_id` (role_id),
  KEY `idx_is_active` (is_active),
  KEY `idx_created_at` (created_at),
  CONSTRAINT `fk_users_role` FOREIGN KEY (role_id) REFERENCES tbl_roles (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

____________________________________________________________


CREATE TABLE tbl_user_remember_tokens (
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id int(11) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at timestamp NOT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_user_id (user_id),
    KEY idx_expires_at (expires_at),
    UNIQUE (token_hash),
    CONSTRAINT fk_users_remember_tokens FOREIGN KEY (user_id) REFERENCES tbl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


_____________________________________________

CREATE TABLE tbl_password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,    
    UNIQUE KEY uk_token_hash (token_hash),
    KEY idx_user_id (user_id),
    KEY idx_expires_at (expires_at),
    CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) 
    REFERENCES tbl_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


____________________________________________________________

CREATE TABLE `tbl_permissions` (
  id int(11) NOT NULL AUTO_INCREMENT,
  slug varchar(100) NOT NULL,
  description varchar(255),
  PRIMARY KEY (id),
  UNIQUE KEY slug (slug)
);

_______________________________________________

CREATE TABLE tbl_role_permissions (
  role_id int(11) NOT NULL,
  permission_id int(11) NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES tbl_roles(id) ON DELETE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES tbl_permissions(id) ON DELETE CASCADE
);

_________________________________________

INSERT INTO tbl_roles (id, name, description) VALUES( 
  1,
  'Somente Leitura',
  ''
);

INSERT INTO tbl_roles (id, name, description) VALUES(
  2,
  'Administrador',
  ''
);

INSERT INTO tbl_roles (id, name, description) VALUES(
  3,
  'Gerente',
  ''
);


INSERT INTO tbl_roles (id, name, description) VALUES(
  4,
  'Supervisores',
  ''
);

____________________________

INSERT INTO tbl_permissions (slug, description) VALUES
('users.view', 'Visualizar usuários'),
('users.create', 'Criar usuários'),
('users.edit', 'Editar usuários'),
('users.delete', 'Excluir usuários');

INSERT INTO tbl_role_permissions (role_id, permission_id)
SELECT 2, id FROM tbl_permissions;

_________________________
