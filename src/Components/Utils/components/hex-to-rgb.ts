export const hexToRgb = (hex: string): number[] => {
  hex = hex.replace('#', '')
  const arrBuff = new ArrayBuffer(4)
  const vw = new DataView(arrBuff)
  vw.setUint32(0, parseInt(hex, 16), false)
  const arrByte = new Uint8Array(arrBuff)
  return [arrByte[1], arrByte[2], arrByte[3]]
}
