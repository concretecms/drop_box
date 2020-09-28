<?php

namespace Concrete5\DropBox\Search\UploadedFile\Field;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['manager/search_field/uploaded_file'] = $this->app->share(function ($app) {
            return $app->make('Concrete5\DropBox\Search\UploadedFile\Field\Manager');
        });
    }
}
