<?php

namespace App\Services;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class CrawlerService
{
    /**
     * Returns a configured instance of Selenium WebDriver.
     * @param array $crawlerOptions
     *
     * @return RemoteWebDriver
     */
    public static function getDriver(array $crawlerOptions = [])
    {
        // 'selenium' is the name of the container in Docker
        $host = env('SELENIUM_HOST', 'http://selenium_container:4444/wd/hub');

        $options = new ChromeOptions();

        if (empty($crawlerOptions)) {
            $crawlerOptions = [
                "--no-sandbox",
                "--disable-dev-shm-usage",
                "--remote-debugging-port=9222",
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

        return RemoteWebDriver::create($host, $capabilities);
    }
}
