<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Lang;

use Psr\Log\LoggerInterface;

class Gettext
{
    /**
     * array of allowed languages
     * this array contains a short 2-character code for the name as index,
     * a name in the own language and a list of possible locales to be tried.
     *
     * @var array
     */
    protected $allowed;

    /**
     * name of active textdomain.
     *
     * @var string
     */
    protected $textdomain = 'default';

    /**
     * path to .po language files.
     *
     * @var string
     */
    protected $path;

    /**
     * the default category is LC_MESSAGES.
     *
     * @var int
     */
    protected $category;

    /**
     * the character set for textdomains.
     *
     * @var string
     */
    protected $charset;

    /**
     * the currently active locale.
     *
     * @var string
     */
    protected $activeLocale;

    /**
     * the currently active language shortcode.
     *
     * @var string
     */
    protected $activeLang;

    public function __construct(
        $allowed = null,
        $path = null,
        $category = LC_MESSAGES,
        $charset = 'UTF-8',
        LoggerInterface $logger
    ) {
        // defaults
        $this->allowed = [
            'en' => [
                // the name in it's language
                'name' => 'English',
                // a list of valid locales
                'locales' => ['en_EN', 'en_GB', 'en_US'],
            ],
            'de' => [
                'name' => 'Deutsch',
                'locales' => ['de_DE', 'German_Germany.1252', 'de_CH', 'de_AT'],
            ],
        ];

        // set custom languages
        if (null !== $allowed) {
            $this->allowed = $allowed;
        }

        // set path for textdomains
        if (null !== $path) {
            $this->path = $path;
        }

        /* windows workaround */
        if (!defined('LC_MESSAGES')) {
            define('LC_MESSAGES', 5);
        }

        $this->category = $category;
        $this->charset = $charset;
        $this->logger = $logger;
    }

    public function setLang($lang = 'de')
    {
        if (!array_key_exists($lang, $this->allowed)) {
            throw new \OutOfRangeException('The selected language is not available.');
        }

        $this->activeLang = $lang;

        // set an environment var to the short lang code
        putenv('LOCALE_LANG='.$lang);

        // setup gettext config with list of allowed locales
        $this->setupGettext($this->allowed[$lang]['locales']);

        return $this;
    }

    /**
     * Sets the active textdomain.
     *
     * @param string $textdomain the textdomain (name of translation file sans .po)
     * @param string $path       optional path for the language files (sans locale and category)
     * @param string $charset    optional character set
     */
    public function setTextdomain($textdomain, $path = null, $charset = 'UTF-8')
    {
        $this->logger->debug('Gettext: Set text domain', [$textdomain, $path, $charset]);

        if (null !== $path) {
            $this->setPath($path);
        }

        if (null !== $charset) {
            $this->setCharset($charset);
        }

        $this->textdomain = $textdomain;

        // set the textdomain including the path
        $result = bindtextdomain($this->getTextdomain(), $this->getPath());
        $this->logger->debug('Gettext: Binding textdomain', [$result, $this->getTextdomain(), $this->getPath()]);
        // set the charset
        $result = bind_textdomain_codeset($this->getTextdomain(), $this->getCharset());
        $this->logger->debug('Gettext: Binding charset', [$result, $this->getCharset()]);

        // read textdomain from e.g: $path/de_DE/LC_MESSAGES/$textdomain.mo
        $result = textdomain($this->getTextdomain());
        $this->logger->debug('Gettext: Set textdomain', [$result]);

        return $this;
    }

    /**
     * Gets the name of active textdomain.
     *
     * @return string
     */
    public function getTextdomain()
    {
        return $this->textdomain;
    }

    /**
     * sets up the gettext locales.
     *
     * @param array $locales locales to try for the language
     *
     * @return self
     */
    protected function setupGettext($locales)
    {
        foreach ($locales as $locale) {
            $result = putenv('LANGUAGE='.$locale);
            if (false !== setlocale(LC_MESSAGES, $locale)) {
                // set the active locale
                $this->activeLocale = $locale;
                $this->logger->debug('Locale is: '.$locale);
                break;
            }
        }

        return $this;
    }

    /**
     * destroy the textdomain cache.
     * this can be used for debugging and needs to be called before self::loadTextDomain().
     *
     * @return self
     */
    public function destroyCache()
    {
        // fuck the cache!
        textdomain(textdomain(null));

        return $this;
    }

    /**
     * Gets the path to .po language files.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path to .po language files.
     *
     * @param string $path the path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Gets the the category.
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the category.
     *
     * @param int $category the category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Gets the the character set for textdomains.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Sets the the character set for textdomains.
     *
     * @param string $charset the charset
     *
     * @return self
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Gets the the currently active locale.
     *
     * @return string
     */
    public function getActiveLocale()
    {
        return $this->activeLocale;
    }

    /**
     * Gets the the currently active language shortcode.
     *
     * @return string
     */
    public function getActiveLang()
    {
        return $this->activeLang;
    }
}
