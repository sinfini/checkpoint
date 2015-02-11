<?php

/**
 * This file is part of the General utility.
 *
 * (c) Sankar suda <sankar.suda@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Checkpoint;

use Checkpoint\Result;

/**
 * @author sankar <sankar.suda@gmail.com>
 */

class Filter
{
    /**
     * @var array
     */
    protected $regExps = array();

    /**
     * @var array
     */
    protected $whileList = array();

    /**
     * @var array
     */
    protected $riskLevels = 2;

    function __construct($patterns, $whitelist = null ,$riskLevels = 2)
    {
        $this->regExps = $patterns;
        $this->whileList = $whitelist;
        $this->riskLevels = array($riskLevels);
    }

    /**
     * Filters some content for bad words and returns a result.
     * If multiple contents are specified, multiple results will be returned.
     *
     * @param string|array $content A single content string or an array of content strings.
     *
     * @return Result|array A single Result or an array of Results.
     *
     * @throws \InvalidArgumentException
     */
    public function filter($content)
    {
        $singleContent = false;
        if(!is_array($content)) {
            $content = array($content);
            $singleContent = true;
        }

        foreach($content as $key => $string) {
            if(!(is_string($string) && mb_strlen(trim($string)) > 0)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid content%s. Please provide a non-empty string.',
                    (count($string) > 1 ? sprintf(' at index "%s".', $key) : null)
                ));
            }
        }

        $results = array();

        foreach($content as $key => $string) {
            array_push($results, new Result(
                $string,
                $this->filterString($string),
                $this->riskLevels
            ));
        }

        return count($results) === 1 && $singleContent ? $results[0] : $results;
    }

      /**
     * Filters a single string for bad words and returns any suspected matches found.
     *
     * @param string $string
     *
     * @return array
     */
    protected function filterString($string)
    {
         $matches = array();

        $dictionaryMatches = array();
        // Loop through the regular expressions
        foreach($this->regExps as $regExp) {
           
            $regExp = '/' . $regExp . '/ui';
            // Run the regular expression on the string and process any matches found
            if(preg_match_all($regExp, $string, $regExpMatches)) {

                // If there's a whitelist, only store each match if it isn't in there
                if($this->whileList) {
                    foreach($regExpMatches[0] as $regExpMatch) {

                        //if(!in_array(
                            //mb_strtolower(trim($regExpMatch)),
                            //$this->whileList
                        //)) {
                          //  array_push($dictionaryMatches, trim($regExpMatch));
                        //}


                        if(!$this->ignoreWhiteList(trim($regExpMatch))) {
                            array_push($dictionaryMatches, trim($regExpMatch));
                        }    

                    }

                // Otherwise just straight store the matches
                } else {
                    $dictionaryMatches = array_merge(
                        $dictionaryMatches,
                        $regExpMatches[0]
                    );
                }
            }
        }

        // Store any matches found against the Dictionary ID
        if($dictionaryMatches) {
            $matches[] =
                array_values(array_unique($dictionaryMatches));
        }
        return $matches;
    }

    protected function ignoreWhiteList ($string)
    {
        foreach ($this->whileList as $regExp) {
            $regExp = '/' . $regExp . '/ui';
            if(preg_match($regExp, $string)) {
                return true;
            }
        }

        return false;
    }
}
