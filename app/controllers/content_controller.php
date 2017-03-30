<?php
require_once('controller.php');
class content_controller extends controller{
	public function index() {
		$dbh = $this->create_db_connection();
		$threads = array();
		foreach($dbh->query('SELECT thread_id, thread_name, username, num_posts from threads t join accounts a on a.account_id = t.account_id order by date_updated desc') as $row) {
			$threads[] = $row;
		}
		return $this->render_action('index', 'content', $threads);
	}
	public function thread($thread_id) {
		$thread_id = (int)$thread_id;
		$dbh = $this->create_db_connection();

		//get thread info
		$stmt = $dbh->prepare('SELECT thread_id, thread_name, thread_body, username, image, content_type, num_posts from threads t join accounts a on a.account_id = t.account_id where thread_id = :thread_id');
		$stmt->execute(array(':thread_id' => $thread_id));
		$thread = $stmt->fetch();

		//get post info
		$stmt = $dbh->prepare('SELECT post_id, post_body, username, image, content_type, p.date_created from posts p join accounts a on a.account_id = p.account_id where thread_id = :thread_id');
		$stmt->execute(array(':thread_id' => $thread_id));
		$posts = $stmt->fetchAll();
		return $this->render_action('thread', 'content', array('thread' => $thread, 'posts'=> $posts));
	}
	public function reply($thread_id) {
		global $user;
		echo $thread_id;
		if($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: /');
			die();
		}

		$dbh = $this->create_db_connection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare('INSERT into posts(thread_id, post_body, account_id) select :thread_id, :post_body, :account_id');
		$post_body = $_POST['respond'];
		if(!is_numeric($thread_id) || strlen($post_body) == 0) {
			$dbh->rollBack();
			return;
		}
		$thread_id = (int)$thread_id;
		$stmt->execute(array(
			':thread_id' => $thread_id
			, ':post_body' => $post_body
			, ':account_id' => $user['account_id']
		));
		$stmt = $dbh->prepare('UPDATE threads set num_posts = num_posts+1, date_updated = current_timestamp() where thread_id = :thread_id');
		$stmt->execute(array(':thread_id' => $thread_id));
		$dbh->commit();
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}
	public function new_thread() {
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_new_thread();
		}

		return $this->render_action('new_thread', 'content');
	}

	private function post_new_thread() {
		global $user;

		$thread_title = trim($_POST['thread_title']);
		$thread_body = trim($_POST['thread_body']);

		if(strlen($thread_title) == 0 || strlen($thread_body) == 0) {
			return $this->render_action('new_thread', 'content', array('error' => 'Must enter title and body'));
		}

		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('INSERT into threads(thread_name, thread_body, account_id) select :thread_title, :thread_body, :account_id');
		$stmt->execute(array(
			':thread_title' => $thread_title,
			':thread_body' => $thread_title,
			':account_id' => $user['account_id'],
		));
		$stmt = $dbh->prepare('SELECT LAST_INSERT_ID() as id');
		$stmt->execute();
		$id = $stmt->fetch()['id'];
		header("Location: /content/thread/$id");
	}
	public function search() {
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_search();
		}
		return $this->render_action('search', 'content');
	}
	private function post_search() {
		$dbh = $this->create_db_connection();

		$username = ($_POST['username']);
		$email = ($_POST['email']);
		$post = ($_POST['posts']);

		$results = array();
		$view_data = array();
		$search_username = '';
		$search_email = '';
		$search_post = '';
		$view_data['username'] = '';
		$view_data['email'] = '';
		$view_data['posts'] = '';
		if(!empty($username)) {
			$view_data['username'] = $username;
			$search_username = $this->strip_sql_specials($username);

			$stmt = $dbh->prepare('SELECT account_id, username, email, image, content_type from accounts where username like :username');
			$stmt->execute(array(':username' => '%' . $search_username . '%'));
			$view_data['user_results'] = $stmt->fetchAll();

		} else if (!empty($email)) {
			$view_data['email'] = $email;
			$search_email = $this->strip_sql_specials($email);

			$stmt = $dbh->prepare('SELECT account_id, username, email, image, content_type from accounts where email like :email');
			$stmt->execute(array(':email' => '%' . $search_email . '%'));
			$view_data['user_results'] = $stmt->fetchAll();

		} else if(!empty($post)) {
			$view_data['posts'] = $post;
			$search_post = $this->strip_sql_specials($post);
			$stmt = $dbh->prepare(
				'SELECT
					a1.account_id as thread_account_id, a1.username as thread_username, a1.email as thread_email, a1.image as thread_image, a1.content_type as thread_content_type,
					a2.account_id as post_account_id, a2.username as post_username, a2.email as post_email, a2.image as post_image, a2.content_type as post_content_type,
					t.thread_id, thread_name, thread_body, p.post_id, post_body
				from threads t
				left join posts p on t.thread_id = p.thread_id and p.post_body like :post
				join accounts a1 on t.account_id = a1.account_id
				left join accounts a2 on p.account_id = a2.account_id
				where thread_name like :post
				or thread_body like :post
				or post_body like :post');
			$stmt->execute(array(':post' => '%' . $search_post . '%'));
			$view_data['content_results'] = $stmt->fetchAll();
		}
		return $this->render_action('search', 'content', $view_data);
	}
	public static function strip_sql_specials($string) {
		return str_replace(
			array(
				'%',
				'_'
			),
			array(
				'\%',
				'\_'
			), $string);
	}
}
