<?php

namespace App\Custom;

/**
 * Extends PheRum\BBCode\BBCodeParser
 *
 * Resolves bug where new line was not replaced correctly
 * in original library
 */
class BBCodeParser extends \PheRum\BBCode\BBCodeParser
{
    public $oneTimeParsersDone = false;
    private $oneTimeParsers = [];

    public function __construct()
    {
        // Move new line on top of parsers array
        $this->parsers = array_merge(array_splice($this->parsers, -1), $this->parsers);
        self::addCustomRules();
        parent::__construct();
    }

    private $smileyMap = [
        "angel" => [
            "emoji" => "o:)",
            "file" => "angel_smile.png"
        ],
        "smiley" => [
            "emoji" => ":)",
            "file" => "regular_smile.png"
        ],
        "sad" => [
            "emoji" => ":(",
            "file" => "sad_smile.png"
        ],
        "wink" => [
            "emoji" => ";)",
            "file" => "wink_smile.png"
        ],
        "laugh" => [
            "emoji" => ":D",
            "file" => "teeth_smile.png"
        ],
        "cheeky" => [
            "emoji" => ":P",
            "file" => "tongue_smile.png"
        ],
        "blush" => [
            "emoji" => ":*)",
            "file" => "embarrassed_smile.png"
        ],
        "suprise" => [
            "emoji" => ":-o",
            "file" => "omg_smile.png"
        ],
        "indecision" => [
            "emoji" => ":|",
            "file" => "whatchutalkingabout_smile.png"
        ],
        "angry" => [
            "emoji" => ">:(",
            "file" => "angry_smile.png"
        ],
        "cool" => [
            "emoji" => "8-)",
            "file" => "shades_smile.png"
        ],
        "devil" => [
            "emoji" => ">:-)",
            "file" => "devil_smile.png"
        ],
        "crying" => [
            "emoji" => ";(",
            "file" => "cry_smile.png"
        ],
        "kiss" => [
            "emoji" => ":-*",
            "file" => "kiss.png"
        ],
    ];

    private function addCustomRules()
    {
        $this->parsers['linkemail'] = [
            'pattern' => '/\[email\](.*?)\[\/email\]/s',
            'replace' => '<a href="mailto:$1">$1</a>',
            'content' => '$1',
        ];

        $this->parsers['faIcon'] = [
            'pattern' => '/\[faIcon\](.*?)\[\/faIcon\]/s',
            'replace' => '<span class="$1"></span>',
            'content' => '$1',
        ];

        // HTML5 Correction
        $this->parsers['size'] = [
            'pattern' => '/\[size\=(xx\-small|x\-small|small|medium|large|x\-large|xx\-large)\](.*?)\[\/size\]/s',
            'replace' => '<span style="font-size: $1;">$2</span>',
            'content' => '$1',
        ];

        // fix get empty lines after that
        $this->parsers = ['listitem' => [
            'pattern' => '/\[\*\](.*(?>\r\n)*)/',
            'replace' => '<li>$1</li>',
            'content' => '$1',
        ]] + $this->parsers;

        $this->parsers['linebreak'] = [
            'pattern' => '/\r\n/',
            'replace' => '<br>',
            'content' => '',
        ];

        // ===================== Emoji
        // Emoji regex map build
        foreach ($this->smileyMap as $name => $smiley) {
            $this->parsers['smiley_' . $name] = [
                'pattern' => '/' . preg_quote($smiley['emoji']) . '/s',
                'replace' => sprintf(
                    '<img src="%s" title="%s" alt="%s">',
                    asset('vendor/ckeditor/plugins/smiley/images/' . $smiley['file']),
                    $name,
                    $name
                ),
                'content' => '$1',
            ];
        }

        // ==================== Image fix
        // Image
        $this->parsers['image'] = [
            'pattern' => '/\[img=(?:alt:(.*?);)?(?:title:(.*?);)?(?:class:(.*?);)?(?:longdesc:(.*?);)?(?:id:(.*?);)?(.*?)\](.*?)?\[\/img\]/s',
            'replace' => '<img src="$7" alt="$1" title="$2" id="$5" class="$3" longdesc="$4" style="$6"/>',
            'content' => '$7',
        ];

        // #### Handling imbricated list TAGS to fix HTML eg: imbricated <ul> should be in a <li>

        // ======================un ordered lists
        // mark list root Tags
        $this->oneTimeParsers['ulRootTags'] = [
            'pattern' => '/\[list\](.*?)\[\/list\](?!(?>\r\n)*(?>(?>\[\*\])|(?>\[\/list)|(?>\[list)))/s',
            'replace' => '[ROOTLIST]$1[/ROOTLIST]'
        ];

        // Replace ROOTLIST tags
        $this->parsers['replaceUlRootListTags'] = [
            'pattern' => '/\[ROOTLIST\](.*?)\[\/ROOTLIST\]/s',
            'replace' => '<ul>$1</ul>'
        ];
        // Replace imbricated [LIST] tags
        $this->parsers['unorderedlist'] = [
            'pattern' => '/\[list\]/s',
            'replace' => '<li style="list-style:none;"><ul>'
        ];
        // Replace imbricated [/LIST] tags
        $this->parsers['unorderedlist2'] = [
            'pattern' => '/\[\/list\]/s',
            'replace' => '</ul></li>'
        ];

        // ======================= ordered lists
        // mark olist root Tags
        $this->oneTimeParsers['olRootTags'] = [
            'pattern' => '/\[olist\](.*?)\[\/olist\](?!(?>\r\n)*(?>(?>\[\*\])|(?>\[\/olist)|(?>\[olist)))/s',
            'replace' => '[ROOTOLIST]$1[/ROOTOLIST]'
        ];

        // Replace ROOTOLIST tags
        $this->parsers['replaceOlRootListTags'] = [
            'pattern' => '/\[ROOTOLIST\](.*?)\[\/ROOTOLIST\]/s',
            'replace' => '<ol>$1</ol>'
        ];
        // Replace imbricated [OLIST] tags
        $this->parsers['orderedlist'] = [
            'pattern' => '/\[olist\]/s',
            'replace' => '<li style="list-style:none;"><ol>'
        ];
        // Replace imbricated [/OLIST] tags
        $this->parsers['orderedlist2'] = [
            'pattern' => '/\[\/olist\]/s',
            'replace' => '</ol></li>'
        ];

        // finally merge imbricated LI with their OL or UL child
        $this->parsers['mergeListWithChild'] = [
            'pattern' => '/<\/li>\n<li style="list-style:none;">/',
            'replace' => ''
        ];

        // =====================  fix HTML5
        // Put this at the end of list to fix HTML5
        // remove line break just after <ul>
        $this->parsers['UL1'] = [
            'pattern' => '/<ul><br>/s',
            'replace' => '<ul>',
            'content' => '',
        ];

        $this->parsers['OL1'] = [
            'pattern' => '/<ol><br>/s',
            'replace' => '<ol>',
            'content' => '',
        ];

        // remove line break just before </li>
        $this->parsers['LI1'] = [
            'pattern' => '/<\/li>(?>\n)*((?><br>)+)+/',
            'replace' => '$1</li>',
            'content' => '',
        ];
    }


    /**
     * Override search and Replace
     * to trigger one time parsers
     * 
     * Must have one capture group
     *
     * @param string $pattern
     * @param string $replace
     * @param string $source
     * @return string Parsed Text
     */
    protected function searchAndReplace($pattern, $replace, $source)
    {
        // Do global replace once
        if (!$this->oneTimeParsersDone) {
            $source = $this->globalSearchAndReplace($pattern, $replace, $source);
            $this->oneTimeParsersDone = true;
        }
        return parent::searchAndReplace($pattern, $replace, $source);
    }

    /**
     * Act as Search And replace function
     * for Regexes with //g (Global)
     * as on Regex101.com site
     * 
     * used for https://regex101.com/r/aO2lN1/1
     * like regexes
     * used to handle root tags with imbricated tags
     * for ex
     *
     * @param string $pattern
     * @param string $replace
     * @param string $source
     * @return string Parsed Text
     */
    protected function globalSearchAndReplace($pattern, $replace, $source)
    {
        foreach ($this->oneTimeParsers as $name => $oneTimeParser) {
            // Find All occurences
            preg_match_all($oneTimeParser['pattern'], $source, $matches, PREG_SET_ORDER, 0);
            // replace All occurences for each first capture group
            foreach ($matches as $k => $match) {
                $replaces = [];
                foreach ($match as $groupMatchID => $groupMatch) {
                    $replaces['$' . $groupMatchID] = $groupMatch;
                }
                // Replaces at once all $X tags with each group match
                $source = str_replace($match[0], strtr($oneTimeParser['replace'], $replaces), $source);
            }
        }
        return $source;
    }
}
