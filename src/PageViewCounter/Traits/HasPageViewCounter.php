<?php

namespace CyrildeWit\PageViewCounter\Traits;

use Request;
use CyrildeWit\PageViewCounter\PageViewManager;
use CyrildeWit\PageViewCounter\Helpers\SessionHistory;
use CyrildeWit\PageViewCounter\Helpers\DateTransformer;

/**
 * Trait HasPageViewCounter for Eloquent models.
 *
 * @copyright  Copyright (c) 2017 Cyril de Wit (http://www.cyrildewit.nl)
 * @author     Cyril de Wit (info@cyrildewit.nl)
 * @license    https://opensource.org/licenses/MIT    MIT License
 */
trait HasPageViewCounter
{
    protected $pageViewManager;

    /**
     * The SessionHistory helper instance.
     *
     * @var \CyrildeWit\PageViewCounter\Helpers\SessionHistory
     */
    protected $sessionHistoryInstance;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [], PageViewManager $pageViewManager)
    {
        $this->pageViewManager = $pageViewManager;
        $this->sessionHistoryInstance = new SessionHistory();

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
        return $pageViewManager->getPageViewsBy($this);
    }

    /**
     * Get the total number of page views after the given date.
     *
     * @param  \Carbon\Carbon|string  $sinceDate
     * @return int
     */
    public function getPageViewsFrom($sinceDate)
    {
        return $pageViewManager->getPageViewsBy($this, $sinceDate);
    }

    /**
     * Get the total number of page views before the given date.
     *
     * @param  \Carbon\Carbon|string  $uptoDate
     * @return int
     */
    public function getPageViewsBefore($uptoDate)
    {
        return $pageViewManager->getPageViewsBy($this, null, $uptoDate);
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
        return $pageViewManager->getPageViewsBy($this, $sinceDate, $uptoDate);
    }

    /**
     * Get the total number of unique page views.
     *
     * @return int
     */
    public function getUniquePageViews()
    {
        return $pageViewManager->getPageViewsBy($this, null, null, true);
    }

    /**
     * Get the total number of unique page views after the given date.
     *
     * @param  \Carbon\Carbon|string  $sinceDate
     * @return int
     */
    public function getUniquePageViewsFrom($sinceDate)
    {
        return $pageViewManager->getPageViewsBy($this, $sinceDate, null, true);
    }

    /**
     * Get the total number of unique page views before the given date.
     *
     * @param  \Carbon\Carbon|string  $uptoDate
     * @return int
     */
    public function getUniquePageViewsBefore($uptoDate)
    {
        return $pageViewManager->getPageViewsBy($this, null, $uptoDate, true);
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
        return $pageViewManager->getPageViewsBy($this, $sinceDate, $uptoDate, true);
    }

    /**
     * Store a new page view and return an instance of it.
     *
     * @return \CyrildeWit\PageViewCounter\Contracts\PageView
     */
    public function addPageView()
    {
        $viewClass = config('page-view-counter.page_view_model');

        $newView = new $viewClass();
        $newView->visitable_id = $this->id;
        $newView->visitable_type = get_class($this);
        $newView->ip_address = Request::ip();
        $this->views()->save($newView);

        return $newView;
    }

    /**
     * Store a new page view and store it into the session with an expiry date.
     *
     * @param  \Carbon\Carbon|string  $expiryDate
     * @return bool
     */
    public function addPageViewThatExpiresAt($expiryDate)
    {
        $expiryDate = DateTransformer::transform($expiryDate);

        if ($this->sessionHistoryInstance->addToSession($this, $expiryDate)) {
            $this->addPageView();

            return true;
        }

        return false;
    }
}
