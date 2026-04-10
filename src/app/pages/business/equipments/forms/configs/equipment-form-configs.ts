import {
  EquipmentFormConfig,
  FormField,
  ServiceCategory,
} from '../models/form-field.model';

// ═══════════════════════════════════════════════════════════════════════
// Field builder helpers – keep individual configs concise
// ═══════════════════════════════════════════════════════════════════════

const text = (
  key: string, label: string, placeholder = '', required = true, gridSpan: 1 | 2 = 1,
): FormField => ({ key, label, type: 'text', placeholder, required, gridSpan });

const num = (
  key: string, label: string, placeholder = '', required = true,
): FormField => ({ key, label, type: 'number', placeholder, required });

const date = (
  key: string, label: string, required = true,
): FormField => ({ key, label, type: 'date', required });

const sel = (
  key: string, label: string, opts: string[], required = true,
): FormField => ({
  key, label, type: 'select', required,
  options: opts.map(o => ({ value: o.toLowerCase().replace(/\s+/g, '-'), label: o })),
});

const file = (
  key: string, label: string, accept = '*', required = false,
): FormField => ({ key, label, type: 'file', accept, required, gridSpan: 2 });

const textarea = (
  key: string, label: string, placeholder = '', required = false,
): FormField => ({ key, label, type: 'textarea', placeholder, required, gridSpan: 2 });

// ═══════════════════════════════════════════════════════════════════════
// Reusable field presets
// ═══════════════════════════════════════════════════════════════════════

const serialNumber    = text('serialNumber', 'Serial Number', 'Enter serial number', false);
const stickerNumber   = text('stickerNumber', 'Sticker Number', 'Enter sticker number', false);
const sealNumber      = text('sealNumber', 'Seal Number', 'Enter seal number', false);
const sealSerial      = text('sealSerialNumber', 'Seal Serial Number', 'Enter seal serial number', false);
const product         = sel('product', 'Product', ['Petrol', 'Diesel', 'Kerosene', 'LPG', 'Jet Fuel', 'Other']);
const verifyDate      = date('verificationDate', 'Verification Date', false);
const nextVerifyDate  = date('nextVerificationDate', 'Next Verification Date', false);
const calibDate       = date('lastCalibrationDate', 'Last Calibration Date', false);
const nextCalibDate   = date('nextCalibrationDate', 'Next Calibration Date', false);
const maxCapacity     = num('maxCapacity', 'Maximum Capacity', 'e.g., 500 kg');
const minCapacity     = num('minCapacity', 'Minimum Capacity', 'e.g., 1 kg');
const scaleDivision   = text('scaleDivision', 'Scale Division', 'e.g., 0.1 kg');

// ── Config shorthand ──
const cfg = (
  serviceTypeKey: string,
  serviceTypeLabel: string,
  category: ServiceCategory,
  itemLabel: string,
  description: string,
  fields: FormField[],
  allowMultiple = true,
): EquipmentFormConfig => ({
  serviceTypeKey, serviceTypeLabel, category, itemLabel, description, allowMultiple, fields,
});

// ═══════════════════════════════════════════════════════════════════════
//  ALL 27 SERVICE-TYPE FORM CONFIGS
// ═══════════════════════════════════════════════════════════════════════

export const EQUIPMENT_FORM_CONFIGS: Record<string, EquipmentFormConfig> = {

  // ─── PETROLEUM & FUEL ──────────────────────────────────────────────

  'vtv': cfg('vtv', 'Vehicle Tank Verification (VTV)', 'petroleum', 'Vehicle Tank',
    'Register vehicle tanks for verification of capacity and compartments.', [
      text('vehicleReg', 'Vehicle Registration No.', 'e.g., T 123 ABC'),
      text('tankNumber', 'Tank / Compartment ID', 'e.g., VTV-C01'),
      product,
      num('compartmentCapacity', 'Compartment Capacity (Litres)', 'e.g., 10000'),
      stickerNumber, sealNumber, serialNumber,
      calibDate, nextCalibDate,
    ]),

  'weighbridge': cfg('weighbridge', 'Weighbridge', 'weighing', 'Weighbridge',
    'Register weighbridges used for vehicle and cargo weighing.', [
      text('weighbridgeName', 'Weighbridge Name / ID', 'e.g., WB-Main Gate'),
      text('location', 'Location', 'e.g., Main Entrance'),
      maxCapacity, minCapacity, scaleDivision,
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'fixed-storage-tank': cfg('fixed-storage-tank', 'Fixed Storage Tank', 'petroleum', 'Tank',
    'Register fixed underground or above-ground storage tanks.', [
      text('tankNumber', 'Tank Number', 'e.g., FST-001'),
      product,
      num('tankCapacity', 'Tank Capacity (Litres)', 'e.g., 30000'),
      stickerNumber, sealNumber, serialNumber,
      calibDate, nextCalibDate,
      file('inspectionChart', 'Upload Underground Tank Inspection Chart', '.xlsx,.xls,.pdf'),
    ]),

  'bst': cfg('bst', 'Bulk Storage Tank (BST)', 'petroleum', 'Tank',
    'Register bulk storage tanks for petroleum products.', [
      text('tankNumber', 'Tank Number', 'e.g., BST-001'),
      product,
      num('tankCapacity', 'Tank Capacity (Litres)', 'e.g., 30000'),
      stickerNumber, sealNumber, serialNumber,
      calibDate, nextCalibDate,
    ]),

  'pre-packaging': cfg('pre-packaging', 'Pre Packaging', 'other', 'Package',
    'Register pre-packaged goods measuring equipment.', [
      text('packageName', 'Package Name', 'e.g., 5L Cooking Oil'),
      sel('packageType', 'Package Type', ['Bottle', 'Can', 'Bag', 'Drum', 'Carton', 'Other']),
      text('productType', 'Product Type', 'e.g., Cooking Oil'),
      num('nominalQuantity', 'Nominal Quantity', 'e.g., 5'),
      sel('unit', 'Unit of Measurement', ['Litres (L)', 'Millilitres (mL)', 'Kilograms (kg)', 'Grams (g)']),
      stickerNumber, sealNumber, serialNumber,
      verifyDate, nextVerifyDate,
    ]),

  'wagon-tank': cfg('wagon-tank', 'Wagon Tank', 'petroleum', 'Wagon Tank',
    'Register railway wagon tanks used for bulk product transport.', [
      text('wagonNumber', 'Wagon Number', 'e.g., WGT-012'),
      product,
      num('tankCapacity', 'Tank Capacity (Litres)', 'e.g., 50000'),
      num('compartments', 'Number of Compartments', 'e.g., 3'),
      stickerNumber, sealNumber, serialNumber,
      calibDate, nextCalibDate,
    ]),

  'fuel-pump': cfg('fuel-pump', 'Fuel Pump', 'petroleum', 'Pump',
    'Register fuel dispensing pumps with nozzle and seal details.', [
      text('pumpName', 'Pump Name', 'e.g., Forecourt Pump FP-01'),
      serialNumber, product,
      stickerNumber, sealNumber, sealSerial,
      sel('pumpType', 'Type of Pump', ['MC (Mechanical Counter)', 'Electronic', 'Suction', 'Other']),
      num('nozzleCount', 'No. of Nozzles', 'e.g., 4'),
      textarea('inspectionReport', 'Inspection Report of Nozzle', 'Enter inspection details…'),
      verifyDate, nextVerifyDate,
    ]),

  'flow-meter': cfg('flow-meter', 'Flow Meter', 'petroleum', 'Flow Meter',
    'Register flow meters used for liquid measurement.', [
      text('meterName', 'Meter Name / ID', 'e.g., FM-002'),
      sel('meterType', 'Meter Type', ['Positive Displacement', 'Turbine', 'Ultrasonic', 'Coriolis', 'Other']),
      text('sizeDiameter', 'Size / Diameter', 'e.g., 2 inches'),
      serialNumber, stickerNumber, sealNumber,
      text('flowRange', 'Flow Range', 'e.g., 20–200 L/min'),
      calibDate, nextCalibDate,
    ]),

  'check-pump': cfg('check-pump', 'Check Pump', 'petroleum', 'Check Pump',
    'Register check pumps for fuel dispenser verification.', [
      text('pumpName', 'Pump Name / ID', 'e.g., CP-003'),
      sel('pumpType', 'Pump Type', ['Manual', 'Automatic', 'Other']),
      serialNumber, stickerNumber, sealNumber,
      num('capacity', 'Capacity (Litres)', 'e.g., 20'),
      verifyDate, nextVerifyDate,
    ]),

  'pressure-gauges': cfg('pressure-gauges', 'Pressure Gauges', 'metering', 'Gauge',
    'Register pressure gauges for calibration and verification.', [
      text('gaugeName', 'Gauge Name / ID', 'e.g., PG-007'),
      sel('gaugeType', 'Gauge Type', ['Bourdon Tube', 'Diaphragm', 'Digital', 'Other']),
      text('pressureRange', 'Pressure Range', 'e.g., 0–100 bar'),
      sel('accuracyClass', 'Accuracy Class', ['0.6', '1.0', '1.6', '2.5', '4.0']),
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'proving-tank': cfg('proving-tank', 'Proving Tank', 'petroleum', 'Proving Tank',
    'Register proving tanks used for volumetric calibration.', [
      text('tankName', 'Tank Name / ID', 'e.g., PT-001'),
      num('capacity', 'Capacity (Litres)', 'e.g., 500'),
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  // ─── METERING ──────────────────────────────────────────────────────

  'taximeter': cfg('taximeter', 'Taximeter', 'metering', 'Taximeter',
    'Register taximeters fitted in commercial vehicles.', [
      text('meterName', 'Meter Name / ID', 'e.g., TM-045'),
      text('vehicleReg', 'Vehicle Registration No.', 'e.g., T 456 DEF'),
      text('meterModel', 'Meter Type / Model', 'e.g., Digitax F2'),
      serialNumber, stickerNumber, sealNumber,
      text('tariffSetting', 'Tariff Setting', 'e.g., Rate A – City'),
      verifyDate, nextVerifyDate,
    ]),

  'domestic-gas-meter': cfg('domestic-gas-meter', 'Domestic Gas Meter', 'metering', 'Gas Meter',
    'Register domestic gas meters for consumption measurement.', [
      text('meterName', 'Meter Name / ID', 'e.g., DGM-101'),
      sel('meterType', 'Meter Type', ['Diaphragm', 'Rotary', 'Turbine', 'Ultrasonic', 'Other']),
      num('maxFlowRate', 'Maximum Flow Rate (m³/h)', 'e.g., 6'),
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  // ─── LENGTH & VOLUME ───────────────────────────────────────────────

  'metre-rule': cfg('metre-rule', 'Metre Rule', 'length', 'Metre Rule',
    'Register metre rules used for length measurement.', [
      text('ruleName', 'Rule Name / ID', 'e.g., MR-010'),
      num('length', 'Length (metres)', 'e.g., 1'),
      sel('material', 'Material', ['Steel', 'Aluminium', 'Wood', 'Other']),
      serialNumber, stickerNumber, sealNumber,
      verifyDate, nextVerifyDate,
    ]),

  'tape-measure': cfg('tape-measure', 'Tape Measure', 'length', 'Tape Measure',
    'Register tape measures used for linear measurement.', [
      text('tapeName', 'Tape Name / ID', 'e.g., TM-008'),
      num('length', 'Length (metres)', 'e.g., 50'),
      sel('material', 'Material', ['Steel', 'Fibreglass', 'Cloth', 'Other']),
      serialNumber, stickerNumber, sealNumber,
      verifyDate, nextVerifyDate,
    ]),

  'brim-measure-system': cfg('brim-measure-system', 'Brim Measure System', 'length', 'Brim Measure',
    'Register brim measure systems for volumetric measurement.', [
      text('measureName', 'Measure Name / ID', 'e.g., BMS-003'),
      num('capacity', 'Capacity (Litres)', 'e.g., 200'),
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'other-measures-of-length': cfg('other-measures-of-length', 'Other Measures of Length', 'length', 'Instrument',
    'Register other length measuring instruments not covered by standard categories.', [
      text('instrumentName', 'Instrument Name', 'Enter instrument name'),
      text('lengthRange', 'Length / Range', 'e.g., 0–30 m'),
      sel('material', 'Material', ['Steel', 'Aluminium', 'Fibreglass', 'Other']),
      serialNumber, stickerNumber, sealNumber,
      verifyDate, nextVerifyDate,
    ]),

  // ─── WEIGHING & MASS ──────────────────────────────────────────────

  'counter-scale': cfg('counter-scale', 'Counter Scale', 'weighing', 'Scale',
    'Register counter-top scales used for retail weighing.', [
      text('scaleName', 'Scale Name / ID', 'e.g., CS-015'),
      maxCapacity, minCapacity, scaleDivision,
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'platform-scale': cfg('platform-scale', 'Platform Scale', 'weighing', 'Scale',
    'Register platform scales for heavy-duty weighing.', [
      text('scaleName', 'Scale Name / ID', 'e.g., PS-022'),
      maxCapacity, minCapacity, scaleDivision,
      text('platformSize', 'Platform Size', 'e.g., 1.2 m × 1.5 m'),
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'spring-balance': cfg('spring-balance', 'Spring Balance', 'weighing', 'Balance',
    'Register spring balances for suspended weighing.', [
      text('balanceName', 'Balance Name / ID', 'e.g., SB-009'),
      maxCapacity, scaleDivision,
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'weigher': cfg('weigher', 'Weigher', 'weighing', 'Weigher',
    'Register general-purpose weighing instruments.', [
      text('weigherName', 'Weigher Name / ID', 'e.g., W-030'),
      sel('weigherType', 'Type', ['Bench', 'Floor', 'Hanging', 'Other']),
      maxCapacity, minCapacity, scaleDivision,
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'automatic-weigher': cfg('automatic-weigher', 'Automatic Weigher', 'weighing', 'Weigher',
    'Register automatic weighing instruments for continuous or batch processing.', [
      text('weigherName', 'Weigher Name / ID', 'e.g., AW-005'),
      sel('weigherType', 'Type', ['Belt Weigher', 'Check Weigher', 'Batch Weigher', 'Other']),
      maxCapacity, minCapacity,
      text('speed', 'Speed / Throughput', 'e.g., 120 items/min'),
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'beam-scale': cfg('beam-scale', 'Beam Scale', 'weighing', 'Scale',
    'Register beam scales (balance-beam weighing instruments).', [
      text('scaleName', 'Scale Name / ID', 'e.g., BS-011'),
      maxCapacity, scaleDivision,
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'sbl': cfg('sbl', 'Sandy & Ballast Lorry (SBL)', 'weighing', 'Lorry',
    'Register sandy and ballast lorries for volumetric compliance.', [
      text('vehicleReg', 'Vehicle Registration No.', 'e.g., T 789 GHI'),
      sel('lorryType', 'Lorry Type', ['Tipper', 'Flatbed', 'Other']),
      num('loadingCapacity', 'Loading Capacity (m³)', 'e.g., 7'),
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'weights': cfg('weights', 'Weights', 'weighing', 'Weight Set',
    'Register standard weights used for calibration and trade.', [
      sel('weightClass', 'Weight Class', ['E1', 'E2', 'F1', 'F2', 'M1', 'M2', 'M3']),
      text('nominalValue', 'Nominal Value', 'e.g., 20'),
      sel('unit', 'Unit', ['kg', 'g', 'mg']),
      sel('material', 'Material', ['Stainless Steel', 'Cast Iron', 'Brass', 'Other']),
      serialNumber, stickerNumber, sealNumber,
      verifyDate, nextVerifyDate,
    ]),

  // ─── OTHER ─────────────────────────────────────────────────────────

  'suspended-digital-ware': cfg('suspended-digital-ware', 'Suspended Digital Ware', 'other', 'Ware',
    'Register suspended digital weighing equipment.', [
      text('wareName', 'Ware Name / ID', 'e.g., SDW-004'),
      maxCapacity, minCapacity, scaleDivision,
      serialNumber, stickerNumber, sealNumber,
      calibDate, nextCalibDate,
    ]),

  'other-measuring-instrument': cfg('other-measuring-instrument', 'Other Measuring Instrument', 'other', 'Instrument',
    'Register measuring instruments not covered by standard categories.', [
      text('instrumentName', 'Instrument Name', 'Enter instrument name'),
      text('instrumentType', 'Instrument Type', 'e.g., Hydrometer'),
      textarea('description', 'Description', 'Describe the instrument and its use…'),
      serialNumber, stickerNumber, sealNumber,
      verifyDate, nextVerifyDate,
    ]),
};

// ═══════════════════════════════════════════════════════════════════════
//  Helpers
// ═══════════════════════════════════════════════════════════════════════

export function getFormConfig(key: string): EquipmentFormConfig | undefined {
  return EQUIPMENT_FORM_CONFIGS[key];
}

export function getAllFormConfigKeys(): string[] {
  return Object.keys(EQUIPMENT_FORM_CONFIGS);
}
