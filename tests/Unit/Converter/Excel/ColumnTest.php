<?php

declare(strict_types=1);

namespace Tests\Unit\Converter\Excel;

use App\Converter\Excel\Cell;
use App\Converter\Excel\Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
	protected function cellFactory(): array
	{
		return [
			1 => new Cell('A', 1, 'cell_a'),
			new Cell('A', 2, 'cell_b'),
			new Cell('A', 3, 'cell_c'),
			new Cell('A', 4, 'cell_d'),
			new Cell('A', 5, 'cell_e'),
		];
	}

	public function test_getCellByRow_returns_cell()
	{
		$column = new Column(...$this->cellFactory());

		// we want to ensure that attributes are equal only!!
		$this->assertEquals($this->cellFactory()[3], $column->getCellByRow(3));
	}

	public function test_getCellByRow_returns_null()
	{
		$column = new Column(...$this->cellFactory());

		$this->assertNull($column->getCellByRow(-1));
	}
}
