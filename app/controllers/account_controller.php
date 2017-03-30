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
		$view_data = array();

		$stmt = $dbh->prepare('SELECT * from accounts where username = :username and password = :password');
		$stmt->execute(array(':username' => $username, ':password' => $password));
		$results = $stmt->fetch();
		if($results !== false) {
			$_SESSION['username'] = $results['username'];
			$_SESSION['account_id'] = $results['account_id'];
			$_SESSION['admin'] = $results['admin'];
			header('Location: /');
		} else {
			$view_data['error'] = 'There was a problem logging in.';
		}
		return $this->render_action('login', 'account', $view_data);
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
			return $this->render_action('register', 'account', array('error' => 'All fields must be filled out, passwords must match, and there must be an image (jpg, gif, png) uploaded.'));
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

	public function profile($account_id = null) {
		global $user;
		if(empty($account_id)){
			if(!is_array($user)) {
				header('Location: /');
				return "";
			}
			$account_id = $user['account_id'];
		}

		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_profile($account_id);
		}
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('SELECT image, username, email, content_type from accounts where account_id = :account_id');
		$stmt->execute(array(':account_id' => $account_id));
		$results = $stmt->fetch();

		$view_data = array(
			'account_id' => $account_id,
			'username' => $results['username'],
			'email' => $results['email'],
			'image' => $results['image'],
			'content_type' => $results['content_type']
		);
		return $this->render_action('profile', 'account', $view_data);
	}
	private function post_profile($account_id) {
		global $user;
		if($account_id != $user['account_id'] || $user['admin']) {
			return "";
		}
		$email = $_POST['email'];
		$view_data = array(
			'account_id' => $account_id,
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

	public function forgot_password() {

		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_forgot_password();
		}
		return $this->render_action('forgot_password', 'account');
	}
	private function post_forgot_password() {
		$view_data = array();
		$email = $_POST['email'];

		$ok = true;
		if(empty($email)) {
			$view_data['error'] = 'Must provide an email.';
			$ok = false;
		} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$view_data['error'] = 'Must provide a properly formatted email address.';
			$ok = false;
		}

		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('SELECT username, email from accounts where email = :email');
		$stmt->execute(array(
			':email' => $email
		));
		$results = $stmt->fetchAll();
		if(count($results) == 0) {
			$view_data['error'] = 'There was no account attached with this email address.';
			$ok = false;
		}
		if($ok) {
			$token = $this->getToken();
			$stmt = $dbh->prepare('UPDATE accounts set  recovery = :token, date_recovery = now() where email = :email');
			$stmt->execute(array(
				':email' => $email,
				':token' => $token
			));
			$href = 'http://' . $_SERVER['SERVER_NAME'] . '/account/change_password/' . $token;

			if(stripos($_SERVER['SERVER_NAME'], 'localhost') !== false) {
				mail($email, "Password Recovery", "Your password has been reset, use this link to recover it $href. This link will remain valid for 1 hour.");
			}

			$view_data['error'] = 'A recovery email has been sent to the address provided.';

		}
		return $this->render_action('forgot_password', 'account', $view_data);
	}

	//http://stackoverflow.com/questions/3290283/what-is-a-good-way-to-produce-a-random-site-salt-to-be-used-in-creating-passwo/3291689#3291689
	private function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
	}

	private function getToken($length=32){
	    $token = "";
	    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	    $codeAlphabet.= "0123456789";
	    for($i=0;$i<$length;$i++){
	        $token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
	    }
	    return $token;
	}

	public function recover_password($token) {
		if(empty($token)) {
			header('Location: /error/file_not_found');
			return "";
		}
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare("SELECT timestampdiff(MINUTE, NOW(), date_add(date_recovery, interval 1 hour)) as time_left from accounts where recovery = :token");
		$stmt->bindParam(':token', $token);
		$stmt->execute();
		$results = $stmt->fetchAll();
		if(count($results) == 0 || ((int)$results[0]['time_left']) < 0) {
			header('Location: /error/file_not_found');
			return "";
		}
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_recover_password($token);
		}
		return $this->render_action('recover_password', 'account', array('token' => $token));
	}

	private function post_recover_password($token) {
		$view_data = array('token' => $token);
		$password = $_POST['password'];
		$password_check = $_POST['password-check'];

		$ok = true;
		if(strlen($password) == 0
		|| strlen($password_check) == 0)
		{
			$view_data['error'] = 'Password must not be empty.';
			$ok = false;
		} else if($password !== $password_check) {
			$view_data['error'] = 'Passwords must match';
			$ok = false;
		}
		if($ok){
			$dbh = $this->create_db_connection();
			$stmt = $dbh->prepare('UPDATE accounts set password = :password where recovery = :token');
			$stmt->execute(array(
				':password' => md5($password),
				':token' => $token
			));
			$view_data['error'] = 'Password has been reset, proceed to <a href="/account/login/">login page</a> to login with your new password.';
		}

		return $this->render_action('recover_password', 'account', $view_data);
	}
}
