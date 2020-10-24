<?php

namespace Concrete\Package\DropBox\Job;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\Job\Job;
use Concrete5\DropBox\Entity\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

class RemoveExpiredFiles extends Job
{
    protected $connection;
    protected $entityManager;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $entityManager
    )
    {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
    }

    public function getJobName()
    {
        return t("Remove Expired Files");
    }

    public function getJobDescription()
    {
        return t("Removes all expired files that are uploaded through the drop box component.");
    }

    public function run()
    {
        $rows = $this->connection->fetchAll("SELECT * FROM UploadedFile WHERE DATE_ADD(createdAt, INTERVAL 45 DAY) < NOW()");

        foreach ($rows as $row) {
            $file = File::getByID($row["fID"]);

            if ($file instanceof \Concrete\Core\Entity\File\File) {
                $file->delete();
            }

            $entry = $this->entityManager->getRepository(UploadedFile::class)->findOneBy([
                "primaryIdentifier" => $row["primaryIdentifier"],
                "fileIdentifier" => $row["fileIdentifier"]
            ]);

            if ($entry instanceof UploadedFile) {
                $this->entityManager->remove($entry);
            }
        }

        $this->entityManager->flush();

        return t2('%s file removed', '%s files removed', count($rows));
    }
}
