<?php

namespace App\Modules\MetrologicalSupervision\Libraries;

use App\Libraries\ArrayLibrary;
use App\Modules\MetrologicalSupervision\Models\ShipModel;
use App\Modules\MetrologicalSupervision\Models\TankModel;
use App\Modules\MetrologicalSupervision\Models\BerthModel;
use App\Modules\MetrologicalSupervision\Models\PortsModel;
use App\Modules\MetrologicalSupervision\Models\VoyageModel;
use App\Modules\MetrologicalSupervision\Models\ProductModel;
use App\Modules\MetrologicalSupervision\Models\TerminalModel;
use App\Modules\MetrologicalSupervision\Models\UllageReportModel;
use App\Modules\MetrologicalSupervision\Models\UllageReadingModel;
use App\Modules\MetrologicalSupervision\Models\VoyageProductModel;
use App\Modules\MetrologicalSupervision\Libraries\UllageUiRenderLibrary;

class UllageLibrary
{
    protected $user;
    protected $uniqueId;
    protected $token;
    protected $views;
    protected $shipModel;
    protected $tankModel;
    protected $productModel;
    protected $terminalModel;
    protected $voyageModel;
    protected $shipId;
    protected $voyageId;
    protected $voyageProductId;
    protected $ullageReport;
    protected $ullageReadingModel;
    protected $voyageProductModel;
    protected $reportType;
    protected $ullageReportModel;
    protected $voyageUiLibrary;
    protected $terminalId;
    protected $portsModel;
    protected $terminalSequenceNumber;
    protected $berthModel;

    /**
     * Constructor for UllageLibrary
     * 
     * @param int|null $voyageId The voyage ID
     * @param string|null $reportType The report type
     * @param int|null $terminalId The terminal ID
     * @param int|null $voyageProductId The voyage product ID
     * @param int|null $terminalSequenceNumber The terminal sequence number (for duplicate terminals)
     */
    public function __construct($voyageId = null, $reportType = null, $terminalId = null, $voyageProductId = null, $terminalSequenceNumber = null)
    {
        $this->user = auth()->user();
        $this->uniqueId = $this->user ? $this->user->unique_id : null;
        $this->token = csrf_hash();
        $this->views = 'App\Modules\MetrologicalSupervision\Views\\';

        $this->shipModel = new ShipModel();
        $this->tankModel = new TankModel();
        $this->productModel = new ProductModel();
        $this->terminalModel = new TerminalModel();
        $this->portsModel = new PortsModel();
        $this->berthModel = new BerthModel();
        $this->voyageModel = new VoyageModel();
        $this->ullageReadingModel = new UllageReadingModel();
        $this->ullageReportModel = new UllageReportModel();
        $this->voyageProductModel = new VoyageProductModel();
        $this->voyageUiLibrary = new UllageUiRenderLibrary();


        $this->voyageId = $voyageId;
        $this->reportType = $reportType;
        $this->terminalId = $terminalId;
        $this->voyageProductId = $voyageProductId;
        $this->terminalSequenceNumber = $terminalSequenceNumber;

        // Get shipId from voyage if voyageId is provided
        if ($voyageId) {
            $voyage = $this->voyageModel->find($voyageId);
            $this->shipId = $voyage ? $voyage->ship_id : null;
        }
    }

    public function getShip()
    {
        return $this->shipModel->where([
            'id' => $this->getVoyage()->ship_id,
        ])->first();
    }



    public function getVoyage()
    {
        return $this->voyageModel->where([
            'id' => $this->voyageId,
        ])->first();
    }

    public function getVoyageProduct()
    {
        if ($this->voyageProductId) {
            $voyageProduct = $this->voyageProductModel->find($this->voyageProductId);
            if (!$voyageProduct) {
                log_message('warning', "UllageLibrary: Voyage product not found with ID {$this->voyageProductId}");
            }
            return $voyageProduct;
        }

        $voyageProduct = $this->voyageProductModel->where([
            'voyage_id' => $this->voyageId,
        ])->first();

        if (!$voyageProduct) {
            log_message('warning', "UllageLibrary: No voyage product found for voyage {$this->voyageId}");
        }

        return $voyageProduct;
    }

    public function getBillOfLading()
    {
        $voyageProduct = $this->getVoyageProduct();
        return $voyageProduct ? ($voyageProduct->bol_quantity ?? 0.000) : 0.000;
    }

    public function getUllageReport()
    {
        $conditions = [
            'voyage_id' => $this->voyageId,
            'report_type' => $this->reportType,
        ];

        // If voyage_product_id is specified, add it to the filter
        if ($this->voyageProductId) {
            $conditions['voyage_product_id'] = $this->voyageProductId;
        }

        // For terminal reports, filter by terminal ID to get the specific terminal's report
        if (
            $this->terminalId && in_array($this->reportType, [
                'TERMINAL_BEFORE_DISCHARGE',
                'TERMINAL_AFTER_LINE_DISPLACE',
                'TERMINAL_AFTER_FULL_DISCHARGE'
            ])
        ) {
            $conditions['discharge_terminal_id'] = $this->terminalId;

            // CRITICAL: Include terminal sequence number for duplicate terminals
            if ($this->terminalSequenceNumber !== null) {
                $conditions['terminal_sequence_number'] = $this->terminalSequenceNumber;
            }
        }

        // For duplicate terminals, order by sequence number and created date to get the correct instance
        $query = $this->ullageReportModel->where($conditions);

        if ($this->terminalId && $this->terminalSequenceNumber === null) {
            // If no sequence number specified but we have terminal, get the most recent one
            $query->orderBy('terminal_sequence_number', 'DESC')->orderBy('created_at', 'DESC');
        }

        return $query->first();
    }

    public function basicInfo()
    {
        $voyage = $this->getVoyage();
        $ullage = $this->getUllageReport();
        $ship = $this->getShip();
        $voyageProduct = $this->getVoyageProduct();
        $terminal = $this->terminalModel->find($this->terminalId);
        $product = $voyageProduct ? $this->productModel->find($voyageProduct->product_id) : null;

        $port = $this->portsModel->selectPort([
            'id' => $voyage->loading_port_id,
        ]);

        $berth = $this->berthModel->where([
            'id' => $voyage->arrival_port_berth,
        ])->first();

        return (object) [
            'shipName' => $ship ? $ship->name : '',
            'product' => $product ? $product->name : '',
            'port' => $port ? $port->name : '',
            'terminal' => $this->terminalId == null ? '' : $terminal->name,
            'berth' => $berth ? $berth->name : '',
            'fwd' => number_format($ullage->draft_fwd_m, 1),
            'aft' => number_format($ullage->draft_aft_m, 1),
            'trim' => number_format($ullage->trim_m, 1),
            'ulg' => $ullage
        ];
    }

    public function getUllageReadings()
    {
        $report = $this->getUllageReport();
        if (!$report) {
            return [];
        }

        // Join with tanks table to get tank names
        return $this->ullageReadingModel
            ->select('ullage_readings.*, tanks.name as tank_name')
            ->join('tanks', 'tanks.id = ullage_readings.tank_id', 'left')
            ->where(['ullage_report_id' => $this->getUllageReport()->id ?? ''])
            ->findAll();
    }

    public function getShoreFigures()
    {
        return $this->ullageReportModel->getShoreFigures([
            'ullage_report_id' => $this->getUllageReport()->id ?? '',

        ]);
    }




    /**
     * Get the total observed temperature.
     *
     * This method calculates the average observed temperature from the ullage data.
     *
     * @return float The average observed temperature formatted to 4 decimal places.
     */
    public function getTotalObservedTemperature()
    {
        return $this->calculateAverage('observed_temp');
    }


    /**
     * Get the total observed volume.
     *
     * This method calculates the total observed volume from the ullage data.
     *
     * @return float The total observed volume formatted to 3 decimal places.
     */

    public function getTotalObservedVolume(): float
    {
        return $this->calculateSum('total_observed_volume');
    }


    /**
     * Get the gross observed volume.
     *
     * This method calculates the total gross observed volume from the ullage data.
     *
     * @return float The total gross observed volume formatted to 3 decimal places.
     */

    public function getGrossObservedVolume(): float
    {
        return $this->calculateSum('total_observed_volume');
    }


    /**
     * Get the total VCF Table 54b at 15 degrees Celsius.
     *
     * @return float The average VCF Table 54b at 15 degrees Celsius.
     */

    public function getTotalVcfTable54bAtFifteen(): float
    {
        return $this->calculateAverage('vcf_54b_15');
    }



    /**
     * Get the gross standard volume at 15 degrees Celsius.
     *
     * @return float The gross standard volume at 15 degrees Celsius.
     */

    public function getGrossStandardVolumeAtFifteen()
    {
        return $this->calculateSum('gross_standard_volume_15');
    }





    //

    /**
     * Get the total of gross standard volume at 20 degrees Celsius.
     *
     * @return float The total gross standard volume at 20 degrees Celsius, 
     */


    public function grossStandardVolumeAtTwenty()
    {
        return $this->calculateSum('gross_standard_volume_20');
    }


    /**
     * Get the total VCF Table 60b at 20 degrees Celsius.
     *
     * @return float The average VCF Table 60b at 20 degrees Celsius.
     */

    public function getTotalVcfTable60bAtTwenty()
    {
        return $this->calculateAverage('vcf_60b_20');
    }


    /**
     * Get the gross standard volume at 20 degrees Celsius.
     *
     * @return float The gross standard volume at 20 degrees Celsius.
     */
    public function getGrossStandardVolumeAtTwenty()
    {
        return $this->calculateSum('gross_standard_volume_20');
    }



    /**
     * Generate a note of fact before discharge.
     *
     * This method calculates various differences and adjustments related to the Bill of Lading and vessel figures
     * before discharge, returning an object with the results.
     *
     * @return object The note of fact containing various calculated values.
     */
    public function noteOfFactBeforeDischarge()
    {
        $loadPortUllage = new self($this->voyageId, 'LOAD_PORT_AFTER_LOADING');
        $arrivalUllage = new self($this->voyageId, 'ARRIVAL_DISCHARGE_PORT_INITIAL');

        $VEF = $this->getVoyage()->vef;
        $billOlLading = $loadPortUllage->getBillOfLading();
        $vesselFiguresAfterLoading = $loadPortUllage->ullageSummary()->quantityBeforeDischargeAtTwentyMT;
        $arrivalQuantityMT = $arrivalUllage->ullageSummary()->quantityBeforeDischargeAtTwentyMT;

        $BL_ArrivalDifference = $arrivalQuantityMT - $billOlLading;
        $BL_ArrivalDifferencePercentage = ($billOlLading != 0) ? ($BL_ArrivalDifference / $billOlLading) * 100 : 0;

        $vesselFigures_ArrivalDifference = $arrivalQuantityMT - $vesselFiguresAfterLoading;
        $vesselFigures_ArrivalDifferencePercentage = ($vesselFiguresAfterLoading != 0) ? ($vesselFigures_ArrivalDifference / $vesselFiguresAfterLoading) * 100 : 0;

        $vesselAdjusted = ($VEF != 0) ? $vesselFiguresAfterLoading / $VEF : 0;
        $arrivalAdjusted = ($VEF != 0) ? $arrivalQuantityMT / $VEF : 0;

        $BL_ArrivalDifferenceAdjusted = $arrivalAdjusted - $billOlLading;
        $BL_ArrivalDifferenceAdjustedPercentage = ($billOlLading != 0) ? ($BL_ArrivalDifferenceAdjusted / $billOlLading) * 100 : 0;

        $vesselFigures_ArrivalDifferenceAdjusted = $arrivalAdjusted - $vesselAdjusted;
        $vesselFigures_ArrivalDifferenceAdjustedPercentage = ($vesselFiguresAfterLoading != 0) ? ($vesselFigures_ArrivalDifferenceAdjusted / $vesselFiguresAfterLoading) * 100 : 0;

        $noteOfFact = (object) [
            'VEF' => $VEF,
            'billOfLading' => $billOlLading,
            'vesselFiguresAfterLoading' => round($vesselFiguresAfterLoading, 3),
            'arrivalQuantityMT' => round($arrivalQuantityMT, 3),
            'BL_ArrivalDifference' => round($BL_ArrivalDifference, 3),
            'BL_ArrivalDifferencePercentage' => round($BL_ArrivalDifferencePercentage, 3),
            'vesselFigures_ArrivalDifference' => round($vesselFigures_ArrivalDifference, 3),
            'vesselFigures_ArrivalDifferencePercentage' => round($vesselFigures_ArrivalDifferencePercentage, 3),
            'vesselAdjusted' => round($vesselAdjusted, 3),
            'arrivalAdjusted' => round($arrivalAdjusted, 3),
            'BL_ArrivalDifferenceAdjusted' => round($BL_ArrivalDifferenceAdjusted, 3),
            'BL_ArrivalDifferenceAdjustedPercentage' => round($BL_ArrivalDifferenceAdjustedPercentage, 3),
            'vesselFigures_ArrivalDifferenceAdjusted' => round($vesselFigures_ArrivalDifferenceAdjusted, 3),
            'vesselFigures_ArrivalDifferenceAdjustedPercentage' => round($vesselFigures_ArrivalDifferenceAdjustedPercentage, 3),
        ];

        return $noteOfFact;
    }



    public function ullageSummary()
    {
        $voyageProduct = $this->getVoyageProduct();

        // Safety check for null voyage product
        if (!$voyageProduct) {
            return (object) [
                'totalGSV15' => 0.000,
                'totalGSV20' => 0.000,
                'WCFT_56CAt15' => 0.000,
                'WCFT_56CAt20' => 0.000,
                'densityAtFifteen' => '0.000',
                'densityAtTwenty' => '0.000',
                'quantityBeforeDischargeAtFifteenMT' => 0.000,
                'quantityBeforeDischargeAtTwentyMT' => 0.000,
                'quantityBeforeDischargeAtFifteenVolume' => 0.000,
                'quantityBeforeDischargeAtTwentyVolume' => 0.000,
            ];
        }

        $densityAtFifteen = $this->reportType === 'LOAD_PORT_AFTER_LOADING'
            ? ($voyageProduct->load_port_ref_density_15 ?? 0.000)
            : ($voyageProduct->tbs_ref_density_15 ?? 0.000);

        $densityAtTwenty = $this->reportType === 'LOAD_PORT_AFTER_LOADING'
            ? ($voyageProduct->load_port_ref_density_20 ?? 0.000)
            : ($voyageProduct->tbs_ref_density_20 ?? 0.000);

        $WCFT_56CAt15 = $densityAtFifteen - 0.0011;
        $WCFT_56CAt20 = $densityAtTwenty - 0.0011;
        $totalGSV15 = $this->getGrossStandardVolumeAtFifteen();
        $totalGSV20 = $this->getGrossStandardVolumeAtTwenty();

        return (object) [
            'totalGSV15' => $totalGSV15,
            'totalGSV20' => $totalGSV20,
            'WCFT_56CAt15' => $WCFT_56CAt15,
            'WCFT_56CAt20' => $WCFT_56CAt20,
            'densityAtFifteen' => $densityAtFifteen ?? '0.000',
            'densityAtTwenty' => $densityAtTwenty ?? '0.000',
            'quantityBeforeDischargeAtFifteenMT' => round($totalGSV15 * $WCFT_56CAt15, 3),
            'quantityBeforeDischargeAtTwentyMT' => round($totalGSV20 * $WCFT_56CAt20, 3),
            'quantityBeforeDischargeAtFifteenVolume' => $totalGSV15 * 1000,
            'quantityBeforeDischargeAtTwentyVolume' => $totalGSV20 * 1000,
        ];
    }




    /**
     * Sum a specific field in the ullage data.
     *
     * @param string $field The field to sum.
     * @return float The sum formatted to 3 decimal places.
     */


    protected function calculateSum(string $field): float
    {
        $ullage = $this->getUllageReadings();

        if (empty($ullage) || !is_iterable($ullage)) {
            return 0.000;
        }

        $sum = (new ArrayLibrary($ullage))
            ->map(fn($u) => floatval($u->$field ?? 0))
            ->reduce(fn($x, $y) => $x + $y, 0)
            ->get();

        // Truncate to 3 decimal places
        $truncated = floor($sum * 1000) / 1000;

        return (float) number_format($truncated, 3, '.', '');
    }


    /**
     * Calculate the average of a specific field in the ullage data.
     *
     * @param string $field The field to average.
     * @return float The average value formatted to 4 decimal places.
     */

    public function calculateAverage(string $field): float
    {
        $ullage = $this->getUllageReadings();

        if (empty($ullage)) {
            return 0.000;
        }

        $count = count($ullage);

        $sum = (new ArrayLibrary($ullage))
            ->map(fn($u) => (float) ($u->$field ?? 0))
            ->reduce(fn($x, $y) => $x + $y, 0)
            ->get();

        $average = $sum / $count;

        return (float) number_format(round($average, 4), 4, '.', '');
    }

    /**
     * Render the ullage report using the VoyageUiRenderLibrary.
     *
     * This method retrieves ullage data and summary, then renders the ullage report.
     *
     * @return string The rendered ullage report.
     */
    public function renderUllage()
    {
        $totals = (object) [
            'observedTemperature' => round($this->getTotalObservedTemperature(), 1),
            'totalObservedVolume' => $this->getTotalObservedVolume(),
            'grossObservedVolume' => $this->getGrossObservedVolume(),
            'totalVCFTable54bAt15' => $this->getTotalVcfTable54bAtFifteen(),
            'grossStandardVolumeAt15' => $this->getGrossStandardVolumeAtFifteen(),
            'totalVcfTable60bAtTwenty' => $this->getTotalVcfTable60bAtTwenty(),
            'grossStandardVolumeAtTwenty' => $this->getGrossStandardVolumeAtTwenty(),
        ];
        $ullageData = $this->getUllageReadings();
        $ullageSummary = $this->ullageSummary();
        $subUllage = ['subsequentUllageLineDisplacement', 'subsequentUllageAfterDischarge'];
        $shoreFigures = in_array($this->reportType, $subUllage) ? $this->getShoreFigures() : [];
        $ullageReport = $this->voyageUiLibrary->renderUllageReport($ullageData, $ullageSummary, $totals, $this->reportType, $shoreFigures);
        return $ullageReport;
    }


    public function renderNoteOfFactBeforeDischarge()
    {
        $noteOfFact = $this->noteOfFactBeforeDischarge();
        $noteOfFactBeforeDischarge = $this->voyageUiLibrary->renderNoteOfFactBeforeDischarge($noteOfFact);
        return $noteOfFactBeforeDischarge;
    }

    public function renderCoq()
    {

        $coqData = $this->coqData();
        return $this->voyageUiLibrary->renderCoq($coqData);


    }
    /**
     * Generate discharge summary for terminal after discharge reports
     * Shows before/after quantities and discharged amounts
     * 
     * @return object|null The discharge summary with all required fields
     */
    public function dischargeSummary()
    {
        // Only applicable for terminal after discharge reports
        if (!in_array($this->reportType, ['TERMINAL_AFTER_LINE_DISPLACE', 'TERMINAL_AFTER_FULL_DISCHARGE'])) {
            return null;
        }

        // Get current report summary (after discharge)
        $currentSummary = $this->ullageSummary();

        // Calculate quantity after discharge (current report's calculated values)
        // This is the quantity remaining in the vessel after discharge at this terminal
        $quantityAfterDischarge15 = $currentSummary->quantityBeforeDischargeAtFifteenMT;
        $quantityAfterDischarge20 = $currentSummary->quantityBeforeDischargeAtTwentyMT;

        try {
            // Get current report to determine terminal sequence number
            $currentReport = $this->getUllageReport();
            $currentTerminalSequenceNumber = $currentReport ? ($currentReport->terminal_sequence_number ?? 1) : 1;

            log_message('info', "Current report terminal sequence: {$currentTerminalSequenceNumber}");

            // Get before discharge report for the same terminal and sequence number
            $beforeDischargeLibrary = new self(
                $this->voyageId,
                'TERMINAL_BEFORE_DISCHARGE',
                $this->terminalId,
                $this->voyageProductId,
                $currentTerminalSequenceNumber // CRITICAL: Pass the same sequence number
            );

            $beforeSummary = $beforeDischargeLibrary->ullageSummary();

            // Calculate quantity before discharge
            $quantityBeforeDischarge15 = $beforeSummary->quantityBeforeDischargeAtFifteenMT;
            $quantityBeforeDischarge20 = $beforeSummary->quantityBeforeDischargeAtTwentyMT;

            // Calculate discharged quantities
            $quantityDischarged15 = $quantityBeforeDischarge15 - $quantityAfterDischarge15;
            $quantityDischarged20 = $quantityBeforeDischarge20 - $quantityAfterDischarge20;

            // Calculate discharged volume using BEFORE discharge W.C.F.T values
            // This is correct because we need the product characteristics from before discharge
            // When tanks are empty after discharge, current W.C.F.T values would be 0 or invalid
            $volumeDischarged15 = $beforeSummary->WCFT_56CAt15 > 0 ? ($quantityDischarged15 / $beforeSummary->WCFT_56CAt15) : 0;
            $volumeDischarged20 = $beforeSummary->WCFT_56CAt20 > 0 ? ($quantityDischarged20 / $beforeSummary->WCFT_56CAt20) : 0;

            // Check if this appears to be a newly created report (no discharge yet)
            $isNewlyCreated = (
                abs($quantityDischarged15) < 0.001 &&
                abs($quantityDischarged20) < 0.001 &&
                $quantityAfterDischarge15 > 0 && $quantityAfterDischarge20 > 0
            );

            return (object) [
                // Current report values (after discharge)
                'totalGSV15' => $currentSummary->totalGSV15,
                'totalGSV20' => $currentSummary->totalGSV20,
                'wcft56At15' => $currentSummary->WCFT_56CAt15,
                'wcft56At20' => $currentSummary->WCFT_56CAt20,
                'referenceDensity15' => $currentSummary->densityAtFifteen,
                'referenceDensity20' => $currentSummary->densityAtTwenty,

                // Quantities
                'quantityBeforeDischarge15' => round($quantityBeforeDischarge15, 3),
                'quantityBeforeDischarge20' => round($quantityBeforeDischarge20, 3),
                'quantityAfterDischarge15' => round($quantityAfterDischarge15, 3),
                'quantityAfterDischarge20' => round($quantityAfterDischarge20, 3),
                'quantityDischarged15' => round($quantityDischarged15, 3),
                'quantityDischarged20' => round($quantityDischarged20, 3),

                // Volume discharged
                'volumeDischarged15' => round($volumeDischarged15, 3),
                'volumeDischarged20' => round($volumeDischarged20, 3),

                // Additional info
                'reportType' => $this->reportType,
                'terminalId' => $this->terminalId,
                'hasBeforeReport' => $beforeSummary->totalGSV20 > 0,
                'beforeReportExists' => true,
                'isNewlyCreated' => $isNewlyCreated
            ];

        } catch (\Exception $e) {
            log_message('error', 'Error calculating discharge summary: ' . $e->getMessage());

            // Return partial data if before report not found
            return (object) [
                // Current report values only
                'totalGSV15' => $currentSummary->totalGSV15,
                'totalGSV20' => $currentSummary->totalGSV20,
                'wcft56At15' => $currentSummary->WCFT_56CAt15,
                'wcft56At20' => $currentSummary->WCFT_56CAt20,
                'referenceDensity15' => $currentSummary->densityAtFifteen,
                'referenceDensity20' => $currentSummary->densityAtTwenty,

                // No before discharge data available - use current values as after discharge
                'quantityBeforeDischarge15' => 0.000,
                'quantityBeforeDischarge20' => 0.000,
                'quantityAfterDischarge15' => round($quantityAfterDischarge15, 3),
                'quantityAfterDischarge20' => round($quantityAfterDischarge20, 3),
                'quantityDischarged15' => 0.000,
                'quantityDischarged20' => 0.000,
                'volumeDischarged15' => 0.000,
                'volumeDischarged20' => 0.000,

                // Additional info
                'reportType' => $this->reportType,
                'terminalId' => $this->terminalId,
                'hasBeforeReport' => false,
                'beforeReportExists' => false,
                'isNewlyCreated' => false,
                'error' => 'Before discharge report not found for this terminal'
            ];
        }
    }


    public function coqData()
    {
        $noteBefore = $this->noteOfFactBeforeDischarge();
        $density = $this->getVoyageProduct()->tbs_ref_density_20;
        $useArrival = $noteBefore->arrivalQuantityMT < $noteBefore->billOfLading ? true : false;
        $MT_VAC = $useArrival ? ($this->ullageSummary()->quantityBeforeDischargeAtTwentyVolume * $density) / 1000 : '';
        $data = (object) [
            'voyage_id' => $this->voyageId,
            'product_id' => $this->voyageProductId,
            'metric_tons_air' => $useArrival ? $noteBefore->arrivalQuantityMT : $noteBefore->billOfLading,
            'metric_tons_vacuum' => $MT_VAC,
            'long_tons' => 0.00,
            'litres_20c' => $useArrival ? $this->ullageSummary()->quantityBeforeDischargeAtTwentyVolume : 0.000,
            'litres_15c' => $useArrival ? $this->ullageSummary()->quantityBeforeDischargeAtFifteenVolume : 0.000,
            'us_bbls_60f' => 0.000,
            'us_gallons_60f' => 0.000,
            'density_15c' => $this->getVoyageProduct()->tbs_ref_density_15,
            'density_20c' => $this->getVoyageProduct()->tbs_ref_density_20,
        ];

        return $data;
    }



    /**
     * TERMINAL NOTE OF FACT – Cargo Difference
     * Calculates discharged quantity (Before – After) and all deltas
     *
     * @param int $voyageId The voyage ID
     * @param int $terminalId The terminal ID
     * @param int $voyageProductId The voyage product ID
     * @return array Array containing cargo difference data
     */
    public static function generateTerminalNoteOfFact(int $voyageId, int $terminalId, int $voyageProductId): array
    {
        try {
            // First, find the terminal sequence number by looking for the before discharge report
            $db = \Config\Database::connect();
            $beforeReport = $db->table('ullage_reports')
                ->where('voyage_id', $voyageId)
                ->where('discharge_terminal_id', $terminalId)
                ->where('voyage_product_id', $voyageProductId)
                ->where('report_type', 'TERMINAL_BEFORE_DISCHARGE')
                ->orderBy('created_at', 'DESC') // Get most recent one
                ->get()
                ->getRow();

            $terminalSequenceNumber = $beforeReport ? ($beforeReport->terminal_sequence_number ?? 1) : 1;

            log_message('info', "generateTerminalNoteOfFact: Using terminal sequence {$terminalSequenceNumber} for terminal {$terminalId}");

            // Create UllageLibrary instances with sequence number
            $before = new self($voyageId, 'TERMINAL_BEFORE_DISCHARGE', $terminalId, $voyageProductId, $terminalSequenceNumber);
            $after = new self($voyageId, 'TERMINAL_AFTER_FULL_DISCHARGE', $terminalId, $voyageProductId, $terminalSequenceNumber);

            // Check if reports exist
            $beforeReportActual = $before->getUllageReport();
            $afterReportActual = $after->getUllageReport();

            if (!$beforeReportActual) {
                log_message('warning', "TERMINAL_BEFORE_DISCHARGE report not found for terminal $terminalId sequence $terminalSequenceNumber, voyage $voyageId");
            }
            if (!$afterReportActual) {
                log_message('warning', "TERMINAL_AFTER_FULL_DISCHARGE report not found for terminal $terminalId sequence $terminalSequenceNumber, voyage $voyageId");
            }

            $b = $before->ullageSummary();
            $a = $after->ullageSummary();

            // Vessel's discharged quantities (before – after)
            $vesselQty15 = $b->quantityBeforeDischargeAtFifteenMT - $a->quantityBeforeDischargeAtFifteenMT;
            $vesselQty20 = $b->quantityBeforeDischargeAtTwentyMT - $a->quantityBeforeDischargeAtTwentyMT;

            // Terminal BOL from ullage_reports.terminal_bol_mt (before discharge report)
            $bolQty = $beforeReportActual ? (float) ($beforeReportActual->terminal_bol_mt ?? 0) : 0;

            // If terminal_bol_mt is not set, fallback to voyage-level BOL
            if ($bolQty == 0) {
                $bolQty = $before->getBillOfLading();
                log_message('info', "generateTerminalNoteOfFact: Using voyage-level BOL as fallback for terminal {$terminalId}");
            }

            // Shore figures from ullage_reports table (AFTER report)
            $shoreQty = $afterReportActual ? (float) ($afterReportActual->shore_weight_mt ?? 0) : 0;

            // Terminal name
            $terminalModel = new \App\Modules\MetrologicalSupervision\Models\TerminalModel();
            $terminal = $terminalModel->find($terminalId);

            // Differences calculations
            $diffQty1 = $vesselQty15 - $bolQty;
            $diffPct1 = $bolQty ? ($diffQty1 / $bolQty) * 100 : 0;
            $diffQty2 = $shoreQty - $vesselQty20;
            $diffPct2 = $vesselQty20 ? ($diffQty2 / $vesselQty20) * 100 : 0;

            return [
                'receiver' => $terminal ? $terminal->name : 'N/A',
                'bol_qty' => round($bolQty, 3),
                'vessel_qty_15' => round($vesselQty15, 3),
                'vessel_qty_20' => round($vesselQty20, 3),
                'shore_qty' => round($shoreQty, 3),
                'diff_qty_1' => round($diffQty1, 3),
                'diff_pct_1' => round($diffPct1, 2),
                'diff_qty_2' => round($diffQty2, 3),
                'diff_pct_2' => round($diffPct2, 2),
            ];

        } catch (\Exception $e) {
            log_message('error', 'generateTerminalNoteOfFact error: ' . $e->getMessage());

            // Return safe defaults on error
            return [
                'receiver' => 'N/A',
                'bol_qty' => 0.000,
                'vessel_qty_15' => 0.000,
                'vessel_qty_20' => 0.000,
                'shore_qty' => 0.000,
                'diff_qty_1' => 0.000,
                'diff_pct_1' => 0.00,
                'diff_qty_2' => 0.000,
                'diff_pct_2' => 0.00,
            ];
        }
    }
}
