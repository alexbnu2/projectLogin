<?php

Class Usuario
{
	private $pdo;
	public $msgErro = "";

	public function conectar($nome, $host, $usuario, $senha)
	{
		global $pdo;
		global $msgErro;
	
		try {
			$pdo = new PDO("mysql:dbname=".$nome.";host=".$host,$usuario,$senha);
			
		} catch (PDOException $e) {
			$msgErro = $e-> getMessage();
		}

	}

	public function cadastrar($nome, $telefone, $email, $senha)
	{
		global $pdo;

		//verificar se já existe um e-mail cadastrado
		$sql = $pdo->prepare("SELECT id_usuario FROM usuario where email = :e");

		$sql->bindValue(":e",$email);
		$sql->execute();
		if($sql->rowCount() > 0)
		{
			return false; //já está cadastrada
		}
		else
		{
			//caso não cadastrado
			$sql = $pdo->prepare("INSERT INTO usuario (nome,telefone,email,senha) VALUES (:n, :t, :e, :s)");
			$sql->bindValue(":n",$nome);
			$sql->bindValue(":t",$telefone);
			$sql->bindValue(":e",$email);
			$sql->bindValue(":s",md5($senha));
			$sql->execute();
			return true;
		}
	}

	public function logar($email, $senha){
		global $pdo;

		//verificar se o email e senha estao cadastrados, se sim
		$sql = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = :e AND senha = :s");
		$sql->bindValue(":s",md5($senha));
		$sql->execute();
		if($sql-> rowCount() > 0){
			//entrar no sistema (sessao)
			$dado = $sql->fetch();
			session_start();
			$_SESSION['id_usuario'] = $dado['id_usuario'];
			return true; //logado com sucesso		
		}
		else{
			return false; //não foi possível logar
		}
	}
}
?>