<?php

namespace App\Controllers;

use App\Libraries\ArrayLibrary;
use App\Libraries\CommonTasksLibrary;
use App\Models\EstimatesModel;


class EstimatesController extends BaseController
{
  protected $estimatesModel;
  protected $session;
  protected $uniqueId;
  protected $user;
  protected $collectionCenter;


  public $token;
  public function __construct()
  {

    helper(['form', 'array', 'regions', 'date']);
    $this->session         = session();
    $this->token         = csrf_hash();
    $this->estimatesModel        = new EstimatesModel();
    $this->uniqueId        = auth()->user()->unique_id;
    $this->collectionCenter        = auth()->user()->collection_center;
    $this->user = auth()->user();
  }

  public function getVariable($var)
  {
    return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
  }



  public function index()
  {
    // if (!$this->user->hasPermission('estimate.manage')) {
    //     return redirect()->to('dashboard');
    // }

    $data['page'] = [
      'title' => 'Collection Estimates',
      'heading' => 'Collection Estimates',
    ];

    $data['user'] = $this->user;
    // $estimates = $this->getTargets();
    $data['estimates'] = $this->estimatesModel->getEstimates(['year' => date('Y')]);
    // Printer($data['estimates']);
    // exit;
    $data['regions'] = (new CommonTasksLibrary())->collectionCenters();
    return view('Pages/Estimates', $data);
  }


  //
  public function activityEstimates()
  {
    // if (!$this->user->hasPermission('estimate.manage')) {
    //     return redirect()->to('dashboard');
    // }

    $data['page'] = [
      'title' => 'Activity Estimates',
      'heading' => 'Activity Estimates',
    ];

    $data['user'] = $this->user;
    $month = date('m');
    $year = date('Y');
    // $estimates = $this->getTargets();
    $params = ['year' => $year, 'month' => date('n'), 'region' => $this->collectionCenter];
    $estimates = $this->estimatesModel->getActivityEstimates($params);
    $activities = (array)gfsCodes();

    $data['title'] = strtoupper(date('F', mktime(0, 0, 0, $month, 1))) . ' ' . date('Y');

    $actual = $this->estimatesModel->getVerifiedItems([
      'MONTH(bill_items.CreatedAt)' => $month,
      'YEAR(bill_items.CreatedAt)' => $year,
      'CollectionCenter' => $this->collectionCenter,
    ]);

    // Printer($actual);

    // exit;

    $options  = ['month' => date('n'), 'year' => date('Y'), 'region' => $this->collectionCenter];
    $estimatesModel = new EstimatesModel();
    $alreadyAllocated = $estimatesModel->getAllAllocations($options);
    $estimate = $estimatesModel->getInstrumentEstimate($options)->instruments ?? 0;
    $data['remaining'] = $estimate - $alreadyAllocated;




    $data['activityData'] = $this->renderActivityData($activities, $estimates, $actual);

    // Printer($this->renderActivityData($activities, $estimates, $actual));

    // exit;



    return view('Pages/ActivityEstimates', $data);
  }




  public function updateEstimate()
  {
    try {
      $id = $this->getVariable('id');
      $month = $this->getVariable('month');
      $year = $this->getVariable('year');
      $amount = $this->getVariable('amount');

      $data = [

        'month' => $month,
        'year' => $year,
        'amount' => str_replace(',', '', $amount),
      ];

      $query = $this->estimatesModel->updateEstimate($data, $id);

      if ($query) {
        return $this->response->setJSON([
          'status' => 1,
          'meg' => 'Estimate Updated',
          'token' => $this->token
        ]);
      }
    } catch (\Throwable $th) {

      return $this->response->setJSON([
        'status' => 0,
        'msg' => $th->getMessage(),
        'token' => $this->token
      ]);
    }
  }


  public function createEstimate()
  {
    try {
      $region = $this->getVariable('region');
      $month = $this->getVariable('month');
      $year = $this->getVariable('year');
      $amount = $this->getVariable('amount');

      $data = [
        'region' => $region,
        'regionName' => str_replace('Wakala Wa Vipimo', '', wmaCenter($region)->centerName),
        'month' => $month,
        'year' => $year,
        'amount' => str_replace(',', '', $amount),
        'userId' => $this->uniqueId,
      ];

      $query = $this->estimatesModel->createEstimate($data);

      if ($query) {
        return $this->response->setJSON([
          'status' => 1,
          'meg' => 'Estimate Created',
          'token' => $this->token
        ]);
      }
    } catch (\Throwable $th) {

      return $this->response->setJSON([
        'status' => 0,
        'msg' => $th->getMessage(),
        'token' => $this->token
      ]);
    }
  }
  public function editEstimate()
  {
    try {
      $id =  $amount = $this->getVariable('id');
      $query = $this->estimatesModel->editEstimate($id);
      return $this->response->setJSON([
        'status' => 1,
        'estimate' => $query,
        'token' => $this->token
      ]);
    } catch (\Throwable $th) {
      $response = [
        'status' => 0,
        'msg' => $th->getMessage(),
        'token' => $this->token
      ];
    }
    return $this->response->setJSON($response);
  }

  //
  public function editActivityEstimate()
  {
    try {
      $id = $this->getVariable('id');
      $query = $this->estimatesModel->editActivityEstimate($id);
      $query->activity = activityName($query->activity);
      return $this->response->setJSON([
        'status' => 1,
        'id' => $id,
        'estimate' => $query,
        'token' => $this->token
      ]);
    } catch (\Throwable $th) {
      $response = [
        'status' => 0,
        'msg' => $th->getMessage(),
        'trace' => $th->getTrace(),
        'token' => $this->token
      ];
    }
    return $this->response->setJSON($response);
  }


  public function createActivityEstimate()
  {
    try {
      $region = $this->collectionCenter;
      $activity = $this->getVariable('activity');
      $quantity = $this->getVariable('quantity');
      $month = $this->getVariable('month');
      $year = $this->getVariable('year');


      $params = [
        'region' => $region,
        'activity' => $activity,
        'quantity' => $quantity,
        'month' => $month,
        'year' => $year,
      ];


      //  return $this->response->setJSON([
      //   'opt' => $options,
      //   'token'=>$this->token
      //  ]);
      //  exit;
      //
      $getRemaining = function () use ($region, $month, $year) {

        $options  = ['month' => $month, 'year' => $year, 'region' => $region];
        $estimatesModel = new EstimatesModel();
        $alreadyAllocated = $estimatesModel->getAllAllocations($options);
        $estimate = $estimatesModel->getInstrumentEstimate($options)->instruments ?? 0;
        return $estimate - $alreadyAllocated;
      };


      $remaining = $getRemaining();
      if ($remaining == 0) {
        return $this->response->setJSON([
          'status' => 0,
          'msg' => 'You have already allocated all the instruments for this month',
          'remaining' => $remaining
        ]);
      } elseif ($quantity > $remaining) {
        return $this->response->setJSON([
          'status' => 0,
          'msg' => "You can not allocate more than the remaining instruments, you have $remaining instruments remaining",
          'remaining' => $remaining
        ]);
      }

      $request = $this->estimatesModel->allocateEstimate($params);
      if ($request) {
        $options = exceptKeys($params, ['activity', 'quantity']);
        $estimates = $this->estimatesModel->getActivityEstimates($options);
        $activities = (array)gfsCodes();
        $actual = $this->estimatesModel->getVerifiedItems([
          'MONTH(bill_items.CreatedAt)' => $month,
          'YEAR(bill_items.CreatedAt)' => $year,
          'CollectionCenter' => $this->collectionCenter,
        ]);
        return $this->response->setJSON([
          'status' => 1,
          'msg' => 'Instruments Allocated Successfully',
          'activityData' => $this->renderActivityData($activities, $estimates, $actual),
          'remaining' => $getRemaining(),
          'token' => $this->token,
          'opt' => $options,
        ]);
      } else {
        return $this->response->setJSON([
          'status' => 0,
          'msg' => 'Something went wrong try again',
          'token' => $this->token
        ]);
      }
    } catch (\Throwable $e) {
      return $this->response->setJSON([
        'status' => 0,
        'msg' =>  $e->getMessage(),
        'trace' =>  $e->getTrace(),
        'token' => $this->token

      ]);
    }
  }
  public function updateActivityEstimate()
  {
    try {
      $id = $this->getVariable('id');
     
      $region = $this->collectionCenter;
      $activity = $this->getVariable('activity');
      $quantity = $this->getVariable('quantity');
      $month = $this->getVariable('month');
      $year = $this->getVariable('year');


      $params = [
        'region' => $region,
        // 'activity' => $activity,
        'quantity' => $quantity,
        'month' => $month,
        'year' => $year,
      ];


      //  return $this->response->setJSON([
      //   'opt' => $options,
      //   'token'=>$this->token
      //  ]);
      //  exit;
      //
      $getRemaining = function () use ($region, $month, $year) {

        $options  = ['month' => $month, 'year' => $year, 'region' => $region];
        $estimatesModel = new EstimatesModel();
        $alreadyAllocated = $estimatesModel->getAllAllocations($options);
        $estimate = $estimatesModel->getInstrumentEstimate($options)->instruments ?? 0;
        return $estimate - $alreadyAllocated;
      };


      $remaining = $getRemaining();
      if ($remaining == 0) {
        return $this->response->setJSON([
          'status' => 0,
          'msg' => 'You have already allocated all the instruments for this month',
          'remaining' => $remaining,
          'token' => $this->token,
        ]);
      } elseif ($quantity > $remaining) {
        return $this->response->setJSON([
          'status' => 0,
          'msg' => "You can not allocate more than the remaining instruments, you have $remaining instruments remaining",
          'remaining' => $remaining,
          'token' => $this->token,
        ]);
      }

      $request = $this->estimatesModel->updateActivityEstimate($id,$params);
      if ($request) {
        $options = exceptKeys($params, ['activity', 'quantity']);
        $estimates = $this->estimatesModel->getActivityEstimates($options);
        $activities = (array)gfsCodes();
        $actual = $this->estimatesModel->getVerifiedItems([
          'MONTH(bill_items.CreatedAt)' => $month,
          'YEAR(bill_items.CreatedAt)' => $year,
          'CollectionCenter' => $this->collectionCenter,
        ]);
        return $this->response->setJSON([
          'status' => 1,
          'msg' => 'Instruments Allocated Successfully',
          'activityData' => $this->renderActivityData($activities, $estimates, $actual),
          'remaining' => $getRemaining(),
          'token' => $this->token,
          'opt' => $options,
        ]);
      } else {
        return $this->response->setJSON([
          'status' => 0,
          'msg' => 'Something went wrong try again',
          'token' => $this->token
        ]);
      }
    } catch (\Throwable $e) {
      return $this->response->setJSON([
        'status' => 0,
        'msg' =>  $e->getMessage(),
        'trace' =>  $e->getTrace(),
        'token' => $this->token

      ]);
    }
  }


  public function renderActivityData($activities, $estimates, $actual)
  {

    $activityEstimates = (new ArrayLibrary(array_keys($activities)))->map(function ($activity) use ($estimates, $actual) {

      $ids = (new ArrayLibrary($estimates))->filter(fn ($estimate) => $estimate->activity == $activity)->map(fn ($x) => $x->id[0])->get();
      $itemsEstimate = (new ArrayLibrary($estimates))->filter(fn ($estimate) => $estimate->activity == $activity)->reduce(fn ($x, $y) => $x + $y->quantity)->get() ?? 0;
      $itemsActual = (new ArrayLibrary((array)$actual))->filter(fn ($act) => $act->activity == $activity)->reduce(fn ($x, $y) => $x + $y->quantity)->get() ?? 0;
      $variance =  $itemsActual - $itemsEstimate;
      $id = array_values($ids);
      $result = (object)[
        'id' => $id[0] ?? '',
        'activity' => activityName($activity),
        'month' => ucfirst(date('F', mktime(0, 0, 0, $estimates[0]->month ?? date('n'), 1))),
        'estimate' => $itemsEstimate,
        'actual' => $itemsActual,
        'variance' => $variance,
        'percentage' => round(($itemsEstimate != 0) ? ($variance / $itemsEstimate) * 100  : 0),
      ];


      return $result;
    })->get();

    // return $activityEstimates;
    // exit;
    $tr = '';
    foreach ($activityEstimates as $estimate) {
      $isDisabled = ($estimate->id == '') ? 'disabled' : '';
      $tr .= <<<HTML
            <tr>
              <td>$estimate->activity  </td>
              <td>$estimate->month </td>
              <td>$estimate->estimate </td>
              <td>$estimate->actual </td>
              <td>$estimate->variance </td>
              <td>$estimate->percentage  %</td>
              <td>
                 <button type="button" class="btn btn-primary btn-sm $isDisabled" onclick="editActivityEstimate('$estimate->id')"> <i class="far fa-pen-alt"></i>  </button>
                  </td>
            </tr>        
      HTML;
    }
    return $tr;
  }
}
