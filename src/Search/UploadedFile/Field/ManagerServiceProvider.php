<?php

namespace Concrete5\DropBox\Search\UploadedFile\Field;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('manager/search_field/uploaded_file', function($app) {
            return $app->make('Concrete5\DropBox\Search\UploadedFile\Field\Manager');
        });
    }
}
