<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\EventBasedData;
use Krixon\MultiFactorAuth\Barcode\GoogleQRGenerator;
use Krixon\MultiFactorAuth\Barcode\Options;
use Krixon\MultiFactorAuth\Barcode\TimeBasedData;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\HTTP\Client;
use Krixon\MultiFactorAuthTests\TestCase;

class GoogleQRGeneratorTest extends TestCase
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
            'size'   => '200x200',
            'ec'     => 'L',
            'margin' => 1,
            'data'   =>
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3Fsecret%3D1234567890%26' .
                'issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
        ];


        $expected = sprintf(
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
            ->with($expected)
            ->willReturn(decbin('blob'));

        /** @noinspection PhpParamsInspection */
        $generator = new GoogleQRGenerator($client, $defaults);

        $generator->generateBarcode($data, $options);
    }


    public function correctRequestProvider()
    {
        $timeData  = new TimeBasedData('1234567890', 'Test Issuer', 'dave.lister@example.com');
        $eventData = new EventBasedData('1234567890', 'Test Issuer', 'dave.lister@example.com');
        $options   = Options::default();

        return [
            // Uses default options when not supplied.
            [$timeData,],
            // Generates the same as the above when default options are explicitly supplied.
            [$timeData, null, [], $options],
            // Account name variations.
            [
                $timeData->withAccountName('Account name can be long and contain whitespace and punctuation.'),
                null,
                [
                    'data' =>
                        'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253AAccount%2520name%2520can%2520be%2520long%2520' .
                        'and%2520contain%2520whitespace%2520and%2520punctuation.%3Fsecret%3D1234567890%26issuer%3D' .
                        'Test%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
                ]
            ],
            [
                $eventData->withAccountName('Account name can be long and contain whitespace and punctuation.'),
                null,
                [
                    'data' =>
                        'otpauth%3A%2F%2Fhotp%2FTest%2520Issuer%253AAccount%2520name%2520can%2520be%2520long%2520' .
                        'and%2520contain%2520whitespace%2520and%2520punctuation.%3Fsecret%3D1234567890%26issuer%3D' .
                        'Test%2520Issuer%26digits%3D6%26counter%3D0%26algorithm%3DSHA1'
                ]
            ],
            [
                $timeData->withAccountName('Url unsafe % characters ? are encoded = correctly'),
                null,
                [
                    'data' =>
                        'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253AUrl%2520unsafe%2520%2525%2520characters' .
                        '%2520%253F%2520are%2520encoded%2520%253D%2520correctly%3Fsecret%3D1234567890%26issuer' .
                        '%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
                ]
            ],
            // Secret variations.
            [
                $timeData->withSecret('Secret can be quite long and also contain whitespace and punctuation.'),
                null,
                [
                    'data' =>
                        'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3Fsecret%3D' .
                        'Secret%2520can%2520be%2520quite%2520long%2520and%2520also%2520contain%2520whitespace' .
                        '%2520and%2520punctuation.%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26' .
                        'algorithm%3DSHA1'
                ]
            ],
            [
                $timeData->withSecret('Url unsafe % characters ? are : encoded = correctly'),
                null,
                [
                    'data' =>
                        'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3Fsecret%3DUrl%2520' .
                        'unsafe%2520%2525%2520characters%2520%253F%2520are%2520%253A%2520encoded%2520%253D%2520' .
                        'correctly%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
                ]
            ],
            // Algorithm variations.
            [
                $timeData->withAlgorithm(Algorithm::SHA1()),
                null,
                [
                    'data' =>
                        'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3F' .
                        'secret%3D1234567890%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
                ]
            ],
            [
                $timeData->withAlgorithm(Algorithm::SHA256()),
                null,
                [
                    'data' =>
                        'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3F' .
                        'secret%3D1234567890%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA256'
                ]
            ],
            [
                $timeData->withAlgorithm(Algorithm::SHA512()),
                null,
                [
                    'data' =>
                        'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3F' .
                        'secret%3D1234567890%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA512'
                ]
            ],
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
