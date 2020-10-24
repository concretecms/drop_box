<?php

namespace Concrete5\DropBox\Console\Command;

use Bitter\EntityDesigner\Generator\GeneratorService;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\Support\Facade\Application;
use Concrete5\DropBox\Entity\UploadedFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RemoveExpiredFiles extends Command
{
    protected function configure()
    {
        $this
            ->setName('drop-box:remove-expired-files')
            ->setDescription(t('Remove expired files.'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();

        $io = new SymfonyStyle($input, $output);

        /** @var Connection $connection */
        $connection= $app->make(Connection::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);

        try {
            $rows = $connection->fetchAll("SELECT * FROM UploadedFile WHERE DATE_ADD(createdAt, INTERVAL 45 DAY) < NOW()");

            foreach ($rows as $row) {
                $file = File::getByID($row["fID"]);

                if ($file instanceof \Concrete\Core\Entity\File\File) {
                    $file->delete();
                }

                $entry = $entityManager->getRepository(UploadedFile::class)->findOneBy([
                    "primaryIdentifier" => $row["primaryIdentifier"],
                    "fileIdentifier" => $row["fileIdentifier"]
                ]);

                if ($entry instanceof UploadedFile) {
                    $entityManager->remove($entry);
                }
            }

            $entityManager->flush();

            $io->success(t2('%s file removed', '%s files removed', count($rows)));
        } catch (Exception $error) {
            $io->error($error->getMessage());
        }
    }
}
