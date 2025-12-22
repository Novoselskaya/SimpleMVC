<?php
namespace application\controllers\admin;

use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;
use application\models\SubcategoryModel;
use application\models\CategoryModel;
use application\models\ArticleModel;

/**
 * Администрирование подкатегорий
 */
class AdminsubcategoriesController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'admin-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['@']],
        ['allow' => false, 'roles' => ['?']],
    ];
    
    /**
     * Список всех подкатегорий
     */
    public function indexAction()
    {
        $Subcategory = new SubcategoryModel();
        $data = $Subcategory->getList();
        $subcategories = $data['results'];
        
        // Получаем категории для отображения
        $Category = new CategoryModel();
        $categoryData = $Category->getList();
        $categories = array();
        foreach ($categoryData['results'] as $category) {
            $categories[$category->id] = $category;
        }
        
        $this->view->addVar('subcategories', $subcategories);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('totalRows', $data['totalRows']);
        $this->view->addVar('pageTitle', 'Подкатегории');
        $this->view->render('subcategory/index.php');
    }
    
    /**
     * Создание новой подкатегории
     */
    public function addAction()
    {
        $Url = Config::get('core.router.class');
        
        if (!empty($_POST)) {
            if (!empty($_POST['saveChanges'])) {
                // Обработка category_id
                if (empty($_POST['category_id']) || $_POST['category_id'] == '0') {
                    unset($_POST['category_id']);
                } else {
                    $_POST['category_id'] = (int)$_POST['category_id'];
                }
                $subcategory = new SubcategoryModel($_POST);
                $subcategory->insert();
                $this->redirect($Url::link("admin/adminsubcategories/index"));
            } 
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/adminsubcategories/index"));
            }
        } else {
            // Получаем категории для формы
            $Category = new CategoryModel();
            $categoryData = $Category->getList();
            $categories = $categoryData['results'];
            
            $subcategory = new SubcategoryModel();
            
            $this->view->addVar('subcategory', $subcategory);
            $this->view->addVar('categories', $categories);
            $this->view->addVar('formAction', 'add');
            $this->view->addVar('pageTitle', 'Новая подкатегория');
            $this->view->render('subcategory/edit.php');
        }
    }
    
    /**
     * Редактирование подкатегории
     */
    public function editAction()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $Url = Config::get('core.router.class');
            $this->redirect($Url::link("admin/adminsubcategories/index"));
            return;
        }
        
        $Url = Config::get('core.router.class');
        $Subcategory = new SubcategoryModel();
        
        if (!empty($_POST)) {
            if (!empty($_POST['saveChanges'])) {
                // Обработка category_id
                if (empty($_POST['category_id']) || $_POST['category_id'] == '0') {
                    $_POST['category_id'] = null;
                } else {
                    $_POST['category_id'] = (int)$_POST['category_id'];
                }
                $subcategory = new SubcategoryModel($_POST);
                $subcategory->update();
                $this->redirect($Url::link("admin/adminsubcategories/index"));
            } 
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/adminsubcategories/index"));
            }
        } else {
            $subcategory = $Subcategory->getById($id);
            
            if (!$subcategory) {
                $this->redirect($Url::link("admin/adminsubcategories/index"));
                return;
            }
            
            // Получаем категории для формы
            $Category = new CategoryModel();
            $categoryData = $Category->getList();
            $categories = $categoryData['results'];
            
            $this->view->addVar('subcategory', $subcategory);
            $this->view->addVar('categories', $categories);
            $this->view->addVar('formAction', 'edit');
            $this->view->addVar('pageTitle', 'Редактирование подкатегории');
            $this->view->render('subcategory/edit.php');
        }
    }
    
    /**
     * Удаление подкатегории
     */
    public function deleteAction()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $Url = Config::get('core.router.class');
            $this->redirect($Url::link("admin/adminsubcategories/index"));
            return;
        }
        
        $Url = Config::get('core.router.class');
        $Subcategory = new SubcategoryModel();
        
        if (!empty($_POST)) {
            if (!empty($_POST['deleteSubcategory'])) {
                // Проверяем, есть ли статьи в этой подкатегории
                $Article = new ArticleModel();
                $articles = $Article->getListFiltered(1000000, null, $id, "publicationDate DESC", false);
                
                if ($articles['totalRows'] > 0) {
                    $this->view->addVar('errorMessage', 'Ошибка: В подкатегории есть статьи. Удалите статьи или назначьте их другой подкатегории перед удалением подкатегории.');
                    $subcategory = $Subcategory->getById($id);
                    $this->view->addVar('subcategory', $subcategory);
                    $this->view->addVar('pageTitle', 'Удаление подкатегории');
                    $this->view->render('subcategory/delete.php');
                    return;
                }
                
                $subcategory = new SubcategoryModel($_POST);
                $subcategory->delete();
                $this->redirect($Url::link("admin/adminsubcategories/index"));
            }
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/adminsubcategories/edit&id=$id"));
            }
        } else {
            $subcategory = $Subcategory->getById($id);
            
            if (!$subcategory) {
                $this->redirect($Url::link("admin/adminsubcategories/index"));
                return;
            }
            
            $this->view->addVar('subcategory', $subcategory);
            $this->view->addVar('pageTitle', 'Удаление подкатегории');
            $this->view->render('subcategory/delete.php');
        }
    }
}

