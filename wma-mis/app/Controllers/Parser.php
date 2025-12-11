<?php namespace App\Controllers;




class Parser extends BaseController{

  public $parser;
  public function __construct(){
     $this->parser = \Config\Services::parser();
  }
    
 

  public function index(){
 

    $data = [
      "title"   => "Programming",
      "heading" => "TOPICS WE WILL LEARN",
      "subject_list"=>[
       [ "subject"=>"JavaScript Object Notation","abr"=>"JSON"],
       [ "subject"=>"HyperText Pre Processor","abr"=>"PHP"],
       [ "subject"=>"Application Programming Interface","abr"=>"API"],
       [ "subject"=>"Asynchronous JavaScript and XML","abr"=>"Ajax"],
    
      ],
      "status"=> false
    ];
     //$parser->setData($data);
     return $this->parser->setData($data)->render('variable_parser');
    // echo view('variable_parser',$data);
    
  }

  public function viewFilters(){
    
    $data = [
      "title"   => "Filters",
      "heading" => "WE FILTER VARIABLES HERE",
      "date"    => "2020-4-10",
      "price"    => "450",
    ];
    return $this->parser->setData($data)->render('filter_view');

  }

}


/* End of file Parser.php */
/* Location: ./app/controllers/Parser.php */