<?php

namespace Tivie\GitLogParser;


use Tivie\Command\Argument;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Tivie\GitLogParser\Parser::parse
     */
    public function testParse()
    {
        $gitlog = file_get_contents(__DIR__ . '/resources/gitlog.txt');
        $gitlogJson = file_get_contents(__DIR__ . '/resources/gitlog.json');

        $cmd = $this->createCommandMock($gitlog);
        $format = $this->createFormatMock();
        $parser = new Parser($format, $cmd);

        $res = $parser->parse();
        $json = json_decode($gitlogJson, true);
        self::assertEquals($json, $res);

    }

    /**
     * @covers \Tivie\GitLogParser\Parser::setGitDir
     */
    public function testSetGitDir()
    {
        $dir = '/foo/bar/baz';

        $cmd = $this->getMockBuilder('\Tivie\Command\Command')
            ->setMethods(array('chdir'))
            ->getMock();

        $cmd->expects($this->once())
            ->method('chdir')
            ->with($dir);

        $parser = new Parser(null, $cmd);
        $parser->setGitDir($dir, false);
    }

    public function testSetBranch()
    {
        $oldArg = new Argument('foo');

        $cmd = $this->getMockBuilder('\Tivie\Command\Command')
            ->setMethods(array('searchArgument', 'replaceArgument'))
            ->getMock();

        $cmd->expects($this->once())
            ->method('searchArgument')
            ->with('HEAD')
            ->willReturn($oldArg);

        $cmd->expects($this->once())
            ->method('replaceArgument')
            ->with($oldArg, $this->anything());

        $parser = new Parser(null, $cmd);
        $parser->setBranch('master');
    }

    private function createCommandMock($result)
    {
        $cmd = $this->getMockBuilder('\Tivie\Command\Command')
            ->setMethods(array('run'))
            ->getMock();

        $cmd->expects($this->any())
            ->method('run')
            ->willReturn(
                $this->getResultMock($result)
            );

        return $cmd;
    }

    private function createFormatMock()
    {
        $format = $this->getMockBuilder('\Tivie\GitLogParser\Format')
            ->setMethods(array('getCommitDelimiter', 'getFieldDelimiter'))
            ->getMock();

        $format->expects($this->once())
            ->method('getCommitDelimiter')
            ->willReturn('%BREAK%');

        $format->expects($this->any())
            ->method('getFieldDelimiter')
            ->willReturn('%|%');

        return $format;
    }

    private function getResultMock($result)
    {
        $mock = $this->getMockBuilder('\Tivie\Command\Result')
            ->setMethods(array('getStdOut'))
            ->getMock();

        $mock->expects($this->any())
            ->method('getStdOut')
            ->willReturn($result);

        return $mock;
    }
}
