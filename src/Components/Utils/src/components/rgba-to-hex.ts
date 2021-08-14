/* eslint-disable no-bitwise */
export const rgbaToHex = (
  red: number,
  green: number,
  blue: number,
  alpha = 1
): string => {
  const hex = `${(red | (1 << 8)).toString(16).slice(1)}${(green | (1 << 8))
    .toString(16)
    .slice(1)}${(blue | (1 << 8)).toString(16).slice(1)}`
  const colorAlpha =
    alpha === 1 ? '' : ((alpha * 255) | (1 << 8)).toString(16).slice(1)
  return `${hex}${colorAlpha}`
}
