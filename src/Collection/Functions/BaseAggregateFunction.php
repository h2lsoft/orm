<?php declare(strict_types = 1);

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace Nextras\Orm\Collection\Functions;

use Nextras\Dbal\QueryBuilder\QueryBuilder;
use Nextras\Orm\Collection\Helpers\ArrayCollectionHelper;
use Nextras\Orm\Collection\Helpers\DbalExpressionResult;
use Nextras\Orm\Collection\Helpers\DbalQueryBuilderHelper;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\InvalidArgumentException;


abstract class BaseAggregateFunction implements IArrayFunction, IQueryBuilderFunction
{
	/** @var string */
	private $sqlFunction;


	protected function __construct(string $sqlFunction)
	{
		$this->sqlFunction = $sqlFunction;
	}


	/**
	 * @param array<number> $values
	 * @return number
	 */
	abstract protected function calculateAggregation(array $values);


	public function processArrayExpression(ArrayCollectionHelper $helper, IEntity $entity, array $args)
	{
		\assert(\count($args) === 1 && \is_string($args[0]));

		$valueReference = $helper->getValue($entity, $args[0]);
		if (!$valueReference->isMultiValue) {
			throw new InvalidArgumentException('Aggregation is not called over has many relationship.');
		}
		\assert(\is_array($valueReference->value));

		return $this->calculateAggregation($valueReference->value);
	}


	public function processQueryBuilderExpression(
		DbalQueryBuilderHelper $helper,
		QueryBuilder $builder,
		array $args
	): DbalExpressionResult
	{
		\assert(\count($args) === 1 && \is_string($args[0]));

		$expression = $helper->processPropertyExpr($builder, $args[0]);
		return new DbalExpressionResult(
			["{$this->sqlFunction}(%ex)", $expression->args],
			true
		);
	}
}
