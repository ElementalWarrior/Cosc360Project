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
			return $this->render_action('register', 'account', array('error' => 'All fields must be filled out, passwords must match, and there must be an image uploaded.'));
		}
		if(!empty($_FILES['image']) && $_FILES['image']['size'] > 200*1024) {

			return $this->render_action('register', 'account', array('error' => 'Image must be at maximum 200KB.'));
		}


		$target_file = $_FILES['image']['tmp_name'];
		$imageFileType = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
		$image_handle = fopen($_FILES['image']['tmp_name'], 'rb');
		$password = md5($password);

		if(array_search($imageFileType, ['jpg', 'png', 'gif']) === false) {
			return $this->render_action('register', 'account', array('error' => 'Image must be of type jpg, png, or gif.'));
		}

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

	public function profile() {
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_profile();
		}

		global $user;
		if(!is_array($user)) {
			header('Location: /');
		}
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('SELECT image, username, email, content_type from accounts where account_id = :account_id');
		$stmt->execute(array(':account_id' => $user['account_id']));
		$results = $stmt->fetch();

		$view_data = array(
			'username' => $results['username'],
			'email' => $results['email'],
			'image' => $results['image'],
			'content_type' => $results['content_type']
		);
		return $this->render_action('profile', 'account', $view_data);
	}
	private function post_profile() {
		global $user;

		$email = $_POST['email'];
		$view_data = array(
			'username' => $user['username'],
			'email' => $email,
			'image' => file_get_contents($_FILES['image']['tmp_name']),
			'content_type' => $_FILES['image']['type']
		);

		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('SELECT email from accounts where email = :email and account_id <> :account_id');
		$stmt->execute(array(':email' => $_POST['email'], ':account_id' => $user['account_id']));
		$results = $stmt->fetchAll();

		$target_file = $_FILES['image']['tmp_name'];
		$imageFileType = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
		$image_handle = fopen($_FILES['image']['tmp_name'], 'rb');

		$ok = true;
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$view_data['error'] = 'Must be a valid email address.';
			$ok = false;
		}
		else if(!empty($results))
		{
			$view_data['error'] = 'This email is already in use.';
			$ok = false;
		}
		else if(!empty($_FILES['image']) && $_FILES['image']['size'] > 200*1024) {
			$view_data['error'] = 'Image must be 200KB or smaller';
			$ok = false;
		}
		else if(array_search($imageFileType, ['jpg', 'png', 'gif']) === false) {
			$view_data['error'] = 'Image must be of type jpg, png, or gif.';
			$ok = false;
		}

		if(!$ok) {
			return $this->render_action('profile', 'account', $view_data);
		}

		$dbh->beginTransaction();

		if(!empty($_FILES)) {

			$stmt = $dbh->prepare('UPDATE accounts set image = :image, content_type = :contenttype where account_id = :account_id');
			$stmt->bindParam(':account_id', $user['account_id']);
			$stmt->bindParam(':contenttype', $imageFileType);
			$stmt->bindParam(':image', $image_handle, PDO::PARAM_LOB);
			$stmt->execute();
		}

		$stmt = $dbh->prepare('UPDATE accounts set email = :email where account_id = :account_id');
		$stmt->bindParam(':account_id', $user['account_id']);
		$stmt->bindParam(':email', $email);
		$stmt->execute();

		$dbh->commit();


		return $this->render_action('profile', 'account', $view_data);
	}
}
