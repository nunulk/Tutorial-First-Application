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
    return $this->renderer->render($response, 'tasks/index.phtml', $data);
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
    $sql = 'SELECT * FROM tickets WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute(['id' => $args['id']]);
    if (!$result) {
        throw new \Exception('could not find the ticket');
    }
    $ticket = $stmt->fetch();
    if (!$ticket) {
        return $response->withStatus(404)->write('not found');
    }
    $data = ['ticket' => $ticket];
    return $this->renderer->render($response, 'tasks/show.phtml', $data);
});

// 編集用フォームの表示
$app->get('/tickets/{id}/edit', function (Request $request, Response $response, array $args) {
    $sql = 'SELECT * FROM tickets WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute(['id' => $args['id']]);
    if (!$result) {
        throw new \Exception('could not find the ticket');
    }
    $ticket = $stmt->fetch();
    if (!$ticket) {
        return $response->withStatus(404)->write('not found');
    }
    $data = ['ticket' => $ticket];
    return $this->renderer->render($response, 'tasks/edit.phtml', $data);
});

// 更新
$app->put('/tickets/{id}', function (Request $request, Response $response, array $args) {
    $sql = 'SELECT * FROM tickets WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute(['id' => $args['id']]);
    if (!$result) {
        throw new \Exception('could not find the ticket');
    }
    $ticket = $stmt->fetch();
    if (!$ticket) {
        return $response->withStatus(404)->write('not found');
    }
    $ticket['subject'] = $request->getParsedBodyParam('subject');
    $stmt = $this->db->prepare('UPDATE tickets SET subject = :subject WHERE id = :id');
    $result = $stmt->execute($ticket);
    if (!$result) {
        throw new \Exception('could not save the ticket');
    }
    return $response->withRedirect("/tickets");
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
