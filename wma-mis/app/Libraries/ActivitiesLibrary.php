<?php

namespace App\Libraries;

class ActivitiesLibrary
{
    public function allActivities($data)
    {
        function getProducts($arr)
        {
            $dataArray = array_map(function($data){
                return $data['productData'];
            },$arr);

           return  array_merge(...$dataArray);
            // return $dataArray;
        }

        function filterPayment($arr, $status)
        {
            $amountArray = array_filter($arr, function ($p) use ($status) {
                return $p['payment'] == $status;
            });

            $amountFigures = array_map(function ($amount) {
                return $amount['amount'];
            }, $amountArray);

            $amount = array_reduce($amountFigures, function ($prev, $next) {
                return $prev + $next;
            });



            return $amount;
        }
    

        $title = 'All Activities ';
        $allActivities = [
            'token' => $data->token,
            'category' => 'all',
            'title' => $title,
            'vtc' => [
                'token' => $data->token,
                'category' => 'vtcOnly',
                'vtcDetails' => $data->vtc,
                'vtcQuantity' => count($data->vtc),
                'vtcPaidQuantity' => paidInstruments($data->vtc),
                'vtcPendingQuantity' => pendingInstruments($data->vtc),
                'paidVtc' => paidAmount($data->vtc),
                'pendingVtc' => pendingAmount($data->vtc),
                'totalVtc' => totalAmount($data->vtc),
            ],
            'sbl' => [
                'token' => $data->token,
                'category' => 'sblOnly',
                'sblDetails' => $data->sbl,
                'sblQuantity' => count($data->sbl),
                'sblPaidQuantity' => paidInstruments($data->sbl),
                'sblPendingQuantity' => pendingInstruments($data->sbl),
                'paidSbl' => paidAmount($data->sbl),
                'pendingSbl' => pendingAmount($data->sbl),
                'totalSbl' => totalAmount($data->sbl),
            ],
            'waterMeter' => [
                'token' => $data->token,
                'category' => 'waterMeterOnly',
                'waterMeterDetails' => $data->waterMeter,
                'waterMeterQuantity' => meterQuantityAll($data->waterMeter),
                'waterMeterPaidQuantity' => meterQuantityPaid($data->waterMeter),
                'waterMeterPendingQuantity' => meterQuantityPending($data->waterMeter),
                'paidWaterMeter' => paidAmount($data->waterMeter),
                'pendingWaterMeter' => pendingAmount($data->waterMeter),
                'totalWaterMeter' => totalAmount($data->waterMeter),
            ],
            'prePackage' => [
                'xxx' => getProducts($data->prePackage),
                'token' => $data->token,
                'category' => 'prePackageOnly',
                'prePackage' => $data->prePackage,

                'paidPrePackage' => count($data->prePackage) == 0 ? 0 : filterPayment(getProducts($data->prePackage), 'Paid'),
                'pendingPrePackage' =>  count($data->prePackage) == 0 ? 0 : filterPayment(getProducts($data->prePackage), 'Pending'),
                // 'pendingPrePackage' =>  7450000,
                'totalPrePackage' => count($data->prePackage) == 0 ? 0 : filterPayment(getProducts($data->prePackage), 'Paid') + filterPayment(getProducts($data->prePackage), 'Pending'),

                // 'paidPrePackage' => 0,
                // 'pendingPrePackage' =>  0,
                // // 'pendingPrePackage' =>  7450000,
                // 'totalPrePackage' => 0,
            ],

        ];
        return $allActivities;
    }
}
