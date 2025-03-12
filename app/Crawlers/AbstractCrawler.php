<?php

namespace App\Crawlers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

abstract class AbstractCrawler
{
    protected RemoteWebDriver $driver;

    public function __construct()
    {
        $this->getDriver();
    }

    /**
     * Returns a configured instance of Selenium WebDriver.
     * @param array $crawlerOptions
     *
     * @return RemoteWebDriver
     */
    public function getDriver(array $crawlerOptions = []): RemoteWebDriver
    {
        // 'selenium' is the name of the container in Docker
        $host = env('SELENIUM_HOST', 'http://selenium_container:4444/wd/hub');

        $options = new ChromeOptions();

        if (empty($crawlerOptions)) {
            $crawlerOptions = [
                "--no-sandbox",
                "--disable-dev-shm-usage",
                "--disable-software-rasterizer",
                "--disable-blink-features=AutomationControlled",
                "--user-agent=ozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36",
                "--disable-infobars"
            ];

            if (env('APP_ENV') === 'production') {
                $crawlerOptions[] = "--headless";
            }
        }

        $options->addArguments($crawlerOptions);
        $options->setExperimentalOption('excludeSwitches', ['enable-automation']);
        $options->setExperimentalOption('useAutomationExtension', false);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability('acceptSslCerts', true);
        $capabilities->setCapability('acceptInsecureCerts', true);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $this->driver = RemoteWebDriver::create(
            $host, 
            $capabilities
        );

        return $this->driver;
    }

    /**
     * @param mixed $text
     * 
     * @return string
     */
    public function normalizeText(string $text): string
    {
        $normalizedText = preg_replace('/\x{A0}/u', ' ', $text);
        $normalizedText = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $normalizedText);
        $normalizedText = preg_replace('/[^\x20-\x7E]/u', '', $normalizedText);
        $normalizedText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $normalizedText = trim(preg_replace('/\s+/', ' ', $normalizedText));

        return $normalizedText;
    }

    /**
     * @param mixed $text
     * 
     * @return string
     */
    public function normalizePrice(string $text): string
    {
        $normalizedText = trim(preg_replace('/\s+/', ',', $text));
        $normalizedText = preg_replace('/R\$/', 'R$ ', $normalizedText);

        return $normalizedText;
    }

    /**
     * @param \Facebook\WebDriver\Remote\RemoteWebElement $item
     * @param string $cssSelector
     * @param bool $normalize
     * @param string $typeNormalize
     * 
     * @return string
     */
    public function getElementText($item, string $cssSelector, bool $normalize = true, string $typeNormalize = 'text'): string
    {
        try {
            $text = $item->findElement(
                WebDriverBy::cssSelector($cssSelector)
            )->getText() ?? '';

            if ($normalize) {
                if ($typeNormalize == 'text') {
                    return $this->normalizeText($text);
                }

                return $this->normalizePrice($text);
            }

            return $text;
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param \Facebook\WebDriver\Remote\RemoteWebElement $item
     * @param string $cssSelector
     * @param string $attribute
     * @param bool $normalize
     * @param string $typeNormalize
     * 
     * @return string
     */
    public function getElementAttributeValue($item, string $cssSelector, string $attribute, bool $normalize = true, string $typeNormalize = 'text'): string
    {
        try {
            $text = $item->findElement(
                WebDriverBy::cssSelector($cssSelector)
            )->getAttribute($attribute) ?? '';

            if ($normalize) {
                if ($typeNormalize == 'text') {
                    return $this->normalizeText($text);
                }

                return $this->normalizePrice($text);
            }

            return $text;
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param array $ticketData
     * 
     * @return array
     */
    abstract public function process(array $ticketData): array;
}
