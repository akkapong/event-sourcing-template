<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;

class ProjectionFile implements GenFile {

	use CreateFunction;

	protected $templatePath;
	protected $destinationPath;
	protected $mappings;
	protected $configs;
	protected $myLib;
	
	public function __construct(string $projectPath, array $configs, array $directories, array $mappings)
	{
		$this->templatePath    = $projectPath.'/script/template/Projection/ModuleProjector.txt';
		$this->destinationPath = $projectPath.'/src/'.$configs['name'].'/'.$directories['projection'].'/'.$configs['module'].'Projector.php';
		
		$this->mappings        = $mappings;
		$this->configs         = $configs;

		$this->myLib        = new MyLib();
	}

	//Method for create event argument on on function
	protected function createOnFunctionArg(array $fields, string $evetVal) : string
	{
		$output = '';

		foreach ($fields as $field => $type) {
			$output .= "\t\t\t'$field' => \$$evetVal->".$this->myLib->underscoreToCamelCase($field)."(),\n";
		}
		return $output;
	}

	//Method for creste on function
	protected function createOnFunction(): string
	{
		$output = '';

		foreach ($this->configs['event'] as $eventName => $fields) {
			if (!empty($output)) { $output .= "\t"; }

			$evetVal = lcfirst($eventName );
			$output .= "//TODO: process data here!!\n";
			$output .= "\tpublic function on$eventName($eventName \$$evetVal): void\n";
			$output .= "\t{\n";
			$output .= "\t\t\$collectionName = \"\\\\Event\\\\".$this->configs['name']."\\\\Collections\\\\\".\$this->collectionName;\n";
			$output .= "\t\t\$this->model = new \$collectionName();\n\n";
			$output .= "\t\t\$params = [\n";
			$output .= "\t\t\t'uuid' => \$".lcfirst($eventName )."->aggregateId(),\n";
			$output .= $this->createOnFunctionArg($fields, $evetVal);
			$output .= "\t\t];\n\n";
			$output .= "\t\t//TODO: process ex (\$res = \$this->insertData(\$params);)\n";
			$output .= "\t}\n";


		}
		return $output;
	}

	//Method for create allow field
	protected function createAllowFields(array $columns): string
	{
		$output = '';
		
		foreach ($columns as $column => $type) {

			if (!empty($output)) { $output .= ", "; }

			if ($column == '*id') { $column = 'uuid'; }
			
			$output .=  "'$column'";
		}
		return "[$output];\n";
	}

	//method for get config value for collection 
	public function getConfigVal(string $keyEvent) :string
	{
		$value = '';

		switch ($keyEvent) {
			case "use_event":
				$value = $this->createUseEvent();
				break;
			case "lower_module":
				$value = strtolower($this->configs['module']);
				break;
			case "allow_fields":
				$value = $this->createAllowFields($this->configs['columns']);
				break;
			case "on_function":
				$value = $this->createOnFunction();
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
		//write the entire string
		file_put_contents($this->destinationPath, $str);
		
	}

}