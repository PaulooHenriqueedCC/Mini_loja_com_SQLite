<?php
session_start();
require 'banco.php';
$logado = $_SESSION['funcionario'] ?? '';
if (!$logado && ($_SERVER['REQUEST_METHOD']==='POST' || isset($_GET['editar']))) { header('Location: funcionario.php'); exit; }
function salvarFoto($arquivo){
    if (!$arquivo || $arquivo['error']===UPLOAD_ERR_NO_FILE) return null;
    if ($arquivo['error']!==UPLOAD_ERR_OK || $arquivo['size']>2*1024*1024) throw new RuntimeException('Imagem inválida ou maior que 2 MB.');
    $info=@getimagesize($arquivo['tmp_name']);
    $tipos=[IMAGETYPE_JPEG=>'jpg',IMAGETYPE_PNG=>'png',IMAGETYPE_WEBP=>'webp'];
    if (!$info || !isset($tipos[$info[2]])) throw new RuntimeException('Use JPG, PNG ou WEBP.');
    $nome=bin2hex(random_bytes(16)).'.'.$tipos[$info[2]];
    if (!move_uploaded_file($arquivo['tmp_name'], __DIR__.'/uploads/'.$nome)) throw new RuntimeException('Não foi possível salvar a foto.');
    return $nome;
}
function apagarFoto($nome){ if ($nome && is_file(__DIR__.'/uploads/'.basename($nome))) unlink(__DIR__.'/uploads/'.basename($nome)); }
$erro=''; $edicao=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    conferirToken(); $nova=null;
    try {
        $id=(int)($_POST['id']??0); $antigo=$id?buscarProduto($id):null;
        if ($id && !$antigo) throw new RuntimeException('Produto não encontrado.');
        if (($_POST['acao']??'')==='excluir') {
            if (!$antigo) throw new RuntimeException('Produto não encontrado.');
            excluirProduto($id); apagarFoto($antigo['imagem']);
        } else {
            $nome=trim($_POST['nome']??''); $preco=filter_var(str_replace(',','.',$_POST['preco']??''),FILTER_VALIDATE_FLOAT);
            if ($nome==='' || $preco===false || !is_finite($preco) || $preco<=0) throw new RuntimeException('Informe nome e preço positivo.');
            $nova=salvarFoto($_FILES['imagem']??null);
            if ($id) { atualizarProduto($id,$nome,$preco,$nova??$antigo['imagem']); if($nova) apagarFoto($antigo['imagem']); }
            else inserirProduto($nome,$preco,$nova);
        }
        header('Location: cadastro.php'); exit;
    } catch (Exception $e) { if($nova) apagarFoto($nova); $erro=$e instanceof PDOException?'Erro no banco de dados.':$e->getMessage(); $edicao=$antigo??null; }
}
if(isset($_GET['editar'])) $edicao=buscarProduto((int)$_GET['editar']);
$produtos=listarProdutos();
require 'tela.php';
