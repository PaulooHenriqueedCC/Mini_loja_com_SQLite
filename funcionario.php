<?php
session_start(); require 'banco.php';
$logado=$_SESSION['funcionario']??'';
$podeCadastrar=$logado!=='' || contarFuncionarios()===0;
$erro=''; $mensagem='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    conferirToken();
    $acao=$_POST['acao']??'';
    $email=strtolower(trim($_POST['email']??'')); $senha=$_POST['senha']??'';
    if ($acao==='sair') { $_SESSION=[]; session_destroy(); header('Location: funcionario.php'); exit; }
    if ($acao==='entrar') {
        $f=buscarFuncionario($email);
        if ($f && password_verify($senha,$f['senha'])) { session_regenerate_id(true); $_SESSION['funcionario']=$f['nome']; header('Location: cadastro.php'); exit; }
        $erro='E-mail ou senha incorretos.';
    } elseif ($acao==='cadastrar') {
        $nome=trim($_POST['nome']??'');
        if (!$podeCadastrar) $erro='Acesso restrito.';
        elseif (!$nome || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($senha)<8 || strlen($senha)>72) $erro='Informe nome, e-mail válido e senha de 8 a 72 bytes.';
        elseif (buscarFuncionario($email)) $erro='E-mail já cadastrado.';
        else { try { inserirFuncionario($nome,$email,$senha); $mensagem='Funcionário cadastrado. Faça login para continuar.'; } catch(PDOException $e){ $erro='Não foi possível cadastrar.'; } }
    }
}
$podeCadastrar=$logado!=='' || contarFuncionarios()===0;
require 'tela-funcionario.php';
