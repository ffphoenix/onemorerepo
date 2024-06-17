<?php declare(strict_types=1);

namespace Test\App;

use Exception;

class App
{
    const array COUNTRIES_IN_EU = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES',
        'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU',
        'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];
    const string BIN_LIST_URL = 'https://lookup.binlist.net/';

    /**
     * found original url in change logs
     * const string EXCHANGE_RATES_API_URL = 'https://api.exchangeratesapi.io/latest';
     */
    const string EXCHANGE_RATES_API_URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    private array $currencyRates = [];
    private array $countryCodesByBin = [];

    /**
     * @return void
     * @throws Exception
     */
    public function exec(): void
    {
        /** Let's make assumption that this file is huge */
        $handle = fopen($this->getConfigFilePath(), "r");
        if ($handle) {
            while (($configLine = fgets($handle)) !== false) {
                if (!empty($configLine)) {
                    try {
                        $config = $this->decodeConfigLine($configLine);
                        echo $this->getAmountByConfig($config) . "\n";
                    } catch (\Throwable $exception) {
                        echo "Error: " . $exception->getMessage() . "\n";
                    }
                }
            }

            fclose($handle);
        }
    }

    /**
     * @throws Exception
     */
    private function getConfigFilePath(): string
    {
        if (empty($_SERVER['argv'][1])) {
            throw new Exception('Config file path is empty');
        }

        if (!file_exists($_SERVER['argv'][1])) {
            throw new Exception('Can\'t load config file: ' . $_SERVER['argv'][1]);
        }

        return $_SERVER['argv'][1];
    }

    /**
     * @param array{bin: string, amount: string, currency: string} $config
     * @throws Exception
     */
    private function getAmountByConfig(array $config): float
    {
        $amount = (float) $config['amount'];
        if ($config['currency'] !== 'EUR') {
            $currencyRate = $this->getCurrencyRate($config['currency']);
            if ($currencyRate > 0) {
                $amount = $amount / $this->getCurrencyRate($config['currency']);
            }
        }

        $countryCode = $this->getCountryCodeByBin($config['bin']);
        $amount *= $this->isCountryInEU($countryCode) ? 0.01 : 0.02;
        return round($amount, 2);
    }

    /**
     * @param string $configLine
     * @return array{bin: string, amount: string, currency: string}
     * @throws Exception
     */
    private function decodeConfigLine(string $configLine): array
    {
        if (!json_validate($configLine)) {
            throw new Exception('Invalid config line: ' . $configLine);
        }

        $config = json_decode($configLine, true);
        if (!array_key_exists('bin', $config)
            || !array_key_exists('amount', $config)
            || !array_key_exists('currency', $config)
        ) {
            throw new Exception('Invalid config line: ' . $configLine);
        }

        return $config;
    }

    /**
     * @throws Exception
     */
    private function getCurrencyRate(string $currency): float
    {
        $currencyRates = $this->getCurrencyRages();
        if (!array_key_exists($currency, $currencyRates)) {
            throw new Exception('Invalid currency: ' . $currency);
        }
        return (float)$currencyRates[$currency];
    }

    /**
     * @throws Exception
     */
    private function getCurrencyRages(): array
    {
        if (empty($this->currencyRates)) {
            $contentString = file_get_contents(self::EXCHANGE_RATES_API_URL);
            if (empty($contentString)) {
                throw new Exception('Response is empty from link: ' . self::EXCHANGE_RATES_API_URL);
            }
            if (!json_validate($contentString, JSON_UNESCAPED_UNICODE)) {
                throw new Exception('Can\'t parse response from link: ' . self::EXCHANGE_RATES_API_URL . ' ' . json_last_error_msg());
            }
            $response = json_decode($contentString, true);
            if (empty($response['rates'])) {
                throw new Exception('Invalid response from link ("rates" is empty): ' . self::EXCHANGE_RATES_API_URL);
            }
            $this->currencyRates = $response['rates'];
        }
        return $this->currencyRates;
    }

    /**
     * @throws Exception
     */
    private function getCountryCodeByBin(string $bin): string
    {
        if (empty($this->countryCodesByBin[$bin])) {
            $resourceUrl = self::BIN_LIST_URL . $bin;
            $contentString = file_get_contents($resourceUrl);
            if (empty($contentString)) {
                throw new Exception('Response is empty from link: ' . $resourceUrl);
            }
            if (!json_validate($contentString)) {
                throw new Exception('Wrong response from link: ' . $resourceUrl . ' ' . json_last_error_msg());
            }
            $response = json_decode($contentString);
            if (empty($response->country->alpha2)) {
                throw new Exception('Invalid country code by `bin`: ' . $resourceUrl);
            }
            $this->countryCodesByBin[$bin] = $response->country->alpha2;
        }
        return $this->countryCodesByBin[$bin];
    }

    private function isCountryInEU(string $countryCode): bool
    {
        return in_array($countryCode, self::COUNTRIES_IN_EU);
    }
}

