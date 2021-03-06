<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Tests\Unit\Configuration;

use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Exception\InvalidConfigurationException;
use Neos\Flow\Tests\UnitTestCase;

class ConfigurationTest extends UnitTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp()
    {
        parent::setUp();
        // Create Configuration instance via reflection to avoid calling the proxy constructor generated by neos,
        // which would inject the settings from the yaml files.
        $reflection = new \ReflectionClass(Configuration::class);
        $this->configuration = $reflection->newInstanceWithoutConstructor();
    }

    /**
     * @throws \Exception
     */
    public function testValid(): void
    {
        $this->configuration->injectSettings($this->validData());

        $this->assertEquals(
            'https://www.example.com',
            $this->configuration->getApiUrl()
        );
        $this->assertEquals(
            '7f4d86bb-4d44-4285-864f-65fca6e6b2cd',
            $this->configuration->getApiKey()
        );
        $this->assertEquals(
            'https://www.example.com?token=CT74.TSzcQWiVXZen2TP0eDf9_ByWT1vDT~sTD6wh5fSG-mrjQaljCH5yMxWFzo',
            $this->configuration->getCallbackUrl()
        );
        $this->assertEquals(
            'CT74.TSzcQWiVXZen2TP0eDf9_ByWT1vDT~sTD6wh5fSG-mrjQaljCH5yMxWFzo',
            $this->configuration->getCallbackToken()
        );
        $this->assertEquals(
            ['imprint'],
            $this->configuration->getServices()
        );
        $this->assertEquals(
            'de',
            $this->configuration->getFallbackLanguage()
        );
    }

    /**
     * @dataProvider invalidDataProvider
     * @param array<string, string> $settings
     * @throws \Exception
     */
    public function testInvalid(array $settings): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configuration->injectSettings($settings);
    }

    /**
     * @return array<string, mixed[]>
     * @throws \Exception
     */
    public function invalidDataProvider(): array
    {
        return [
            'missing apiUrl' => [[$this->unsetKey($this->validData(), 'apiUrl')]],
            'missing apiKey' => [[$this->unsetKey($this->validData(), 'apiKey')]],
            'missing callbackUrl' => [[$this->unsetKey($this->validData(), 'callbackUrl')]],
            'missing callbackToken' => [[$this->unsetKey($this->validData(), 'callbackToken')]],
            'missing fallbackLanguage' => [[$this->unsetKey($this->validData(), 'fallbackLanguage')]],
            'empty apiUrl' => [[array_merge($this->validData(), ['apiUrl' => ''])]],
            'empty apiKey' => [[array_merge($this->validData(), ['apiKey' => ''])]],
            'empty callbackUrl' => [[array_merge($this->validData(), ['callbackUrl' => ''])]],
            'empty callbackToken' => [[array_merge($this->validData(), ['callbackToken' => ''])]],
            'empty fallbackLanguage' => [[array_merge($this->validData(), ['fallbackLanguage' => ''])]],
            'non-string apiUrl' => [[array_merge($this->validData(), ['apiUrl' => 1])]],
            'non-string apiKey' => [[array_merge($this->validData(), ['apiKey' => false])]],
            'non-string callbackUrl' => [[array_merge($this->validData(), ['callbackUrl' => new \DateTimeImmutable()])]],
            'non-string callbackToken' => [[array_merge($this->validData(), ['callbackToken' => null])]],
            'non-string fallbackLanguage' => [[array_merge($this->validData(), ['fallbackLanguage' => 42])]],
            'missing token placeholder in callbackUrl' => [[array_merge($this->validData(), ['callbackUrl' => 'https://www.example.com/'])]],
            'invalid character in callbackToken' => [[array_merge($this->validData(), ['callbackToken' => 'in!valid'])]],
            'missing services' => [[$this->unsetKey($this->validData(), 'services')]],
            'empty services' => [[array_merge($this->validData(), ['services' => []])]],
            'services not array' => [[array_merge($this->validData(), ['services' => ''])]],
            'services contains non-string' => [[array_merge($this->validData(), ['services' => [1]])]],
        ];
    }

    /**
     * @return array
     */
    private function validData(): array
    {
        return [
            'apiUrl' => 'https://www.example.com',
            'apiKey' => '7f4d86bb-4d44-4285-864f-65fca6e6b2cd',
            'callbackUrl' => 'https://www.example.com?token={token}',
            'callbackToken' => 'CT74.TSzcQWiVXZen2TP0eDf9_ByWT1vDT~sTD6wh5fSG-mrjQaljCH5yMxWFzo',
            'services' => ['imprint'],
            'fallbackLanguage' => 'de'
        ];
    }

    /**
     * @param array $array
     * @param string $key
     * @return array
     */
    private function unsetKey(array $array, string $key): array
    {
        unset($array[$key]);
        return $array;
    }
}
