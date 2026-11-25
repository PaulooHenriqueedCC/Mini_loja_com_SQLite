<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/mini-loja.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->exec('CREATE TABLE IF NOT EXISTS produtos (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, preco REAL NOT NULL, imagem TEXT)');
$pdo->exec('CREATE TABLE IF NOT EXISTS funcionarios (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, email TEXT UNIQUE NOT NULL, senha TEXT NOT NULL)');
function listarProdutos(){ global $pdo; return $pdo->query('SELECT * FROM produtos ORDER BY nome')->fetchAll(); }
function buscarProduto($id){ global $pdo; $s=$pdo->prepare('SELECT * FROM produtos WHERE id=?'); $s->execute([$id]); return $s->fetch(); }
function inserirProduto($nome,$preco,$imagem){ global $pdo; $pdo->prepare('INSERT INTO produtos(nome,preco,imagem) VALUES(?,?,?)')->execute([$nome,$preco,$imagem]); }
function atualizarProduto($id,$nome,$preco,$imagem){ global $pdo; $pdo->prepare('UPDATE produtos SET nome=?,preco=?,imagem=? WHERE id=?')->execute([$nome,$preco,$imagem,$id]); }
function excluirProduto($id){ global $pdo; $pdo->prepare('DELETE FROM produtos WHERE id=?')->execute([$id]); }
function contarFuncionarios(){ global $pdo; return (int)$pdo->query('SELECT COUNT(*) FROM funcionarios')->fetchColumn(); }
function buscarFuncionario($email){ global $pdo; $s=$pdo->prepare('SELECT * FROM funcionarios WHERE email=?'); $s->execute([$email]); return $s->fetch(); }
function inserirFuncionario($nome,$email,$senha){ global $pdo; $pdo->prepare('INSERT INTO funcionarios(nome,email,senha) VALUES(?,?,?)')->execute([$nome,$email,password_hash($senha,PASSWORD_DEFAULT)]); }
function h($v){ return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function token(){ return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function conferirToken(){ if(!hash_equals(token(), (string)($_POST['csrf']??''))){ http_response_code(403); exit('Solicitação inválida'); } }
