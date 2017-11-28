<?php
declare(strict_types=1);

namespace Script;

interface GenFile {

	public function getConfigVal(string $keyEvent): string;
	public function createFile(): void;
}