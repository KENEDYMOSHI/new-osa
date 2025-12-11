<?php

namespace Config;

use App\Filters\CSRF;
use App\Filters\Frame;
use App\Filters\AuthFilter;
use App\Filters\AdminFilter;
use App\Filters\RateLimiter;
use CodeIgniter\Filters\Cors;
use App\Filters\ApiAuthFilter;
use App\Filters\ManagerFilter;
use App\Filters\OfficerFilter;
use App\Filters\TopLevelFilter;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Config\Filters as BaseFilters;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf' => CSRF::class,
        'toolbar' => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'invalidchars' => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors' => Cors::class,
        'forcehttps' => ForceHTTPS::class,
        'pagecache' => PageCache::class,
        'performance' => PerformanceMetrics::class,
        'AuthFilter' => AuthFilter::class,
        'AdminFilter' => AdminFilter::class,
        'OfficerFilter' => OfficerFilter::class,
        'ManagerFilter' => ManagerFilter::class,
        'TopLevelFilter' => TopLevelFilter::class,
        'ApiAuthFilter' => ApiAuthFilter::class,
        'Frame' => Frame::class,
        'RateLimiter' => RateLimiter::class,
    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * Filters set by default provide framework functionality. If removed,
     * those functions will no longer work.
     *
     * @see https://codeigniter.com/user_guide/incoming/filters.html#provided-filters
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // Force Global Secure Requests
            'pagecache',  // Web Page Caching

        ],
        'after' => [
            'pagecache',   // Web Page Caching
            'performance', // Performance Metrics
            'toolbar',
            'Frame',    // Debug Toolbar

        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            'RateLimiter',
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
            'csrf' => [
                'except' => [
                    'pay',
                    'control_number',
                    'bill_request',
                    'bill_payment',
                    'bill_reconciliation',
                    'api/login',
                    'api/logout',
                    'api/profile',
                    'api/billRequest',
                    'api/searchBill',
                    'api/selectBill',
                    'api/searchReceipt',
                    'api/selectReceipt',
                    'api/searchInstrument',
                    'api/selectInstrument',
                    'api/billCancellationRequest',
                    'api/verifyInstrument',
                    'api/billRenewRequest',
                    'csvData',
                    'api/generateBill',
                    'billPaymentSimulation',
                    'controlNumberHandler',
                    'paymentHandler',
                    'reconHandler',
                    'serviceBillRequest',
                    'requestCertificateOfQuantity',
                    'metrological/save-ullage-report',
                    'metrological/ullage-report/save'
                ]
            ]
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'POST' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}
