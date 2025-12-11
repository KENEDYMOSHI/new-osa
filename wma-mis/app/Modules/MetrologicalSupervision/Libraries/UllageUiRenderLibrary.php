<?php

namespace App\Modules\MetrologicalSupervision\Libraries;

class UllageUiRenderLibrary
{

    public function renderUllageReport($ullage, $ullageSummary, $totals, $ullageType, $shoreFigures)
    {

        $summary = $this->ullageSummary($ullageSummary, $ullageType, $shoreFigures);
        $tr = '';
        foreach ($ullage as $data) {
            $gov = $data->total_observed_volume - $data->free_water_volume;
            $vcf54B = number_format($data->vcf_54b_15, 4, '.', '');
            $vcf60B = number_format($data->vcf_60b_20, 4, '.', '');
            $tr .= <<<HTML
                <tr>
                    <td style="font-size:11.5px">{$data->tank_name}</td>
                    <td style="font-size:11.5px">{$data->corrected_ullage}</td>
                    <td style="font-size:11.5px">{$data->observed_temp}</td>
                    <td style="font-size:11.5px">{$data->total_observed_volume}</td>
                    <td style="font-size:11.5px">{$data->free_water_dip}</td>
                    <td style="font-size:11.5px">{$data->free_water_volume}</td>
                    <td style="font-size:11.5px">{$gov}</td>
                    <td style="font-size:11.5px">{$vcf54B}</td>
                    <td style="font-size:11.5px">{$data->gross_standard_volume_15}</td>
                    <td style="font-size:11.5px">{$vcf60B}</td>
                    <td style="font-size:11.5px">{$data->gross_standard_volume_20}</td>
                </tr>
            HTML;
        }
        $table = <<<HTML
           <div class="table-responsive">
            <table id="ullageTable" class="table table-bordered main-table" border="1" >
                <thead>
                    <tr>
                        <th >TANK No.</th>
                        <th >CORRECTED<br>ULLAGE (m)</th>
                        <th >OBSVD TEMP °C</th>
                        <th >TOTAL OBSVD<br>Vol m³</th>
                        <th >FREE WATER<br>(m)</th>
                        <th >FREE WATER Vol.<br>m³</th>
                        <th >GROSS OBSVD<br>Vol. (m³)</th>
                        <th >V.C.F TABLE-<br>54B @15°C</th>
                        <th >Gross.S.V @15°C<br>(m³)</th>
                        <th >V.C.F TABLE-<br>60B @20°C</th>
                        <th >Gross.S.V @20°C<br>(m³)</th>
                    </tr>
                </thead>
                <tbody>
                     {$tr}
                  
                    <tr class="total-row">
                        <td style="font-size:11.5px">TOTAL G.S.V (m³)</td>
                        <td style="font-size:11.5px"></td>
                        <td style="font-size:11.5px">{$totals->observedTemperature}</td>
                        <td style="font-size:11.5px">{$totals->totalObservedVolume}</td>
                        <td style="font-size:11.5px"></td>
                        <td style="font-size:11.5px"></td>
                        <td style="font-size:11.5px">{$totals->grossObservedVolume}</td>
                        <td style="font-size:11.5px">{$totals->totalVCFTable54bAt15}</td>
                        <td style="font-size:11.5px">{$totals->grossStandardVolumeAt15}</td>
                        <td style="font-size:11.5px">{$totals->totalVcfTable60bAtTwenty}</td>
                        <td style="font-size:11.5px">{$totals->grossStandardVolumeAtTwenty}</td>
                    </tr>
                </tbody>
            </table>
        </div>
 
           {$summary} 
                   
        HTML;

        return $table;
    }


    public function ullageSummary($ullageSummary, $ullageType, $shoreFigures)
    {
        if ($ullageType == 'ARRIVAL_DISCHARGE_PORT_INITIAL' || $ullageType == 'LOAD_PORT_AFTER_LOADING'|| 'TERMINAL_BEFORE_DISCHARGE') {

            $summary = <<<HTML
                <div class="">
            <div class="summary-table">
                <table id="ullageSummary" class="table table-bordered" border= '1'   style="width:50%;">
                    <thead>
                        <tr class="standard-temp-header">
                            <th></th>
                            <th colspan="2">STANDARD TEMPERATURE</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="temp-15">15°C</th>
                            <th class="temp-20">20°C</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>TOTAL G.S.V (m³)</strong></td>
                            <td>{$ullageSummary->totalGSV15}</td>
                            <td>{$ullageSummary->totalGSV20}</td>
                        </tr>
                        <tr>
                            <td><strong>W.C.F.T-56/C</strong></td>
                            <td>{$ullageSummary->WCFT_56CAt15}</td>
                            <td>{$ullageSummary->WCFT_56CAt20}</td>
                        </tr>
                        <tr>
                            <td><strong>Reference Density</strong></td>
                            <td>{$ullageSummary->densityAtFifteen}</td>
                            <td>{$ullageSummary->densityAtTwenty}</td>
                        </tr>
                        <tr>
                            <td><strong>QUANTITY BEFORE DISCHARGE (MT)</strong></td>
                            <td>{$ullageSummary->quantityBeforeDischargeAtFifteenMT}</td>
                            <td>{$ullageSummary->quantityBeforeDischargeAtTwentyMT}</td>
                        </tr>
                        <tr>
                            <td><strong>VOLUME(L)</strong></td>
                            <td>{$ullageSummary->quantityBeforeDischargeAtFifteenVolume}</td>
                            <td>{$ullageSummary->quantityBeforeDischargeAtTwentyVolume}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
          </div>        
         HTML;
        } else {
            $quantityAfterDischarge15 = $ullageSummary->totalGSV15 * $ullageSummary->WCFT_56CAt15;
            $quantityAfterDischarge20 = $ullageSummary->totalGSV20 * $ullageSummary->WCFT_56CAt20;

            $quantityDischarged15 = $ullageSummary->quantityBeforeDischargeAtFifteenMT - $quantityAfterDischarge15;
            $quantityDischarged20 = $ullageSummary->quantityBeforeDischargeAtTwentyMT - $quantityAfterDischarge20;

            $volumeAtFifteen = 00.00;//$quantityDischarged15 / $ullageSummary->WCFT_56CAt15;
            $volumeAtTwenty = 00.00;//$quantityDischarged20 / $ullageSummary->WCFT_56CAt20;


            $shoreGOV = $shoreFigures->shoreGOV ?? 0.00;
            $shoreGSV = $shoreFigures->shoreGSV ?? 0.00;
            $shoreWeights = $shoreFigures->shoreWeights ?? 0.00;

            $diff1 = 00;
            $diff2 = 00;
            $diff3 = 00;


            $summary = <<<HTML
                  <table class="table table-bordered table-hover" border="1">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 25%;">Parameter</th>
                            <th colspan="2" >STANDARD TEMPERATURE</th>
                            <th colspan="2" class="shore-header">SHORE FIGURE:</th>
                            <th rowspan="2" style="background-color: #f5c6cb;">DIFF</th>
                        </tr>
                        <tr>
                            <th >15°C</th>
                            <th >20°C</th>
                            <th class="shore-header">Parameter</th>
                            <th class="shore-header">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="row-header">TOTAL G.S.V (m³)</td>
                            <td>{$ullageSummary->totalGSV15}</td>
                            <td>{$ullageSummary->totalGSV20}</td>
                            <td class="row-header">SHORE G.O.V (m³)</td>
                            <td>{$shoreGOV}</td>
                            <td class="negative-value">{$diff1}</td>
                        </tr>
                        <tr>
                            <td class="row-header">W.C.F.T-56/C</td>
                             <td>{$ullageSummary->WCFT_56CAt15}</td>
                            <td>{$ullageSummary->WCFT_56CAt20}</td>
                            <td class="row-header">SHORE G.S.V (m³)</td>
                            <td>{$shoreGSV}</td>
                            <td class="negative-value">{$diff2}</td>
                        </tr>
                        <tr>
                            <td class="row-header">Reference Density</td>
                            <td>{$ullageSummary->densityAtFifteen}</td>
                            <td>{$ullageSummary->densityAtTwenty}</td>
                            <td class="row-header">WEIGHTS (MT)</td>
                            <td>{$shoreWeights}</td>
                            <td class="negative-value">{$diff3}</td>
                        </tr>
                        <tr>
                            <td class="row-header">QUANTITY BEFORE DISCHARGE (MT)</td>
                            <td>{$ullageSummary->quantityBeforeDischargeAtFifteenMT}</td>
                            <td>{$ullageSummary->quantityBeforeDischargeAtTwentyMT}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="row-header">QUANTITY AFTER DISCHARGE (MT)</td>
                            <td>{$quantityAfterDischarge15}</td>
                            <td>{$quantityAfterDischarge20}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="row-header">QUANTITY DISCHARGED (MT)</td>
                            <td>{$quantityDischarged15}</td>
                            <td>{$quantityDischarged20}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="row-header">VOLUME (m³)</td>
                            <td>{$volumeAtFifteen}</td>
                            <td>{$volumeAtTwenty}</td>
                            <td class="row-header">VESSEL G.O.V (m³)</td>
                            <td>1491.200</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>        
            HTML;
        }
        return $summary;
    }




    public function renderNoteOfFactBeforeDischarge($noteOfFact)
    {


        $html = <<<HTML
                 <!-- First Table -->
          <table class="table table-bordered note" border ='1' style="border-collapse: collapse; width: 100%; margin-top: 10px;">
            <tbody>
                <tr>
                    <td><strong>BILL OF LADING (MT)</strong></td>
                    <td><strong>{$noteOfFact->billOfLading}</strong></td>
                    <td><strong>VESSEL FIGURE AFTER LOADING (MT)</strong></td>
                    <td><strong>{$noteOfFact->vesselFiguresAfterLoading}</strong></td>
                </tr>
                <tr>
                    <td><strong>VESSEL ARRIVAL QUANTITIES (MT)</strong></td>
                    <td><strong>{$noteOfFact->arrivalQuantityMT}</strong></td>
                    <td><strong>VESSEL ARRIVAL QUANTITIES (MT)</strong></td>
                    <td><strong>{$noteOfFact->arrivalQuantityMT}</strong></td>
                </tr>
                <tr>
                    <td><strong>DIFFERENCE</strong></td>
                    <td><strong>{$noteOfFact->BL_ArrivalDifference}</strong></td>
                    <td><strong>DIFFERENCE</strong></td>
                    <td><strong>{$noteOfFact->vesselFigures_ArrivalDifference}</strong></td>
                </tr>
                <tr>
                    <td><strong>%-DIFFERENCE</strong></td>
                    <td><strong>{$noteOfFact->BL_ArrivalDifferencePercentage}</strong></td>
                    <td><strong>%-DIFFERENCE</strong></td>
                    <td><strong>{$noteOfFact->vesselFigures_ArrivalDifferencePercentage}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- VEF Section -->
        <div class="vef-section">
            <div>After adjusting ship's figure with the Vessel Experience Factor (VEF) {$noteOfFact->VEF}</div>
            <div>The following is noted:</div>
            <div style="float: right; font-size: 1.2em;"></div>
            <div style="clear: both;"></div>
        </div>

        <!-- Second Table -->
        <table class="table table-bordered note" border ='1' style="border-collapse: collapse; width: 100%; margin-top: 10px;">
            <tbody>
                <tr>
                    <td><strong>BILL OF LADING (MT)</strong></td>
                    <td><strong>{$noteOfFact->billOfLading}</strong></td>
                    <td><strong>VESSEL FIGURE AFTER LOADING (MT)</strong></td>
                    <td><strong>{$noteOfFact->vesselAdjusted}</strong></td>
                </tr>
                <tr>
                    <td><strong>VESSEL ARRIVAL QUANTITIES (MT)</strong></td>
                    <td><strong>{$noteOfFact->arrivalAdjusted}</strong></td>
                    <td><strong>VESSEL ARRIVAL QUANTITIES (MT)</strong></td>
                    <td><strong>{$noteOfFact->arrivalAdjusted}</strong></td>
                </tr>
                <tr>
                    <td><strong>DIFFERENCE</strong></td>
                    <td><strong>{$noteOfFact->BL_ArrivalDifferenceAdjusted}</strong></td>
                    <td><strong>DIFFERENCE</strong></td>
                    <td><strong>{$noteOfFact->vesselFigures_ArrivalDifferenceAdjusted}</strong></td>
                </tr>
                <tr>
                    <td><strong>%-DIFFERENCE</strong></td>
                    <td><strong>{$noteOfFact->BL_ArrivalDifferenceAdjustedPercentage}</strong></td>
                    <td><strong>%-DIFFERENCE</strong></td>
                    <td><strong>{$noteOfFact->vesselFigures_ArrivalDifferenceAdjustedPercentage}</strong></td>
                </tr>
            </tbody>
        </table>      
     HTML;

        return $html;
    }


    public function renderCOQ($data)
    {
        // a table with tr and 2 td for each row, each td is 50% of the width 9 rows

        $html = <<<HTML
            <table class="table coq table-bordered" border ='1' style="border-collapse: collapse; width: 100%; margin-top: 10px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong>Metric Tones in Air</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->metric_tons_air}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong>Metric Tones in Vac</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->metric_tons_vacuum}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong>Long Tons</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->long_tons}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong>Litres At 20 &deg; C</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->litres_20c}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong>Litres At 15 &deg; C</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->litres_15c}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong>USBBLS @ 60 &#8457;</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->us_bbls_60f}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong>US GALLONS @ 60 &#8457;</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->us_gallons_60f}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong>Std Density @ 20 &deg; C</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->density_20c}</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align:left;"><strong> Density @ 15 &deg; C</strong> </td>
                        <td style="width: 50%; text-align:left;">{$data->density_15c}</td>
                    </tr>

                   
                </tbody>
            </table>
        HTML;
        return $html;
    }

public function coqForm($data) {
    $html = <<<HTML
        <form id="coqForm">
            <input type="text" value="$data->id" name="id" hidden>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="metric_tons_air">Metric Tons In Air</label>
                        <input type="number" class="form-control" name="metric_tons_air" readonly id="metric_tons_air" value="$data->metric_tons_air">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="metric_tons_vacuum">Metric Tons In Vac</label>
                        <input type="number" class="form-control" name="metric_tons_vacuum" id="metric_tons_vacuum" value="$data->metric_tons_vacuum">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="long_tons">Long Tons In</label>
                        <input type="number" class="form-control" name="long_tons" id="long_tons" value="$data->long_tons">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="litres_20c">Litres @ 20</label>
                        <input type="number" class="form-control" name="litres_20c" id="litres_20c" value="$data->litres_20c">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="litres_15c">Litres @ 15</label>
                        <input type="number" class="form-control" name="litres_15c" id="litres_15c" value="$data->litres_15c">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="us_bbls_60f">USBBLS @ 60F</label>
                        <input type="number" class="form-control" name="us_bbls_60f" id="us_bbls_60f" value="$data->us_bbls_60f">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="us_gallons_60f">USGALLONS @ 60F</label>
                        <input type="number" class="form-control" name="us_gallons_60f" id="us_gallons_60f" value="$data->us_gallons_60f">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="density_20c">Std Density @20</label>
                        <input type="number" class="form-control" name="density_20c" id="density_20c" readonly value="$data->density_20c">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="density_15c">Density @20</label>
                        <input type="number" class="form-control" name="density_15c" id="density_15c" readonly value="$data->density_15c">
                    </div>
                </div>
            </div>
            
        </form>           
    HTML;

    return $html;
}
    
}
