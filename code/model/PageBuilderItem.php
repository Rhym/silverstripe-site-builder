<?php

/**
 * Class PageBuilderItem
 *
 * @property Int SortOrder
 * @property Varchar Title
 * @property Int SizeX
 * @property Enum Type
 * @property HTMLText Content
 * @property Varchar ExternalLink
 * @property Boolean OpenLinkInNewTab
 * @property Text YoutubeLink
 * @property Text VimeoLink
 *
 * @method PageBuilderContainer PageBuilderContainer
 * @method SiteTree InternalLink
 * @method Image Image
 * @method SliderItem SliderItems
 */
class PageBuilderItem extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'SortOrder' => 'Int',
        'SizeX' => 'Int',
        'Type' => 'Enum(array("Content", "Image", "Video", "Slider"))',
        'Title' => 'Varchar',
        'Content' => 'HTMLText',
        'ExternalLink' => 'Varchar(255)',
        'OpenLinkInNewTab' => 'Boolean',
        'YoutubeLink' => 'Varchar(255)',
        'VimeoLink' => 'Varchar(255)'
    );

    /**
     * @var string
     */
    private static $singular_name = 'Item';

    /**
     * @var string
     */
    private static $plural_name = 'Items';

    /**
     * @var array
     */
    private static $has_one = array(
        'Container' => 'PageBuilderContainer',
        'InternalLink' => 'SiteTree',
        'Image' => 'Image'
    );

    /**
     * @var array
     */
    private static $has_many = array(
        'SliderItems' => 'PageBuilderSliderItem'
    );

    /**
     * @var array
     */
    private static $defaults = array(
        'Title' => 'Item',
        'Type' => 'Content',
        'SizeX' => 12
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

    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields(array(
            'Title',
            'Type'
        ));
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        /** =========================================
         * @var FieldList $fields
         * @var TextField $title
         * @var OptionsetField $type
         * @var HtmlEditorField $content
         * @var CheckboxField $externalLink
         * @var UploadField $image
         * @var FieldGroup $linkSettings
         * @var GridFieldConfig_RelationEditor $config
        ===========================================*/

        $fields = FieldList::create(TabSet::create('Root'));
        $fields->addfieldToTab('Root.Main', $title = TextField::create('Title'));
        $title->setRightTitle('Title used in the cms as a visual cue for this piece of content.');
        $fields->addfieldToTab('Root.Main',
            $type = OptionsetField::create('Type', 'Type', $this->dbObject('Type')->enumValues()));
        $type->setRightTitle('Determines what fields are displayed in the CMS, and what content is displayed in the front-end.');

        $contentField = $content = HtmlEditorField::create('Content');
        $content->setRows(15);

        if ($this->Type == 'Content') {
            $fields->addfieldToTab('Root.Main', $contentField);
        }

        if ($this->Type == 'Image') {
            $fields->addfieldToTab('Root.Main', $image = UploadField::create('Image'));
            $image->setAllowedExtensions(array(
                'jpeg',
                'jpg',
                'gif',
                'png'
            ));
            $image->setFolderName('Uploads/site-builder/items');
            $fields->addfieldToTab('Root.Main', HeaderField::create('', 'Link (optional)', 4));
            $fields->addfieldToTab('Root.Main',
                TreeDropdownField::create('InternalLinkID', 'Internal Link', 'SiteTree'));
            $fields->addfieldToTab('Root.Main', $externalLink = TextField::create('ExternalLink'));
            $externalLink->setAttribute('placeholder', 'http://');
            $externalLink->setRightTitle('Setting an external link will override the internal link.');
            $fields->addfieldToTab('Root.Main', $linkSettings = FieldGroup::create(array(
                CheckboxField::create('OpenLinkInNewTab', 'Open link in a new tab?')
            )));
            $linkSettings->setTitle('Settings');
            $fields->addfieldToTab('Root.Main', HeaderField::create('', 'Content (optional)', 4));
            $fields->addfieldToTab('Root.Main', $contentField);
        }

        if ($this->Type == 'Video') {
            $fields->addFieldToTab('Root.Main', TextareaField::create('YoutubeLink'));
            $fields->addFieldToTab('Root.Main', TextareaField::create('VimeoLink'));
        }

        if ($this->Type == 'Slider') {
            $config = GridFieldConfig_RelationEditor::create(10);
            $config->addComponent(GridFieldOrderableRows::create('SortOrder'))
                ->addComponent(new GridFieldDeleteAction());
            $gridField = GridField::create(
                'SliderItems',
                'Items',
                $this->SliderItems(),
                $config
            );
            $fields->addFieldToTab('Root.Main', $gridField);
        }

        return $fields;
    }

    /**
     * @return bool|mixed|string
     */
    public function getLink()
    {
        if ($internalLink = $this->InternalLink()->ID) {
            return $this->InternalLink()->Link();
        } else {
            if ($externalLink = $this->externalLink) {
                return $externalLink;
            }
        }
        return false;
    }

    /**
     * @return bool|Text
     */
    public function getVideoLink()
    {
        if ($youtubeLink = $this->YoutubeLink) {
            return $youtubeLink;
        } else {
            if ($vimeoLink = $this->VimeoLink) {
                return $vimeoLink;
            }
        }
        return false;
    }

    /**
     * @return HTMLText
     */
    public function forTemplate()
    {
        switch ($this->Type) {
            case 'Content':
                $template = 'SiteBuilderItem_Content';
                break;
            case 'Image':
                $template = 'SiteBuilderItem_Image';
                break;
            case 'Video':
                $template = 'SiteBuilderItem_Video';
                break;
            case 'Slider':
                $template = 'SiteBuilderItem_Slider';
                break;
            default:
                $template = 'SiteBuilderItem_Content';
        }
        return $this->renderWith($template);

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

    public function getClasses()
    {
        $classes = '';
        $columnClass = 'is-column-';
        $classes .= $columnClass . $this->SizeX;

        return $classes;
    }

}