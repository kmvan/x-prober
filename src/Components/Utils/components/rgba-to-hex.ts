export const rgbaToHex = (
  red: number,
  green: number,
  blue: number,
  alpha = 1
): string => {
  const hex = `${red.toString(16).padStart(2, '0')}${green.toString(16).padStart(2, '0')}${blue.toString(16).padStart(2, '0')}`;
  const colorAlpha =
    alpha === 1
      ? ''
      : Math.round(alpha * 255)
          .toString(16)
          .padStart(2, '0');
  return `${hex}${colorAlpha}`;
};
