<?php
namespace application\controllers;

use application\models\ArticleModel;
use application\models\CategoryModel;
use application\models\SubcategoryModel;
use ItForFree\SimpleMVC\Router\WebRouter;

/**
 * Контроллер для работы с подкатегориями
 */
class SubcategoryController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'cms-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['?', '@'], 'actions' => ['archive']],
    ];
    
    /**
     * Архив статей по подкатегории
     */
    public function archiveAction()
    {
        $results = array();
        
        $subcategoryId = isset($_GET['subcategoryId']) ? (int)$_GET['subcategoryId'] : null;
        
        if (!$subcategoryId) {
            $this->redirect(WebRouter::link("homepage/index"));
            return;
        }
        
        $Subcategory = new SubcategoryModel();
        $results['subcategory'] = $Subcategory->getById($subcategoryId);
        
        if (!$results['subcategory']) {
            $this->redirect(WebRouter::link("homepage/index"));
            return;
        }
        
        $Article = new ArticleModel();
        $data = $Article->getListFiltered(1000000, null, $subcategoryId, "publicationDate DESC", true);
        $results['articles'] = $data['results'];
        $results['totalRows'] = $data['totalRows'];
        
        // Получаем все категории
        $Category = new CategoryModel();
        $categoryData = $Category->getList();
        $results['categories'] = array();
        foreach ($categoryData['results'] as $category) {
            $results['categories'][$category->id] = $category;
        }
        
        $results['pageHeading'] = $results['subcategory']->name;
        $results['pageTitle'] = $results['pageHeading'] . " | Простой CMS блог";
        
        $this->view->addVar('results', $results);
        $this->view->render('subcategory/archive.php');
    }
}

