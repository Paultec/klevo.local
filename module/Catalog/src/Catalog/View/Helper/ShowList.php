<?php
namespace Catalog\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ShowList extends AbstractHelper
{
    public function __invoke($list, $idParent = null)
    {
        $tmp = array();
        /**
         * @todo think about the best solution
         */
        for ($i = 0, $item = count($list); $i < $item; $i++) {
            if (!is_null($list[$i]['idParent']) && !in_array($list[$i]['idParent'], $tmp)) {
                $tmp[] = $list[$i]['idParent'];
            }
        }

        echo '<div class="accordion">';

        foreach ($list as $elem) {
            if ($elem['idParent'] == $idParent) {
                if (in_array($elem['id'], $tmp)) {
                    echo '<h3 class="filled">' . $elem['name'] . '</h3>';

                    $this->__invoke($list, $elem['id']);
                } else {
                    echo '<a href="/product/' . $elem['id'] . '" class="empty">' . $elem['name'] . '</a>';

                    $this->__invoke($list, $elem['id']);
                }
            }
        }

        echo '</div>';
    }
}