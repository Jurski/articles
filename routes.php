<?php

return [
    ['GET', '/', ['App\Controllers\ArticleController', 'index']],
    ['GET', '/index', ['App\Controllers\ArticleController', 'index']],
    ['GET', '/articles/new', ['App\Controllers\ArticleController', 'createArticle']],
    ['POST', '/articles', ['App\Controllers\ArticleController', 'storeArticle']],
    ['DELETE', '/articles/{id}', ['App\Controllers\ArticleController', 'deleteArticle']],
    ['PATCH', '/articles/{id}', ['App\Controllers\ArticleController', 'storeEditArticle']],
    ['GET', '/articles/{id}', ['App\Controllers\ArticleController', 'show']],
    ['GET', '/articles/{id}/edit', ['App\Controllers\ArticleController', 'editArticle']],
    ['POST', '/articles/{id}/likes', ['App\Controllers\ArticleController', 'likeArticle']],
    ['POST', '/articles/{articleId}/comments', ['App\Controllers\CommentController', 'storeComment']],
    ['POST', '/comments/{id}/likes', ['App\Controllers\CommentController', 'likeComment']],
    ['DELETE', '/comments/{id}', ['App\Controllers\CommentController', 'deleteComment']],

    ['GET', '/404', ['App\Controllers\ErrorController', 'show']],
    ['GET', '/405', ['App\Controllers\ErrorController', 'show']],
    ['GET', '/error', ['App\Controllers\ErrorController', 'show']]
];