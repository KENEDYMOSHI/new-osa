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
  { key: 'vtv', label: 'Vehicle Tank Verification (VTV)' },
  { key: 'weighbridge', label: 'Weighbridge' },
  { key: 'fixed-storage-tank', label: 'Fixed Storage Tank' },
  { key: 'bst', label: 'Bulk Storage Tank (BST)' },
  { key: 'pre-packaging', label: 'Pre Packaging' },
  { key: 'wagon-tank', label: 'Wagon Tank' },
  { key: 'fuel-pump', label: 'Fuel pump' },
  { key: 'flow-meter', label: 'Flow Meter' },
  { key: 'check-pump', label: 'Check pump' },
  { key: 'pressure-gauges', label: 'Pressure gauges' },
  { key: 'proving-tank', label: 'Proving Tank' },
  { key: 'taximeter', label: 'Taximeter' },
  { key: 'metre-rule', label: 'Metre Rule' },
  { key: 'tape-measure', label: 'Tape Measure' },
  { key: 'brim-measure-system', label: 'Brim Measure system' },
  { key: 'suspended-digital-ware', label: 'Suspended Digital Ware' },
  { key: 'counter-scale', label: 'Counter scale' },
  { key: 'platform-scale', label: 'Platform scale' },
  { key: 'spring-balance', label: 'Spring Balance' },
  { key: 'weigher', label: 'Weigher' },
  { key: 'automatic-weigher', label: 'Automatic Weigher' },
  { key: 'beam-scale', label: 'Beam Scale' },
  { key: 'sbl', label: 'Sandy & Ballast lorry (SBL)' },
  { key: 'other-measuring-instrument', label: 'Other Measuring Instrument' },
  { key: 'other-measures-of-length', label: 'Other Measures of Length' },
  { key: 'domestic-gas-meter', label: 'Domestic gas meter' },
  { key: 'weights', label: 'Weights' },
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
