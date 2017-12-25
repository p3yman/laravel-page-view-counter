<?php

namespace CyrildeWit\PageViewCounter;

use Illuminate\Contracts\Cache\Repository;

class PageViewManager
{
    /**
     *
     */
    public function __construct()
    {
        //
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
}
