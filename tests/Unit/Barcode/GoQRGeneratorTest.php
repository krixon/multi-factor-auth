<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\GoQRGenerator;
use Krixon\MultiFactorAuth\Barcode\Options;
use Krixon\MultiFactorAuth\Barcode\TimeBasedData;
use Krixon\MultiFactorAuth\HTTP\Client;
use Krixon\MultiFactorAuthTests\TestCase;

class GoQRGeneratorTest extends TestCase
{
    /**
     * @dataProvider correctRequestProvider
     *
     * @param Data         $data
     * @param Options|null $options
     * @param array        $expected
     * @param Options|null $defaults
     */
    public function testSendsCorrectRequest(
        Data $data,
        Options $options = null,
        array $expected = [],
        Options $defaults = null
    ) {
        $expected += [
            'size'           => '200x200',
            'ec'             => 'L',
            'charset-source' => 'UTF-8',
            'format'         => 'png',
            'color'          => 'FFFFFF',
            'bgcolor'        => '000000',
            'data'           =>
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3Fsecret%3D1234567890%26' .
                'issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1',
        ];


        $expected = sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?data=%s&size=%s&charset-source=%s&ecc=%s&format=%s' .
            '&color=%s&bgcolor=%s',

            $expected['data'],
            $expected['size'],
            $expected['charset-source'],
            $expected['ec'],
            $expected['format'],
            $expected['color'],
            $expected['bgcolor']
        );

        $client = $this->createMock(Client::class);

        $client
            ->expects($this->once())
            ->method('get')
            ->with($expected)
            ->willReturn(decbin('blob'));

        /** @noinspection PhpParamsInspection */
        $generator = new GoQRGenerator($client, $defaults);

        $generator->generateBarcode($data, $options);
    }


    public function correctRequestProvider()
    {
        $timeData = new TimeBasedData('1234567890', 'Test Issuer', 'dave.lister@example.com');
        $options  = Options::default();

        return [
            // Uses default options when not supplied.
            [$timeData,],
            // Generates the same as the above when default options are explicitly supplied.
            [$timeData, null, [], $options],
            // Size variations.
            [$timeData, $options->withWidth(350)->withHeight(250), ['size' => '350x250']],
            [$timeData, $options->withWidth(350.5)->withHeight(250.145), ['size' => '350x250']],
            [$timeData, $options->withWidth('350.5')->withHeight('250.145'), ['size' => '350x250']],
            // Error correction level variations.
            [$timeData, $options->withErrorCorrectionLevel('L'), ['ec' => 'L']],
            [$timeData, $options->withErrorCorrectionLevel('M'), ['ec' => 'M']],
            [$timeData, $options->withErrorCorrectionLevel('Q'), ['ec' => 'Q']],
            [$timeData, $options->withErrorCorrectionLevel('H'), ['ec' => 'H']],
            [$timeData, $options->withErrorCorrectionLevel('UNKNOWN'), ['ec' => 'L']],
            [$timeData, $options->withErrorCorrectionLevel(''), ['ec' => 'L']],
            [$timeData, $options->withErrorCorrectionLevel('123'), ['ec' => 'L']],
            [$timeData, $options->withErrorCorrectionLevel(123), ['ec' => 'L']],
            // Format variations.
            [$timeData, $options->withFormat('png'), ['format' => 'png']],
            [$timeData, $options->withFormat('gif'), ['format' => 'gif']],
            [$timeData, $options->withFormat('jpg'), ['format' => 'jpg']],
            [$timeData, $options->withFormat('svg'), ['format' => 'svg']],
            [$timeData, $options->withFormat('eps'), ['format' => 'eps']],
            [$timeData, $options->withFormat('jpeg'), ['format' => 'jpeg']],
        ];
    }
}
