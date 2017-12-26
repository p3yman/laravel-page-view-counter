<?php

namespace CyrildeWit\PageViewCounter;

use Request;
use Illuminate\Contracts\Cache\Repository;
use CyrildeWit\PageViewCounter\Helpers\SessionHistory;
use CyrildeWit\PageViewCounter\Helpers\DateTransformer;

class PageViewManager
{
    /**
     * The DateTransformer helper instance.
     *
     * @var \CyrildeWit\PageViewCounter\Helpers\DateTransformer
     */
    protected $dateTransformer;

    /**
     * The SessionHistory helper instance.
     *
     * @var \CyrildeWit\PageViewCounter\Helpers\SessionHistory
     */
    protected $sessionHistory;

    /**
     * The cache repository instance.
     *
     * @var \Illuminate\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new PageViewManager instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dateTransformer = new DateTransformer;
        $this->sessionHistory = new SessionHistory;
        $this->cache = app(Repository::class);
    }

    /**
     * Get the page views based upon the given options.
     *
     * @param  \Eloquen\Model  $model
     * @param  \Carbon\Carbon|null  $sinceDate
     * @param  \Carbon\Carbon|null  $uptoDate
     * @param  bool  $unique  Should the page views be unique.
     * @return int|string
     */
    public function getPageViewsBy($model, $sinceDate = null, $uptoDate = null, bool $unique = false)
    {
        // Transform the dates
        $sinceDate = ! is_null($sinceDate) ? $this->dateTransformer->transform($sinceDate) : null;
        $uptoDate = ! is_null($uptoDate) ? $this->dateTransformer->transform($uptoDate) : null;

        // Create new Query Builder instance of views relationship
        $query = $model->views();

        // Apply the following if the since date is given
        if ($sinceDate) {
            $query->where('created_at', '>=', $sinceDate);
        }

        // Apply the following if the upto date is given
        if ($uptoDate) {
            $query->where('created_at', '=<', $sinceDate);
        }

        // Apply the following if page views should be unique
        if ($unique) {
            $query->select('ip_address')->groupBy('ip_address');
        }

        // If the unique option is false then just use the SQL count method,
        // otherwise get the results and count them
        $countedPageViews = ! $unique ? $query->count() : $query->get()->count();

        return $countedPageViews;
    }

    /**
     * Store a new page view and return an instance of it.
     *
     * @return \CyrildeWit\PageViewCounter\Contracts\PageView
     */
    public function addPageView($model)
    {
        $viewClass = config('page-view-counter.page_view_model');

        $newPageView = new $viewClass();
        $newPageView->visitable_id = $model->id;
        $newPageView->visitable_type = get_class($model);
        $newPageView->ip_address = Request::ip();
        $model->views()->save($newPageView);

        return $newPageView;


    }

    /**
     * Store a new page view and store it into the session with an expiry date.
     *
     * @param  \Carbon\Carbon|string  $expiryDate
     * @return bool
     */
    public function addPageViewThatExpiresAt($model, $expiryDate)
    {
        // Transform the date
        $expiryDate = ! is_null($expiryDate) ? $this->dateTransformer->transform($expiryDate) : null;

        if ($this->sessionHistory->addToSession($this, $expiryDate)) {
            $this->addPageView();

            return true;
        }

        return false;
    }
}
