<?php declare(strict_types = 1);

namespace NextrasTests\Orm;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;


/**
 * @method Book|NULL getById($id)
 * @method ICollection|Book[] findBooksWithEvenId()
 * @method Book findFirstBook()
 */
final class BooksRepository extends Repository
{
	static function getEntityClassNames(): array
	{
		return [Book::class];
	}


	public function findLatest()
	{
		return $this->findAll()
			->orderBy('id', ICollection::DESC)
			->limitBy(3);
	}


	public function findByTags($name)
	{
		return $this->findBy(['tags->name' => $name]);
	}


	public function createCollectionFunction(string $name)
	{
		if ($name === LikeFunction::class) {
			return new LikeFunction();
		} else {
			return parent::createCollectionFunction($name);
		}
	}
}
