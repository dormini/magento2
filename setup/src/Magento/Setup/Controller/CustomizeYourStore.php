<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Magento\Framework\Filesystem;
use Magento\Framework\Module\FullModuleList;
use Magento\Framework\Setup\Lists;
use Magento\Setup\Model\ObjectManagerProvider;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class CustomizeYourStore extends AbstractActionController
{
    /**
     * @var FullModuleList
     */
    protected $moduleList;

    /**
     * @var Lists
     */
    protected $list;

    /**
     * @var ObjectManagerProvider
     */
    protected $objectManagerProvider;

    /**
     * @param FullModuleList $moduleList
     * @param Lists $list
     * @param ObjectManagerProvider $objectManagerProvider
     */
    public function __construct(FullModuleList $moduleList, Lists $list, ObjectManagerProvider $objectManagerProvider)
    {
        $this->moduleList = $moduleList;
        $this->list = $list;
        $this->objectManagerProvider = $objectManagerProvider;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $sampleDataDeployed = $this->moduleList->has('Magento_SampleData');
        if ($sampleDataDeployed) {
            /** @var \Magento\SampleData\Model\SampleData $sampleData */
            $sampleData = $this->objectManagerProvider->get()->get('Magento\SampleData\Model\SampleData');
            $isSampleDataInstalled = $sampleData->isInstalledSuccessfully();
            $isSampleDataErrorInstallation = $sampleData->isInstallationError();
        } else {
            $isSampleDataInstalled = false;
            $isSampleDataErrorInstallation = false;
        }

        $view = new ViewModel([
            'timezone' => $this->list->getTimezoneList(),
            'currency' => $this->list->getCurrencyList(),
            'language' => $this->list->getLocaleList(),
            'isSampledataEnabled' => $sampleDataDeployed,
            'isSampleDataInstalled' => $isSampleDataInstalled,
            'isSampleDataErrorInstallation' => $isSampleDataErrorInstallation
        ]);
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Getting default time zone from server settings
     *
     * @return JsonModel
     */
    public function defaultTimeZoneAction()
    {
        $defaultTimeZone = trim(@date_default_timezone_get());
        if (empty($defaultTimeZone)) {
            return new JsonModel(['defaultTimeZone' => 'UTC']);
        } else {
            return new JsonModel(['defaultTimeZone' => $defaultTimeZone]);
        }
    }
}
