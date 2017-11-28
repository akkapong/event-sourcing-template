<?php
declare(strict_types=1);

namespace Script;

use Script\CollectionFile;

class Generate {
	//get arguments
	public $projectPath;
	public $moduleName;
	public $configs;
	public $mappings;
	public $directories = [
		'collection'     => 'Collections',
		'infrastructure' => 'Infrastructure',
		'command'        => 'Model/Command',
		'event'          => 'Model/Event',
		'module'         => 'Model',
		'projection'     => 'Projection',
		'controller'     => 'UI/Controllers',
		'worker'         => 'UI/Worker',
		'command_route'  => '',
		'event_route'    => '',
	];

	public function __construct(string $moduleName)
	{
		$this->moduleName  = $moduleName;
		$this->configs     = require "event/$moduleName.php";
		$this->mappings    = require "mapping.php";
		$this->projectPath = dirname(__DIR__);
	}

	//method for create directory
	public function createDirectory()
	{
		$base = $this->projectPath.'/src/';

		foreach ($this->directories as $each) {
			//get fullpath
			$fullpath = $base.$this->configs['name'].'/'.$each;

			if (file_exists($fullpath)) { continue; }

			if (!mkdir($fullpath, 0755, true)) { die('Failed to create folders...'); }
		}
		

	}

	//method for get generate file class
	public function getGenClass(string $type) :?GenFile 
	{
		$class = null;
		switch ($type) {
			case "collection": 
				$class = new CollectionFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "infrastructure": 
				$class = new InfrastructureFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "command": 
				$class = new CommandFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "command_handle": 
				$class = new CommandHandleFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "event": 
				$class = new EventFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "module": 
				$class = new ModuleFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "module_repo": 
				$class = new ModuleRepositoryFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "projection": 
				$class = new ProjectionFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "controller": 
				$class = new ControllerFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "worker": 
				$class = new WorkerFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "command_route": 
				$class = new CommandRouteFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
			case "event_route": 
				$class = new EventRouteFile($this->projectPath, $this->configs, $this->directories, $this->mappings[$type]);
				break;
		}

		return $class;
	}

}