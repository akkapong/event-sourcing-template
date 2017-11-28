<?php
use Event\Notes\Infrastructure\NoteRepository;
//@useRepo


use Event\Notes\Model\Command\CreateNote;
use Event\Notes\Model\Command\CreateNoteHandler;
//@useCommand


$noteRepository = new NoteRepository($eventStore, $pdoSnapshotStore);
//@createRepository


$eventRouter->route(CreateNote::class)->to(new CreateNoteHandler($noteRepository));
//@addRoute
