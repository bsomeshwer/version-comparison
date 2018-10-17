<?php namespace Someshwer\VersionComparison;

use Illuminate\Support\ServiceProvider;
use Someshwer\VersionComparison\Lib\VersionComparator;
use Someshwer\VersionComparison\Repo\ExpressionEvaluator;
use Someshwer\VersionComparison\Repo\ExpressionValidator;

/**
 * VersionComparisonServiceProvider class
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 26-09-2018
 */
class VersionComparisonServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Resolving VersionComparator
        $this->app->bind('VersionComparator', function () {
            return new VersionComparator(new ExpressionEvaluator());
        });

        // Resolving ExpressionValidator
        $this->app->bind('ExpressionValidator', function () {
            return new ExpressionValidator(new ExpressionEvaluator());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Loading routes inside package
        $this->loadRoutesFrom(__DIR__ . '\Routes\routes.php');
    }

}
