<?php

return [
	'collection' => [
		'[[name]]'       => 'name',
		'[[module]]'     => 'module',
		'[[collection]]' => 'collection',
		'[[columns]]'    => 'columns',
	],
	'infrastructure' => [
		'[[name]]'       => 'name',
		'[[module]]'     => 'module',
	],
	'command' => [
		'[[name]]'     => 'name',
		'[[command]]'  => 'command',
		'[[function]]' => 'command',
	],
	'command_handle' => [
		'[[name]]'    => 'name',
		'[[module]]'  => 'module',
		'[[command]]' => 'command',
		'[[handle]]'  => 'handle',
	],
	'event' => [
		'[[name]]'     => 'name',
		'[[event]]'    => 'event',
		'[[function]]' => 'command',
	],
	'module' => [
		'[[name]]'          => 'name',
		'[[module]]'        => 'module',
		'[[use]]'           => 'use_event',
		'[[columns]]'       => 'columns',
		'[[function_data]]' => 'function_data',
		'[[case_event]]'    => 'case_event',
	],
	'module_repo' => [
		'[[name]]'          => 'name',
		'[[module]]'        => 'module',
	],
	'projection' => [
		'[[name]]'         => 'name',
		'[[module]]'       => 'module',
		'[[use]]'          => 'use_event',
		'[[lower_module]]' => 'lower_module',
		'[[allow_fields]]' => 'allow_fields',
		'[[on_function]]'  => 'on_function',
	],
	'controller' => [
		'[[name]]'         => 'name',
		'[[module]]'       => 'module',
		'[[use]]'          => 'use_command',
		'[[lower_module]]' => 'lower_module',
		'[[param]]'        => 'param',
	],
	'worker' => [
		'[[name]]'         => 'name',
		'[[module]]'       => 'module',
	],
	'command_route' => [
		'@useRepo'          => 'use_repo',
		'@useCommand'       => 'use_command',
		'@createRepository' => 'create_repository',
		'@addRoute'         => 'add_route',
	],
	'event_route' => [
		'@useProjector'    => 'use_projector',
		'@useEvent'        => 'use_event',
		'@createProjector' => 'create_projector',
		'@addRoute'        => 'add_route',
	],
];