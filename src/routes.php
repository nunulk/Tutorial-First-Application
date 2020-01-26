<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

// 一覧表示
$app->get('/tickets', function (Request $request, Response $response) {
    $sql = 'SELECT * FROM tickets';
    $stmt = $this->db->query($sql);
    $tickets = [];
    while($row = $stmt->fetch()) {
        $tickets[] = $row;
    }
    $data = ['tickets' => $tickets];
    return $this->renderer->render($response, 'tickets/index.phtml', $data);
});

// 新規作成用フォームの表示
$app->get('/tickets/create', function (Request $request, Response $response) {
    return $this->renderer->render($response, 'tickets/create.phtml');
});

// 新規作成
$app->post('/tickets', function (Request $request, Response $response) {
    $subject = $request->getParsedBodyParam('subject');
    // ここに保存の処理を書く
    $sql = 'INSERT INTO tickets (subject) values (:subject)';
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['subject' => $subject]);

    // 保存が正常にできたら一覧ページへリダイレクトする
    return $response->withRedirect("/tickets");
});

// 表示
$app->get('/tickets/{id}', function (Request $request, Response $response, array $args) {
    $sql = 'SELECT * FROM tickets WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $args['id']]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        return $response->withStatus(404)->write('not found');
    }
    $data = ['ticket' => $ticket];
    return $this->renderer->render($response, 'tickets/show.phtml', $data);
});

// 編集用フォームの表示
$app->get('/tickets/{id}/edit', function (Request $request, Response $response, array $args) {
    $sql = 'SELECT * FROM tickets WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $args['id']]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        return $response->withStatus(404)->write('not found');
    }
    $data = ['ticket' => $ticket];
    return $this->renderer->render($response, 'tickets/edit.phtml', $data);
});

// 更新
$app->patch('/tickets/{id}', function (Request $request, Response $response, array $args) {
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
