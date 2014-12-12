<?php

namespace Article\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Stdlib\DateTime;
use Zend\Dom\Query;

use Article\Entity\Article as ArticleEntity;

class EditController extends AbstractActionController
{
    const ARTICLE_ENTITY  = 'Article\Entity\Article';
    const USER_ENTITY     = 'User\Entity\User';

    protected $em;
    protected $imageFolder = './public/img/article/';
    private $currentUser;
    private $translitService;
    private $cache;

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
        $articles = $this->getEntityManager()->getRepository(self::ARTICLE_ENTITY)->findAll();

        return new ViewModel(array(
                'articles' => $articles
            ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();

            $user = $this->getEntityManager()->getRepository(self::USER_ENTITY)->findOneBy(array('id' => $this->currentUser));

            // Транслит title
            $translitedTitle  = $this->translitService->getTranslit($postData['title']);
            $postData['text'] = $this->imageReplace($translitedTitle, $postData['text']);

            $article = new ArticleEntity();

            $article->setDate(new \DateTime());
            $article->setIdUser($user);
            $article->setTitle($postData['title']);
            $article->setTranslit($translitedTitle);
            $article->setText('<div class="article-text">' . $this->stripTags($postData['text']) . '</div>');

            $this->getEntityManager()->persist($article);
            $this->getEntityManager()->flush();

            $this->cache->removeItem('articles');

            return $this->redirect()->toRoute('edit-article');
        }

        return new ViewModel();
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('edit-article');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = $this->getEntityManager()->getRepository(self::USER_ENTITY)->findOneBy(array('id' => $this->currentUser));

            $postData = (array)$request->getPost();

            // Транслит title
            $translitedTitle  = $this->translitService->getTranslit($postData['title']);
            $postData['text'] = $this->imageReplace($translitedTitle, $postData['text']);

            $qb = $this->getEntityManager()->createQueryBuilder();

            $qu = $qb->update(self::ARTICLE_ENTITY, 'a')
                ->set('a.idUser',   '?1')
                ->set('a.title',    '?2')
                ->set('a.text',     '?3')
                ->set('a.translit', '?4')
                ->set('a.date',     '?5')
                ->where('a.id = ?6')
                ->setParameter(1, $user)
                ->setParameter(2, $postData['title'])
                ->setParameter(4, $this->translitService->getTranslit($postData['title']))
                ->setParameter(3, '<div class="article-text">' .$this->stripTags($postData['text']) . '</div>')
                ->setParameter(5, new \DateTime())
                ->setParameter(6, $postData['id'])
                ->getQuery();
            $qu->execute();

            $this->cache->removeItem('articles');

            return $this->redirect()->toRoute('edit-article');
        }

        $content = $this->getEntityManager()->getRepository(self::ARTICLE_ENTITY)->findOneBy(array('id' => $id));

        return new ViewModel(array(
                'id'    => $content->getId(),
                'title' => $content->getTitle(),
                'text'  => $content->getText()
            ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function hideAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('edit-article');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $hide = $request->getPost('hide', 'Нет');

            if ($hide == 'Да') {
                $id = (int)$request->getPost('id');

                $brand = $this->getEntityManager()->find(self::ARTICLE_ENTITY, $id);

                // set status (null or 3 for show, 4 for hidden)
                if (!is_null($brand->getIdStatus())) {
                    $idStatus = $brand->getIdStatus()->getId();

                    if (is_null($idStatus) || $idStatus == 3) {
                        $idStatus = 4;
                    } else {
                        $idStatus = 3;
                    }
                } else {
                    $idStatus = 4;
                }

                $qb = $this->getEntityManager()->createQueryBuilder();

                $qu = $qb->update(self::ARTICLE_ENTITY, 'a')
                    ->set('a.idStatus', '?1')
                    ->where('a.id = ?2')
                    ->setParameter(1, $idStatus)
                    ->setParameter(2, $id)
                    ->getQuery();
                $qu->execute();

                $this->cache->removeItem('articles');
            }

            return $this->redirect()->toRoute('edit-article');
        }

        return new ViewModel(array(
                'id'      => $id,
                'article' => $this->getEntityManager()->find(self::ARTICLE_ENTITY, $id)
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
     * Set translit service
     *
     * @param $tanslit
     */
    public function setTranslit($tanslit)
    {
        $this->translitService = $tanslit;
    }

    /**
     * @param $translitedTitle
     * @param $text
     *
     * @return mixed
     */
    protected function imageReplace($translitedTitle, $text)
    {
        // Папка для изображений (для каждой статьи)
        if (!file_exists($this->imageFolder . $translitedTitle) && !is_dir($this->imageFolder . $translitedTitle)) {
            mkdir($this->imageFolder . $translitedTitle);
        }

        if (strpos($text, 'img') !== false) {
            $dom = new Query($text);

            $nodeList = $dom->execute('img');

            foreach ($nodeList as $node) {
                $href = $node->getAttribute('src');

                $explode    = explode('/', $href);
                $fileName   = $explode[count($explode) - 1];
                $folder     = $explode[count($explode) - 2];

                if ($folder != 'article') { continue; }

                rename($this->imageFolder . '/' .$fileName, $this->imageFolder . $translitedTitle . '/' . $fileName);

                $text = str_replace($href, '/img/article/' . $translitedTitle . '/' . $fileName, $text);
            }
        }

        return $text;
    }

    /**
     * @param $str
     *
     * @return string
     */
    protected function stripTags($str)
    {
        return strip_tags($str, '<p><a><strong><ul><ol><li><em><s><blockquote><img><hr><br><table><thead><tbody><tfoot><th><tr><td><h1><h2><h3><h4><h5><h6>');
    }

    /**
     * @return object
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }
}