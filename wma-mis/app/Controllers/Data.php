<?php

namespace App\Controllers;
use CodeIgniter\View\Table;

class Data extends BaseController
{



  public function index()
  {
    $data = [
      ['Name', 'Color', 'Size','City'],
      ['Cassim', 'Blue',  'Small','Arusha'],
      ['Mary', 'Red',   'Large','Mwanza'],
      ['John', 'Green', 'Medium','Tanga'],
    ];
    $club_member = [
      ['Name', 'ID'],
      ['Allan', '045',],
      ['Samson', 'G142'],
      ['Caren', 'FG1'],
    ];

    


    $table = new Table();
    $users = new Table();

    // $table->setHeading(['Name', 'Color', 'Size']);
    // $table->addRow(['Cassim', 'Maroon', 'XL']);
    // $table->addRow(['Eunice', 'Yellow', 'Medium']);
    // $table->addRow(['Denis', 'Blue', 'Large']);

    $template = [
      'table_open'         => '<table border="1" class="data-table">',

      
    ];

    $table->setTemplate($template);
    $users->setTemplate($template);
    $result['users'] =  $table->generate($data);
    $result['members'] =  $table->generate($club_member);
    echo view('dataview', $result);
  }
}