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
}
