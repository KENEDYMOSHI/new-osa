<?php 
// public function downloadCustomDateReport($activity,$dateFrom,$dateTo,$status){
    $date = date('d-M,Y h:i:s ');
    $dompdf = new \Dompdf\Dompdf();
    $options = new \Dompdf\Options();

    $reportTitle = dateFormatter($dateFrom). ' To ' . dateFormatter($dateTo);
     
    
    $role = $this->role;
    $uniqueId = $this->uniqueId;
    $region = $this->city;

       switch ($activity) {
           case 'All':
            $title = 'All Activities ' . $reportTitle;
           
            if($role == 1){

                
                
                $vtc = $this->vtcModel->vtcWithDateRangeReportOfficer($uniqueId,$dateFrom,$dateTo); 
                $sbl = $this->lorriesModel->sblWithDateRangeReportOfficer($uniqueId,$dateFrom,$dateTo); 
                $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportOfficer($uniqueId,$dateFrom,$dateTo); 
             
            }
            elseif($role == 2){
                $vtc = $this->vtcModel->vtcWithDateRangeReportManager($region,$dateFrom,$dateTo); 
                $sbl = $this->lorriesModel->sblWithDateRangeReportManager($region,$dateFrom,$dateTo); 
                $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportManager($region,$dateFrom,$dateTo); 
            }
            elseif($role == 3){
                $vtc = $this->vtcModel->vtcWithDateRangeReportDirector($dateFrom,$dateTo); 
                $sbl = $this->lorriesModel->sblWithDateRangeReportDirector($dateFrom,$dateTo); 
                $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportDirector($dateFrom,$dateTo); 
            }
         


           $allActivities = [
               'category'=>'all',
               'title' => $title,
               'vtc' =>[
                'vtcQuantity'=> count($vtc),
                'vtcPaidQuantity'=> paidInstruments($vtc),
                'vtcPendingQuantity'=> pendingInstruments($vtc),
                'paidVtc'=> paidAmount($vtc),
                'pendingVtc'=>  pendingAmount($vtc),
                'totalVtc'=> totalAmount($vtc),
               ],
               'sbl' =>[
                'sblQuantity'=> count($sbl),
                'sblPaidQuantity'=> paidInstruments($sbl),
                'sblPendingQuantity'=> pendingInstruments($sbl),
                'paidSbl'=> paidAmount($sbl),
                'pendingSbl'=>  pendingAmount($sbl),
                'totalSbl'=> totalAmount($sbl),
               ],
               'waterMeter' =>[
                'waterMeterQuantity'=> meterQuantityAll($waterMeter),
                'waterMeterPaidQuantity'=> meterQuantityPaid($waterMeter),
                'waterMeterPendingQuantity'=> meterQuantityPending($waterMeter),
                'paidWaterMeter'=> paidAmount($waterMeter),
                'pendingWaterMeter'=>  pendingAmount($waterMeter),
                'totalWaterMeter'=> totalAmount($waterMeter),
               ],

           ];

           $data['allActivities'] = $allActivities;
           $data['reportTitle'] = $title;
           //=================loading a report template====================
           $dompdf->loadHtml(view('ReportTemplates/allActivities',$data));
             break; 
           
        //=================VTC WithDateRange report start====================
           case 'vtc':
            $title = 'Vehicle Tank Calibration ' .$reportTitle;

       
//=================check payment status and render a report====================
           switch ($status) {
               case 'total':
                if($role == 1){

                    $vtc = $this->vtcModel->vtcWithDateRangeReportOfficer($uniqueId,$dateFrom,$dateTo,$status); 
                }
                elseif($role == 2){
                    $vtc = $this->vtcModel->vtcWithDateRangeReportManager($region,$dateFrom,$dateTo); 
                }
                elseif($role == 3){
                    $vtc = $this->vtcModel->vtcWithDateRangeReportDirector($dateFrom,$dateTo); 
                }
                   break;

                   case 'Paid':
                    if($role == 1){

                        $vtc = $this->vtcModel->vtcWithDateRangeReportOfficerStatus($uniqueId,$dateFrom,$dateTo,$status); 
                    }
                    elseif($role == 2){
                        $vtc = $this->vtcModel->vtcWithDateRangeReportManagerStatus($region,$dateFrom,$dateTo,$status); 
                    }
                    elseif($role == 3){
                        $vtc = $this->vtcModel->vtcWithDateRangeReportDirectorStatus($dateFrom,$dateTo,$status); 
                    }

                    break;
                    case 'Pending':
                        if($role == 1){
    
                            $vtc = $this->vtcModel->vtcWithDateRangeReportOfficerStatus($uniqueId,$dateFrom,$dateTo,$status); 
                        }
                        elseif($role == 2){
                            $vtc = $this->vtcModel->vtcWithDateRangeReportManagerStatus($region,$dateFrom,$dateTo,$status); 
                        }
                        elseif($role == 3){
                            $vtc = $this->vtcModel->vtcWithDateRangeReportDirectorStatus($dateFrom,$dateTo,$status); 
                        }
    
               
               default:
               
          break;
           }
//=================end check payment status====================
           
           	
            //=================throwing vtc data to the template====================
            

                    $vtcSummary =[

                        'vtcQuantity'        => count($vtc),
                        'vtcPaidQuantity'    => paidInstruments($vtc),
                        'vtcPendingQuantity' => pendingInstruments($vtc),
                        'paidVtc'            => paidAmount($vtc),
                        'pendingVtc'         => pendingAmount($vtc),
                        'totalVtc'           => totalAmount($vtc),
                    ];
            
                     $data['reportTitle'] = $title;
                     $data['vtcClients'] = $vtc;
                     $data['vtcSummary'] = $vtcSummary;
                     $dompdf->loadHtml(view('ReportTemplates/vtcReport',$data));
               break;
               //=================vtc WithDateRange report ends here====================
           #############################
           # 
           //=================SBL PRINTING====================
           #
           ##############################
               case 'sbl':
                $title = 'Sandy & Ballast Lorries ' .$reportTitle;
                //=================check payment status and render a report====================
           switch ($status) {
            case 'total':
             if($role == 1){

                 $sbl = $this->lorriesModel->sblWithDateRangeReportOfficer($uniqueId,$dateFrom,$dateTo,$status); 
             }
             elseif($role == 2){
                 $sbl = $this->lorriesModel->sblWithDateRangeReportManager($region,$dateFrom,$dateTo); 
             }
             elseif($role == 3){
                 $sbl = $this->lorriesModel->sblWithDateRangeReportDirector($dateFrom,$dateTo); 
             }
                break;

                case 'Paid':
                 if($role == 1){

                     $sbl = $this->lorriesModel->sblWithDateRangeReportOfficerStatus($uniqueId,$dateFrom,$dateTo,$status); 
                 }
                 elseif($role == 2){
                     $sbl = $this->lorriesModel->sblWithDateRangeReportManagerStatus($region,$dateFrom,$dateTo,$status); 
                 }
                 elseif($role == 3){
                     $sbl = $this->lorriesModel->sblWithDateRangeReportDirectorStatus($dateFrom,$dateTo,$status); 
                 }

                 break;
                 case 'Pending':
                     if($role == 1){
 
                         $sbl = $this->lorriesModel->sblWithDateRangeReportOfficerStatus($uniqueId,$dateFrom,$dateTo,$status); 
                     }
                     elseif($role == 2){
                         $sbl = $this->lorriesModel->sblWithDateRangeReportManagerStatus($region,$dateFrom,$dateTo,$status); 
                     }
                     elseif($role == 3){
                         $sbl = $this->lorriesModel->sblWithDateRangeReportDirectorStatus($dateFrom,$dateTo,$status); 
                     }
 
            
            default:
            
       break;
        }
//=================end check payment status====================
        
            
         //=================throwing SBL data to the template====================
         $sblSummary =[

            'sblQuantity'        => count($sbl),
            'sblPaidQuantity'    => paidInstruments($sbl),
            'sblPendingQuantity' => pendingInstruments($sbl),
            'paidSbl'            => paidAmount($sbl),
            'pendingSbl'         => pendingAmount($sbl),
            'totalSbl'           => totalAmount($sbl),
        ];

         $data['reportTitle'] = $title;
         $data['sblClients'] = $sbl;
         $data['sblSummary'] = $sblSummary;
         $dompdf->loadHtml(view('ReportTemplates/sblReport',$data));
         ##################################
         ##############WATER METER#################
         ###################################
              
               break;
           case 'water':
            $title = 'Water Meters ' .$reportTitle;
            //=================check payment status and render a report====================
       switch ($status) {
        case 'total':
         if($role == 1){

             $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportOfficer($uniqueId,$dateFrom,$dateTo,$status); 
         }
         elseif($role == 2){
             $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportManager($region,$dateFrom,$dateTo); 
         }
         elseif($role == 3){
             $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportDirector($dateFrom,$dateTo); 
         }
            break;

            case 'Paid':
             if($role == 1){

                 $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportOfficerStatus($uniqueId,$dateFrom,$dateTo,$status); 
             }
             elseif($role == 2){
                 $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportManagerStatus($region,$dateFrom,$dateTo,$status); 
             }
             elseif($role == 3){
                 $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportDirectorStatus($dateFrom,$dateTo,$status); 
             }

             break;
             case 'Pending':
                 if($role == 1){

                     $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportOfficerStatus($uniqueId,$dateFrom,$dateTo,$status); 
                 }
                 elseif($role == 2){
                     $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportManagerStatus($region,$dateFrom,$dateTo,$status); 
                 }
                 elseif($role == 3){
                     $waterMeter = $this->waterMeterModel->waterMeterWithDateRangeReportDirectorStatus($dateFrom,$dateTo,$status); 
                 }

        
        default:
        
   break;
    }
//=================end check payment status====================
    
        
     //=================throwing SBL data to the template====================
     $waterMeterSummary =[

        'waterMeterQuantity'        => count($waterMeter),
        'waterMeterPaidQuantity'    => paidInstruments($waterMeter),
        'waterMeterPendingQuantity' => pendingInstruments($waterMeter),
        'paidWaterMeter'            => paidAmount($waterMeter),
        'pendingWaterMeter'         => pendingAmount($waterMeter),
        'totalWaterMeter'           => totalAmount($waterMeter),
    ];

     $data['role'] = $this->role;
     $data['reportTitle'] = $title;
     $data['waterMeterClients'] = $waterMeter;
     $data['waterMeterSummary'] = $waterMeterSummary;
     $dompdf->loadHtml(view('ReportTemplates/waterMeterReport',$data));
              
               break;
          
            
       }
     
    
     
   
    // (Optional) Setup the paper size and orientation
     $dompdf->setPaper('A4', 'landscape');
     $options->set('isRemoteEnabled', TRUE);
    
    // Render the HTML as PDF
     $dompdf->render();
    
    $dompdf->stream($title.':'.$date. '.pdf', array('Attachment' => 0));	

//    }
   

?>