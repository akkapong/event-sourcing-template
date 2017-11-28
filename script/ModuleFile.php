<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;
use Script\MyLib;

class ModuleFile implements GenFile {

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
		$this->templatePath    = $projectPath.'/script/template/Model/Module.txt';
		$this->destinationPath = $projectPath.'/src/'.$configs['name'].'/'.$directories['module'].'/'.$configs['module'].'.php';
		
		$this->mappings        = $mappings;
		$this->configs         = $configs;
		
		$this->myLib           = new MyLib();
	}

	//method for create all column arguments
	protected function createArgAllColumn(): string
	{
		$args = "";
		foreach ($this->configs['columns'] as $column => $type) {
			if (!empty($args)) { $args .= ", "; }

			$args .= '$'.str_replace('*', '', $column);
		}
		return "private $args;";
	}

	//method for create argument with type
	protected function createArgWithType(array $fields): string
	{
		$output = "";

		foreach ($fields as $field => $type) {
			if (!empty($output)) { $output .= ", "; }

			$output .= "$type \$".str_replace('*', '', $field);
		}

		return $output;
	}

	//method for get event name from command
	protected function getEventByCommand(string $command): string
	{
		return $this->configs['command_mappings'][$command];
	}

	//method for get cpmmand name from event
	protected function getCommandByEvent(string $event): string
	{
		foreach ($this->configs['command_mappings'] as $commandName => $eventName) {
			if ($eventName == $event) { return $commandName; }
		}
		return '';
	}

	//method for get primary key
	protected function getPrimaryKey(): string
	{
		$columns    = $this->configs['columns'];

		foreach ($columns as $field => $type) {
			if (substr($field, 0, 1) == '*') { return substr($field, 1); }
		}
		return '';
	}

	//method for create occer arg fromn event 
	protected function createOccerArg(string $event): string
	{
		$output = "";
		$fields = $this->configs['event'][$event];

		foreach ($fields as $field => $type) {
			$output .= "\t\t\t'$field' => \$$field,\n";
		}
		return $output;
	}

	//method for create finction data by command
	protected function createFunctionData(): string
	{
		$output = "";

		foreach ($this->configs['command'] as $command => $fields) {
			if (!empty($output)) { $output .= "\t"; }

			$eventName = $this->getEventByCommand($command);

			$output .= "static public function ".lcfirst($command)."WithData(".$this->createArgWithType($fields)."): self\n";
			$output .= "\t{\n";
			$output .= "\t\t\$obj = new self;\n";
			$output .= "\t\t\$obj->recordThat($eventName::occur($".$this->getPrimaryKey().", [\n";
			$output .= $this->createOccerArg($eventName);
			$output .= "\t\t]));\n";
			$output .= "\t\treturn \$obj;\n";
			$output .= "\t}\n";

		}
		return $output;
	}

	//Method for create case event methods by field
	protected function getCaseEventMethodByField(string $field): string
	{
		if (substr($field, 0, 1) == '*') { 
			return "aggregateId()"; 
		}
		return $this->myLib->underscoreToCamelCase($field)."()";
	}

	//method for create case event value by command
	protected function createCaseEventValue(string $command): string
	{
		$output = '';
		$fields = $this->configs['command'][$command];

		foreach ($fields as $field => $type) {
			$fieldComplete = str_replace('*', '', $field);
			$output .= "\t\t\t\t\$this->$fieldComplete = \$event->".$this->getCaseEventMethodByField($field).";\n";
		}
		return $output;
	}

	//method for create case event
	protected function createCaseEvent(): string
	{
		$output = "";

		foreach ($this->configs['event'] as $event => $fields) {
			if (!empty($output)) { $output .= "\t\t\t"; }

			$command = $this->getCommandByEvent($event);
			$output .= "case $event::class:\n";
			$output .= "\t\t\t\t/** @var UserRegistered \$event */\n";
			$output .= $this->createCaseEventValue($command);
			$output .= "\t\t\t\tbreak;\n";

		}
		return $output;

	}

	//method for get config value for collection 
	public function getConfigVal(string $keyEvent) :string
	{
		$value = '';
		
		switch ($keyEvent) {
			case "columns":
				$value = $this->createArgAllColumn();
				break;
			case "function_data":
				$value = $this->createFunctionData();
				break;
			case "case_event":
				$value = $this->createCaseEvent();
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
			$value = $this->getConfigVal($keyEvent);
			
			//replace something in the file string 
			$str = str_replace("$keyTemplate", $value, $str);

		}
		//write the entire string
		file_put_contents($this->destinationPath, $str);
		
	}

}