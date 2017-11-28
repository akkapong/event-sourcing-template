<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;
use Script\MyLib;

class CommandHandleFile implements GenFile {

	protected $templatePath;
	protected $destinationPath;
	protected $projectPath;
	protected $mappings;
	protected $directories;
	protected $configs;
	protected $myLib;
	
	public function __construct(string $projectPath, array $configs, array $directories, array $mappings)
	{
		$this->templatePath = $projectPath.'/script/template/Model/Command/CommandHandle.txt';
		
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
		$keys  = explode(".", $keyEvent);
		$datas = $this->myLib->getValueFormObj($keys, $this->configs);
		if (is_array($datas)) {

			$value .= "\$obj = ".$this->configs['module']."::".lcfirst($keys[1])."WithData(";
			$args   = "";
			foreach ($datas as $field => $type) {
				if (!empty($args)) { $args .= ", "; }

				$args .= "\$commandClass->".$this->myLib->underscoreToCamelCase(str_replace('*', '', $field))."()";
			}

			$value .= $args .");\n";
			$value .= "\t\t\$this->repository->save(\$obj);\n";

		} else {
			$value = $this->configs[$keyEvent];
		}

		return $value;
	}

	//Method for mange content in file 
	protected function manageContent(string $source, string $destination, array $mappings, string $commandName)
	{
		//get data from file
		$str = file_get_contents($source);

		foreach ($mappings as $keyTemplate => $keyEvent) {

			switch ($keyTemplate) {
				case '[[command]]':
					$value = $commandName;
					break;
				case '[[handle]]':
					$value = $this->getConfigVal("command.$commandName");
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
		$commands = $this->configs['command'];

		foreach ($commands as $commandName => $fields) {
			$destination = $this->projectPath.'/src/'.$this->configs['name'].'/'.$this->directories['command'].'/'.$commandName.'Handler.php';

			$this->manageContent($this->templatePath, $destination, $this->mappings, $commandName);
		}
		
	}

}