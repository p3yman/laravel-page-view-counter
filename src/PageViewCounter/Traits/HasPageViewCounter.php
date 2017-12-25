<?php

namespace CyrildeWit\PageViewCounter\Traits;


use CyrildeWit\PageViewCounter\PageViewManager;

/**
 * Trait HasPageViewCounter for Eloquent models.
 *
 * @copyright  Copyright (c) 2017 Cyril de Wit (http://www.cyrildewit.nl)
 * @author     Cyril de Wit (info@cyrildewit.nl)
 * @license    https://opensource.org/licenses/MIT    MIT License
 */
trait HasPageViewCounter
{
    /**
     * The PageViewManager instance.
     *
     * @var \CyrildeWit\PageViewCounter\PageViewManager
     */
    protected $pageViewManager;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->pageViewManager = new PageViewManager;

        parent::__construct($attributes);
    }

    /**
     * Get the page views associated with the given model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function views()
    {
        return $this->morphMany(
            config('page-view-counter.page_view_model'),
            'visitable'
        );
    }

    /**
     * Get the total number of page views.
     *
     * @return int
     */
    public function getPageViews()
    {
        return $this->pageViewManager->getPageViewsBy($this);
    }

    /**
     * Get the total number of page views after the given date.
     *
     * @param  \Carbon\Carbon|string  $sinceDate
     * @return int
     */
    public function getPageViewsFrom($sinceDate)
    {
        return $this->pageViewManager->getPageViewsBy($this, $sinceDate);
    }

    /**
     * Get the total number of page views before the given date.
     *
     * @param  \Carbon\Carbon|string  $uptoDate
     * @return int
     */
    public function getPageViewsBefore($uptoDate)
    {
        return $this->pageViewManager->getPageViewsBy($this, null, $uptoDate);
    }

    /**
     * Get the total number of page views between the given two dates.
     *
     * @param  \Carbon\Carbon|string  $sinceDate
     * @param  \Carbon\Carbon|string  $uptoDate
     * @return int
     */
    public function getPageViewsBetween($sinceDate, $uptoDate)
    {
        return $this->pageViewManager->getPageViewsBy($this, $sinceDate, $uptoDate);
    }

    /**
     * Get the total number of unique page views.
     *
     * @return int
     */
    public function getUniquePageViews()
    {
        return $this->pageViewManager->getPageViewsBy($this, null, null, true);
    }

    /**
     * Get the total number of unique page views after the given date.
     *
     * @param  \Carbon\Carbon|string  $sinceDate
     * @return int
     */
    public function getUniquePageViewsFrom($sinceDate)
    {
        return $this->pageViewManager->getPageViewsBy($this, $sinceDate, null, true);
    }

    /**
     * Get the total number of unique page views before the given date.
     *
     * @param  \Carbon\Carbon|string  $uptoDate
     * @return int
     */
    public function getUniquePageViewsBefore($uptoDate)
    {
        return $this->pageViewManager->getPageViewsBy($this, null, $uptoDate, true);
    }

    /**
     * Get the total number of unique page views between the given two dates.
     *
     * @param  \Carbon\Carbon|string  $sinceDate
     * @param  \Carbon\Carbon|string  $uptoDate
     * @return int
     */
    public function getUniquePageViewsBetween($sinceDate, $uptoDate)
    {
        return $this->pageViewManager->getPageViewsBy($this, $sinceDate, $uptoDate, true);
    }

    /**
     * Store a new page view and return an instance of it.
     *
     * @return \CyrildeWit\PageViewCounter\Contracts\PageView
     */
    public function addPageView()
    {
        return $this->pageViewManager->addPageView($this);
    }

    /**
     * Store a new page view and store it into the session with an expiry date.
     *
     * @param  \Carbon\Carbon|string  $expiryDate
     * @return bool
     */
    public function addPageViewThatExpiresAt($expiryDate)
    {
        return $this->pageViewManager->addPageViewThatExpiresAt($this, $expiryDate);
    }
}
