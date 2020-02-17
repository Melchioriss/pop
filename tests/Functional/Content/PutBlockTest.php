<?php

namespace PlayOrPay\Tests\Functional\Content;

use Exception;
use PlayOrPay\Tests\Functional\FunctionalTest;
use Ramsey\Uuid\Uuid;

class PutBlockTest extends FunctionalTest
{
    /**
     * @test
     * @throws Exception
     */
    public function should_successfully_create_a_new_one()
    {
        $this->applyFixtures(__DIR__.'/../../fixtures/default.yaml');

        $code = (string)Uuid::uuid4();
        $content = 'hello world';

        $this->authorizeAsAdmin();
        $this->request('put_block', [
            'code' => $code,
            'content' => $content,
        ]);

        $this->assertSuccessfulResponse();

        $responseBody = $this->request('get_block', [
            'code' => $code,
        ])->getContent();

        $response = json_decode($responseBody, true);

        $this->assertArrayHasKey('data', $response);
        $this->assertSame($content, $response['data']['content']);
        $this->assertSame($code, $response['data']['code']);
    }
}
