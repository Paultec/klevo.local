<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;

class IndexController extends AbstractActionController
{
    const PRODUCT_ENTITY = 'Product\Entity\Product';

    protected $em;
    protected $cache;
    protected $articleLimit = 5;
    protected $topStatus    = 5;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $topProductsArray = array();

        // articles
        if (!$this->cache->hasItem('articles')) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qs =  $qb
                ->select(array('a'))
                ->from('Article\Entity\Article', 'a')
                ->where(
                    $qb->expr()->orX('a.idStatus != 4', 'a.idStatus IS NULL')
                )
                ->orderBy('a.id', 'DESC')
                ->setMaxResults($this->articleLimit)
                ->getQuery();
            $qs->execute();

            try {
                $qr = $qs->getArrayResult();
            } catch (\Exception $e) {
                $qr = null;
            }

            $this->cache->setItem('articles', serialize($qr));
        } else {
            $qr = unserialize($this->cache->getItem('articles'));
        }

        // top products
        if (!$this->cache->hasItem('topProduct')) {
            $topProducts = $this->getEntityManager()->getRepository(self::PRODUCT_ENTITY)
                ->findBy(array('idStatus' => $this->topStatus));

            $count = 0;
            foreach ($topProducts as $productItem) {
                $topProductsArray[$count]['href']   = $productItem->getTranslit();
                $topProductsArray[$count]['img']    = $productItem->getImg();
                $topProductsArray[$count]['title']  = $productItem->getName();

                $count++;
            }

            $this->cache->setItem('topProduct', serialize($topProductsArray));
        } else {
            $topProductsArray = unserialize($this->cache->getItem('topProduct'));
        }

        $index = new ViewModel(array(
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'articles'      => $qr,
            'top'           => !empty($topProductsArray) ? array_chunk($topProductsArray, 4) : null
        ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index');
        $index->addChild($catalog, 'catalog');

        return $index;
    }

    /**
     * @return ViewModel
     */
    public function paymentDeliveryAction()
    {
        return new ViewModel();
    }

    /**
     * @return ViewModel
     */
    public function contactsAction()
    {
        return new ViewModel();
    }

    /**
     * @return mixed
     */
    public function feedbackAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost()->toArray();

            if (empty($postData['email']) || empty($postData['subject']) || empty($postData['text'])) {
                $this->flashMessenger()->addMessage('При отправки сообщения, произошла ошибка.
                                                    Скорее всего вы заполнили не все данные.
                                                    Если ошибка будет повторяться -
                                                    свяжитесь с нами позвонив по телефону.');
            }

            if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
                $this->flashMessenger()->addMessage('При отправки сообщения, произошла ошибка.
                                                    Скорее всего вы некорректный email.
                                                    Если ошибка будет повторяться -
                                                    свяжитесь с нами позвонив по телефону.');
            }

            array_walk($postData, function(&$item) {
                $item = strip_tags($item);
            });

            $mail = new Mail\Message();

            $mail->setBody($postData['text'])
                 ->setFrom('klevo@mail.com.ua', 'From Site')
                 ->addTo('fake@email.com', 'Fake User')
                 ->setSubject($postData['subject']);

            try {
                $transport = new Mail\Transport\Sendmail();
                $transport->send($mail);

                $this->flashMessenger()->addMessage('Ваше письмо отправлено, мы ответим вам в ближайшее время!');
            } catch(\Exception $e) {
                $this->flashMessenger()->addMessage('При отправки сообщения, произошла ошибка.
                                                    Свяжитесь с нами позвонив по телефону.');
            }
        }

        return $this->redirect()->toRoute('home');
    }

    /**
     * Set cache from factory
     *
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
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