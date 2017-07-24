<?php
/**
 * -- tivie/php-git-log-parser --
 * Parser.php created at 22-12-2014
 * 
 * Copyright 2014 Estevão Soares dos Santos
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

namespace Tivie\GitLogParser;

use Tivie\Command\Argument;
use Tivie\Command\Command;
use Tivie\GitLogParser\Exception\Exception;
use Tivie\GitLogParser\Exception\InvalidArgumentException;

/**
 * Class Parser
 * @package Tivie\GitLogParser
 */
class Parser
{
    /**
     * @var Format
     */
    private $format;

    /**
     * @var Command
     */
    private $command;

    private $gitDir;

    private $branch;

    public function __construct(Format $format = null, Command $command = null)
    {
        if ($format === null) {
            $format = new Format();
        }
        $this->format = $format;

        $this->gitDir = __DIR__;

        $this->branch = 'HEAD';

        $this->command = ($command) ? $command : $this->buildCommand();
    }

    /**
     * Set the Format to use
     *
     * @param Format $format
     * @return $this
     */
    public function setFormat(Format $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get the used Format
     * @return Format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the Command to use
     *
     * @param Command $command
     * @return $this
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get the Command used
     *
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set the directory where git log should be run on
     *
     * @param string $dir
     * @param boolean $check Check if the directory exists
     * @return $this
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function setGitDir($dir, $check = true)
    {
        if (!is_string($dir)) {
            throw new InvalidArgumentException('string', 0);
        }

        if ($check && !realpath($dir)) {
            throw new Exception("Directory $dir does not exist");
        }
        $this->gitDir = $dir;
        $this->command->chdir($dir);
        return $this;
    }

    /**
     * Set the branch that should be logged
     *
     * @param string $branch
     * @return $this
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws \Tivie\Command\Exception\DomainException
     */
    public function setBranch($branch)
    {
        if (!is_string($branch)) {
            throw new InvalidArgumentException('string', 0);
        }

        $oldBranch = $this->branch;
        $oldArg = $this->command->searchArgument($oldBranch);
        if (!$oldArg) {
            throw new Exception("Couldn't change the command to new branch. Was the Command object modified?");
        }
        $newArg = new Argument($branch);
        $this->command->replaceArgument($oldArg, $newArg);

        $this->branch = $branch;
        return $this;
    }

    /**
     * Parse the git log
     *
     * @return array
     */
    public function parse()
    {
        $result = $this->command->run();
        $log = $result->getStdOut();

        $buffer = array();
        $commits = explode($this->format->getCommitDelimiter(), $log);

        foreach ($commits as $commit) {
            $fields = explode($this->format->getFieldDelimiter(), $commit);
            $entry = array();

            foreach ($fields as $field) {
                if (!preg_match('^\[(\S*?)\](.*)/', $field, $matches)) {
                    continue;
                }
                $entry[trim($matches[1])] = trim($matches[2]);
            }
            if (!empty($entry)) {
                $buffer[] = $entry;
            }

        }
        return $buffer;
    }

    private function buildCommand()
    {
        $command = new Command(\Tivie\Command\DONT_ADD_SPACE_BEFORE_VALUE);
        $command
            ->chdir(realpath($this->gitDir))
            ->setCommand('git')
            ->addArgument(new Argument('log'))
            ->addArgument(new Argument($this->branch))
            ->addArgument(new Argument('--decorate'))
            ->addArgument(new Argument('--pretty=format:', $this->format->getFormatString(), null, true));

        return $command;
    }
}
