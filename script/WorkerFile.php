<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;

class WorkerFile implements GenFile {

	use CreateFunction;

	protected $templatePath;
	protected $destinationPath;
	protected $mappings;
	protected $configs;
	
	public function __construct(string $projectPath, array $configs, array $directories, array $mappings)
	{
		$this->templatePath    = $projectPath.'/script/template/UI/Worker/Sample.txt';
		$this->destinationPath = $projectPath.'/src/'.$configs['name'].'/'.$directories['worker'].'/'.$configs['module'].'Task.php';
		
		$this->mappings        = $mappings;
		$this->configs         = $configs;
		
	}

	//method for get config value for controller  
	public function getConfigVal(string $keyEvent): string
	{
		return $this->configs[$keyEvent];
	}

	//method for create collection
	public function createFile() :void
	{
		//get data from file
		$str = file_get_contents($this->templatePath);

		foreach ($this->mappings as $keyTemplate => $keyEvent) {
			$value = $this->getConfigVal($keyEvent);
			
			//replace something in the file string 
			$str = str_replace("$keyTemplate", $value, $str);

		}

		// print_r($str); exit;
		//write the entire string
		file_put_contents($this->destinationPath, $str);
		
	}

}