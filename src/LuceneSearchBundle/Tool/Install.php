<?php

namespace LuceneSearchBundle\Tool;

use LuceneSearchBundle\Configuration\Configuration;
use LuceneSearchBundle\LuceneSearchBundle;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\Property;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Install
 *
 * @package LuceneSearchBundle\Tool
 */
class Install extends AbstractInstaller
{
    /**
     * @var string
     */
    private $installSourcesPath;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $currentVersion;

    /**
     * Install constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->installSourcesPath = __DIR__ . '/../Resources/install';
        $this->fileSystem = new Filesystem();
        $this->currentVersion = Versions::getVersion(LuceneSearchBundle::PACKAGE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function install(): void
    {
        $this->installOrUpdateConfigFile();
        $this->createDirectories();
        $this->installProperties();
    }

    /**
     * install or update config file
     */
    private function installOrUpdateConfigFile()
    {
        if (!$this->fileSystem->exists(Configuration::SYSTEM_CONFIG_DIR_PATH)) {
            $this->fileSystem->mkdir(Configuration::SYSTEM_CONFIG_DIR_PATH);
        }

        $config = ['version' => $this->currentVersion];
        $yml = Yaml::dump($config);
        file_put_contents(Configuration::SYSTEM_CONFIG_FILE_PATH, $yml);

        if (!$this->fileSystem->exists(Configuration::STATE_FILE_PATH)) {
            $content = serialize(Configuration::STATE_DEFAULT_VALUES);
            $this->fileSystem->appendToFile(Configuration::STATE_FILE_PATH, $content);
        }

    }

    /**
     * @return bool
     */
    public function createDirectories()
    {
        if (!$this->fileSystem->exists(Configuration::CRAWLER_PERSISTENCE_STORE_DIR_PATH)) {
            $this->fileSystem->mkdir(Configuration::CRAWLER_PERSISTENCE_STORE_DIR_PATH, 0755);
        }

        if (!$this->fileSystem->exists(Configuration::INDEX_DIR_PATH)) {
            $this->fileSystem->mkdir(Configuration::INDEX_DIR_PATH, 0755);
        }

        if (!$this->fileSystem->exists(Configuration::INDEX_DIR_PATH_STABLE)) {
            $this->fileSystem->mkdir(Configuration::INDEX_DIR_PATH_STABLE, 0755);
        }

        if (!$this->fileSystem->exists(Configuration::INDEX_DIR_PATH_GENESIS)) {
            $this->fileSystem->mkdir(Configuration::INDEX_DIR_PATH_GENESIS, 0755);
        }

        return true;
    }

    /**
     *
     */
    public function installProperties()
    {
        $propertiesToInstall = [
            'assigned_language' => [
                'name'        => 'Assigned Language',
                'description' => 'Set a specific language which lucene search should respect while crawling.'
            ],
            'assigned_country'  => [
                'name'        => 'Assigned Country',
                'description' => 'Set a specific country which lucene search should respect while crawling.'
            ]
        ];

        foreach ($propertiesToInstall as $propertyKey => $propertyData) {
            $defProperty = Property\Predefined::getByKey($propertyKey);

            if (!$defProperty instanceof Property\Predefined) {
                $data = 'all,';
                if ($propertyKey === 'assigned_language') {
                    $languages = \Pimcore\Tool::getValidLanguages();
                    foreach ($languages as $language) {
                        $data .= $language . ',';
                    }
                }

                $data = rtrim($data, ',');

                $property = new Property\Predefined();
                $property->setType('select');
                $property->setName($propertyData['name']);
                $property->setKey($propertyKey);
                $property->setDescription($propertyData['description']);
                $property->setCtype('asset');
                $property->setData('all');
                $property->setConfig($data);
                $property->setInheritable(false);
                $property->save();
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function update()
    {
        $this->installOrUpdateConfigFile();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(): void
    {
        if ($this->fileSystem->exists(Configuration::SYSTEM_CONFIG_FILE_PATH)) {
            $this->fileSystem->remove(Configuration::SYSTEM_CONFIG_FILE_PATH);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isInstalled(): bool
    {
        return $this->fileSystem->exists(Configuration::SYSTEM_CONFIG_FILE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeInstalled(): bool
    {
        return !$this->fileSystem->exists(Configuration::SYSTEM_CONFIG_FILE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUninstalled(): bool
    {
        return $this->fileSystem->exists(Configuration::SYSTEM_CONFIG_FILE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function needsReloadAfterInstall(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUpdated(): bool
    {
        $needUpdate = false;
        if ($this->fileSystem->exists(Configuration::SYSTEM_CONFIG_FILE_PATH)) {
            $config = Yaml::parse(file_get_contents(Configuration::SYSTEM_CONFIG_FILE_PATH));
            if ($config['version'] !== $this->currentVersion) {
                $needUpdate = true;
            }
        }

        return $needUpdate;
    }

}
