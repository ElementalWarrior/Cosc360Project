<?php
require_once('controller.php');
class account_controller extends controller {
	public function login() {
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_login();
		}
		return $this->render_action('login', 'account');
	}
	private function post_login() {
		$dbh = $this->create_db_connection();
		$username = $_POST['username'];
		$password = md5($_POST['password']);
		// $username = $dbh->quote($_POST['username']);

		$stmt = $dbh->prepare('SELECT * from accounts where username = :username and password = :password');
		$stmt->execute(array(':username' => $username, ':password' => $password));
		$results = $stmt->fetch();
		if($results !== false) {
			$_SESSION['username'] = $results['username'];
			$_SESSION['account_id'] = $results['account_id'];
			header('Location: /');
		}
		return $this->render_action('login', 'account', $results);
	}
	public function logout() {
		session_unset();
		header('Location: /');
	}
	public function register() {
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_register();
		}

		return $this->render_action('register', 'account');
	}

	private function post_register() {
		$username = trim($_POST['username']);
		$password = $_POST['password'];
		$password_check = $_POST['password-check'];
		$email = $_POST['email'];

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)
		|| strlen(trim($username)) == 0
		|| strlen(trim($password)) == 0
		|| strlen(trim($password_check)) == 0
		|| strlen(trim($email)) == 0
		|| $password !== $password_check
		|| empty($_FILES['image'])
		|| getimagesize($_FILES["image"]["tmp_name"]) === false) {
			return;
		}

		$target_file = $_FILES['image']['tmp_name'];
		$imageFileType = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
		$image_handle = fopen($_FILES['image']['tmp_name'], 'rb');
		$password = md5($password);
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('INSERT into accounts(username, email, password, image, content_type) select :username, :email, :password, :image, :contenttype');
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':contenttype', $imageFileType);
		$stmt->bindParam(':image', $image_handle, PDO::PARAM_LOB);
		$stmt->execute();

		//login
		$this->post_login();

	}
}
