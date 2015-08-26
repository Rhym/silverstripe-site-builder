<?php

/**
 * Class SiteBuilderPageExtension
 *
 * @method SiteBuilderPageExtension PageBuilderContainer
 */
class SiteBuilderPageExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = array();

    /**
     * @var array
     */
    private static $has_many = array(
        'PageBuilderContainers' => 'PageBuilderContainer'
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /**
         * Create a GridField so we can steal it's edit methods.
         *
         * @var GridField $gridField
         */
        $gridField = GridField::create(
            'PageBuilderContainers',
            'PageBuilderContainers',
            $this->owner->PageBuilderContainers(),
            GridFieldConfig_RelationEditor::create(999)
        );
        $gridField->addExtraClass('hide');
        $fields->addFieldToTab('Root.PageBuilder', $gridField);
        $fields->addFieldToTab('Root.PageBuilder', SiteBuilder::create(
            'SiteBuilder',
            'SiteBuilder',
            $this->owner->PageBuilderContainers(),
            $gridField->Name,
            $this->owner->ID
        ));
    }

}