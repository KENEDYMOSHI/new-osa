/**
 * Service Types – canonical list of WMA equipment / service categories.
 *
 * Each entry has:
 *  • `key`   – a stable, kebab-case identifier safe for routing, forms, DB keys, etc.
 *  • `label` – the human-readable display name.
 *
 * Import `SERVICE_TYPES` for the full array, or `ServiceTypeKey` / `ServiceType`
 * when you need type-safety.
 */

export interface ServiceType {
  /** Stable identifier (kebab-case). Use this in URLs, form values, DB records, etc. */
  readonly key: string;
  /** Human-readable display name. */
  readonly label: string;
}

export const SERVICE_TYPES: readonly ServiceType[] = [
  { key: 'vtv', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.VTV' },
  { key: 'weighbridge', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.WEIGHBRIDGE' },
  { key: 'fixed-storage-tank', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.FIXED_STORAGE_TANK' },
  { key: 'bst', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.BST' },
  { key: 'pre-packaging', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.PRE_PACKAGING' },
  { key: 'wagon-tank', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.WAGON_TANK' },
  { key: 'fuel-pump', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.FUEL_PUMP' },
  { key: 'flow-meter', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.FLOW_METER' },
  { key: 'check-pump', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.CHECK_PUMP' },
  { key: 'pressure-gauges', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.PRESSURE_GAUGES' },
  { key: 'proving-tank', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.PROVING_TANK' },
  { key: 'taximeter', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.TAXIMETER' },
  { key: 'metre-rule', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.METRE_RULE' },
  { key: 'tape-measure', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.TAPE_MEASURE' },
  { key: 'brim-measure-system', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.BRIM_MEASURE_SYSTEM' },
  { key: 'suspended-digital-ware', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.SUSPENDED_DIGITAL_WARE' },
  { key: 'counter-scale', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.COUNTER_SCALE' },
  { key: 'platform-scale', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.PLATFORM_SCALE' },
  { key: 'spring-balance', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.SPRING_BALANCE' },
  { key: 'weigher', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.WEIGHER' },
  { key: 'automatic-weigher', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.AUTOMATIC_WEIGHER' },
  { key: 'beam-scale', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.BEAM_SCALE' },
  { key: 'sbl', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.SBL' },
  { key: 'other-measuring-instrument', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.OTHER_MEASURING_INSTRUMENT' },
  { key: 'other-measures-of-length', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.OTHER_MEASURES_OF_LENGTH' },
  { key: 'domestic-gas-meter', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.DOMESTIC_GAS_METER' },
  { key: 'weights', label: 'BUSINESS.EQUIPMENT_PAGE.SERVICE_TYPES.WEIGHTS' },
] as const;

/** Union type of all valid service-type keys. */
export type ServiceTypeKey = (typeof SERVICE_TYPES)[number]['key'];

// ── Helpers ───────────────────────────────────────────────────────────

/** Map for O(1) lookups by key. */
const SERVICE_TYPE_MAP = new Map<string, ServiceType>(
  SERVICE_TYPES.map((st) => [st.key, st]),
);

/** Get the label for a given service-type key. Returns the key itself as fallback. */
export function getServiceTypeLabel(key: string): string {
  return SERVICE_TYPE_MAP.get(key)?.label ?? key;
}

/** Get the full ServiceType object for a given key, or `undefined`. */
export function getServiceType(key: string): ServiceType | undefined {
  return SERVICE_TYPE_MAP.get(key);
}

/** Get just the labels as a flat string array (useful for dropdowns / selects). */
export function getServiceTypeLabels(): string[] {
  return SERVICE_TYPES.map((st) => st.label);
}
