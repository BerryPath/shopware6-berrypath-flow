<?php declare(strict_types=1);

namespace BerryPath\Flow\Service;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class CustomFieldsInstaller
{
    private const CUSTOM_FIELDSET_NAME = 'berrypath_flow_product';

    private const CUSTOM_FIELDSET = [
        'name' => self::CUSTOM_FIELDSET_NAME,
        'config' => [
            'label' => [
                'en-GB' => 'BerryPath Flow',
                'nl-NL' => 'BerryPath Flow',
                'de-DE' => 'BerryPath Flow',
                Defaults::LANGUAGE_SYSTEM => 'BerryPath Flow',
            ],
        ],
        'customFields' => [
            [
                'name' => 'berrypath_flow_uuid',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Flow UUID',
                        'nl-NL' => 'Flow UUID',
                        'de-DE' => 'Flow UUID',
                        Defaults::LANGUAGE_SYSTEM => 'Flow UUID',
                    ],
                    'helpText' => [
                        'en-GB' => 'Enter a BerryPath Flow UUID to show the Flow widget on this product page.',
                        'nl-NL' => 'Vul een BerryPath Flow UUID in om de Flow widget op deze productpagina te tonen.',
                        'de-DE' => 'Trage eine BerryPath Flow UUID ein, um das Flow widget auf dieser Produktseite anzuzeigen.',
                    ],
                    'customFieldPosition' => 1,
                ],
            ],
            [
                'name' => 'berrypath_flow_display_type',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Display type',
                        'nl-NL' => 'Weergavetype',
                        'de-DE' => 'Anzeigetyp',
                        Defaults::LANGUAGE_SYSTEM => 'Display type',
                    ],
                    'customFieldPosition' => 2,
                    'customFieldType' => 'select',
                    'componentName' => 'sw-single-select',
                    'options' => [
                        [
                            'label' => [
                                'en-GB' => 'Popup',
                                'nl-NL' => 'Popup',
                                'de-DE' => 'Popup',
                                Defaults::LANGUAGE_SYSTEM => 'Popup',
                            ],
                            'value' => 'popup',
                        ],
                        [
                            'label' => [
                                'en-GB' => 'Sidebar',
                                'nl-NL' => 'Sidebar',
                                'de-DE' => 'Sidebar',
                                Defaults::LANGUAGE_SYSTEM => 'Sidebar',
                            ],
                            'value' => 'sidebar',
                        ],
                        [
                            'label' => [
                                'en-GB' => 'Inline',
                                'nl-NL' => 'Inline',
                                'de-DE' => 'Inline',
                                Defaults::LANGUAGE_SYSTEM => 'Inline',
                            ],
                            'value' => 'inline',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'berrypath_flow_title',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Title',
                        'nl-NL' => 'Titel',
                        'de-DE' => 'Titel',
                        Defaults::LANGUAGE_SYSTEM => 'Title',
                    ],
                    'customFieldPosition' => 3,
                ],
            ],
            [
                'name' => 'berrypath_flow_description',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Description',
                        'nl-NL' => 'Beschrijving',
                        'de-DE' => 'Beschreibung',
                        Defaults::LANGUAGE_SYSTEM => 'Description',
                    ],
                    'customFieldPosition' => 4,
                ],
            ],
            [
                'name' => 'berrypath_flow_cta_text',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Button text',
                        'nl-NL' => 'Knoptekst',
                        'de-DE' => 'Buttontext',
                        Defaults::LANGUAGE_SYSTEM => 'Button text',
                    ],
                    'customFieldPosition' => 5,
                ],
            ],
            [
                'name' => 'berrypath_flow_market',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Market override',
                        'nl-NL' => 'Market override',
                        'de-DE' => 'Market override',
                        Defaults::LANGUAGE_SYSTEM => 'Market override',
                    ],
                    'helpText' => [
                        'en-GB' => 'Optional. Falls back to the plugin default market code.',
                        'nl-NL' => 'Optioneel. Valt terug op de standaard market code uit de pluginconfiguratie.',
                        'de-DE' => 'Optional. Fallback auf den Standard-Marketcode aus der Plugin-Konfiguration.',
                    ],
                    'customFieldPosition' => 6,
                ],
            ],
        ],
    ];

    /**
     * @param EntityRepository<\Shopware\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetCollection> $customFieldSetRepository
     * @param EntityRepository<\Shopware\Core\System\CustomField\CustomFieldCollection> $customFieldRepository
     * @param EntityRepository<\Shopware\Core\System\CustomField\Aggregate\CustomFieldSetRelation\CustomFieldSetRelationCollection> $customFieldSetRelationRepository
     * @param EntityRepository<\Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection> $cmsSlotRepository
     */
    public function __construct(
        private readonly EntityRepository $customFieldSetRepository,
        private readonly EntityRepository $customFieldRepository,
        private readonly EntityRepository $customFieldSetRelationRepository,
        private readonly EntityRepository $cmsSlotRepository
    ) {
    }

    public function install(Context $context): void
    {
        $this->customFieldSetRepository->upsert([$this->buildCustomFieldSetPayload($context)], $context);
    }

    public function addRelations(Context $context): void
    {
        $relations = [];

        foreach ($this->getCustomFieldSetIds($context) as $customFieldSetId) {
            if ($this->hasProductRelation($customFieldSetId, $context)) {
                continue;
            }

            $relations[] = [
                'customFieldSetId' => $customFieldSetId,
                'entityName' => 'product',
            ];
        }

        if ($relations === []) {
            return;
        }

        $this->customFieldSetRelationRepository->upsert($relations, $context);
    }

    public function uninstall(Context $context): void
    {
        $customFieldSetIds = $this->getCustomFieldSetIds($context);

        if ($customFieldSetIds !== []) {
            $this->customFieldSetRepository->delete($this->buildDeletePayload($customFieldSetIds), $context);
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('type', 'berrypath-flow'));
        $cmsSlotIds = $this->cmsSlotRepository->searchIds($criteria, $context)->getIds();

        if ($cmsSlotIds !== []) {
            $this->cmsSlotRepository->delete($this->buildDeletePayload($cmsSlotIds), $context);
        }
    }

    /**
     * @return list<string>
     */
    private function getCustomFieldSetIds(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELDSET_NAME));

        return $this->customFieldSetRepository->searchIds($criteria, $context)->getIds();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCustomFieldSetPayload(Context $context): array
    {
        $payload = self::CUSTOM_FIELDSET;
        $customFieldSetId = $this->getCustomFieldSetIds($context)[0] ?? null;

        if ($customFieldSetId) {
            $payload['id'] = $customFieldSetId;
        }

        $customFieldIds = $this->getCustomFieldIds($context);

        foreach ($payload['customFields'] as &$customField) {
            $customFieldId = $customFieldIds[$customField['name']] ?? null;

            if ($customFieldId) {
                $customField['id'] = $customFieldId;
            }
        }

        unset($customField);

        return $payload;
    }

    /**
     * @return array<string, string>
     */
    private function getCustomFieldIds(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('name', array_column(self::CUSTOM_FIELDSET['customFields'], 'name')));

        $ids = [];

        $customFields = $this->customFieldRepository->search($criteria, $context);

        /** @var CustomFieldEntity $customField */
        foreach ($customFields as $customField) {
            $ids[$customField->getName()] = $customField->getId();
        }

        return $ids;
    }

    private function hasProductRelation(string $customFieldSetId, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customFieldSetId', $customFieldSetId));
        $criteria->addFilter(new EqualsFilter('entityName', 'product'));
        $criteria->setLimit(1);

        return $this->customFieldSetRelationRepository->searchIds($criteria, $context)->getTotal() > 0;
    }

    /**
     * @param list<string> $ids
     *
     * @return list<array{id: string}>
     */
    private function buildDeletePayload(array $ids): array
    {
        return array_map(static fn (string $id): array => ['id' => $id], $ids);
    }
}
