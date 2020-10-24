<?php

namespace Concrete\Package\DropBox\Controller\Dialog\UploadedFile\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Validation\CSRF\Token;
use Concrete5\DropBox\Entity\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/uploaded_file/bulk/delete';

    /** @var EntityManagerInterface */
    protected $entityManager;

    protected function canAccess()
    {
        return true;
    }

    public function on_start()
    {
        parent::on_start();

        $this->entityManager = $this->app->make(EntityManagerInterface::class);
    }

    public function view()
    {
        $uploadedFileEntries = [];

        $items = $this->request->query->get("item");

        if (!is_array($items)) {
            $items = [$this->request->query->get("item")];
        }

        foreach ($items as $ids) {
            list($primaryIdentifier, $fileIdentifier) = explode("_", $ids);

            $uploadedFileEntry = $this->entityManager->getRepository(UploadedFile::class)->findOneBy([
                "primaryIdentifier" => $primaryIdentifier,
                "fileIdentifier" => $fileIdentifier
            ]);

            if ($uploadedFileEntry instanceof UploadedFile) {
                $uploadedFileEntries[] = $uploadedFileEntry;
            }
        }

        $this->set("uploadedFileEntries", $uploadedFileEntries);
    }

    public function submit()
    {
        $editResponse = new EditResponse();

        /** @var Token $token */
        $token = $this->app->make(Token::class);

        if (!$token->validate('bulk_delete_uploaded_file_entries')) {
            throw new \Exception($token->getErrorMessage());
        }

        $items = $this->request->request->get("uploadedFileEntries");

        foreach ($items as $ids) {
            list($primaryIdentifier, $fileIdentifier) = explode("_", $ids);

            $uploadedFileEntry = $this->entityManager->getRepository(UploadedFile::class)->findOneBy([
                "primaryIdentifier" => $primaryIdentifier,
                "fileIdentifier" => $fileIdentifier
            ]);

            if ($uploadedFileEntry instanceof UploadedFile) {
                $file = $uploadedFileEntry->getFile();

                if ($file instanceof File) {
                    $file->delete();
                }

                $this->entityManager->remove($uploadedFileEntry);
            }
        }

        $this->entityManager->flush();

        $editResponse->setTitle(t("Entries successfully deleted"));
        $editResponse->setMessage(t("The entries were successfully deleted."));
        return $editResponse->outputJSON();
    }
}
