<?php declare(strict_types=1);

namespace BerryPath\Flow;

use BerryPath\Flow\Service\CustomFieldsInstaller;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

class SidworksBerryPathFlow extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        $this->getCustomFieldsInstaller()->install($installContext->getContext());

        parent::install($installContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        $this->getCustomFieldsInstaller()->install($updateContext->getContext());
        $this->getCustomFieldsInstaller()->addRelations($updateContext->getContext());

        parent::update($updateContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->getCustomFieldsInstaller()->addRelations($activateContext->getContext());

        parent::activate($activateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            $this->getCustomFieldsInstaller()->uninstall($uninstallContext->getContext());
        }

        parent::uninstall($uninstallContext);
    }

    private function getCustomFieldsInstaller(): CustomFieldsInstaller
    {
        /** @var EntityRepository $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        /** @var EntityRepository $customFieldRepository */
        $customFieldRepository = $this->container->get('custom_field.repository');
        /** @var EntityRepository $customFieldSetRelationRepository */
        $customFieldSetRelationRepository = $this->container->get('custom_field_set_relation.repository');
        /** @var EntityRepository $cmsSlotRepository */
        $cmsSlotRepository = $this->container->get('cms_slot.repository');

        return new CustomFieldsInstaller(
            $customFieldSetRepository,
            $customFieldRepository,
            $customFieldSetRelationRepository,
            $cmsSlotRepository
        );
    }
}
