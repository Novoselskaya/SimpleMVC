<?php
namespace application\controllers;

use application\models\ArticleModel;
use application\models\CategoryModel;
use ItForFree\SimpleMVC\Router\WebRouter;

/**
 * Контроллер для работы с категориями
 */
class CategoryController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'cms-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['?', '@'], 'actions' => ['archive']],
    ];
    
    /**
     * Архив статей по категории
     */
    public function archiveAction()
    {
        $results = array();
        
        $categoryId = isset($_GET['categoryId']) ? (int)$_GET['categoryId'] : null;
        
        $Article = new ArticleModel();
        $data = $Article->getListFiltered(1000000, $categoryId, null, "publicationDate DESC", true);
        $results['articles'] = $data['results'];
        $results['totalRows'] = $data['totalRows'];
        
        // Получаем все категории
        $Category = new CategoryModel();
        $categoryData = $Category->getList();
        $results['categories'] = array();
        foreach ($categoryData['results'] as $category) {
            $results['categories'][$category->id] = $category;
        }
        
        // Получаем текущую категорию
        $results['category'] = null;
        if ($categoryId) {
            $results['category'] = $Category->getById($categoryId);
        }
        
        if ($results['category']) {
            $results['pageHeading'] = $results['category']->name;
            $results['pageTitle'] = $results['pageHeading'] . " | Простой CMS блог";
        } else {
            $results['pageHeading'] = "Архив статей";
            $results['pageTitle'] = $results['pageHeading'] . " | Простой CMS блог";
        }
        
        $this->view->addVar('results', $results);
        $this->view->render('category/archive.php');
    }
}

