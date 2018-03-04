<?php

namespace Tests\Functional;

class TicketsTest extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->container['db']->beginTransaction();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->container['db']->rollback();
    }

    public function testIndex()
    {
        $response = $this->runApp('GET', '/tickets');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('チケット一覧', (string)$response->getBody());
    }

    public function testCreate()
    {
        $response = $this->runApp('GET', '/tickets/create');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('チケット作成', (string)$response->getBody());
    }

    public function testStore()
    {
        $response = $this->runApp('POST', '/tickets', ['subject' => 'テストチケット']);

        $id = $this->container['db']->lastInsertId();
        $stmt = $this->container['db']->query('SELECT * FROM tickets WHERE id = ' . $id);
        $ticket = $stmt->fetch();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/tickets', (string)$response->getHeaderLine('Location'));
        $this->assertEquals('テストチケット', $ticket['subject']);
    }

    public function testShow()
    {
        $this->container['db']->query("INSERT INTO tickets (subject) VALUES ('テストチケット')");
        $id = $this->container['db']->lastInsertId();

        $response = $this->runApp('GET', '/tickets/' . $id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('テストチケット', (string)$response->getBody());
    }

    public function testEdit()
    {
        $this->container['db']->query("INSERT INTO tickets (subject) VALUES ('テストチケット')");
        $id = $this->container['db']->lastInsertId();

        $response = $this->runApp('GET', '/tickets/' . $id . '/edit');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('チケット編集', (string)$response->getBody());
    }

    public function testUpdate()
    {
        $this->container['db']->query("INSERT INTO tickets (subject) VALUES ('テストチケット')");
        $id = $this->container['db']->lastInsertId();

        $response = $this->runApp('PUT', '/tickets/' . $id, ['subject' => 'テストチケット（変更）']);

        $stmt = $this->container['db']->query('SELECT * FROM tickets WHERE id = ' . $id);
        $ticket = $stmt->fetch();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/tickets', (string)$response->getHeaderLine('Location'));
        $this->assertEquals('テストチケット（変更）', $ticket['subject']);
    }

    public function testDelete()
    {
        $this->container['db']->query("INSERT INTO tickets (subject) VALUES ('テストチケット')");
        $id = $this->container['db']->lastInsertId();

        $response = $this->runApp('DELETE', '/tickets/' . $id);

        $stmt = $this->container['db']->query('SELECT * FROM tickets WHERE id = ' . $id);
        $ticket = $stmt->fetch();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/tickets', (string)$response->getHeaderLine('Location'));
        $this->assertEquals($ticket, false);
    }
}
