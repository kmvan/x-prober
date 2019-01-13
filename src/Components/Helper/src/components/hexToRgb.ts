const hexToRgb = (hex: string): number[] => {
  const rgb: number[] = []

  for (let i = 1; i < 7; i += 2) {
    rgb.push(parseInt('0x' + hex.slice(i, i + 2), 16))
  }

  return rgb
}

export default hexToRgb
