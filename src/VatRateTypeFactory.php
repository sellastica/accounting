<?php
namespace Sellastica\Accounting;

class VatRateTypeFactory
{
	/** @var array */
	private static $vatRateByYears = [
		'CZ' => [
			2018 => [
				0 => VatRateType::ZERO,
				21 => VatRateType::BASIC,
				15 => VatRateType::REDUCED1,
				10 => VatRateType::REDUCED2,
			],
			2019 => [
				0 => VatRateType::ZERO,
				21 => VatRateType::BASIC,
				15 => VatRateType::REDUCED1,
				10 => VatRateType::REDUCED2,
			],
		],
		'SK' => [
			2018 => [
				0 => VatRateType::ZERO,
				20 => VatRateType::BASIC,
				10 => VatRateType::REDUCED1,
			],
			2019 => [
				0 => VatRateType::ZERO,
				20 => VatRateType::BASIC,
				10 => VatRateType::REDUCED1,
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
		\DateTime $dateTime = null,
		bool $need = true,
		string $vatRateType = VatRateType::BASIC
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
