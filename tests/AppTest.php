<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    private function getAppMethodReflection(string $methodName): ReflectionMethod
    {
        $appReflection = new \ReflectionClass('Test\App\App');
        $refDecodeConfigLine = $appReflection->getMethod($methodName);
        $refDecodeConfigLine->setAccessible(true);
        return $refDecodeConfigLine;
    }

    private function setAppProtectedPropertyValue(
        \Test\App\App $appInstance,
        string        $propertyName,
        array         $propertyValue
    ): void
    {
        $appReflection = new \ReflectionClass('Test\App\App');
        $myProtectedProperty = $appReflection->getProperty($propertyName);
        $myProtectedProperty->setAccessible(true);
        $myProtectedProperty->setValue($appInstance, $propertyValue);
    }

    /**
     * @throws ReflectionException
     */
    public function testDecodeConfigLine(): void
    {
        $appInstance = new Test\App\App();
        $refDecodeConfigLine = $this->getAppMethodReflection('decodeConfigLine');

        $testString = '{"bin":"45717360","amount":"100.00","currency":"EUR"}';
        $result = $refDecodeConfigLine->invokeArgs($appInstance, [$testString]);
        $this->assertArrayIsIdenticalToArrayIgnoringListOfKeys([
            'bin' => '45717360',
            'amount' => '100.00',
            'currency' => 'EUR',
        ], $result, []);

        $this->expectException(\Exception::class);
        $testString = '{"amount":"100.00","currency":"EUR"}';
        $result = $refDecodeConfigLine->invokeArgs($appInstance, [$testString]);
        $this->assertArrayIsIdenticalToArrayIgnoringListOfKeys([
            'amount' => '100.00',
            'currency' => 'EUR',
        ], $result, []);
    }


    /**
     * @throws ReflectionException
     */
    public function testGetAmountByConfig(): void
    {
        $appInstance = new Test\App\App();
        $reflectedMethod = $this->getAppMethodReflection('getAmountByConfig');

        $testConfig = [
            'bin' => '45717360',
            'amount' => '100.00',
            'currency' => 'EUR',
        ];
        $this->setAppProtectedPropertyValue($appInstance, 'currencyRates', ['BWP' => '13.318607']);
        $this->setAppProtectedPropertyValue($appInstance, 'countryCodesByBin', ['45717360' => 'AU']);
        $result = $reflectedMethod->invokeArgs($appInstance, [$testConfig]);
        $this->assertEquals($result, 2.0);

        $testConfig2 = [
            'bin' => '111111',
            'amount' => '31241.00',
            'currency' => 'BWP',
        ];
        $this->setAppProtectedPropertyValue($appInstance, 'currencyRates', ['BWP' => '13.318607']);
        $this->setAppProtectedPropertyValue($appInstance, 'countryCodesByBin', ['111111' => 'IT']);
        $result = $reflectedMethod->invokeArgs($appInstance, [$testConfig2]);
        $this->assertEquals($result, 23.46);
    }
}