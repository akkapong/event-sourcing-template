<?php
use Event\Notes\Projection\NoteProjector;
//@useProjector


use Event\Notes\Model\Event\NoteCreated;
//@useEvent


$noteProjector = new NoteProjector();
//@createProjector


$eventRouter->route(NoteCreated::class)->to([$noteProjector, 'onNoteCreated']);
//@addRoute
