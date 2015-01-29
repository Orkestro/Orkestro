<?php

namespace Orkestro\Bundle\LocaleBundle\Model;

class Locale
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $title;

    /**
     * @var boolean
     */
    private $isEnabled = true;

    /**
     * @var boolean
     */
    private $isFallback = false;


    /**
     * Set code
     *
     * @param string $code
     * @return Locale
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Locale
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set enabled
     *
     * @param boolean $isEnabled
     * @return Locale
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set fallback
     *
     * @param boolean $isFallback
     * @return Locale
     */
    public function setIsFallback($isFallback)
    {
        $this->isFallback = $isFallback;

        return $this;
    }

    /**
     * Get fallback
     *
     * @return boolean 
     */
    public function getIsFallback()
    {
        return $this->isFallback;
    }
}
