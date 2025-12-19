$(function(){
    console.log('Привет, это новый js ))');
    init_get();
    init_post();
    init_new_buttons();
});

function init_get() 
{
    $('a.ajaxArticleBodyByGet').one('click', function(){
        // Пропускаем NEW кнопки
        if ($(this).text().includes('NEW')) {
            return true;
        }
        
        var contentId = $(this).attr('data-contentId');
        console.log('GET запрос для статьи:', contentId); 
        showLoaderIdentity();
        
        $.ajax({
            url:'/ajax/showContentsHandler.php?articleId=' + contentId, 
            dataType: 'json'
        })
        .done (function(obj){
            hideLoaderIdentity();
            console.log('GET ответ получен');
            if (obj.success) {
                $('li.article-' + contentId).find('.content').html(obj.content);
            } else {
                alert('Ошибка: ' + obj.message);
            }
        })
        .fail(function(xhr, status, error){
            hideLoaderIdentity();
            console.log('Ошибка GET:', error);
            alert('Ошибка загрузки (GET)');
        });
        
        return false;
    });  
}

function init_post() 
{
    $('a.ajaxArticleBodyByPost').one('click', function(){
        // Пропускаем NEW кнопки
        if ($(this).text().includes('NEW')) {
            return true;
        }
        
        var contentId = $(this).attr('data-contentId');
        console.log('POST запрос для статьи:', contentId);
        showLoaderIdentity();
        
        $.ajax({
            url:'/ajax/showContentsHandler.php', 
            dataType: 'json',
            data: { articleId: contentId },
            method: 'POST'
        })
        .done (function(obj){
            hideLoaderIdentity();
            console.log('POST ответ получен', obj);
            if (obj.success) {
                $('li.article-' + contentId).find('.content').html(obj.content);
            } else {
                alert('Ошибка: ' + obj.message);
            }
        })
        .fail(function(xhr, status, error){
            hideLoaderIdentity();
            console.log('Ошибка POST:', error);
            alert('Ошибка загрузки (POST)');
        });
        
        return false;
    });  
}

function init_new_buttons() 
{
    // Обработка NEW кнопок
    $('a.ajaxArticleBodyByPost, a.ajaxArticleBodyByGet').filter(function() {
        return $(this).text().includes('NEW');
    }).one('click', function(e) {
        e.preventDefault();
        var contentId = $(this).attr('data-contentId');
        var method = $(this).hasClass('ajaxArticleBodyByPost') ? 'POST' : 'GET';
        console.log(method + ' NEW кнопка для статьи:', contentId);
        
        show_new_full_article(contentId, $(this));
    });
}

function show_new_full_article(articleId, button) {
    var articleElement = $('li.article-' + articleId);
    
    if (!articleElement.length) {
        console.error('Статья не найдена:', articleId);
        alert('Статья не найдена');
        return;
    }
    
    var contentElement = articleElement.find('.content');
    if (!contentElement.length) {
        console.error('Элемент контента не найден');
        return;
    }
    
    // Если уже раскрыто - переходим на страницу статьи
    if (contentElement.hasClass('expanded')) {
        console.log('Контент уже раскрыт - переход к статье');
        window.location.href = '.?action=viewArticle&articleId=' + articleId;
        return;
    }
    
    // Показываем загрузку
    var originalContent = contentElement.html();
    contentElement.html('<em>Загрузка полного текста...</em>');
    
    // Делаем AJAX запрос
    $.ajax({
        url: 'index.php?action=getArticleContent&articleId=' + articleId,
        dataType: 'json',
        method: 'GET'
    })
    .done(function(data) {
        console.log('NEW ответ:', data);
        
        if (data.success && data.content) {
            // Показываем полный контент
            contentElement.html(data.content);
            contentElement.addClass('expanded');
            
            // Меняем текст всех NEW кнопок этой статьи
            articleElement.find('a[href*="viewArticle"]').filter(function() {
                return $(this).text().includes('NEW');
            }).text('Перейти к статье');
            
            console.log('Контент успешно раскрыт');
        } else {
            throw new Error(data.message || 'Контент не получен');
        }
    })
    .fail(function(xhr, status, error) {
        console.error('NEW ошибка:', error);
        contentElement.html('<em style="color: red;">Ошибка загрузки: ' + error + '</em>');
        
        // Восстанавливаем оригинальный контент через 3 секунды
        setTimeout(function() {
            contentElement.html(originalContent);
            contentElement.removeClass('expanded');
        }, 3000);
    });
}

function showLoaderIdentity() {
    $("#loader-identity").show("slow");
}

function hideLoaderIdentity() {
    $("#loader-identity").hide();  
}