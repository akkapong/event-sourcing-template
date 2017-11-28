<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;
use Script\MyLib;

class EventFile implements GenFile {

	use CreateFunction;

	protected $templatePath;
	protected $destinationPath;
	protected $projectPath;
	protected $mappings;
	protected $directories;
	protected $configs;
	protected $myLib;
	
	public function __construct(string $projectPath, array $configs, array $directories, array $mappings)
	{
		$this->templatePath = $projectPath.'/script/template/Model/Event/Event.txt';
		
		$this->projectPath  = $projectPath;
		$this->mappings     = $mappings;
		$this->configs      = $configs;
		$this->directories  = $directories;
		
		$this->myLib        = new MyLib();
	}

	//method for get config value for collection 
	public function getConfigVal(string $keyEvent) :string
	{
		$value = '';
		$datas = $this->myLib->getValueFormObj(explode(".", $keyEvent), $this->configs);
		if (is_array($datas)) {
			
			$value .= $this->createFieldMethod($datas);

		} else {
			$value = $this->configs[$keyEvent];
		}

		return $value;
	}

	//Method for mange content in file 
	protected function manageContent(string $source, string $destination, array $mappings, string $eventName)
	{
		//get data from file
		$str = file_get_contents($source);

		foreach ($mappings as $keyTemplate => $keyEvent) {

			switch ($keyTemplate) {
				case '[[event]]':
					$value = $eventName;
					break;
				case '[[function]]':
					$value = $this->getConfigVal("event.$eventName");
					break;
				default:
					$value = $this->getConfigVal($keyEvent);
					break;
			}
			//replace something in the file string 
			$str = str_replace("$keyTemplate", $value, $str);

		}
		//write the entire string
		file_put_contents($destination, $str);
	}

	//method for create collection
	public function createFile() :void
	{
		$events = $this->configs['event'];

		foreach ($events as $eventName => $fields) {
			$destination = $this->projectPath.'/src/'.$this->configs['name'].'/'.$this->directories['event'].'/'.$eventName.'.php';

			$this->manageContent($this->templatePath, $destination, $this->mappings, $eventName);
		}
		
	}

}