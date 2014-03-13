<?php
namespace Catalog\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ShowList extends AbstractHelper
{
    public function __invoke($list, $idParent)
    {
        echo '<ul>';
        foreach ($list as $elem) {
            if ($elem['idParent'] == $idParent) {
                echo '<li>';
                echo $elem['name'];
                echo '</li>';
                $this->__invoke($list, $elem['id']);
            }
        }
        echo '</ul>';
    }
}