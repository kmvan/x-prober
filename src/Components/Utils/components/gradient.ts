import { hexToRgb } from './hex-to-rgb'
import { rgbaToHex } from './rgba-to-hex'
export const gradientColors = (
  startColor: string,
  endColor: string,
  step = 100,
): string[] => {
  const sColor = hexToRgb(startColor)
  const eColor = hexToRgb(endColor)
  const rStep = (eColor[0] - sColor[0]) / step
  const gStep = (eColor[1] - sColor[1]) / step
  const bStep = (eColor[2] - sColor[2]) / step
  const colors: string[] = []
  for (let i = 0; i < step; i += 1) {
    colors.push(
      rgbaToHex(
        Number(rStep * i + sColor[0]),
        Number(gStep * i + sColor[1]),
        Number(bStep * i + sColor[2]),
      ),
    )
  }
  return colors
}
