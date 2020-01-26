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
        return $this->renderer->render($response, 'tickets/index.phtml', $data);
    }

    public function create(Request $request, Response $response)
    {
        return $this->renderer->render($response, 'tickets/create.phtml');
    }

    public function store(Request $request, Response $response)
    {
        $subject = $request->getParsedBodyParam('subject');
        // ここに保存の処理を書く
        $sql = 'INSERT INTO tickets (subject) values (:subject)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['subject' => $subject]);

        // 保存が正常にできたら一覧ページへリダイレクトする
        return $response->withRedirect("/tickets");
    }

    public function show(Request $request, Response $response, array $args)
    {
        try {
            $ticket = $this->fetchTicket($args['id']);
        } catch (\Exception $e) {
            return $response->withStatus(404)->write('not found');
        }
        $data = ['ticket' => $ticket];
        return $this->renderer->render($response, 'tickets/show.phtml', $data);
    }

    public function edit(Request $request, Response $response, array $args)
    {
        try {
            $ticket = $this->fetchTicket($args['id']);
        } catch (\Exception $e) {
            return $response->withStatus(404)->write('not found');
        }
        $data = ['ticket' => $ticket];
        return $this->renderer->render($response, 'tickets/edit.phtml', $data);
    }

    public function update(Request $request, Response $response, array $args)
    {
        try {
            $ticket = $this->fetchTicket($args['id']);
        } catch (\Exception $e) {
            return $response->withStatus(404)->write('not found');
        }
        $ticket['subject'] = $request->getParsedBodyParam('subject');
        $stmt = $this->db->prepare('UPDATE tickets SET subject = :subject WHERE id = :id');
        $stmt->execute($ticket);
        return $response->withRedirect("/tickets");
    }

    public function delete(Request $request, Response $response, array $args)
    {
        try {
            $ticket = $this->fetchTicket($args['id']);
        } catch (\Exception $e) {
            return $response->withStatus(404)->write('not found');
        }
        $stmt = $this->db->prepare('DELETE FROM tickets WHERE id = :id');
        $stmt->execute(['id' => $ticket['id']]);
        return $response->withRedirect("/tickets");
    }

    private function fetchTicket(int $id): array
    {
        $sql = 'SELECT * FROM tickets WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $ticket = $stmt->fetch();
        if (!$ticket) {
            throw new \Exception('not found');
        }
        return $ticket;
    }
}
