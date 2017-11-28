<?php
declare(strict_types=1);

namespace Script;

trait CreateFunction {

    protected function createFieldMethod(array $datas): string
    {  
    	$outputs = "";
    	foreach ($datas as $field => $type) {
			if (!empty($outputs)) { $outputs .= "\t"; }

			$outputs .= "public function ".$this->myLib->underscoreToCamelCase(str_replace('*', '', $field))."(): $type\n";
			$outputs .= "\t{\n";
			$outputs .= "\t\treturn \$this->payload()['".str_replace('*', '', $field)."'];\n";
			$outputs .= "\t}\n\n";
		}

		return $outputs;
    }

    //Method for create use event
	protected function createUseEvent()
	{
		$output = '';

		foreach ($this->configs['event'] as $eventName => $fields) {
			$output .= 'use Event\\'.$this->configs['name'].'\\Model\\Event\\'.$eventName.";\n";
		}
		return $output;
	}

	//Method for create use command
	protected function createUseCommand()
	{
		$output = '';

		foreach ($this->configs['command'] as $commandName => $fields) {
			$output .= 'use Event\\'.$this->configs['name'].'\\Model\\Command\\'.$commandName.";\n";
			$output .= 'use Event\\'.$this->configs['name'].'\\Model\\Command\\'.$commandName."Handler;\n";
		}
		return $output;
	}

	//Method for create use repo
	protected function createUseRepo()
	{
		return 'use Event\\'.$this->configs['name'].'\\Infrastructure\\'.$this->configs['module']."Repository;\n";
	}

	//Method for create use projector
	protected function createUseProjector()
	{
		return 'use Event\\'.$this->configs['name'].'\\Projection\\'.$this->configs['module']."Projector;\n";
	}
}