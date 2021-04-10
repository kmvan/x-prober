import { hexToRgb } from './hex-to-rgb'
import { rgbaToHex } from './rgba-to-hex'
export const gradientColors = (
  startColor: string,
  endColor: string,
  step: number = 100
) => {
  const sColor = hexToRgb(startColor)
  const eColor = hexToRgb(endColor)
  const rStep = (eColor[0] - sColor[0]) / step
  const gStep = (eColor[1] - sColor[1]) / step
  const bStep = (eColor[2] - sColor[2]) / step
  const colors: string[] = []
  for (let i = 0; i < step; i++) {
    colors.push(
      rgbaToHex(
        ~~(rStep * i + sColor[0]),
        ~~(gStep * i + sColor[1]),
        ~~(bStep * i + sColor[2])
      )
    )
  }
  return colors
}
