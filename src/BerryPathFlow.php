<?php declare(strict_types=1);

namespace BerryPath\Flow;

use BerryPath\Flow\Service\CustomFieldsInstaller;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

class BerryPathFlow extends Plugin
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

    private function getCustomFieldsInstaller(): CustomFieldsInstaller
    {
        if ($this->container->has(CustomFieldsInstaller::class)) {
            return $this->container->get(CustomFieldsInstaller::class);
        }

        return new CustomFieldsInstaller(
            $this->container->get('custom_field_set.repository'),
            $this->container->get('custom_field.repository'),
            $this->container->get('custom_field_set_relation.repository')
        );
    }
}
