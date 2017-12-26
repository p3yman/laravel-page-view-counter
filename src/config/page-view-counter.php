<?php

use Carbon\Carbon;

return [

    /*
     * The class name of the page view Eloquent model to be used.
     */
    'page_view_model' => CyrildeWit\PageViewCounter\Models\PageView::class,

    /*
     * The table name of the page views database table.
     * It is used by creating the migrations files and default Eloquent model.
     */
    'page_views_table_name' => 'page-views',

    /*
     * The key thas used to store page views into the session. This is used by
     * the SessionHistory class that handles the page views with expiry dates.
     */
    'page_view_history_session_key' => 'page-view-counter.history',

    /*
     * Configure here your custom recognisable dates. When the package sees one
     * of the keys, it will use the value instead.
     *
     * Keep it empty, if you don't want any date transformers!
     *
     * Example:
     * - $article->getPageViewsFrom('24h'); // Get the total page views of the last 24 hours
     * - $article->getPageViewsFrom('14d'); // Get the total page views of the last 14 days
     */
    'date-transformers' => [
        // '24h' => Carbon::now()->subDays(1),
        // '7d' => Carbon::now()->subWeeks(1),
        // '14d' => Carbon::now()->subWeeks(2),
    ],

    /*
     * Here you may define the cache store that should be used to store
     * requests. This can be the name of any store that is
     * configured in app/config/cache.php
     */
    'cache_store' => env('RESPONSE_CACHE_DRIVER', 'file'),

    /*
     * If the cache driver you configured supports tags, you may specify a tag name
     * here. All responses will be tagged. When clearing the responsecache only
     * items with that tag will be flushed.
     *
     * You may use a string are an array here.
     */
    'cache_tag' => '',

];
