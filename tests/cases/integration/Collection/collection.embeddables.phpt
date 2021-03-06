<?php declare(strict_types = 1);

/**
 * @testCase
 * @dataProvider ../../../sections.ini
 */

namespace NextrasTests\Orm\Integration\Collection;

use Nextras\Orm\InvalidArgumentException;
use NextrasTests\Orm\Currency;
use NextrasTests\Orm\DataTestCase;
use NextrasTests\Orm\Money;
use Tester\Assert;


$dic = require_once __DIR__ . '/../../../bootstrap.php';


class CollectionEmbeddablesTest extends DataTestCase
{
	public function testBasics()
	{
		$books1 = $this->orm->books->findBy(['price->cents>=' => 1000]);
		Assert::same(0, $books1->count());
		Assert::same(0, $books1->countStored());

		$book = $this->orm->books->getById(1);
		$book->price = new Money(1000, Currency::CZK());
		$this->orm->persistAndFlush($book);

		$books2 = $this->orm->books->findBy(['price->cents>=' => 1000]);
		Assert::same(1, $books2->count());
		Assert::same(1, $books2->countStored());
	}


	public function testInvalidExpression()
	{
		Assert::exception(function() {
			$this->orm->authors->findBy(['books->price' => 20])->fetchAll();
		}, InvalidArgumentException::class, 'Property expression \'books->price\' does not fetch specific property.');
	}
}


$test = new CollectionEmbeddablesTest($dic);
$test->run();
