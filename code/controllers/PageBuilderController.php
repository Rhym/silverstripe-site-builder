<?php

/**
 * Class PageBuilderController
 */
class PageBuilderController extends Page_Controller
{

    /**
     * @var array
     */
    public static $allowed_actions = array(
        'changeSizeX'
    );

    /**
     * @var array
     */
    private static $url_handlers = array(
        'changeSizeX/$ID/$X' => 'changeSizeX'
    );

    /** Get data object by ID and return the object */

    /** Create new Item */

    /** Delete Item */

    /** Change item's size */

    /** Change Items Sort Order */

    /**
     * @return bool
     * @throws ValidationException
     * @throws null
     */
    public function changeSizeX()
    {
        /** =========================================
         * @var PageBuilderItem $pageItem
        ===========================================*/

        $id = $this->getRequest()->param('ID');
        $x = $this->getRequest()->param('X');
        if (Director::is_ajax()) {
            $pageItem = DataObject::get_by_id('PageBuilderItem', $id);
            if ($pageItem) {
                $pageItem->SizeX = $x;
                $pageItem->write();
            }
        } else {

        }
        return true;
    }

}