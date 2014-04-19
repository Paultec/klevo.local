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
                if (is_object($list[$i]['idParent'])) {
                    $this->newList[$i] = $list[$i];
                    $this->newList[$i]['idParent'] = (int)$list[$i]['idParent']->getId();
                } else {
                    $this->newList[$i] = $list[$i];
                }
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
                    echo '<a href="/product/' . $elem['id'] . '" class="empty">' . $elem['name'] . '</a>' . "\n";

                    $this->__invoke($this->newList, $elem['id']);
                }
            }
        }

        echo '</div>' . "\n";
    }
}