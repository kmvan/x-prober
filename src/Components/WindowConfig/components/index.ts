export const WindowConfig = {
  IS_DEV: (window as unknown as { IS_DEV: boolean }).IS_DEV,
  AUTHORIZATION: (window as unknown as { AUTHORIZATION: string }).AUTHORIZATION,
} satisfies {
  IS_DEV: boolean;
  AUTHORIZATION: string;
};
