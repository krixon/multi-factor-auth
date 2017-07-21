<?php

namespace Krixon\MultiFactorAuthTests\Unit\HTTP;

use Krixon\MultiFactorAuth\HTTP\CurlClient;
use Krixon\MultiFactorAuthTests\TestCase;
use phpmock\phpunit\PHPMock;

class CurlClientTest extends TestCase
{
    use PHPMock;


    public function testThrowsExpectedExceptionWhenGenerationFails()
    {
        $expectedData = 'FooBar';
        $curlExec     = $this->getFunctionMock('Krixon\MultiFactorAuth\HTTP', 'curl_exec');

        $curlExec->expects($this->any())->willReturn($expectedData);

        $client     = new CurlClient();
        $actualData = $client->get('http://example.com');

        static::assertSame($expectedData, $actualData);
    }
}
