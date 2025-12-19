<?php
namespace application\controllers;

use application\models\ArticleModel;
use application\models\CategoryModel;
use application\models\SubcategoryModel;
use ItForFree\SimpleMVC\Router\WebRouter;

/**
 * Контроллер для работы со статьями
 */
class ArticleController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'cms-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['?', '@'], 'actions' => ['view']],
    ];
    
    /**
     * Просмотр статьи
     */
    public function viewAction()
    {
        if (!isset($_GET["articleId"]) || !$_GET["articleId"]) {
            $this->redirect(WebRouter::link("homepage/index"));
            return;
        }
        
        $results = array();
        $articleId = (int)$_GET["articleId"];
        
        $Article = new ArticleModel();
        $results['article'] = $Article->getById($articleId);
        
        if (!$results['article']) {
            $this->redirect(WebRouter::link("homepage/index"));
            return;
        }
        
        $results['category'] = null;
        if ($results['article']->categoryId) {
            $Category = new CategoryModel();
            $results['category'] = $Category->getById($results['article']->categoryId);
        }
        
        $results['subcategory'] = null;
        if ($results['article']->subcategory_id) {
            $Subcategory = new SubcategoryModel();
            $results['subcategory'] = $Subcategory->getById($results['article']->subcategory_id);
        }
        
        $results['pageTitle'] = $results['article']->title . " | Простой CMS блог";
        
        $this->view->addVar('results', $results);
        $this->view->render('article/view.php');
    }
}

