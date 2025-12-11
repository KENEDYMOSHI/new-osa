<?php namespace App\Libraries ;
use App\Models\MiscellaneousModel;
use App\Libraries\ActivitiesLibrary;



class DownloadReport{
    public function activitiesDownload($dataArray)
    {
        $activities = new ActivitiesLibrary();
        $contacts = new MiscellaneousModel();
        $date = date('d-M,Y h:i:s ');
        $dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();

        switch ($dataArray->activity) {
            case 'All':
                $title = 'All Activities ' . $dataArray->reportTitle;


                $allActivities = $activities->allActivities($dataArray);
              
                $data['allActivities'] = $allActivities;
                $data['reportTitle'] = $title;
                $data['collectionRegion'] = $dataArray->collectionRegion;

                $theRegion = ($dataArray->collectionRegion == 'Tanzania') ? 'Tanzania' : $dataArray->collectionRegion;
                $data['contacts'] = renderContacts($contacts->getContacts($theRegion));

                //=================loading a report template====================
                $dompdf->loadHtml(view('ReportTemplates/allActivities', $data));
                break;

                //=================VTC quarter report start====================
            case 'vtc':
                $title = 'Vehicle Tank Verification ' . $dataArray->reportTitle;


                $vtcSummary = $activities->allActivities($dataArray)['vtc'];
             

                $data['role'] = $dataArray->role;
                $data['collectionRegion'] = $dataArray->collectionRegion;
                $data['reportTitle'] = $title;
                $data['vtcClients'] = $dataArray->vtc;
                $data['vtcSummary'] = $vtcSummary;


                $theRegion = ($dataArray->collectionRegion == 'Tanzania') ? $dataArray->city : $dataArray->collectionRegion;
                $data['contacts'] = renderContacts($contacts->getContacts($dataArray->collectionRegion));
                $dompdf->loadHtml(view('ReportTemplates/vtcReport', $data));
                break;
                //=================vtc quarter report ends here====================
                #############################
                #
                //=================SBL PRINTING====================
                #
                ##############################
            case 'sbl':
                $title = 'Sandy & Ballast Lorries ' . $dataArray->reportTitle;
                //=================check payment status and render a report====================

                $sblSummary = $activities->allActivities($dataArray)['sbl'];
                
               
                $data['role'] = $dataArray->role;
                $data['collectionRegion'] = $dataArray->collectionRegion;
                $data['reportTitle'] = $title;
                $data['sblClients'] = $dataArray->sbl;
                $data['sblSummary'] = $sblSummary;
                $theRegion = ($dataArray->collectionRegion == 'Tanzania') ? $dataArray->city : $dataArray->collectionRegion;
                $data['contacts'] = renderContacts($contacts->getContacts($theRegion));
                $dompdf->loadHtml(view('ReportTemplates/sblReport', $data));
                ##################################
                ##############WATER METER#################
                ###################################

                break;
            case 'water':
                $title = 'Water Meters ' . $dataArray->reportTitle;

                $waterMeterSummary = $activities->allActivities($dataArray)['waterMeter'];
              

                $data['role'] = $dataArray->role;
                $data['collectionRegion'] = $dataArray->collectionRegion;
                $data['reportTitle'] = $title;
                $data['waterMeterClients'] = $dataArray->waterMeter;
                $data['waterMeterSummary'] = $waterMeterSummary;
                $theRegion = ($dataArray->collectionRegion == 'Tanzania') ? $dataArray->city : $dataArray->collectionRegion;
                $data['contacts'] = renderContacts($contacts->getContacts($theRegion));
                $dompdf->loadHtml(view('ReportTemplates/waterMeterReport', $data));

                break;
            case 'prePackage':
                $title = 'Pre Package' . $dataArray->reportTitle;

                $prePackageSummary = $activities->allActivities($dataArray)['prePackage'];
              

                $data['role'] = $dataArray->role;
                $data['collectionRegion'] = $dataArray->collectionRegion;
                $data['reportTitle'] = $title;
                $data['prePackageData'] = $dataArray->prePackage;
                $data['prePackageSummary'] = $prePackageSummary;
                $theRegion = ($dataArray->collectionRegion == 'Tanzania') ? $dataArray->city : $dataArray->collectionRegion;
                $data['contacts'] = renderContacts($contacts->getContacts($theRegion));
             
                $dompdf->loadHtml(view('ReportTemplates/prePackagingReport', $data));

                break;
        }

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        //$dompdf->setBasePath(base_url() . '/dist/css/fonts.css');
        $options->set('isRemoteEnabled', true);

        // Render the HTML as PDF
        $dompdf->render();

        $dompdf->stream($title . ':' . time() . '.pdf', array('Attachment' => 0));
    }
}


?>