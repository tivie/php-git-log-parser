<?php
/**
 * -- tivie/php-git-log-parser --
 * Format.php created at 22-12-2014
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

use Tivie\GitLogParser\Exception\InvalidArgumentException;

/**
 * Class Format
 *
 * @package Tivie\GitLogParser
 */
class Format
{
    private $format;

    private $commitDelimiter;

    private $fieldDelimiter;

    private static $phArray = array(
        "commitHash"                => '%H',
        "abbreviatedCommitHash"     => '%h',
        "treeHash"                  => "%T",
        "abbreviatedTreeHash"       => "%t",
        "parentHashes"              => "%P",
        "abbreviatedParentHashes"   => "%p",
        "authorName"                => "%an",
        "authorNameMailmap"         => "%aN",
        "authorEmail"               => "%ae",
        "authorEmailMailmap"        => "%aE",
        "authorDate"                => "%ad",
        "authorDateRFC2822"         => "%aD",
        "authorDateRelative"        => "%ar",
        "authorDateTimestamp"       => "%at",
        "authorDateISO8601"         => "%ai",
        "committerName"             => "%cn",
        "committerNameMailmap"      => "%cN",
        "committerEmail"            => "%ce",
        "committerEmailMailmap"     => "%cE",
        "committerDate"             => "%cd",
        "committerDateRFC2822"      => "%cD",
        "committerDateRelative"     => "%cr",
        "committerDateTimestamp"    => "%ct",
        "committerDateISO8601"      => "%ci",
        "refs"                      => "%d",
        "encoding"                  => "%e",
        "subject"                   => "%s",
        "sanitizedSubject"          => "%f",
        "body"                      => "%b",
        "rawBody"                   => "%B",
        "commitNotes"               => "%N",
        "rawGPGMsg"                 => "%GG",
        "signature"                 => "%G?",
        "signerName"                => "%GS",
        "signerKey"                 => "%GK",
        "reflog"                    => "%gD",
        "shortReflog"               => "%gd",
        "reflogName"                => "%gn",
        "reflogNameMailmap"         => "%gN",
        "reflogEmail"               => "%ge",
        "reflogEmailMailmap"        => "%gE",
        "reflogSubject"             => "%gs"
    );

    /**
     * Create a new Format object
     */
    public function __construct()
    {
        $this->fieldDelimiter = "%|%";
        $s = "%".$this->fieldDelimiter."%";
        $this->commitDelimiter = '%BREAK%';
        $br = "%".$this->commitDelimiter."%";

        $format = '';
        foreach (self::$phArray as $name => $field) {
            $format .= "[$name]$field$s";
        }
        $format = trim($format, $s);
        $format .= $br;

        $this->format = $format;
    }

    /**
     * Get the commit delimiter
     *
     * @return string
     */
    public function getCommitDelimiter()
    {
        return $this->commitDelimiter;
    }

    /**
     * Get the field delimiter
     *
     * @return string
     */
    public function getFieldDelimiter()
    {
        return $this->fieldDelimiter;
    }

    /**
     * Set the format that should be used by git log command
     *
     * @param string $format A string that follows the git log format specification.
     * @param string $commitDelimiter The commit delimiter used by this format
     * @param string $fieldDelimiter The field delimiter used by this format
     * @return $this
     * @throws InvalidArgumentException
     * @see http://git-scm.com/docs/git-log#_pretty_formats
     */
    public function setFormat($format, $commitDelimiter, $fieldDelimiter)
    {
        if (!is_string($format)) {
            throw new InvalidArgumentException('string', 0);
        }
        if (!is_string($commitDelimiter)) {
            throw new InvalidArgumentException('string', 1);
        }
        if (!is_string($fieldDelimiter)) {
            throw new InvalidArgumentException('string', 2);
        }
        $this->commitDelimiter = $commitDelimiter;
        $this->fieldDelimiter = $fieldDelimiter;
        $this->format = $format;

        return $this;
    }

    /**
     * Get the format
     *
     * @return string
     */
    public function getFormatString()
    {
        return $this->format;
    }

    public function getPHArray()
    {
        return self::$phArray;
    }

    /**
     * Used when typecasting to string. Must return a valid string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format;
    }
}
