<?php
namespace Concrete\Package\DropBox\Job;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\Job\Job;

class RemoveExpiredFiles extends Job
{
    protected $connection;

    public function __construct(
        Connection $connection
    )
    {
        $this->connection = $connection;
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
        $rows = $this->connection->fetchAll("SELECT * FROM UploadedFile WHERE DATE_ADD(createdAt, INTERVAL 45 DAY) > NOW()");

        foreach($rows as $row) {
            $file = File::getByID($row["fID"]);
            $file->delete();
        }

        return t2('%s file removed', '%s files removed', count($rows));
    }
}
