<?php

namespace Gallery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Paginator\Paginator;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Gd\Imagine;

use Gallery\Entity\Gallery as GalleryEntity;

use Gallery\Form;

class IndexController extends AbstractActionController
{
    const GALLERY_ENTITY = 'Gallery\Entity\Gallery';
    const STATUS_ENTITY  = 'Data\Entity\Status';
    const USER_ENTITY    = 'User\Entity\User';

    protected $em;
    protected $cache;
    protected $articleLimit = 5;
    protected $imageFolder  = './public/img/gallery/';
    private $currentUser;

    public function __construct()
    {
        $auth = new AuthenticationService();
        $this->currentUser = $auth->getIdentity();
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
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

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qs = $qb
            ->select(array('g'))
            ->from(self::GALLERY_ENTITY, 'g')
            ->where('g.idStatus != 4')
            ->orderBy('g.id', 'DESC');

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($qs));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(5);

        $index = new ViewModel(array(
            'isLogin'   => $this->currentUser,
            'paginator' => $paginator,
            'articles'  => $qr
        ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index');
        $index->addChild($catalog, 'catalog');

        return $index;
    }

    /**
     * @return ViewModel
     */
    public function addAction()
    {
        $form = new Form\ImgUploadForm('img-file-form');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $formData = $form->getData();

                $user = $this->getEntityManager()->getRepository(self::USER_ENTITY)->findOneBy(array('id' => $this->currentUser));

                $imagePath = $this->imgManipulation($formData, $user);

                $gallery = new GalleryEntity();
                $gallery->setIdStatus($this->getEntityManager()->getRepository(self::STATUS_ENTITY)->findOneBy(array('id' => 4)));
                $gallery->setIdUser($user);
                $gallery->setDate(new \DateTime());
                $gallery->setComment($formData['comment'] ?: null);
                $gallery->setImg($imagePath);

                $this->getEntityManager()->persist($gallery);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('gallery');
            }
        }

        return new ViewModel(array(
            'isLogin' => $this->currentUser,
            'form'    => $form
        ));
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
     * @param $uploadedFile
     * @param $user
     *
     * @return string
     */
    protected function imgManipulation($uploadedFile, $user)
    {
        $email   = $user->getEmail();
        $explode = explode(DIRECTORY_SEPARATOR, $uploadedFile['image']['tmp_name']);

        $dir      = md5($email);
        $fileName = $explode[1];

        if (!is_dir($this->imageFolder . $dir)) {
            mkdir($this->imageFolder . $dir);
        }

        rename($this->imageFolder . $fileName, $this->imageFolder . $dir . '/' . $fileName);

        // resize
        $imagine = new Imagine();
        $image   = $imagine->open($this->imageFolder . $dir . '/' . $fileName);

        $size    = $image->getSize();
        $width   = $size->getWidth();
        $height  = $size->getHeight();

        $imageOrientation = ($width - $height) > 0 ? 'landscape' : 'portrait';

        if ($imageOrientation == 'landscape') {
            $rate = $width / $height;

            $image->resize(new Box(640, (640 / $rate)))->save();
        } else {
            $rate = $height / $width;

            $image->resize(new Box((640 / $rate), 640))->save();
        }

        return $dir . '/' . $fileName;
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