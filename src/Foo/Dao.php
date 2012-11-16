<?php

namespace Foo;

interface Dao {
	public function get();

	public function runSql($sql);

	public function insert($table, $data);

}