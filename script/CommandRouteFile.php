<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;

class CommandRouteFile implements GenFile {

	use CreateFunction;

	protected $templatePath;
	protected $destinationPath;
	protected $mappings;
	protected $configs;
	
	public function __construct(string $projectPath, array $configs, array $directories, array $mappings)
	{
		$this->templatePath    = $projectPath.'/script/template/command_route.txt';
		$this->destinationPath = $projectPath.'/config/command_route.php';
		
		$this->mappings        = $mappings;
		$this->configs         = $configs;
		
	}

	//Method for add route
	protected function addRoute(string $repoName): string
	{
		$output = '';
		foreach ($this->configs['command'] as $commandName => $fields) {
			$output .= "\$eventRouter->route($commandName::class)->to(new ".$commandName."Handler(\$$repoName));\n";
		}

		return $output;
	}

	//Method for create retository
	protected function createRepository(string $repoName): string
	{
		return "\$$repoName = new ".$this->configs['module']."Repository(\$eventStore, \$pdoSnapshotStore);\n";
	}



	//method for get config value for controller  
	public function getConfigVal(string $keyEvent): string
	{
		$value = '';

		switch ($keyEvent) {
			case "add_route":
				$repoName = strtolower($this->configs['module']).'Repository';
				$value = $this->addRoute($repoName);
				break;
			case "create_repository":
				$repoName = strtolower($this->configs['module']).'Repository';
				$value = $this->createRepository($repoName);
				break;
			case "use_repo":
				$value = $this->createUseRepo();
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
			$value  = $this->getConfigVal($keyEvent);
			
			$value .= "//$keyTemplate\n";
			//replace something in the file string 
			$str = str_replace("$keyTemplate", $value, $str);

		}

		//write the entire string
		file_put_contents($this->destinationPath, $str);
		
	}

}