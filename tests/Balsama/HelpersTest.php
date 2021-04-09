<?php

namespace Balsama;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{

    private $singleLevelArray = [
        'a' => 'foo',
        'b' => 'bar',
    ];

    private $multilevelArray = [
        'a' => ['foo', 'bar'],
        'b' => ['baz', 'bat'],
    ];

    public function testIncludeArrayKeysInArray()
    {
        // Test array of strings.
        $newArray = Helpers::includeArrayKeysInArray($this->singleLevelArray);
        $expected = [
            'a' => ['a', 'foo'],
            'b' => ['b', 'bar'],
        ];
        $this->assertEquals($expected['a'][0], $newArray['a'][0]);
        $this->assertEquals($expected['b'][1], $newArray['b'][1]);

        // Test array of arrays.
        $newArray = Helpers::includeArrayKeysInArray($this->multilevelArray);
        $expected = [
            'a' => ['a', 'foo', 'bar'],
            'b' => ['b', 'baz', 'bat'],
        ];
        $this->assertEquals($expected['a'][0], $newArray['a'][0]);
        $this->assertEquals($expected['b'][2], $newArray['b'][2]);
    }

    public function testFillMissingDateArrayKeys()
    {
        // Test months with single values
        $array = [
            '2020-01' => 1,
            '2020-02' => 2,
            '2020-04' => 3,
            '2020-09' => 4,
            '2020-10' => 5,
        ];
        $expectedFilledArray = [
            '2020-01' => 1,
            '2020-02' => 2,
            '2020-03' => 0,
            '2020-04' => 3,
            '2020-05' => 0,
            '2020-06' => 0,
            '2020-07' => 0,
            '2020-08' => 0,
            '2020-09' => 4,
            '2020-10' => 5,
        ];
        $filledArray = Helpers::fillMissingDateArrayKeys($array);
        $this->assertEquals(count($expectedFilledArray), count($filledArray));
        $this->assertEquals($expectedFilledArray, $filledArray);

        // Test days with array values
        $array = [
            '2020-01-02' => ['a', 'b'],
            '2020-01-03' => ['a', 'b'],
            '2020-01-05' => ['a', 'b'],
        ];
        $expectedFilledArray = [
            '2020-01-02' => ['a', 'b'],
            '2020-01-03' => ['a', 'b'],
            '2020-01-04' => [],
            '2020-01-05' => ['a', 'b'],
        ];
        $filledArray = Helpers::fillMissingDateArrayKeys($array);
        $this->assertEquals(count($expectedFilledArray), count($filledArray));
        $this->assertEquals($expectedFilledArray, $filledArray);
    }

    public function testFillLowerLevelDates()
    {
        $array = [
            'foo' => [
                '2020-01-01' => 1,
                '2020-01-02' => 2,
            ],
            'bar' => [
                '2020-01-02' => 3,
                '2020-01-03' => 4,
            ],
            'baz' => [
                '2020-01-03' => 5,
                '2020-01-04' => 6,
            ],
        ];

        $newArray = Helpers::fillLowerLevelDates($array);

        foreach ($newArray as $values) {
            $this->assertCount(4, $values);
        }
    }

    public function testImageTtfTextJustified()
    {
        $inputFilePath = __DIR__ . '/fixtures/test.jpg';
        $outputFilePath = __DIR__ . '/fixtures/testoutput.jpg';
        $controlFilePath = __DIR__ . '/fixtures/testcontrol.jpg';
        $fontFile = __DIR__ . '/fixtures/fonts/SFCompact.ttf';

        // Cleanup and assert that output and control files do not exist.
        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }
        $this->assertFileNotExists($outputFilePath);
        if (file_exists($controlFilePath)) {
            unlink($controlFilePath);
        }
        $this->assertFileNotExists($controlFilePath);

        $originalImage = imagecreatefromjpeg($inputFilePath);
        $testImage = imagecreatefromjpeg($inputFilePath);

        imagejpeg($originalImage, $inputFilePath, 100);
        imagejpeg($originalImage, $controlFilePath, 100);

        $originalImageFile = file_get_contents($inputFilePath);
        $controlImageFile = file_get_contents($controlFilePath);

        // Control.
        $this->assertEquals($originalImageFile, $controlImageFile);

        Helpers::imageTtfTextJustified(
            $testImage,
            'How quickly deft jumping zebras vex.',
            20,
            50,
            25,
            null,
            null,
            $fontFile
        );

        imagejpeg($testImage, $outputFilePath);

        // Confirms that the the image was processed and saved.
        $this->assertFileExists($outputFilePath);

        $editedImageFile = file_get_contents($outputFilePath);

        // Confirms that the saved image is different from the original.
        $this->assertNotEquals($originalImageFile, $editedImageFile);
        // Control.
        $this->assertEquals($originalImageFile, $controlImageFile);

        // Cleanup and assert that output file does not exist again.
        unlink($outputFilePath);
        $this->assertFileNotExists($outputFilePath);
    }
}
