<?php

namespace App\Utility\Chinghwa\Helper;

class Temper
{
	protected $repo = [];

	public function __set($key, $val)
	{
		$this->repo[strtolower($key)] = $val;

		return $this;
	}

	public function __get($key)
	{
		return (array_key_exists(strtolower($key), $this->repo)) ? $this->repo[strtolower($key)] : NULL;
	}

	public function __call($function, $args)
	{
		$methodType = substr($function, 0, 3);

		if ('set' === $methodType) {
			$this->repo[substr(strtolower($function), 3)] = $args[0];

			return $this;
		}

		if ('get' === $methodType) {
			return $this->repo[substr(strtolower($function), 3)];
		}

		return NULL;
	}
}