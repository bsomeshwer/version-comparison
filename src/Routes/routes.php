<?php

/**
 * Route inside the package and which tells some useful information about the package.
 *
 * @author Someshwer <bsomeshwer89@gmail.com>
 * Date: 24-09-2018
 */
Route::get('version-comparison/info', function () {

    $description = 'Laravel Version Comparison - This Laravel package compares two version strings
    and gives the Boolean result. This package also resolves version expressions like
    (($v > 1.24.0) && ($v < 1.25.1.0)) || ($v == 1.26 || $v == 1.27) where $v must be
    substituted with the version number to be compared. Hence the package can be used
    for version expressions evaluation.';
    return [
        'package_name' => 'Laravel - VersionComparison',
        'description' => preg_replace('/\s+/', ' ', trim($description)),
        'latest_release' => '1.4.2',
        'stable_version' => '1.4.2',
        'author' => 'Someshwer Bandapally <bsomeshwer89@gmail.com>',
    ];

});
