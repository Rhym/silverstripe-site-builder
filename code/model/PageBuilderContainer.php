<?php

/**
 * Class PageBuilderContainer
 *
 * @property Int PageID
 * @property Int SortOrder
 * @property Varchar Title
 * @property Boolean IsFullWidth
 * @property Design Style
 * @property Enum BackgroundType
 *
 * @method Page Page
 * @method Image Image
 * @method PageBuilderItem Items
 */
class PageBuilderContainer extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'SortOrder' => 'Int',
        'Title' => 'Varchar',
        'IsFullWidth' => 'Boolean',
        'Style' => 'Design',
        'BackgroundType' => 'Enum(array("Cover", "Fixed", "Repeat"))'
    );

    /**
     * @var string
     */
    private static $singular_name = 'Container';

    /**
     * @var string
     */
    private static $plural_name = 'Containers';

    /**
     * @var array
     */
    private static $has_one = array(
        'Page' => 'Page',
        'Image' => 'Image'
    );

    /**
     * @var array
     */
    private static $has_many = array(
        'Items' => 'PageBuilderItem'
    );

    /**
     * @var array
     */
    private static $defaults = array(
        'Title' => 'Container'
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'SortOrder' => 'Order'
    );

    /**
     * @var string
     */
    private static $default_sort = 'SortOrder';

//    /**
//     * @return RequiredFields
//     */
//    public function getCMSValidator() {
//        return new RequiredFields(array());
//    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        /** =========================================
         * @var FieldList $fields
         * @var FieldGroup $settings
         * @var UploadField $image
         * @var GridFieldConfig_RelationEditor $config
         * @var GridField $gridField
        ===========================================*/

        $fields = FieldList::create(TabSet::create('Root'));
        $fields->addfieldToTab('Root.Main', $title = TextField::create('Title'));
        $title->setRightTitle('Title used in the cms as a visual cue for this piece of content.');
        $fields->addFieldToTab('Root.Main', $settings = FieldGroup::create(array(
            CheckboxField::create('IsFullWidth', 'Container full width')
        )));
        $settings->setTitle('Settings');
        $fields->addFieldToTab('Root.Main', HeaderField::create('', 'Style'));
        $fields->addFieldToTab('Root.Main', DesignField::create('Style', 'Container', '.container', array(
            'padding-top' => 'TextField',
            'padding-bottom' => 'TextField',
            'margin-top' => 'TextField',
            'margin-bottom' => 'TextField',
            'color' => 'ColorField',
            'background' => 'ColorField'
        )));
        $fields->addFieldToTab('Root.Main', HeaderField::create('', 'Background (optional)', 4));
        $fields->addFieldToTab('Root.Main',
            $image = UploadField::create('Image', _t('PageBuilderContainer.IMAGE', 'Image')));
        $image->setAllowedExtensions(array(
            'jpeg',
            'jpg',
            'gif',
            'png'
        ));
        $image->setFolderName('Uploads/site-builder/containers');
        $fields->addFieldToTab('Root.Main',
            DropdownField::create('BackgroundType', 'Type', $this->dbObject('BackgroundType')->enumValues()));

        $config = GridFieldConfig_RelationEditor::create(10);
        $config->addComponent(GridFieldOrderableRows::create('SortOrder'))
            ->addComponent(new GridFieldDeleteAction());
        $gridField = GridField::create(
            'Items',
            'Items',
            $this->Items(),
            $config
        );
        $gridField->addExtraClass('hide');
        $fields->addFieldToTab('Root.Main', $gridField);

        return $fields;
    }

    /**
     * @return PageBuilderItem
     */
    protected function getChildren()
    {
        return $this->Items();
    }

    /**
     * @return bool|string
     */
    protected function getAdditionalStyles()
    {
        $style = '';
        if ($this->Image()->ID) {
            $style .= 'background-image: url("' . $this->Image()->getURL() . '");';
        }
        if ($backgroundType = $this->BackgroundType) {
            switch ($backgroundType) {
                case 'Fixed':
                    $style .= 'background-size: cover;';
                    $style .= 'background-attachment: fixed;';
                    break;
                case 'Repeat':
                    break;
                default:
                    $style .= 'background-size: cover;';
            }
        }
        return $style;
    }

    /**
     * @return mixed
     */
    protected function getInlineStyle()
    {
        $additionalStyles = (string)$this->getAdditionalStyles();
        return $this->dbObject('Style')->InlineStyle($additionalStyles);
    }

    /**
     * On Before Write
     */
    protected function onBeforeWrite()
    {
        /** Set SortOrder */
        if (!$this->SortOrder) {
            $this->SortOrder = DataObject::get($this->ClassName)->max('SortOrder') + 1;
        }
        parent::onBeforeWrite();
    }

    protected function onAfterDelete()
    {
        /**
         * Delete all has_many relationships associated with this controller.
         */
        $items = $this->Items();
        foreach ($items as $key => $item) {
            DataObject::delete_by_id('PageBuilderItem', $item->ID);
        }

        parent::onAfterDelete();
    }

}