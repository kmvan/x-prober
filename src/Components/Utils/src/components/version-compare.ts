export const versionCompare = (
  left: string,
  right: string
): boolean | number => {
  if (typeof left + typeof right !== 'stringstring') {
    return false
  }
  const a = left.split('.')
  const b = right.split('.')
  const len = Math.max(a.length, b.length)
  for (let i = 0; i < len; i += 1) {
    if ((a[i] && !b[i] && Number(a[i]) > 0) || Number(a[i]) > Number(b[i])) {
      return 1
    }
    if ((b[i] && !a[i] && Number(b[i]) > 0) || Number(a[i]) < Number(b[i])) {
      return -1
    }
  }
  return 0
}
