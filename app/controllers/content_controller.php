<?php
require_once('controller.php');
class content_controller extends controller{
	public function index() {
		$this->log_activity("view");
		$dbh = $this->create_db_connection();
		$threads = array();
		foreach($dbh->query('SELECT thread_id, thread_name, username, num_posts, a.account_id from threads t join accounts a on a.account_id = t.account_id where date_deleted is null order by date_updated desc') as $row) {
			$threads[] = $row;
		}
		return $this->render_action('index', 'content', $threads);
	}
	public function thread($thread_id) {
		$this->log_activity("view", $thread_id);
		$thread_id = (int)$thread_id;
		$dbh = $this->create_db_connection();

		//get thread info
		$stmt = $dbh->prepare('SELECT thread_id, thread_name, thread_body, username, image, content_type, num_posts, a.account_id, t.date_created from threads t join accounts a on a.account_id = t.account_id where thread_id = :thread_id');
		$stmt->execute(array(':thread_id' => $thread_id));
		$thread = $stmt->fetch();

		//get post info
		$stmt = $dbh->prepare('SELECT post_id, post_body, username, image, content_type, p.date_created, a.account_id from posts p join accounts a on a.account_id = p.account_id where thread_id = :thread_id and date_deleted is null');
		$stmt->execute(array(':thread_id' => $thread_id));
		$posts = $stmt->fetchAll();
		return $this->render_action('thread', 'content', array('thread' => $thread, 'posts'=> $posts));
	}
	public function reply($thread_id) {
		global $user;
		global $sub_path;
		echo $thread_id;
		if($_SERVER['REQUEST_METHOD'] !== 'POST') {
			global $sub_path;
			header("Location: $sub_path/");
			die();
		}
		$thread_id = (int)$thread_id;

		$dbh = $this->create_db_connection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare('INSERT into posts(thread_id, post_body, account_id) select :thread_id, :post_body, :account_id from threads where thread_id = :thread_id and date_deleted is null');
		$post_body = $_POST['respond'];
		if(!is_numeric($thread_id) || strlen($post_body) == 0) {
			$dbh->rollBack();
			return;
		}
		$stmt->execute(array(
			':thread_id' => $thread_id
			, ':post_body' => $post_body
			, ':account_id' => $user['account_id']
		));
		$stmt = $dbh->prepare('UPDATE threads set num_posts = num_posts+1, date_updated = current_timestamp() where thread_id = :thread_id and date_deleted is null');
		$stmt->execute(array(':thread_id' => $thread_id));
		$dbh->commit();
		$this->log_activity("post_submit", $thread_id);
		header("Location: $sub_path/content/thread/$thread_id");
	}
	public function new_thread() {
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_new_thread();
		}

		$this->log_activity("view");
		return $this->render_action('new_thread', 'content');
	}

	private function post_new_thread() {
		global $user;
		global $sub_path;

		$thread_title = trim($_POST['thread_title']);
		$thread_body = trim($_POST['thread_body']);

		if(strlen($thread_title) == 0 || strlen($thread_body) == 0) {
			return $this->render_action('new_thread', 'content', array('error' => 'Must enter title and body'));
		}

		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('INSERT into threads(thread_name, thread_body, account_id) select :thread_title, :thread_body, :account_id');
		$stmt->execute(array(
			':thread_title' => $thread_title,
			':thread_body' => $thread_body,
			':account_id' => $user['account_id'],
		));
		$stmt = $dbh->prepare('SELECT LAST_INSERT_ID() as id');
		$stmt->execute();
		$id = $stmt->fetch()['id'];
		$this->log_activity("thread_submit", $id);
		header("Location: $sub_path/content/thread/$id");
	}
	public function search() {
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_search();
		}
		$this->log_activity("view");
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
				where (thread_name like :post
				or thread_body like :post
				or post_body like :post)
				and t.date_deleted is null
				and p.date_deleted is null');
			$stmt->execute(array(':post' => '%' . $search_post . '%'));
			$view_data['content_results'] = $stmt->fetchAll();
		}
		$this->log_activity("search");
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
	public function hot_threads() {
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('
			select * from (
			    select t.thread_id, thread_name, count(*) as hour_count, num_posts, date_updated
				from posts p join threads t on t.thread_id = p.thread_id
				where p.date_created > DATE_SUB(NOW(), INTERVAL 30*10000 MINUTE)
				and t.date_deleted is null
				and p.date_deleted is null
			    group by thread_id, thread_name, date_updated
			    union ALL
			    (select thread_id, thread_name, 0 as hour_count, num_posts, date_updated from threads where num_posts > 0 and date_deleted is null)) agg
			group by thread_id, thread_name, num_posts
			order by hour_count desc, date_updated desc
			limit 10');
		$stmt->execute();
		$results = $stmt->fetchAll();
		return $this->render_action('hot_threads', 'content', $results);
	}

	public function remove_thread($thread_id) {
		global $user;
		if(!is_array($user) || !$user['admin']) {
			header('HTTP/1.0 403 Forbidden', true, 403);
			return "";
		}
		$thread_id = (int)$thread_id;

		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('
			UPDATE threads set date_deleted = now() where thread_id = :thread_id');
		$stmt->execute(array(
			':thread_id' => $thread_id
		));
		$this->log_activity("thread_remove", $thread_id, null, true);
	}
	public function remove_post($thread_id, $post_id) {
		global $user;
		if(!is_array($user) || !$user['admin']) {
			header('HTTP/1.0 403 Forbidden', true, 403);
			return "";
		}
		$post_id = (int)$post_id;
		$thread_id = (int)$thread_id;

		$dbh = $this->create_db_connection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare('
			UPDATE posts set date_deleted = now() where post_id = :post_id and thread_id = :thread_id');
		$stmt->execute(array(
			':post_id' => $post_id,
			':thread_id' => $thread_id
		));
		$stmt = $dbh->prepare('
			UPDATE threads set num_posts = num_posts - 1 where thread_id = :thread_id');
		$stmt->execute(array(
			':thread_id' => $thread_id
		));
		$dbh->commit();
		$this->log_activity("post_remove", $thread_id, $post_id, true);
	}
	public function edit_thread($thread_id) {
		global $user;
		if(!is_array($user) || !$user['admin']) {
			header('HTTP/1.0 403 Forbidden', true, 403);
			return "";
		}
		$thread_id = (int)$thread_id;
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_edit_thread($thread_id);
		}
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('SELECT thread_name, thread_id, thread_name, thread_body from threads where thread_id = :thread_id');
		$stmt->execute(array(
			':thread_id' => $thread_id
		));
		$view_data = $stmt->fetch();

		$this->log_activity("view", $thread_id, null, true);
		return $this->render_action('edit_thread', 'content', $view_data);
	}

	private function post_edit_thread($thread_id) {
		global $user;
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('SELECT thread_name, thread_id, thread_name, thread_body from threads where thread_id = :thread_id');
		$stmt->execute(array(
			':thread_id' => $thread_id
		));
		$view_data = $stmt->fetch();
		$thread_title = trim($_POST['thread_title']);
		$thread_body = trim($_POST['thread_body']);
		$view_data['thread_name'] = $thread_title;
		$view_data['thread_body'] = $thread_body;
		if(strlen($thread_title) == 0 || strlen($thread_body) == 0) {
			$view_data['error'] = 'Must enter title and body';
			return $this->render_action('edit_thread', 'content', $view_data);
		}

		$stmt = $dbh->prepare('UPDATE threads set thread_name = :thread_name, thread_body = :thread_body where thread_id = :thread_id');
		$stmt->execute(array(
			':thread_name' => $thread_title,
			':thread_body' => $thread_body,
			':thread_id' => $thread_id
		));
		$this->log_activity("thread_edit", $thread_id, null, true);
		return $this->render_action('edit_thread', 'content', $view_data);
	}
	public function edit_post($thread_id, $post_id) {
		global $user;
		if(!is_array($user) || !$user['admin']) {
			header('HTTP/1.0 403 Forbidden', true, 403);
			return "";
		}
		$post_id = (int)$post_id;
		$thread_id = (int)$thread_id;
		if(empty($thread_id) || empty($post_id)) {
			header("Location: $sub_path/error/server_error");
			return "";
		}
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			return $this->post_edit_post($thread_id, $post_id);
		}
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare(
			'SELECT (select thread_name from threads t where t.thread_id = p.thread_id limit 1) as thread_name, thread_id, post_id, post_body, a.account_id, a.username from posts p
			join accounts a on a.account_id = p.account_id
			where post_id = :post_id and thread_id = :thread_id');
		$stmt->execute(array(
			':thread_id' => $thread_id,
			':post_id' => $post_id
		));
		$view_data = $stmt->fetch();
		$this->log_activity("view", $thread_id, $post_id, true);

		return $this->render_action('edit_post', 'content', $view_data);
	}

	private function post_edit_post($thread_id, $post_id) {
		global $user;
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare(
			'SELECT (select thread_name from threads t where t.thread_id = p.thread_id limit 1) as thread_name, thread_id, post_id, post_body, a.account_id, a.username from posts p
			join accounts a on a.account_id = p.account_id
			where post_id = :post_id and thread_id = :thread_id');
		$stmt->execute(array(
			':thread_id' => $thread_id,
			':post_id' => $post_id
		));
		$view_data = $stmt->fetch();
		$post_body = trim($_POST['post_body']);
		$view_data['post_body'] = $post_body;

		if(strlen($post_body) == 0) {
			$view_data['error'] = 'Must enter body';
			return $this->render_action('edit_post', 'content', $view_data);
		}

		$stmt = $dbh->prepare('UPDATE posts set post_body = :post_body where post_id = :post_id and thread_id = :thread_id');
		$stmt->execute(array(
			':post_body' => $post_body,
			':post_id' => $post_id,
			':thread_id' => $thread_id
		));
		$this->log_activity("post_edit", $thread_id, $post_id, true);
		return $this->render_action('edit_post', 'content', $view_data);
	}
	public function activity_admin($date = null, $account_id = null) {
		global $user;
		if(empty($user['admin']) || !$user['admin']) {
			return Html::render_action('file_not_found', 'error');
		}
		$this->log_activity("view",null,null,true);
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare(
			'SELECT al.*, thread_name from activity_log al
			left join threads t on t.thread_id = al.thread_id
			where (admin_action = 0 or admin_action = :admin)
			and (:date is null or al.date_created >= :date and al.date_created < date_add(:date, interval 1 day))
			and (:account_id is null or al.account_id = :account_id)
			order by al.date_created desc');
		$stmt->execute(array(
			':admin' => empty($user['admin']) ? 0 : $user['admin'] ? 1 : 0,
			':date' => $date,
			':account_id' => $account_id
		));
		$results = $stmt->fetchAll();
		$view_data = array(
			'results' => $results
		);
		return $this->render_action('activity_by_date', 'content', $view_data);
	}
	public function activity_by_date($date = null, $account_id = null) {
		global $user;
		if($account_id != null) {
			$account_id = (int)$account_id;
		}
		if(stripos($date, 'null') === 0) {
			$date = null;
		}
		$this->log_activity("view");
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare(
			'SELECT al.*, thread_name from activity_log al
			left join threads t on t.thread_id = al.thread_id
			where (admin_action = 0 or admin_action = :admin)
			and (:date is null or al.date_created >= :date and al.date_created < date_add(:date, interval 1 day))
			and (:account_id is null or al.account_id = :account_id)
			order by al.date_created desc');
		$stmt->execute(array(
			':admin' => empty($user['admin']) ? false : empty($user['admin']),
			':date' => $date,
			':account_id' => $account_id
		));
		$results = $stmt->fetchAll();
		$view_data = array(
			'results' => $results,
			'date' => $date
		);
		return $this->render_action('activity_by_date', 'content', $view_data);
	}
	public function administrate() {
		global $user;
		if(empty($user['admin']) || !$user['admin']) {
			return Html::render_action('file_not_found', 'error');
		}
		$this->log_activity("view",null,null,true);
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare(
			"SELECT 'visitors_today' as metric, count(*) as value from (select 1 from activity_log where date_format(date_created, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d') group by ip, username, user_agent) individ_users union all

			SELECT 'visitors_members_today' as metric, count(*) as value from (select 1 from activity_log where account_id is not null and date_format(date_created, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d') group by ip, username, user_agent) individ_users union all

			SELECT  'visitors_daily_average' as metric, sum(visitors_day) / datediff(now(), min_date) from (
				SELECT count(*) as visitors_day, min_date
				from (
					select 1 as value, date_format(date_created, '%Y-%m-%d') as day
					from activity_log group by ip, username, user_agent, date_format(date_created, '%Y-%m-%d')
				) individ_users,
				(select min(date_created) as min_date from activity_log) min_date
				group by day
			) agg  union all

			select 'members', count(*) from accounts union all

			select 'members_today', count(*) from accounts where date_format(date_created, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d') union all

			select 'threads_today', count(*) from threads where date_format(date_created, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d') and date_deleted is null union all

			select 'posts_today', count(*) from posts where date_format(date_created, '%Y-%m-%d') = date_format(now(), '%Y-%m-%d') and date_deleted is null union all

			select 'threads_total', count(*) from threads where date_deleted is null
			"
		);
		$stmt->execute();
		$results = $stmt->fetchAll();

		$stats = array();

		foreach($results as $row) {
			$stats[$row['metric']] = $row['value'];
		}
		$stmt = $dbh->prepare(
		"SELECT day, count(*) as visitors_day
		from (
			select 1 as value, date_format(date_created, '%Y-%m-%d') as day
			from activity_log group by ip, username, user_agent, date_format(date_created, '%Y-%m-%d')
		) individ_users
		group by day");
		$stmt->execute();
		$results = $stmt->fetchAll();

		return $this->render_action('administrate', 'content', array(
			'stats' => $stats,
			'daily_visitors' => $results
		));
	}
	public function check_thread($date_last_updated) {

		$date_last_updated = (new DateTime(urldecode($date_last_updated)))->format('Y-m-d H:i:s');
		$dbh = $this->create_db_connection();

		$stmt = $dbh->prepare('SELECT *, username from threads join (select account_id, username from accounts) a on a.account_id = threads.account_id where date_created > :date_last_updated and date_deleted is null order by date_updated');
		$stmt->execute(array(
			':date_last_updated' => $date_last_updated
		));
		$results = $stmt->fetchAll();

		$ret = array('result' => json_encode($results), 'include_layout' => 0);
		return $ret;
	}
	public function check_posts($date_last_updated, $thread_id) {

		$date_last_updated = (new DateTime(urldecode($date_last_updated)))->format('Y-m-d H:i:s');
		$dbh = $this->create_db_connection();

		$stmt = $dbh->prepare('SELECT *, username, image, content_type from posts join (select account_id, username, image, content_type from accounts) a on a.account_id = posts.account_id where date_created > :date_last_updated and date_deleted is null order by date_created');
		$stmt->execute(array(
			':date_last_updated' => $date_last_updated
		));
		$results = $stmt->fetchAll();
		for($i = 0; $i < count($results); $i++) {
			$results[$i]['image'] = base64_encode($results[$i]['image']);
		}

		$ret = array('result' => json_encode($results), 'include_layout' => 0);
		return $ret;
	}
	public function thread_content($thread_id) {
		return array('result' => Html::render_action('thread', 'content', [$thread_id]), 'include_layout' => 0);
	}
	public function announcements() {

		$dbh = $this->create_db_connection();

		$stmt = $dbh->prepare('SELECT an.*, a.username from announcements an
			join accounts a on an.account_id = a.account_id
			where date_archived is null
			order by date_created desc');
		$stmt->execute();
		$results = $stmt->fetchAll();
		return $this->render_action('announcements', 'content', $results);
	}
	public function submit_announcement() {
		global $user;
		global $sub_path;
		if($_SERVER['REQUEST_METHOD'] !== "POST" || empty($user) || !$user['admin']) {
			return Html::render_action('file_not_found', 'error');
		}
		$dbh = $this->create_db_connection();

		$stmt = $dbh->prepare('INSERT into announcements(announcement_title, announcement_body, account_id) select :title, :body, :account_id');
		$stmt->execute(array(
			':title' => $_POST['announcement_title'],
			':body' => $_POST['announcement_body'],
			':account_id' => $user['account_id']
		));
		$this->log_activity("announcement_submit");
		header("Location: $sub_path/");
	}
	public function remove_announcement($announcement_id) {
		global $user;
		global $sub_path;
		if(empty($user) || !$user['admin']) {
			return Html::render_action('file_not_found', 'error');
		}
		$announcement_id = (int)$announcement_id;
		$dbh = $this->create_db_connection();

		$stmt = $dbh->prepare('UPDATE announcements set date_archived = now() where announcement_id = :id');
		$stmt->execute(array(
			':id' => $announcement_id
		));
		$this->log_activity("announcement_remove");
		header("Location: $sub_path/");
	}
}
