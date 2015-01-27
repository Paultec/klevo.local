<?php

namespace Gallery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EditController extends AbstractActionController
{
    const GALLERY_ENTITY = 'Gallery\Entity\Gallery';

    protected $em;
    protected $imageFolder  = './public/img/gallery/';

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();

            if (isset($postData['gallery-remove'])) {
                $this->removeGalleryItem($postData['id']);
            }

            $qb = $this->getEntityManager()->createQueryBuilder();

            $qu = $qb->update(self::GALLERY_ENTITY, 'g')
                ->set('g.idStatus', '?1')
                ->where('g.id = ?2')
                ->setParameter(1, $postData['status'])
                ->setParameter(2, $postData['id'])
                ->getQuery();
            $qu->execute();
        }

        $gallery = $this->getEntityManager()->getRepository(self::GALLERY_ENTITY)->findAll();

        return new ViewModel(array(
            'gallery' => array_reverse($gallery)
        ));
    }

    /**
     * @param $id
     *
     * @return array|\Zend\Http\Response
     */
    protected function removeGalleryItem($id)
    {
        $galleryItem = $this->getEntityManager()->getRepository(self::GALLERY_ENTITY)->findOneBy(array('id' => $id));

        $this->getEntityManager()->remove($galleryItem);
        $this->getEntityManager()->flush();

        unlink($this->imageFolder . $galleryItem->getImg());

        return $this->prg('/edit-gallery', true);
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