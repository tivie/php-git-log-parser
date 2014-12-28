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

    public function __construct(Format $format = null, Command $command = null)
    {
        if ($format === null) {
            $format = new Format();
        }
        $this->format = $format;

        if ($command === null) {
            $cmd = new Command(\Tivie\Command\DONT_ADD_SPACE_BEFORE_VALUE);
            $cmd->setCommand('git log');
            $cmd->setCommand('git')
                ->chdir(__DIR__)
                ->addArgument(new Argument('log'))
                ->addArgument(new Argument('--decorate'))
                ->addArgument(new Argument('--pretty=format:', $format->getFormatString(), null, true));
            $command = $cmd;
        }
        $this->command = $command;
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
     * Get the Command used
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
     * Set the Command to use
     *
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }

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
                if (!preg_match('/^\[(\S*)\](.*)/', $field, $matches)) {
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
}