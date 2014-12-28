<?php
/**
 * Created by PhpStorm.
 * User: Estevao
 * Date: 28-12-2014
 * Time: 00:42
 */

namespace Tivie\GitLogParser;


class FormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Tivie\GitLogParser\Format::getCommitDelimiter
     */
    public function testGetCommitDelimiter()
    {
        $format = new Format();

        self::assertEquals('%BREAK%', $format->getCommitDelimiter());
    }

    /**
     * @covers \Tivie\GitLogParser\Format::getFieldDelimiter
     */
    public function testGetFieldDelimiter()
    {
        $format = new Format();

        self::assertEquals('%|%', $format->getFieldDelimiter());
    }

    /**
     * @covers \Tivie\GitLogParser\Format::setFormat
     * @covers \Tivie\GitLogParser\Format::getFormatString
     * @covers \Tivie\GitLogParser\Format::getCommitDelimiter
     * @covers \Tivie\GitLogParser\Format::getFieldDelimiter
     */
    public function testSetGetFormat()
    {
        $format = new Format();
        $cDel = '<br>';
        $fDel = '|';
        $formatStr = "%h$fDel%d$fDel%B$cDel";

        $format->setFormat($formatStr,$cDel, $fDel);
        self::assertEquals($cDel, $format->getCommitDelimiter());
        self::assertEquals($fDel, $format->getFieldDelimiter());
        self::assertEquals($formatStr, $format->getFormatString());
    }
}
