<?php

namespace jtojnar\Wunderdisplay;

use Exception;
use GuzzleHttp;
use JohnRivs\Wunderlist\Wunderlist;
use Latte;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

class Wunderdisplay {
	private const DEFAULT_TITLE = 'to-do list';

	/** @var string */
	private $clientId;

	/** @var string */
	private $clientSecret;

	/** @var string */
	private $accessToken;

	/** @var int */
	private $listId;

	/** @var string */
	private $title;

	/** @var string */
	private $cacheDirectory;

	/**
	 * @param array $config dictionary of configuration keys
	 *  - string client_id
	 *  - string client_secret
	 *  - string access_token
	 *  - int list_id
	 *  - ?string title
	 */
	public function __construct(array $config) {
		if (!isset($config['client_id'])) {
			throw new Exception('Missing config value “client_id”.');
		}

		if (!isset($config['client_secret'])) {
			throw new Exception('Missing config value “client_secret”.');
		}

		if (!isset($config['access_token'])) {
			throw new Exception('Missing config value “access_token”.');
		}

		if (!isset($config['list_id'])) {
			throw new Exception('Missing config value “list_id”.');
		}

		$this->clientId = $config['client_id'];
		$this->clientSecret = $config['client_secret'];
		$this->accessToken = $config['access_token'];
		$this->listId = $config['list_id'];
		$this->title = $config['title'] ?? self::DEFAULT_TITLE;
		$this->cacheDirectory = __DIR__ . '/../tmp';
	}

	public function run() {
		$this->render();
	}

	private function render() {
		$latte = new Latte\Engine();

		$latte->setTempDirectory($this->cacheDirectory);

		$latte->addFilter('formatTask', function($task) {
			$task = htmlspecialchars($task);
			$task = preg_replace('(#(\w+))i', '<a href="#$1" rel="noopener noreferrer">#$1</a>', $task);
			$task = preg_replace('(([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/]))i', '<a href="$1" rel="noopener noreferrer">$1</a>', $task);

			return $task;
		});

		$parameters = [
			'title' => $this->title,
			'tasks' => $this->fetchData(),
		];

		$latte->render(__DIR__ . '/template.latte', $parameters);
	}

	/**
	 * @throws GuzzleHttp\Exception\ClientException
	 */
	private function fetchData() {
		$storage = new FileStorage($this->cacheDirectory);
		$cache = new Cache($storage);

		$tasks = $cache->load('tasks', function(&$dependencies) {
			$dependencies[Cache::EXPIRATION] = '1 hour';

			$wunderlist = new Wunderlist($this->clientId, $this->clientSecret, $this->accessToken);
			$tasks = $wunderlist->getTasks(['list_id' => $this->listId]);

			$tasks = array_map(function($task) {
				preg_match_all('(#(\w+))i', $task['title'], $matches);
				$task['tags'] = $matches[1];

				return $task;
			}, $tasks);

			return $tasks;
		});

		return $tasks;
	}
}
