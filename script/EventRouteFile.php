<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;

class EventRouteFile implements GenFile {

	use CreateFunction;

	protected $templatePath;
	protected $destinationPath;
	protected $mappings;
	protected $configs;
	
	public function __construct(string $projectPath, array $configs, array $directories, array $mappings)
	{
		$this->templatePath    = $projectPath.'/script/template/event_route.txt';
		$this->destinationPath = $projectPath.'/config/event_route.php';
		
		$this->mappings        = $mappings;
		$this->configs         = $configs;
		
	}

	//Method for add route
	protected function addRoute(string $projectorName): string
	{
		$output = '';
		foreach ($this->configs['event'] as $eventName => $fields) {
			$output .= "\$eventRouter->route($eventName::class)->to([\$$projectorName, 'on".$eventName."']);\n";
		}

		return $output;
	}

	//Method for create projector
	protected function createProjector(string $projectorName): string
	{
		return "\$$projectorName = new ".$this->configs['module']."Projector();\n";
	}



	//method for get config value for controller  
	public function getConfigVal(string $keyEvent): string
	{
		$value = '';

		switch ($keyEvent) {
			case "add_route":
				$repoName = strtolower($this->configs['module']).'Projector';
				$value = $this->addRoute($repoName);
				break;
			case "create_projector":
				$repoName = strtolower($this->configs['module']).'Projector';
				$value = $this->createProjector($repoName);
				break;
			case "use_projector":
				$value = $this->createUseProjector();
				break;
			case "use_event":
				$value = $this->createUseEvent();
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