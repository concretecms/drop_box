<?php

namespace Concrete\Package\DropBox\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Entity\User\User;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Concrete5\DropBox\Form\Service\Validation;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Legacy\Pagination;
use Concrete\Core\File\File;
use Concrete5\DropBox\Entity\UploadedFile as UploadedFileEntity;
use Concrete\Package\DropBox\Controller\Element\Header\UploadedFile as HeaderController;

class DropBox extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;
    /** @var DateTime */
    protected $dateTime;

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->request = $this->app->make(Request::class);
        $this->dateTime = $this->app->make(DateTime::class);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     * @param UploadedFileEntity $entry
     * @return Response
     */
    private function save($entry)
    {
        $data = $this->request->request->all();

        if ($this->validate($data)) {
            /** @var User $owner */
            $owner = $this->entityManager->getRepository(User::class)->findOneBy(["uID" => $data["owner"]]);
            $createdAt = $this->dateTime->translate("created_at", $data, true);
            $entry->setPrimaryIdentifier($data["primaryIdentifier"]);
            $entry->setFileIdentifier($data["fileIdentifier"]);
            $entry->setFile(File::getById($data["file"]));
            $entry->setOwner($owner);
            $entry->setCreatedAt($createdAt);

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/files/drop_box/saved"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->setDefaults($entry);
        }
    }

    private function setDefaults($entry = null)
    {

        $this->set("entry", $entry);
        $this->render("/dashboard/files/drop_box/edit");
    }

    public function removed()
    {
        $this->set("success", t('The item has been successfully removed.'));
        $this->view();
    }

    public function saved()
    {
        $this->set("success", t('The item has been successfully updated.'));
        $this->view();
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @param array $data
     * @return bool
     */
    public function validate($data = null)
    {
        /** @var Validation $validator */
        $validator = $this->app->make(Validation::class);
        $validator->setData($data);
        $validator->addRequiredGuid("primaryIdentifier", t("You need to enter a primary identifier."));
        $validator->addRequiredGuid("fileIdentifier", t("You need to enter a file identifier."));
        $validator->addRequiredFile("file", t("You need to select a valid file."));
        $validator->addRequiredDateTime("created_at", t("You need to enter a valid date."));

        if ($validator->test()) {
            return true;
        } else {
            $this->error = $validator->getError();
            return false;
        }
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new UploadedFileEntity();

        if ($this->token->validate("save_drop_box_entity")) {
            return $this->save($entry);
        }

        $this->setDefaults($entry);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function edit($primaryIdentifier = null, $fileIdentifier = null)
    {
        /** @var UploadedFileEntity $entry */
        $entry = $this->entityManager->getRepository(UploadedFileEntity::class)->findOneBy([
            "primaryIdentifier" => $primaryIdentifier,
            "fileIdentifier" => $fileIdentifier
        ]);

        if ($entry instanceof UploadedFileEntity) {
            if ($this->token->validate("save_drop_box_entity")) {
                return $this->save($entry);
            }

            $this->setDefaults($entry);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function remove($primaryIdentifier = null, $fileIdentifier = null)
    {
        /** @var UploadedFileEntity $entry */
        $entry = $this->entityManager->getRepository(UploadedFileEntity::class)->findOneBy([
            "primaryIdentifier" => $primaryIdentifier,
            "fileIdentifier" => $fileIdentifier
        ]);

        if ($entry instanceof UploadedFileEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/files/drop_box/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    public function view()
    {
        $headerMenu = new HeaderController();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\DropBox\Controller\Search\UploadedFile $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\DropBox\Controller\Search\UploadedFile::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
