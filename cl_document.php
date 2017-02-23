<?php 

class document {
    private $document;
    private $table;

    function __construct() {
        $this->document = "";
        $this->table = array();
    }

    function get() {
        return $this->document;
    }

    function initFromFile($input_path) {
        return $this->document = file_get_contents($input_path);
    }

    function findRegexMatchPositions($regex) {
        mb_regex_encoding('UTF-8');
        mb_ereg_search_init($this->document);
        
        while ($arr = mb_ereg_search_pos($regex)) {
            $this->table[$regex][] = array($arr[0], $arr[0]+$arr[1]);
        }
    }

    function highlightDocument($regex, $opening, $closing) {
        if (array_key_exists ($regex, $this->table))
            foreach ($this->table[$regex] as &$coordinates){
                $this->insertSubstring($opening, $coordinates[0]);
                $this->updateRegexMatchPositions($coordinates[0], strlen($opening));
                $this->insertSubstring($closing, $coordinates[1]);
                $this->updateRegexMatchPositions($coordinates[1], strlen($closing));
            }
    }

    function updateRegexMatchPositions($idx, $len) {
        foreach ($this->table as &$regex)
            foreach($regex as &$cors)
                foreach($cors as &$cor)
                    if ($cor >= $idx)
                        $cor += $len;
    }

    function insertSubstring ($substring, $offset) {
        $this->document = mb_strcut($this->document, 0, $offset) . $substring . mb_strcut($this->document, $offset);
    }
}
?>