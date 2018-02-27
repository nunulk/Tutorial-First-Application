<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

// 一覧表示
$app->get('/tickets', function (Request $request, Response $response) {
    return $response->write('tickets');
});

// 新規作成用フォームの表示
$app->get('/tickets/create', function (Request $request, Response $response) {
    return $this->renderer->render($response, 'tasks/create.phtml');
});

// 新規作成
$app->post('/tickets', function (Request $request, Response $response) {
    $subject = $request->getParsedBodyParam('subject');
    // ここに保存の処理を書く
    $sql = 'INSERT INTO tickets (subject) values (:subject)';
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute(['subject' => $subject]);
    if (!$result) {
        throw new \Exception('could not save the ticket');
    }

    // 保存が正常にできたら一覧ページへリダイレクトする
    return $response->withRedirect("/tickets");
});

// 表示
$app->get('/tickets/{id}', function (Request $request, Response $response, array $args) {
});

// 編集用フォームの表示
$app->get('/tickets/{id}/edit', function (Request $request, Response $response, array $args) {
});

// 更新
$app->put('/tickets/{id}', function (Request $request, Response $response, array $args) {
});

// 削除
$app->delete('/tickets/{id}', function (Request $request, Response $response, array $args) {
});

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
