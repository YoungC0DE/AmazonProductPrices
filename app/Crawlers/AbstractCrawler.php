<?php

namespace App\Crawlers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

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
    public function getDriver(array $crawlerOptions = [])
    {
        // 'selenium' is the name of the container in Docker
        $host = env('SELENIUM_HOST', 'http://selenium_container:4444/wd/hub');

        $options = new ChromeOptions();

        if (empty($crawlerOptions)) {
            $crawlerOptions = [
                "--no-sandbox",
                "--disable-dev-shm-usage",
                "--disable-software-rasterizer",
            ];

            if (env('APP_ENV') === 'production') {
                $crawlerOptions[] = "--headless";
            }
        }

        $options->addArguments($crawlerOptions);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability('acceptSslCerts', true);
        $capabilities->setCapability('acceptInsecureCerts', true);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $this->driver = RemoteWebDriver::create($host, $capabilities);

        return $this->driver;
    }

    public abstract function process();
}
