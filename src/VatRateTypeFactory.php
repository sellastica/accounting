<?php
namespace Sellastica\Accounting;

class VatRateTypeFactory
{
	/** @var array */
	private static $vatRateByYears = [
		'CZ' => [
			2018 => [
				21 => VatRateType::BASIC,
				15 => VatRateType::REDUCED1,
				10 => VatRateType::REDUCED2,
				0 => VatRateType::ZERO,
			],
			2019 => [
				21 => VatRateType::BASIC,
				15 => VatRateType::REDUCED1,
				10 => VatRateType::REDUCED2,
				0 => VatRateType::ZERO,
			],
		],
		'DE' => [
			2019 => [
				19 => VatRateType::BASIC,
				7 => VatRateType::REDUCED1,
				0 => VatRateType::ZERO,
			],
		],
		'GB' => [
			2019 => [
				20 => VatRateType::BASIC,
				5 => VatRateType::REDUCED1,
				0 => VatRateType::ZERO,
			],
		],
		'IT' => [
			2019 => [
				22 => VatRateType::BASIC,
				10 => VatRateType::REDUCED1,
				5 => VatRateType::REDUCED2,
				4 => VatRateType::REDUCED3,
				0 => VatRateType::ZERO,
			],
		],
		'PL' => [
			2019 => [
				23 => VatRateType::BASIC,
				8 => VatRateType::REDUCED1,
				5 => VatRateType::REDUCED2,
				0 => VatRateType::ZERO,
			],
		],
		'SK' => [
			2018 => [
				20 => VatRateType::BASIC,
				10 => VatRateType::REDUCED1,
				0 => VatRateType::ZERO,
			],
			2019 => [
				20 => VatRateType::BASIC,
				10 => VatRateType::REDUCED1,
				0 => VatRateType::ZERO,
			],
		],
	];

	/**
	 * @param float $vatRate
	 * @param \Sellastica\Localization\Model\Country $country
	 * @param \DateTime|null $dateTime
	 * @return VatRateType|null
	 */
	public static function create(
		float $vatRate,
		\Sellastica\Localization\Model\Country $country,
		\DateTime $dateTime = null
	): ?VatRateType
	{

		if (!isset($dateTime)) {
			$dateTime = new \DateTime();
		}

		$year = self::getYear($dateTime);

		return isset(self::$vatRateByYears[$country->getCode()][$year][$vatRate])
			? new VatRateType(self::$vatRateByYears[$country->getCode()][$year][$vatRate])
			: null;
	}

	/**
	 * @param \Sellastica\Localization\Model\Country $country
	 * @param \DateTime|null $dateTime
	 * @param bool $need
	 * @param string $vatRateType
	 * @return float|null
	 * @throws \InvalidArgumentException
	 */
	public static function getVatRate(
		\Sellastica\Localization\Model\Country $country,
		string $vatRateType = VatRateType::BASIC,
		\DateTime $dateTime = null,
		bool $need = true
	): ?float
	{
		$year = self::getYear($dateTime);
		if (isset(self::$vatRateByYears[$country->getCode()][$year])) {
			$vatRate = array_search($vatRateType, self::$vatRateByYears[$country->getCode()][$year]);
		}

		if (!isset($vatRate) && $need) {
			throw new \InvalidArgumentException(
				sprintf('Vat rate for year %s and country %s not found', $year, $country->getCode())
			);
		}

		return $vatRate;
	}

	/**
	 * @param \DateTime|null $dateTime
	 * @return int
	 */
	private static function getYear(?\DateTime $dateTime): int
	{
		if (!isset($dateTime)) {
			$dateTime = new \DateTime();
		}

		$year = $dateTime->format('Y');
		if ($year > 2019) {
			$year = 2019;
		}

		return $year;
	}
}
