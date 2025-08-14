export type ButtonStatusKey = 'Error' | 'Loading' | 'Warning' | 'Pointer';
export type ButtonStatusValue = 'error' | 'loading' | 'warning' | 'pointer';
export const ButtonStatus = {
  Error: 'error',
  Loading: 'loading',
  Warning: 'warning',
  Pointer: 'pointer',
} as const satisfies Record<ButtonStatusKey, ButtonStatusValue>;
