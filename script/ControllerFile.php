<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;

class ControllerFile implements GenFile {

	use CreateFunction;

	protected $templatePath;
	protected $destinationPath;
	protected $mappings;
	protected $configs;
	
	public function __construct(string $projectPath, array $configs, array $directories, array $mappings)
	{
		$this->templatePath    = $projectPath.'/script/template/UI/Controllers/ModuleController.txt';
		$this->destinationPath = $projectPath.'/src/'.$configs['name'].'/'.$directories['controller'].'/'.$configs['module'].'Controller.php';
		
		$this->mappings        = $mappings;
		$this->configs         = $configs;
	}

	//Method for get parameter from request
	protected function createGetParams(): string
	{
		return "\$params = \$this->getUrlParams();\n";
	}

	//method for get config value for controller  
	public function getConfigVal(string $keyEvent): string
	{
		$value = '';
		
		switch ($keyEvent) {
			case "param":
				$value = $this->createGetParams();
				break;
			case "lower_module":
				$value = strtolower($this->configs['module']);
				break;
			case "use_command":
				$value = $this->createUseCommand();
				break;
			default:
				$value = $this->configs[$keyEvent];
				break;

		}
		
		return $value;
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