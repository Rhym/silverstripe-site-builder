<?php

/**
 * Class SiteBuilder
 *
 * @property DataList $list
 */
class SiteBuilder extends FormField
{

    /**
     * @var string
     */
    private static $loading_icon = 'fa fa-cog fa-spin';

    /**
     * @var null
     */
    protected static $list = null;

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'siteBuilderAddContainer',
        'siteBuilderDeleteContainer',
        'siteBuilderAddItem',
        'siteBuilderChangeItemSize',
        'siteBuilderDeleteItem',
        'siteBuilderSort',
        'siteBuilderReDraw'
    );

    /**
     * @param string $name
     * @param null $title
     * @param SS_List|null $dataList
     * @param null $gridFieldName
     * @param null $pageID
     * @param null $config
     */
    public function __construct(
        $name,
        $title = null,
        SS_List $dataList = null,
        $gridFieldName = null,
        $pageID = null,
        $config = null
    ) {
        parent::__construct($name, $title, null);
        $this->GridFieldName = $gridFieldName;
        $this->PageID = $pageID;
        if ($dataList) {
            $this->setList($dataList);
        }
    }

    /**
     * @param SS_List $list
     * @return $this
     */
    public function setList(SS_List $list)
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @return DataList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param $id
     * @param $children
     * @return string
     */
    protected function newContainer($id, $children = null)
    {

        $content = array(
            'header' => '',
            'content' => ''
        );

        $container = DataObject::get_by_id('PageBuilderContainer', $id);

        /** -----------------------------------------
         * Header
         * ----------------------------------------*/

        //@TODO stop hacking this.
        $editLink = Controller::join_links(str_replace($this->Name, '',
                $this->Link()) . $this->GridFieldName . '/item/', $id, 'edit');
        $childrenEditLink = Controller::join_links(str_replace($this->Name, '',
                $this->Link()) . $this->GridFieldName . '/item/', $id);
        $contentActions = FormField::create_tag(
            'a',
            array(
                'class' => 'ss-ui-button action js-action ss-ui-action-constructive site-builder__actions__item site-builder__action',
                'href' => $this->Link() . '/siteBuilderAddItem?ParentID=' . (int)$id
            ),
            _t('SiteBuilder.ADDITEM', 'Add Item') . ' <i class="icon ' . $this->config()->loading_icon . '"></i>'
        );
        $contentActions .= FormField::create_tag(
            'a',
            array(
                'class' => 'ss-ui-button action',
                'href' => $editLink
            ),
            _t('SiteBuilder.EDITITEM', '<i class="fa fa-pencil-square-o"></i>')
        );
        $contentActions .= FormField::create_tag(
            'a',
            array(
                'class' => 'ss-ui-button js-action action',
                'data-confirm' => 'true',
                'href' => $this->Link() . '/siteBuilderDeleteContainer?ContainerID=' . (int)$id
            ),
            _t('SiteBuilder.DELELETEITEM', '<i class="fa fa-trash-o"></i>')
        );
        $content['header'] = FormField::create_tag(
            'div',
            array('class' => 'site-builder__container__header'),
            FormField::create_tag(
                'div',
                array(),
                '<span class="site-builder__container__header__heading">' . $container->Title . '</span><div class="grouped-actions">' . $contentActions . '</div>'
            )
        );

        /** -----------------------------------------
         * Children
         * ----------------------------------------*/

        $childrenList = array();
        if ($children) {
            foreach ($children as $key => $item) {
                //@TODO stop hacking this.
                $editLink = $childrenEditLink . '/ItemEditForm/field/Items/item/' . (int)$item->ID . '/edit';
                $deleteLink = $this->Link() . '/siteBuilderDeleteItem?ItemID=' . (int)$item->ID;
                $sizeLink = $this->Link() . '/siteBuilderChangeItemSize?ItemID=' . (int)$item->ID . '&Size=';
                $childrenList[] = FormField::create_tag(
                    'div',
                    array(
                        'id' => $item->ID,
                        'class' => $item->getClasses() . ' site-builder__container__content__item'
                    ),
                    '<div class="site-builder__container__content__item__content">
                        <span class="site-builder__container__content__item__content__heading">' . $item->Title . '</span>
                        <div class="grouped-actions">
                        <select class="ss-ui-select js-select action">
                          <option ' . ((int)$item->SizeX == 12 ? 'selected' : '') . ' data-action="' . $sizeLink . '12">Full</option>
                          <option ' . ((int)$item->SizeX == 6 ? 'selected' : '') . ' data-action="' . $sizeLink . '6">1/2</option>
                          <option ' . ((int)$item->SizeX == 4 ? 'selected' : '') . ' data-action="' . $sizeLink . '4">1/3</option>
                          <option ' . ((int)$item->SizeX == 8 ? 'selected' : '') . ' data-action="' . $sizeLink . '8">2/3</option>
                          <option ' . ((int)$item->SizeX == 3 ? 'selected' : '') . ' data-action="' . $sizeLink . '3">1/4</option>
                          <option ' . ((int)$item->SizeX == 9 ? 'selected' : '') . ' data-action="' . $sizeLink . '9">3/4</option>
                        </select>
                        <a class="ss-ui-button action" href="' . $editLink . '">' . _t('SiteBuilder.EDITITEM',
                        '<i class="fa fa-pencil-square-o"></i>') . '</a>
                         <a class="ss-ui-button js-action action" data-confirm="true" href="' . $deleteLink . '">' . _t('SiteBuilder.DELETEITEM',
                        '<i class="fa fa-trash-o"></i>') . '</a>
                        </div>
                    </div>'
                );
            }
            $content['content'] = FormField::create_tag(
                'div',
                array('class' => 'site-builder__container__content'),
                '<div class="row">' . implode("\n", $childrenList) . '</div>'
            );
        }

        /** -----------------------------------------
         * Return
         * ----------------------------------------*/

        return FormField::create_tag(
            'li',
            array(
                'id' => $id,
                'class' => 'site-builder__container'
            ),
            implode('', $content)
        );
    }

    /**
     * @param array $properties
     * @return string
     */
    public function FieldHolder($properties = array())
    {

        $content = array(
            'actions' => '',
            'content' => '',
            'gridfield' => ''
        );

        /** -----------------------------------------
         * Actions
         * ----------------------------------------*/

        $newContainer = FormField::create_tag(
            'a',
            array(
                'id' => $this->ID,
                'class' => 'ss-ui-action-constructive ss-ui-button js-action site-builder__actions__item site-builder__action site-builder__actions__item--new-container',
                'href' => $this->Link() . '/siteBuilderAddContainer?ParentID=' . $this->PageID
            ),
            _t('SiteBuilder.ADDCONTAINER',
                'Add Container') . ' <i class="icon ' . $this->config()->loading_icon . '"></i>'
        );

        $content['actions'] = FormField::create_tag(
            'ul',
            array('class' => 'site-builder__actions'),
            $newContainer
        );

        /** -----------------------------------------
         * Content
         * ----------------------------------------*/

        $items = $this->getList();
        $containers = array();
        foreach ($items as $key => $item) {
            /** @var PageBuilderContainer $item */
            $containers[] = $this->newContainer($item->ID, $item->Items());
        }
        $content['content'] = FormField::create_tag(
            'div',
            array(
                'data-url' => $this->Link(),
                'class' => 'site-builder'
            ),
            implode("\n", $containers)
        );

        /** -----------------------------------------
         * Attributes
         * ----------------------------------------*/

        $attributes = array_merge(parent::getAttributes(), array('data-url' => $this->Link()));

        /** -----------------------------------------
         * Return
         * ----------------------------------------*/

        return FormField::create_tag('fieldset', array(),
            FormField::create_tag('div', $attributes, implode('', $content))
        );
    }

    /**
     * @return bool
     * @throws ValidationException
     * @throws null
     */
    public function siteBuilderAddContainer()
    {
        if (Director::is_ajax()) {
            $pageID = (int)$this->getRequest()->getVars()['ParentID'];
            /** @var PageBuilderContainer $pageBuilderContainer */
            $pageBuilderContainer = PageBuilderContainer::create();
            $pageBuilderContainer->PageID = $pageID;
            $pageBuilderContainer->write();
        }
        return true;

    }

    /**
     * @return bool
     */
    public function siteBuilderDeleteContainer()
    {
        if (Director::is_ajax()) {
            $id = (int)$this->getRequest()->getVars()['ContainerID'];
            $container = DataObject::get_one('PageBuilderContainer', (int)$id);
            if ($container) {
                DataObject::delete_by_id('PageBuilderContainer', (int)$id);
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws ValidationException
     * @throws null
     */
    public function siteBuilderAddItem()
    {
        if (Director::is_ajax()) {
            $parentID = (int)$this->getRequest()->getVars()['ParentID'];
            /** @var PageBuilderItem $pageBuilderItem */
            $pageBuilderItem = PageBuilderItem::create();
            $pageBuilderItem->ContainerID = $parentID;
            $pageBuilderItem->write();
        }
        return true;
    }

    /**
     * @return bool
     * @throws ValidationException
     * @throws null
     */
    public function siteBuilderChangeItemSize()
    {
        if (Director::is_ajax()) {
            $id = (int)$this->getRequest()->getVars()['ItemID'];
            $size = (int)$this->getRequest()->getVars()['Size'];
            /** @var PageBuilderItem $pageBuilderItem */
            $item = DataObject::get_by_id('PageBuilderItem', $id);
            $item->SizeX = $size;
            $item->write();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function siteBuilderDeleteItem()
    {
        if (Director::is_ajax()) {
            $id = (int)$this->getRequest()->getVars()['ItemID'];
            $container = DataObject::get_by_id('PageBuilderItem', $id);
            if ($container) {
                DataObject::delete_by_id('PageBuilderItem', $id);
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws ValidationException
     * @throws null
     */
    public function siteBuilderSort()
    {
        if (Director::is_ajax()) {
            if ((array)$postVars = $this->getRequest()->postVars()) {
                $count = (int)1;
                foreach ($postVars['items'] as $id) {
                    /** @var DataObject $dataObject */
                    $dataObject = DataObject::get_by_id((string)$postVars['className'], (int)$id);
                    $dataObject->SortOrder = (int)$count;
                    $dataObject->write();
                    $count++;
                }
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function siteBuilderReDraw()
    {
        return $this->FieldHolder();
    }

}