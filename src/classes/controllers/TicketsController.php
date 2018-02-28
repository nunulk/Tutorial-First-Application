<?php

namespace Classes\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class TicketsController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $sql = 'SELECT * FROM tickets';
        $stmt = $this->db->query($sql);
        $tickets = [];
        while($row = $stmt->fetch()) {
            $tickets[] = $row;
        }
        $data = ['tickets' => $tickets];
        return $this->renderer->render($response, 'tasks/index.phtml', $data);
    }

    public function create(Request $request, Response $response)
    {
        return $this->renderer->render($response, 'tasks/create.phtml');
    }

    public function store(Request $request, Response $response)
    {
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
    }

    public function show(Request $request, Response $response, array $args)
    {
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
    }

    public function edit(Request $request, Response $response, array $args)
    {
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
    }

    public function update(Request $request, Response $response, array $args)
    {
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
    }

    public function delete(Request $request, Response $response, array $args)
    {
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
        $stmt = $this->db->prepare('DELETE FROM tickets WHERE id = :id');
        $result = $stmt->execute(['id' => $ticket['id']]);
        if (!$result) {
            throw new \Exception('could not delete the ticket');
        }
        return $response->withRedirect("/tickets");
    }
}
