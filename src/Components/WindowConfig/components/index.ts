import type { WindowConfigProps, WindowProps } from './typings.ts';
export const WindowConfig = {
  IS_DEV: Boolean(
    (window as unknown as WindowProps)?.GLOBAL_CONFIG?.IS_DEV ?? false
  ),
  AUTHORIZATION: String(
    (window as unknown as WindowProps)?.GLOBAL_CONFIG?.AUTHORIZATION ?? ''
  ),
} as const satisfies WindowConfigProps;
