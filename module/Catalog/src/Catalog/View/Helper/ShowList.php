<?php
namespace Catalog\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class ShowList
 * @package Catalog\View\Helper
 */
class ShowList extends AbstractHelper
{
    private $newList = array();
    private $tmp     = array();

    /**
     * @param $list
     * @param null $idParent
     */
    public function __invoke($list, $idParent = null)
    {
        if (empty($this->newList)) {
            for ($i = 0, $item = count($list); $i < $item; $i++) {
                // Если статус NULL или 3 - показать -> перенес в IndexController
                //if (is_null($list[$i]['idStatus']) || ($list[$i]['idStatus'] == 3)) {
                    if (is_object($list[$i]['idParent'])) {
                        $this->newList[$i] = $list[$i];
                        $this->newList[$i]['idParent'] = (int)$list[$i]['idParent']->getId();
                    } else {
                        $this->newList[$i] = $list[$i];
                    }
                //}
            }

            /**
             * @todo think about the best solution
             */
            for ($i = 0, $item = count($this->newList); $i < $item; $i++) {
                if (!in_array($this->newList[$i]['idParent'], $this->tmp)) {

                    $this->tmp[] = $this->newList[$i]['idParent'];
                }
            }
        }

        echo '<div class="accordion">' . "\n";

        foreach ($this->newList as $elem) {
            if ($elem['idParent'] == $idParent) {
                if (in_array($elem['id'], $this->tmp)) {
                    echo '<h3 class="filled">' . $elem['name'] . '</h3>' . "\n";

                    $this->__invoke($this->newList, $elem['id']);
                } else {
                    echo '<a href="/product/' . $elem['translit'] . '" class="empty catalog-link">' . $elem['name'] . '</a>' . "\n";
//                    echo $this->partialLoop('catalog/partial/categoryLoop.phtml', $elem);

//                    echo '<a class="empty brand-link" href="'. $this->url('product/catalog', array('category' => $elem['translit'])) .'">'. $elem['name'] .'</a>';

                    $this->__invoke($this->newList, $elem['id']);
                }
            }
        }

        echo '</div>' . "\n";
    }
}