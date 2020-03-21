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
            'pattern' => '/\[faIcon\]\((.*?)\)\[\/faIcon\]/s',
            'replace' => '<span class="$1"></span>',
            'content' => '$1',
        ];

        // HTML5 Correction
        $this->parsers['size'] = [
            'pattern' => '/\[size\=(xx\-small|x\-small|small|medium|large|x\-large|xx\-large)\](.*?)\[\/size\]/s',
            'replace' => '<span style="font-size: $1;">$2</span>',
            'content' => '$1',
        ];

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

        // Image
        $this->parsers['image'] = [
            'pattern' => '/\[img=(?:alt:(.*?);)?(?:title:(.*?);)?(?:class:(.*?);)?(?:longdesc:(.*?);)?(?:id:(.*?);)?(.*?)\](.*?)?\[\/img\]/s',
            'replace' => '<img src="$7" alt="$1" title="$2" id="$5" class="$3" longdesc="$4" style="$6"/>',
            'content' => '$7',
        ];
    }
}
