<?php 

// App/Libraries/CustomPager.php
namespace App\Libraries;

use CodeIgniter\Pager\PagerRenderer;

class PagerLibrary extends PagerRenderer
{
    // public function __construct($config = null, $uri = null, $request = null)
    // {
    //     parent::__construct($config, $uri, $request);
    // }
    public function setCustomSurroundCount(int $count)
    {
       return $this->setSurroundCount($count);
    }
}

?>