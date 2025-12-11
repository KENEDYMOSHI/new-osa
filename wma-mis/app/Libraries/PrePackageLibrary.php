<?php

namespace App\Libraries;

use App\Models\PrePackageModel;

class  PrePackageLibrary
{

    public function getTheAllowedLimit($sampleSize, $appliedMethod)
    {

        if ($sampleSize == 20 && $appliedMethod == 'Destructive') {
            return 0;


          
        } else if ($sampleSize == 50 && $appliedMethod == 'Non Destructive') {
            return 3;
            
       
        } else if ($sampleSize == 80 && $appliedMethod == 'Non Destructive') {
            return 5;
           
        } else if ($sampleSize == 125 && $appliedMethod == 'Non Destructive') {
            return 7;
            
        }

        // if ($lot > 100 && $lot <= 500 && $appliedMethod == 'Non Destructive') {
        //     return 3;
        // } else if ($lot >= 100 && $appliedMethod == 'Destructive') {
        //     return 1;
        // } else if ($lot > 501 && $lot <= 3200 &&  $appliedMethod == 'Non Destructive') {
        //     return 3;
        // } else if ($lot >= 501 && $lot <= 3200 && $appliedMethod == 'Non Destructive') {
        //     return 5;
        // } else if ($lot > 3200 && $appliedMethod == 'Non Destructive') {
        //     return 7;
        // }
    }

    public function getCorrectionFactor($lotSize, $appliedMethod)
    {

        if ($lotSize >= 100 && $lotSize <= 500 && $appliedMethod == 'Non Destructive') {


            return 0.379;
        } else if ($lotSize >= 501 && $lotSize <= 3200 && $appliedMethod == 'Non Destructive') {


            return 0.295;
        } else if ($lotSize > 3200) {

            return 0.234;
        } else if ($lotSize >= 100 && $appliedMethod == 'Destructive') {

            return 0.640;
        }
    }

    /*this function takes a set of measurement data and return an array of result for 
    -T1 Error
    -T1 Error
    -Average Error
    -Corrected Average Error
    */
    public function processingMeasurements($measurementData)
    {


        $status = [];

        $productId = $measurementData[0]->product_id;
        $prepackageModal = new PrePackageModel();
        // $measurementData = $prepackageModal->getMeasurementData(['product_id' => $productId]);
        $product = $prepackageModal->selectProduct($productId);
        $lotSize = $product->lot;
        $appliedMethod = $product->method;
        $sampleSize = $product->sample_size;
        
        $declaredQuantity = preg_replace('/[^0-9.]/', '', substr($measurementData[0]->quantity_id, 11));

        $withT1error = array_filter($measurementData, function ($data) {
            return $data->status == 1;
        });

        // filter t2
        $withT2error = array_filter($measurementData, function ($data) {
            return $data->status == 2;
        });

        $netQuantities = array_map(function ($net) use ($declaredQuantity) {
            return  $net->net_quantity - $declaredQuantity ;
        }, $measurementData);

        // calculate individual error
        $individualError = array_reduce($netQuantities, function ($prev, $next) {
            return $prev + $next;
        });


        $sampleErrorLimit = standardDeviation($netQuantities) * $this->getCorrectionFactor($lotSize, $appliedMethod);


        $averageError = $individualError / $sampleSize;


        $correctedAverageError =  $averageError +  $sampleErrorLimit;
          
        
        


   

        if ($averageError < 0) {
            array_push($status, 'failed');
        } else {
            array_push($status, 'pass');
        }
        if ($correctedAverageError < 0) {
            array_push($status, 'failed correctedAverageError');
        } else {
            array_push($status, 'pass');
        }

        if (count($withT1error) > $this->getTheAllowedLimit($sampleSize, $appliedMethod)) {
            array_push($status, 'failed');
        } else {
            array_push($status, 'pass');
        }




        if (count($withT1error) < $this->getTheAllowedLimit($sampleSize, $appliedMethod) && count($withT2error) > 0) {
            array_push($status, 'failed');
        } else {
            array_push($status, 'pass');
        }


        if (count($withT1error) > $this->getTheAllowedLimit($sampleSize, $appliedMethod) && count($withT2error) == 0) {
            array_push($status, 'failed');
        } else {
            array_push($status, 'pass');
        }

        return $status;
    }

    //This function combines 2 set of result array generated from generate status function and provide a collective product status for linear and area category
    public function evaluateStatus(array $data)
    {

       
        if (in_array('failed', $data)) {
            return 'Failed';
        } else {
            return 'Pass';
        }
    }

    public function formatDataset($prePackageData)
    {

        $prepackageModal = new PrePackageModel();


        $company = [];
        $prepackage = array_map(function ($data) use ($company, $prepackageModal) {
            $measurements = $prepackageModal->getMeasurementData(['product_id' => $data->product_id]);
            array_push($company, $data->name);
            $sampleSize = $data->sample_size;

            $set1Measurements = array_slice($measurements, 0, $sampleSize);
            $set2Measurements = array_slice($measurements, $sampleSize);

            $set1Status = $this->processingMeasurements($set1Measurements);
            $set2Status =
                !empty($set2Measurements) ? $this->processingMeasurements($set2Measurements) : [];

            $statusCollection = array_merge($set1Status, $set2Status);

            $status = $this->evaluateStatus($statusCollection);
            //

            return [

                'name' => $data->name,
                'date' => $data->created_at,
                'details' => [
                    'measurements' => $measurements,
                    'commodity' => $data->commodity . ' ' . $data->quantity . ' ' . $data->unit,
                    'batchNumber' => $data->batch_number,
                    'amount' => $data->amount,
                    'controlNumber' =>$data->PayCntrNum,
                    'PaymentStatus' => $data->PaymentStatus,
                    'officer' => $data->fName . ' ' . $data->lName,
                    //'status' => $this->processingMeasurements($measurements, $data->gross_weight, $data->lot),
                    'status' =>  $status,
                    'sampleSize' =>  $sampleSize,
                    'set1Measurements' =>  $set1Measurements,
                    'set2Measurements' =>  $set2Measurements,
                    'region' => $data->region,
                    'location' => $data->location,
                    'date' => dateFormatter($data->created_at),
                ]
            ];
        }, $prePackageData);


        $output = [];
        if (count($prepackage) == 0) {
            $output = [];
        } else {
            foreach ($prepackage as $arg) {
                $tmp[$arg['name']][] = $arg['details'];
            }


            foreach ($tmp as $customer => $data) {
                $output[] = [
                    'customer' => $customer,
                    'productData' => $data,
                    'region' => $data[0]['region'],
                    'location' => $data[0]['location'],
                    'date' => $data[0]['date'],
                    'controlNumber' => $data[0]['controlNumber'],
                    'PaymentStatus' => $data[0]['PaymentStatus'],
                    'set1Measurements' => $data[0]['set1Measurements'],
                    'set2Measurements' => $data[0]['set2Measurements'],
                    'status' => $data[0]['status'],
                ];

                // 'controlNumber' => $data[0]['controlNumber'],

            }
        }



        return $output;
    }
}
