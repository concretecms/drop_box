<?php

namespace Concrete\Package\DropBox\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\StorageLocation\Type\Type;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardPageController;

class DropBoxSettings extends DashboardPageController
{
    /** @var Repository */
    protected $config;

    public function on_start()
    {
        parent::on_start();

        $this->config = $this->app->make(Repository::class);
    }

    private function setDefaults()
    {
        $storageLocationList = [];

        /** @var StorageLocationFactory $storageLocationFactory */
        $storageLocationFactory = $this->app->make(StorageLocationFactory::class);
        $storageLocations = $storageLocationFactory->fetchList();

        foreach ($storageLocations as $storageLocation) {
            /** @var Type $storageLocation */
            $storageLocationList[$storageLocation->getID()] = $storageLocation->getName();
        }

        $this->set('storageLocationList', $storageLocationList);
        $this->set('storageLocation', (int)$this->config->get("drop_box.storage_location", 0));
        $this->set('uploadDirectoryId', (int)$this->config->get("drop_box.target_upload_directory_id", 0));
    }

    private function validate()
    {
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);

        $formValidator->setData($this->request->request->all());

        $formValidator->addRequiredToken("save_drop_box_settings");

        if ($formValidator->test()) {
            return true;
        } else {
            foreach($formValidator->getError() as $error) {
                $this->error->add($error);
            }

            return false;
        }
    }

    public function view()
    {
        $this->setDefaults();

        if ($this->request->getMethod() === "POST" && $this->validate()) {
            $this->config->save("drop_box.storage_location", (int)$this->request->request->get("storageLocation"));
            $this->config->save("drop_box.target_upload_directory_id", (int)$this->request->request->get("uploadDirectoryId"));

            $this->set("success", t("The settings has been updated successfully."));
        }
    }
}

