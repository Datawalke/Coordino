<?php
class InstallerController extends AppController {

	var $name = 'Installer';
    var $helpers = array('Form', 'Time', 'Html', 'Javascript', 'Session');
	
	public function beforeFilter() {
		$this->layout = 'install';
	}
	
    public function start() {
        $writeChecks = array(
            'config' => is_writable(APP.'/config'),
            'tmp' => is_writable(TMP),
            'tmp_cache' => is_writable(TMP.'/cache'),
            'uploads' => is_writable(APP.'/webroot/img/uploads/users'),
            'thumbs' => is_writable(APP.'/webroot/img/thumbs'),
            'database' => is_writable(APP.'/config/database.php')
        );
        $this->set('writeChecks', $writeChecks);
	}
	
	public function license() {
		if(!empty($this->data['License'])) {
			if($this->__licenseValidate($this->data['License']['key'])) {
				$file = fopen(APP.'/config/license.txt', "w");
				fwrite($file, trim($this->data['License']['key']));
				fclose($file);
				$this->Session->setFlash('Coordino key added.', 'success');
				$this->redirect('/install/database-config');
			} else {
				$this->Session->setFlash('Invalid Key', 'error');
			}
		}
	}
	
	public function database() {
		if(!empty($this->data['Database'])) {

			$connection = @mysql_connect($this->data['Database']['host'], $this->data['Database']['login'], $this->data['Database']['password']);
			$database = @mysql_select_db($this->data['Database']['database']);
			if(!$connection || !$database) {
				$this->Session->setFlash('Cannot connect to the database: ' . mysql_error(), 'error');
				$this->redirect('/install/database-config');
			} 

            $filePath = APP.'/config/database.php';


			$file = fopen($filePath, "w");
			$data = "
<?php
class DATABASE_CONFIG {

	var ".'$default'." = array(
		'driver' => 'mysql',
		'host' => '" . $this->data['Database']['host'] . "',
		'login' => '" . $this->data['Database']['login'] . "',
		'password' => '" . $this->data['Database']['password'] . "',
		'database' => '" . $this->data['Database']['database'] . "',
		'encoding' => 'utf8',
	);
}
?>
			";
			fwrite($file, trim($data));
			fclose($file);
		$this->Session->setFlash('Connection to database established.', 'success');
		$this->redirect('/install/run-sql');

		}
	}
	
	public function run_sql() {
        $db = ConnectionManager::getDataSource('default');

        if(!$db->isConnected()) {
			$this->Session->setFlash('Could not connect to database. Please check the settings in app/config/database.php and try again ', 'error');
			$this->redirect('/install/database-config');
        }

		$this->__executeSQLScript($db, CONFIGS.'sql'.DS.'coordino.sql');
		
		$this->loadModel('Setting');
		$settings['Setting'] = array(
			'name' => 'blacklist',
			'value' => '0',
			'autoload' => '1',
			'description' => serialize(array('fuck', 'ass','vagina','cunt','nigger','nig','penis','dicks','asshole','assholes','bitches','bitch','faggot'))
		);
		$this->Setting->create();
		$this->Setting->save($settings);
		
		$settings['Setting'] = array(
			'name' => 'remote_auth_key',
			'value' => uniqid(),
			'autoload' => '1',
			'description' => 'The authentication key allowing for remote logins.'
		);
		$this->Setting->create();
		$this->Setting->save($settings);
		
		$this->Session->setFlash('SQL imported correctly.', 'success');
        $this->redirect('/install/admin-account');
	}
	
	public function admin_account() {
		if(!empty($this->data['User'])) {
            App::import('Component', 'Session');
            App::import('Component', 'Auth');
            $this->Session = new SessionComponent();
            $this->Auth = new AuthComponent();
            $this->Auth->Session = $this->Session;
            $this->Auth->allow('*');
			$user = $this->__userSave($this->Auth->hashPasswords($this->data));
			file_put_contents(TMP.'installed.txt', date('Y-m-d, H:i:s'));
			$this->Auth->login($user);
	        $this->redirect('/');
		}
	}
	
    function __executeSQLScript($db, $fileName) {
        $statements = file_get_contents($fileName);
        $statements = explode(';', $statements);

        foreach ($statements as $statement) {
            if (trim($statement) != '') {
                $db->query($statement);
            }
        }
    }
	public function __userSave($data) {
		$this->loadModel('User');
		$this->loadModel('Post');
		$data['User']['id'] = 1;
		$data['User']['public_key'] = uniqid();
		$data['User']['password'] = $data['User']['password'];
		$data['User']['joined'] = time();
		$data['User']['ip'] = $_SERVER['REMOTE_ADDR'];
		$data['User']['url_title'] = $this->Post->niceUrl($data['User']['username']);
        $data['User']['permission'] = serialize(array('create','read','update','delete','admin'));
		$data['User']['registered'] = '1';
		
		/**
		 * Save the data
		 */
		$this->User->save($data);	
		
		$data['User']['id'] = $this->User->id;
		
		return $data;
	}

}
