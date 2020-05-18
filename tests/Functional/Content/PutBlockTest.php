<?php

namespace PlayOrPay\Tests\Functional\Content;

use Exception;
use PlayOrPay\Tests\Functional\FunctionalTest;
use Ramsey\Uuid\Uuid;

class PutBlockTest extends FunctionalTest
{
    /**
     * @test
     *
     * @throws Exception
     */
    public function should_successfully_create_a_new_one(): void
    {
        $this->applyFixtures(__DIR__ . '/../../fixtures/empty_event.yaml');

        $code = Uuid::uuid4()->toString();
        $content = 'hello world';

        $this->authorizeAsAdmin();
        $this->request('put_block', [
            'code'    => $code,
            'content' => $content,
        ]);

        $responseBody = $this->request('get_block', [
            'code' => $code,
        ])->getContent();

        $response = json_decode($responseBody, true);

        $this->assertArrayHasKey('data', $response);
        $this->assertSame($content, $response['data']['content']);
        $this->assertSame($code, $response['data']['code']);
    }
}
