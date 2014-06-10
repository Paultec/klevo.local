<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use GoSession;

use Register\Entity\RegisterTable as RegisterTableEntity;

class RemainsController extends AbstractActionController
{
    const ATTRIBUTE_ENTITY          = 'Data\Entity\Attribute';
    const OPERATION_ENTITY          = 'Data\Entity\Operation';
    const PAYMENT_TYPE_ENTITY       = 'Data\Entity\PaymentType';
    const STATUS_ENTITY             = 'Data\Entity\Status';
    const STORE_ENTITY              = 'Data\Entity\Store';
    const USER_ENTITY               = 'User\Entity\User';
    const REGISTER_ENTITY           = 'Register\Entity\Register';
    const REGISTER_TABLE_ENTITY     = 'Register\Entity\RegisterTable';
    const PRODUCT_ENTITY            = 'Product\Entity\Product';
    const PRODUCT_QTY_ENTITY        = 'Product\Entity\ProductCurrentQty';
    const BRAND_ENTITY              = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY           = 'Catalog\Entity\Catalog';

    const MY_STORE_ATTRIBUTE = 1;
    /**
     * @var
     */
    protected $em = null;

    protected $fullName = null;

    public function indexAction()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 'a')
           ->add('from', 'Data\Entity\Attribute a')
           ->add('where', 'a.id = :id')
           ->setParameter('id', self::MY_STORE_ATTRIBUTE);
        $query = $qb->getQuery();
        $myAttribute = $query->getResult();

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 's')
           ->add('from', 'Data\Entity\Store s')
           ->add('where', 's.idAttrib = :idAttribute')
           ->setParameter('idAttribute', $myAttribute);
        $query = $qb->getQuery();
        $myStore = $query->getResult();

        $remainsByStore = array();

        foreach ($myStore as $store) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->add('select', 'r')
               ->add('from', 'Register\Entity\Register r')
               ->andWhere('r.idStoreTo = :idStore')
               ->setParameter('idStore', $store)
               ->add('orderBy', 'r.date');

            $query = $qb->getQuery();
            $registerIn = $query->getResult();

            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->add('select', 'r')
               ->add('from', 'Register\Entity\Register r')
               ->andWhere('r.idStoreFrom = :idStore')
               ->setParameter('idStore', $store)
               ->add('orderBy', 'r.date');

            $query = $qb->getQuery();
            $registerOut = $query->getResult();

            foreach ($registerIn as $register) {
                $qb = $this->getEntityManager()->createQueryBuilder();
                $qb->add('select', 'rt')
                   ->add('from', 'Register\Entity\RegisterTable rt')
                   ->andWhere('rt.idRegister = :idRegister')
                   ->setParameter('idRegister', $register);

                $query = $qb->getQuery();
                $registerTableIn = $query->getResult();

                while ($registerTableIn) {
                    $product = array_shift($registerTableIn);
                    $remainsByStore[$store->getId()][$product->getIdProduct()->getId()][] = $product;
                }
            }

            foreach ($registerOut as $register) {
                $qb = $this->getEntityManager()->createQueryBuilder();
                $qb->add('select', 'rt')
                    ->add('from', 'Register\Entity\RegisterTable rt')
                    ->andWhere('rt.idRegister = :idRegister')
                    ->setParameter('idRegister', $register);

                $query = $qb->getQuery();
                $registerTableOut = $query->getResult();

                while ($registerTableOut) {
                    $product = array_shift($registerTableOut);
                    $qtyOut = $product->getQty();
                    while ($qtyOut > 0) {
                        $firstIncome = array_shift($remainsByStore[$store->getId()][$product->getIdProduct()->getId()]);
                        if ($firstIncome->getQty() > $qtyOut) {
                            $qtyIncome = $firstIncome->getQty();
                            $firstIncome->setQty($qtyIncome - $qtyOut);
                            array_unshift($remainsByStore[$store->getId()][$product->getIdProduct()->getId()], $firstIncome);
                            $qtyOut = 0;
                        } elseif ($firstIncome->getQty() == $qtyOut) {
                            $qtyOut = 0;
                        } else {
                            $qtyOut = $qtyOut - $firstIncome->getQty();
                        }
                    }
                }
            }
        }

        // расчет остатков независимо от склада
//        $qb = $this->getEntityManager()->createQueryBuilder();
//        $qb->add('select', 'rt')
//           ->add('from', 'Register\Entity\RegisterTable rt');
//
//        $query = $qb->getQuery();
//        $registerTable = $query->getResult();
//
//        $remains = array();
//
//        while ($registerTable) {
//            $product = array_shift($registerTable);
//            if (!$remains) {
//                $remains[$product->getIdProduct()->getId()][] = $product;
//            } elseif (!array_key_exists($product->getIdProduct()->getId(), $remains)) {
//                $remains[$product->getIdProduct()->getId()][] = $product;
//            } else {
//                if (1 == $product->getIdOperation()->getId() || 3 == $product->getIdOperation()->getId()) {
//                    // случаи покупки товара (1) или возврата от покупателя (3)
//                    $remains[$product->getIdProduct()->getId()][] = $product;
//                } elseif (2 == $product->getIdOperation()->getId() || 4 == $product->getIdOperation()->getId() || 6 == $product->getIdOperation()->getId()) {
//                    // случаи продажи (2), возврата поставщику (4) или списания (6)
//                    $qtyOut = $product->getQty();
//                    while ($qtyOut > 0) {
//                        $firstIncome = array_shift($remains[$product->getIdProduct()->getId()]);
//                        if ($firstIncome->getQty() > $qtyOut) {
//                            $qtyIncome = $firstIncome->getQty();
//                            $firstIncome->setQty($qtyIncome - $qtyOut);
//                            array_unshift($remains[$product->getIdProduct()->getId()], $firstIncome);
//                            $qtyOut = 0;
//                        } elseif ($firstIncome->getQty() == $qtyOut) {
//                            $qtyOut = 0;
//                        } else {
//                            $qtyOut = $qtyOut - $firstIncome->getQty();
//                        }
//                    }
//                } elseif (5 == $product->getIdOperation()->getId()) {
//                    // случай перемещения товара между своими складами
//                }
//            }
//            if (!count($remains[$product->getIdProduct()->getId()])) {
//                unset($remains[$product->getIdProduct()->getId()]);
//            }
//        }

        $remainsTemp = array();
        $remains = array();
        foreach ($remainsByStore as $store => $productOnStore) {
            foreach ($productOnStore as $product) {
                foreach ($product as $item) {
                    $qty = $item->getQty();
                    $price = $item->getPrice();
                    $remains[$item->getIdProduct()->getId()][] = array('qty' => $qty,'price' => $price);
//                    if (!array_key_exists($item->getIdProduct()->getId(), $remains)) {
                        $remainsTemp[$item->getIdProduct()->getId()][] = $item;
//                    } else {
//                        $checkPrice = null;
//                        $checkItem = 0;
//                        foreach ($remains[$item->getIdProduct()->getId()] as $currentRemains) {
//                            if ($currentRemains->getPrice() == $item->getPrice()) {
//                                $checkPrice = true;
//                                ++$checkItem;
//                                var_dump($checkItem);
//                                var_dump($currentRemains);
//                            }
//                        }
//                        if ($checkPrice) {
////                            var_dump($remains[$item->getIdProduct()->getId()]);
//
////                            var_dump($item);
//                            $oldQty = $remains[$item->getIdProduct()->getId()][$checkItem - 1]->getQty();
//                            $remains[$item->getIdProduct()->getId()][$checkItem - 1]->setQty($oldQty + $item->getQty());
//                        } else {
//                            $remains[$item->getIdProduct()->getId()][] = $item;
//                        }
//                    }
                }
            }
        }

        //var_dump($remainsTemp);

        while ($remainsTemp) {
            $registerTable = array_shift($remainsTemp);
            //var_dump($registerTable);
            foreach ($registerTable as $item) {
//                $currentProduct = $item->getIdProduct();
//                $currentIdProduct = $currentProduct->getId();
//                $currentProduct->setQty($item->getQty());
//                $currentProduct->setPrice($item->getPrice());
//                var_dump($currentIdProduct);
//                var_dump($item->getQty());
//                var_dump($item->getPrice());
//                if (!isset($remains[$currentIdProduct])) {
//                    $remains[$currentIdProduct][] = $currentProduct;
//                } else {
//                    $checkPrice = false;
//                    while ($remains[$currentIdProduct]) {
//                        $remainsThisProduct = array_shift($remains[$currentIdProduct]);
//                        if ($remainsThisProduct->getPrice() == $currentProduct->getPrice()) {
//                            $qtyThisProduct = $remainsThisProduct->getQty();
//                            $remainsThisProduct->setQty($qtyThisProduct + $currentProduct->getQty());
//                            $checkPrice = true;
//                            array_unshift($remains[$currentIdProduct], $remainsThisProduct);
//                            break;
//                        } else {
//                            $tempRemains[] = $remainsThisProduct;
//                        }
//                    }
//                }
                //var_dump($remains[$currentIdProduct]);
                //var_dump($remains);
            }
//            var_dump($registerTable);
//            while ($registerTable) {
//                $item = array_shift($registerTable );
//                $currentProduct = $item->getIdProduct();
//                //var_dump($item);
//                if (!isset($remains[$currentProduct->getId()])) {
//                    $currentProduct->setQty($item->getQty());
//                    $currentProduct->setPrice($item->getPrice());
//                    $remains[$currentProduct->getId()][] = $currentProduct;
//                } else {
////                    $tempRemains = array();
////                    while ($remains[$currentProduct->getId()]) {
////                        $checkRemains = array_shift($remains[$currentProduct->getId()]);
////                        //var_dump($checkRemains);
////                        if ($checkRemains->getPrice() == $item->getPrice()) {
////                            $currentQty = $checkRemains->getQty();
////                            $checkRemains->setQty($currentQty + $item->getQty());
////                            array_unshift($remains[$currentProduct->getId()], $checkRemains);
////                            var_dump($remains);
////                            break;
////                        } else {
////                            $tempRemains[] = $checkRemains;
////                        }
////                    }
////                    $remains[$currentProduct->getId()] = $tempRemains;
////                    $currentProduct->setQty($item->getQty());
////                    $currentProduct->setPrice($item->getPrice());
////                    $remains[$currentProduct->getId()][] = $currentProduct;
//                    $count = count($remains[$currentProduct->getId()]);
//                    for ($i = 0; $i < $count; ++$i) {
//                        if ($currentProduct->getId() == $item->getIdProduct()-getId() && $remains[$currentProduct->getId()][$i]->getPrice() == $item->getPrice()) {
//                            $currentQty = $remains[$currentProduct->getId()][$i]->getQty();
//                            $remains[$currentProduct->getId()][$i]->getQty($currentQty + $item->getQty());
//                        } else {
//
//                        }
//                    }
//                }
//            }
        }

        return new ViewModel(array(
            'remains' => $remains,
            //'remainsByStore' => $remainsByStore,
        ));
    }

    public function getDetailAction()
    {
        return new ViewModel();
    }

    /**
     * @return array|object
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }

    /**
     * Get full category name with parent category
     * @param $id
     *
     * @return mixed
     */
    public function getFullNameCategory($id)
    {
        $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $id);
        $fullName = $category->getName();

        if (null == $category->getIdParent()) {
            if (!$this->fullName) {
                $this->fullName = $fullName;
            }

            return $this->fullName;
        } else {
            $parent = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $category->getIdParent());
            $parentName = $parent->getName();

            if ($this->fullName) {
                $this->fullName = $parentName . " :: " . $this->fullName;
            } else {
                $this->fullName = $parentName . " :: " . $fullName;
            }
        }

        return $this->getFullNameCategory($parent->getId());
    }
}

