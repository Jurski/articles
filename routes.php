<?php

return [
    ['GET', '/index', ['App\Controllers\ArticleController', 'index']],
    ['GET', '/articles/new', ['App\Controllers\ArticleController', 'createArticle']],
    ['POST', '/articles', ['App\Controllers\ArticleController', 'storeArticle']],
    ['POST', '/articles/{id}', ['App\Controllers\ArticleController', 'storeEditArticle']],
    ['POST', '/articles/{id}/delete', ['App\Controllers\ArticleController', 'deleteArticle']],
    ['GET', '/articles/{id}', ['App\Controllers\ArticleController', 'show']],
    ['GET', '/articles/{id}/edit', ['App\Controllers\ArticleController', 'editArticle']],
    ['POST', '/articles/{id}/like', ['App\Controllers\ArticleController', 'likeArticle']],
    ['POST', '/comments/{articleId}', ['App\Controllers\ArticleController', 'storeComment']],
    ['POST', '/comments/{id}/like', ['App\Controllers\ArticleController', 'likeComment']],
    ['POST', '/comments/{id}/delete', ['App\Controllers\ArticleController', 'deleteComment']],

    ['GET', '/404', ['App\Controllers\ErrorController', 'show']],
    ['GET', '/405', ['App\Controllers\ErrorController', 'show']],
    ['GET', '/error', ['App\Controllers\ErrorController', 'show']]
];