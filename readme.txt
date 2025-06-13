
INSERT INTO dados.stakeholdersPessoa (nome, email, celular, cpfcnpj, senha) VALUES
('Natalino Barros Amaral', 'natal.barros@sstars.com.br', '11941706685', '43072830827', CONVERT(VARCHAR(100), HASHBYTES('SHA2_256', '452424'), 2)),
('João Henrique', 'joao.gonzales@sstars.com.br', '11941706685', '43072830827', CONVERT(VARCHAR(100), HASHBYTES('SHA2_256', '452424'), 2));