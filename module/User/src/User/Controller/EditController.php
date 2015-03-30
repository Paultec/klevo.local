<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class EditController extends AbstractActionController
{
    const USER_ENTITY           = 'User\Entity\User';
    const USER_ADDITION_ENTITY  = 'User\Entity\UserAddition';

    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qs = $qb
            ->select(array('u',
                           'ua.phone', 'ua.address', 'ua.totalBuy', 'ua.discount'
            ))
            ->from(self::USER_ENTITY, 'u')
            ->join(
                self::USER_ADDITION_ENTITY, 'ua',
                'WITH', 'u.id = ua.idUser'
            )
            ->orderBy('u.username', 'ASC');

        $adapter   = new DoctrineAdapter(new ORMPaginator($qs, false));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber(1)
            ->setItemCountPerPage(40);

        return new ViewModel(array(
            'paginator' => $paginator
        ));
    }

    /**
     * @return mixed
     */
    public function discountAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();

            if (isset($postData['idUser'])) {
                $userAddition = $this->getEntityManager()
                    ->getRepository(self::USER_ADDITION_ENTITY)->findOneBy(array('idUser' => $postData['idUser']));

                $userAddition->setDiscount($postData['discount']);

                $this->getEntityManager()->persist($userAddition);
                $this->getEntityManager()->flush();
            }
        }

        return $this->redirect()->toRoute('user-info');
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
}