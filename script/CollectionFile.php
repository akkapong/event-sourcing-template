<?php
declare(strict_types=1);

namespace Script;

use Script\GenFile;

class CollectionFile implements GenFile {

	protected $templatePath;
	protected $destinationPath;
	protected $mappings;
	protected $configs;
	
	public function __construct(string $projectPath, array $configs, array $directories, array $mappings)
	{
		$this->templatePath    = $projectPath.'/script/template/Collections/ModuleCollection.txt';
		$this->destinationPath = $projectPath.'/src/'.$configs['name'].'/'.$directories['collection'].'/'.$configs['module'].'Collection.php';
		
		$this->mappings        = $mappings;
		$this->configs         = $configs;
	}

	//method for complete field name
	protected function completeFieldName(string $field): string
	{
		$field = str_replace('*', '', $field);

		if ($field == 'id') { $field = 'uuid'; }
		return $field;
	}

	//method for get config value for collection 
	public function getConfigVal(string $keyEvent) :string
	{
		$value = '';
		if (is_array($this->configs[$keyEvent])) {
			foreach ($this->configs[$keyEvent] as $field => $type) {
				if (!empty($value)) { $value .= "\t"; }
				$value .= "public $".$this->completeFieldName($field).";\n";
			}

		} else {
			$value = $this->configs[$keyEvent];
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