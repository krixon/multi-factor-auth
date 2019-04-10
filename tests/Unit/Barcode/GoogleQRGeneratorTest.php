<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\GoogleQRGenerator;
use Krixon\MultiFactorAuth\Barcode\Options;
use Krixon\MultiFactorAuth\Barcode\TimeBasedData;
use Krixon\MultiFactorAuth\HTTP\Client;
use Krixon\MultiFactorAuthTests\TestCase;
use ReflectionException;

class GoogleQRGeneratorTest extends TestCase
{
    /**
     * @dataProvider correctRequestProvider
     *
     * @param Data         $data
     * @param Options|null $options
     * @param array        $expected
     * @param Options|null $defaults
     *
     * @throws ReflectionException
     */
    public function testSendsCorrectRequest(
        Data $data,
        Options $options = null,
        array $expected = [],
        Options $defaults = null
    ) : void {
        /** @noinspection SpellCheckingInspection */
        $expected += [
            'size'   => '200x200',
            'ec'     => 'L',
            'margin' => 1,
            'data'   =>
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3Fsecret%3D1234567890%26' .
                'issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1',
        ];


        $expectedUri = sprintf(
            'https://chart.googleapis.com/chart?cht=qr&chs=%s&chld=%s|%d&chl=%s',
            $expected['size'],
            $expected['ec'],
            $expected['margin'],
            $expected['data']
        );

        $client = $this->createMock(Client::class);

        $client
            ->expects($this->once())
            ->method('get')
            ->with($expectedUri)
            ->willReturn(decbin('blob'));

        /** @noinspection PhpParamsInspection */
        $generator = new GoogleQRGenerator($client, $defaults);

        $generator->generateBarcode($data, $options);
    }


    public function correctRequestProvider() : array
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
        ];
    }
}
