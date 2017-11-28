<?php
declare(strict_types=1);
namespace Script;

class MyLib {


	//Method for get value from object
    public function getValueFormObj(array $keys, array $obj)
    {
        $key  = $keys[0];

        //keep in $obj
        if (isset($obj[$key])) {
            $obj  = $obj[$key];

            if (count($keys) > 1) {
                //cut first
                $keys = array_slice($keys, 1);
                return $this->getValueFormObj($keys, $obj);
            }
        } else {
            return "";
        }

        return $obj;

    }

	//Method for convert underscore to camel case
    public function underscoreToCamelCase(string $str): string
	{
		$output = '';
		$datas  = explode("_", $str);

		foreach ($datas as $data) {
			if (!empty($output)) {
				$data = ucfirst($data);
			}
			$output .= $data;
		}

	    return $output;
	}
}