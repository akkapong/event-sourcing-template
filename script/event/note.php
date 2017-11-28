<?php

return [
	'name'       => 'Notes',
	'module'     => 'Note',
	'collection' => 'notes',
	'columns'    => [
		'*id'      => 'string',
		'user_id' => 'string',
		'note'    => 'string', 
	],
	'command'    => [
		'CreateNote' => [
			'*id'      => 'string',
			'user_id' => 'string',
			'note'    => 'string',
		],
	],
	'event'      => [
		'NoteCreated' => [
			'user_id' => 'string',
			'note'    => 'string',
		]
	],
	'command_mappings' => [
		'CreateNote' => 'NoteCreated',
	],
	'commnad_request' => [
		'CreateNote' => [
			'method' => 'POST',
			'uri'    => 'api/note',
		]
	]
];