<?php
namespace Sellastica\Accounting;

class VatRateType
{
	const ZERO = 'zero',
		BASIC = 'basic',
		REDUCED1 = 'reduced1',
		REDUCED2 = 'reduced2',
		REDUCED3 = 'reduced3';

	/** @var string */
	private $type;


	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$rc = new \ReflectionClass(self::class);
		if (!in_array($type, $rc->getConstants())) {
			throw new \InvalidArgumentException('Unknown vat rate type ' . $type);
		}

		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}
}
